<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\AngsuranKolektif;
use App\Models\AngsuranKolektifDetail;
use App\Models\Kantor;
use App\Models\Kelompok;
use App\Models\Pinjaman;
use Illuminate\Http\Request;

class AngsuranKolektifController extends Controller
{
    /**
     * Map a route name to a variant page directory and title.
     */
    private function variantMeta(string $routeName): array
    {
        $map = [
            'penalti-kolektif-tunai' => ['page' => 'PenaltiKolektifTunai', 'title' => 'Penalti Pinjaman Kolektif Tunai', 'jenis' => 'penalti', 'metode' => 'tunai'],
            'setoran-angsuran-bank' => ['page' => 'SetoranAngsuranBank', 'title' => 'Setoran Simpanan & Angsuran Bank', 'jenis' => 'angsuran_dan_setoran', 'metode' => 'bank'],
            'setoran-angsuran-custom' => ['page' => 'SetoranAngsuranCustom', 'title' => 'Setoran Simpanan & Angsuran Custom', 'jenis' => 'angsuran_dan_setoran', 'metode' => 'custom'],
            'angsuran-kolektif-tunai' => ['page' => 'AngsuranKolektifTunai', 'title' => 'Angsuran Kolektif Tunai', 'jenis' => 'angsuran', 'metode' => 'tunai'],
            'angsuran-kolektif-debet-simpanan' => ['page' => 'AngsuranKolektifDebetSimpanan', 'title' => 'Angsuran Kolektif Debet Simpanan', 'jenis' => 'angsuran', 'metode' => 'debet_simpanan'],
        ];

        foreach ($map as $key => $meta) {
            if (str_contains($routeName, $key)) {
                return $meta;
            }
        }

        return ['page' => 'AngsuranKolektif', 'title' => 'Angsuran Kolektif'];
    }

    private function detectRouteName(): string
    {
        return request()->route()?->getName() ?? '';
    }

    public function index(Request $request)
    {
        $routeName = $this->detectRouteName();
        $meta = $this->variantMeta($routeName);

        $query = AngsuranKolektif::query()
            ->with(['kelompok:id,nama', 'user:id,nama', 'kantor:id,nama_kantor']);

        // Auto-apply jenis/metode filter from variant
        if (!empty($meta['jenis'])) {
            $query->where('jenis', $meta['jenis']);
        }
        if (!empty($meta['metode'])) {
            $query->where('metode_pembayaran', $meta['metode']);
        }

        $query->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('no_transaksi', 'LIKE', "%{$term}%")
                    ->orWhereHas('kelompok', fn ($k) => $k->where('nama', 'LIKE', "%{$term}%")));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')))
            ->orderByDesc('tgl_transaksi')
            ->orderByDesc('id');

        $data = $query->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia("Superadmin/{$meta['page']}/Index", [
            'transaksi' => $data,
            'filters' => $request->only(['search', 'status', 'mulai', 'sampai']),
            'variantTitle' => $meta['title'],
        ]);
    }

    public function create(Request $request)
    {
        $routeName = $this->detectRouteName();
        $meta = $this->variantMeta($routeName);
        $jenis = $request->query('jenis', 'angsuran');
        $metode = $request->query('metode', 'tunai');

        return inertia("Superadmin/{$meta['page']}/Create", [
            'kelompoks' => Kelompok::select('id', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenis' => $jenis,
            'metode' => $metode,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl_transaksi' => 'required|date',
            'kelompok_id' => 'required|exists:kelompok,id',
            'jenis' => 'required|in:angsuran,penalti,angsuran_dan_setoran',
            'metode_pembayaran' => 'required|in:tunai,debet_simpanan,bank,custom',
            'nominal_total' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kantor_id' => 'required|exists:kantor,id',
            'status' => 'in:draft,posted,batal',
            'details' => 'required|array|min:1',
            'details.*.pinjaman_id' => 'required|exists:pinjaman,id',
            'details.*.anggota_id' => 'required|exists:anggota,id',
            'details.*.angsuran_ke' => 'required|integer|min:1',
            'details.*.nominal_pokok' => 'required|numeric|min:0',
            'details.*.nominal_bunga' => 'required|numeric|min:0',
            'details.*.total_angsuran' => 'required|numeric|min:0',
            'details.*.setoran_simpanan' => 'nullable|numeric|min:0',
            'details.*.denda' => 'nullable|numeric|min:0',
        ]);

        $validated['no_transaksi'] = 'AK-' . date('YmdHis') . rand(10, 99);
        $validated['user_id'] = auth()->id();
        $validated['status'] = $validated['status'] ?? 'draft';
        $validated['jumlah_anggota'] = count($validated['details']);
        $validated['denda'] = $validated['denda'] ?? 0;

        $details = $validated['details'];
        unset($validated['details']);

        $kolektif = AngsuranKolektif::create($validated);

        $userId = $kolektif->user_id;

        foreach ($details as $detail) {
            $detail['angsuran_kolektif_id'] = $kolektif->id;
            $detail['denda'] = $detail['denda'] ?? 0;
            $detail['user_id'] = $userId;
            AngsuranKolektifDetail::create($detail);
        }

        $routeName = $this->detectRouteName();

        // Get the base route prefix (without .store suffix) for redirect
        $basePrefix = str_replace('.store', '', $routeName);

        return redirect()->route($basePrefix)
            ->with('success', 'Transaksi kolektif berhasil ditambahkan.');
    }

    public function show(AngsuranKolektif $angsuranKolektif)
    {
        $angsuranKolektif->load(['kelompok', 'user', 'kantor', 'details.pinjaman.anggota']);

        $routeName = $this->detectRouteName();
        $meta = $this->variantMeta($routeName);

        $data = $angsuranKolektif->toArray();
        $data['variant_title'] = $meta['title'];

        return inertia("Superadmin/{$meta['page']}/Show", [
            'transaksi' => $data,
        ]);
    }

    public function edit(AngsuranKolektif $angsuranKolektif)
    {
        $angsuranKolektif->load(['details.pinjaman.anggota', 'kelompok']);

        $routeName = $this->detectRouteName();
        $meta = $this->variantMeta($routeName);

        $data = $angsuranKolektif->toArray();
        $data['variant_title'] = $meta['title'];

        return inertia("Superadmin/{$meta['page']}/Edit", [
            'transaksi' => $data,
            'kelompoks' => Kelompok::select('id', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
        ]);
    }

    public function update(Request $request, AngsuranKolektif $angsuranKolektif)
    {
        $validated = $request->validate([
            'tgl_transaksi' => 'sometimes|date',
            'keterangan' => 'nullable|string',
            'kantor_id' => 'sometimes|exists:kantor,id',
            'status' => 'in:draft,posted,batal',
        ]);

        $angsuranKolektif->update($validated);

        $routeName = $this->detectRouteName();
        $basePrefix = str_replace(['.update', '.destroy'], '', $routeName);

        return redirect()->route($basePrefix)
            ->with('success', 'Transaksi kolektif berhasil diupdate.');
    }

    public function destroy(AngsuranKolektif $angsuranKolektif)
    {
        $angsuranKolektif->delete();

        $routeName = $this->detectRouteName();
        $basePrefix = str_replace(['.update', '.destroy'], '', $routeName);

        return redirect()->route($basePrefix)
            ->with('success', 'Transaksi kolektif berhasil dihapus.');
    }



    public function pinjamanByKelompok(Kelompok $kelompok)
    {
        $anggotas = Anggota::where('kelompok_id', $kelompok->id)->pluck('id');
        $pinjaman = Pinjaman::whereIn('anggota_id', $anggotas)
            ->where('aktif', '1')
            ->with('anggota:id,no_anggota,nama')
            ->select('id', 'no_pinjaman', 'plafon', 'angsuranke', 'jangka_waktu', 'anggota_id')
            ->get();

        return response()->json($pinjaman);
    }
}
