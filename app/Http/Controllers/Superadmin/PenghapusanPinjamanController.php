<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\PenghapusanPinjaman;
use App\Models\Pinjaman;
use Illuminate\Http\Request;

class PenghapusanPinjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = PenghapusanPinjaman::query()
            ->with([
                'pinjaman:id,no_pinjaman,plafon,anggota_id',
                'pinjaman.anggota:id,no_anggota,nama',
                'user:id,nama',
                'kantor:id,nama_kantor',
            ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('no_transaksi', 'LIKE', "%{$term}%")
                    ->orWhereHas('pinjaman', fn ($p) => $p
                        ->where('no_pinjaman', 'LIKE', "%{$term}%")
                        ->orWhereHas('anggota', fn ($a) => $a->where('nama', 'LIKE', "%{$term}%"))));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')))
            ->orderByDesc('tgl_transaksi')
            ->orderByDesc('id')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString()
            ->through(fn (PenghapusanPinjaman $h) => $this->toRow($h));

        return inertia('Superadmin/PenghapusanPinjaman/Index', [
            'transaksi' => $query,
            'filters' => $request->only(['search', 'status', 'mulai', 'sampai']),
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/PenghapusanPinjaman/Create', [
            'anggotas' => Anggota::select('id', 'no_anggota', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl_transaksi' => 'required|date',
            'pinjaman_id' => 'required|exists:pinjaman,id',
            'sisa_pokok' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kantor_id' => 'required|exists:kantor,id',
        ]);

        $validated['no_transaksi'] = 'PHP-' . date('YmdHis') . rand(10, 99);
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'draft';
        $validated['keterangan'] = $validated['keterangan'] ?? null;

        PenghapusanPinjaman::create($validated);

        return redirect()->route('superadmin.pinjaman.penghapusan')
            ->with('success', 'Penghapusan pinjaman berhasil ditambahkan.');
    }

    public function show(PenghapusanPinjaman $penghapusanPinjaman)
    {
        $penghapusanPinjaman->load(['pinjaman.anggota', 'user', 'kantor']);

        return inertia('Superadmin/PenghapusanPinjaman/Show', [
            'transaksi' => $penghapusanPinjaman,
        ]);
    }

    public function edit(PenghapusanPinjaman $penghapusanPinjaman)
    {
        $penghapusanPinjaman->load('pinjaman.anggota');

        return inertia('Superadmin/PenghapusanPinjaman/Edit', [
            'transaksi' => $penghapusanPinjaman,
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
        ]);
    }

    public function update(Request $request, PenghapusanPinjaman $penghapusanPinjaman)
    {
        $validated = $request->validate([
            'tgl_transaksi' => 'sometimes|date',
            'sisa_pokok' => 'sometimes|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kantor_id' => 'sometimes|exists:kantor,id',
            'status' => 'in:draft,posted,batal',
        ]);

        $penghapusanPinjaman->update($validated);

        // Saat penghapusan di-post, pinjaman terkait otomatis dinonaktifkan.
        if (($validated['status'] ?? null) === 'posted' && $penghapusanPinjaman->pinjaman) {
            $penghapusanPinjaman->pinjaman->update(['aktif' => '0']);
        }

        return redirect()->route('superadmin.pinjaman.penghapusan')
            ->with('success', 'Penghapusan pinjaman berhasil diupdate.');
    }

    public function destroy(PenghapusanPinjaman $penghapusanPinjaman)
    {
        $penghapusanPinjaman->delete();

        return redirect()->route('superadmin.pinjaman.penghapusan')
            ->with('success', 'Penghapusan pinjaman berhasil dihapus.');
    }

    /** Daftar pinjaman aktif milik anggota + sisa pokok untuk dropdown form. */
    public function pinjamanByAnggota(Anggota $anggota)
    {
        $pokokTerbayar = 'COALESCE((SELECT SUM(ap.nominal_pokok) FROM angsuran_pinjaman ap WHERE ap.pinjaman_id = pinjaman.id), 0)';

        return response()->json(
            Pinjaman::where('anggota_id', $anggota->id)
                ->where('aktif', '1')
                ->select('pinjaman.id', 'pinjaman.no_pinjaman', 'pinjaman.plafon')
                ->selectRaw("{$pokokTerbayar} AS pokok_terbayar")
                ->get()
                ->map(fn (Pinjaman $p) => [
                    'id' => $p->id,
                    'no_pinjaman' => $p->no_pinjaman,
                    'plafon' => (float) $p->plafon,
                    'sisa_pokok' => max(0, (float) $p->plafon - (float) $p->pokok_terbayar),
                ])
        );
    }

    private function toRow(PenghapusanPinjaman $h): array
    {
        return [
            'id' => $h->id,
            'no_transaksi' => $h->no_transaksi,
            'tgl_transaksi' => optional($h->tgl_transaksi)->toDateString(),
            'sisa_pokok' => (float) $h->sisa_pokok,
            'keterangan' => $h->keterangan,
            'status' => $h->status,
            'pinjaman' => $h->pinjaman ? [
                'id' => $h->pinjaman->id,
                'no_pinjaman' => $h->pinjaman->no_pinjaman,
                'plafon' => (float) $h->pinjaman->plafon,
                'anggota' => $h->pinjaman->anggota ? [
                    'id' => $h->pinjaman->anggota->id,
                    'no_anggota' => $h->pinjaman->anggota->no_anggota,
                    'nama' => $h->pinjaman->anggota->nama,
                ] : null,
            ] : null,
            'user' => $h->user ? ['id' => $h->user->id, 'nama' => $h->user->nama] : null,
            'kantor' => $h->kantor ? ['id' => $h->kantor->id, 'nama_kantor' => $h->kantor->nama_kantor] : null,
        ];
    }
}