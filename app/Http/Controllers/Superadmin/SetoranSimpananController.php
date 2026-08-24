<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\SetoranSimpanan;
use App\Models\Simpanan;
use App\Models\SimpananJenis;
use App\Models\SimpananKode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller CRUD Setoran Simpanan untuk frontend Inertia (React + TypeScript).
 * Menggantikan Livewire Superadmin\Setoransimpanan (stub).
 *
 * Menyediakan juga endpoint JSON rekening simpanan per anggota yang dipakai
 * bersama oleh seluruh form transaksi simpanan (setoran/tarikan/penutupan/
 * pemindahbukuan).
 */
class SetoranSimpananController extends Controller
{
    public function index(Request $request)
    {
        $setoran = SetoranSimpanan::query()
            ->with([
                'anggota:id,nama,no_anggota',
                'simpanan:id,no_rekening,anggota_id',
                'kodeTransaksi:id,kode,nama',
                'kantor:id,nama_kantor',
            ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('no_transaksi', 'LIKE', "%{$term}%")
                    ->orWhereHas('anggota', fn ($a) => $a
                        ->where('nama', 'LIKE', "%{$term}%")
                        ->orWhere('no_anggota', 'LIKE', "%{$term}%")));
            })
            ->when($request->filled('status'), fn ($q) => $q
                ->where('status', $request->string('status')))
            ->when($request->filled('mulai'), fn ($q) => $q
                ->whereDate('tgl_transaksi', '>=', $request->date('mulai')))
            ->when($request->filled('sampai'), fn ($q) => $q
                ->whereDate('tgl_transaksi', '<=', $request->date('sampai')))
            ->orderByDesc('tgl_transaksi')
            ->orderByDesc('id')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/SetoranSimpanan/Index', [
            'transaksi' => $setoran,
            'filters' => [
                'search' => $request->input('search', ''),
                'status' => $request->input('status', ''),
                'mulai' => $request->input('mulai', ''),
                'sampai' => $request->input('sampai', ''),
            ],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/SetoranSimpanan/Create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validateTransaksi($request);

        SetoranSimpanan::create($this->payload($request, $validated));

        return redirect()
            ->route('superadmin.transaksi-simpanan.setoran-simpanan')
            ->with('flash.status', 'Setoran simpanan berhasil dicatat!');
    }

    public function show(SetoranSimpanan $setoranSimpanan)
    {
        $setoranSimpanan->load([
            'anggota',
            'simpanan.jenis_simpanan:id,nama,jenis',
            'kodeTransaksi',
            'user:id,nama',
            'kantor:id,nama_kantor',
        ]);

        return inertia('Superadmin/SetoranSimpanan/Show', [
            'transaksiData' => $setoranSimpanan,
        ]);
    }

    public function edit(SetoranSimpanan $setoranSimpanan)
    {
        return inertia('Superadmin/SetoranSimpanan/Edit', [
            ...$this->formData(),
            'transaksiData' => $setoranSimpanan,
        ]);
    }

    public function update(Request $request, SetoranSimpanan $setoranSimpanan)
    {
        $validated = $this->validateTransaksi($request);

        $setoranSimpanan->update($this->payload($request, $validated));

        return redirect()
            ->route('superadmin.transaksi-simpanan.setoran-simpanan')
            ->with('flash.status', 'Setoran simpanan berhasil diperbarui!');
    }

    public function destroy(SetoranSimpanan $setoranSimpanan)
    {
        $setoranSimpanan->delete();

        return redirect()
            ->route('superadmin.transaksi-simpanan.setoran-simpanan')
            ->with('flash.status', 'Setoran simpanan berhasil dihapus!');
    }

    /**
     * Endpoint JSON: daftar rekening simpanan milik satu anggota.
     * Dipakai dropdown bertingkat "pilih anggota → pilih rekening".
     */
    public function simpananByAnggota(Anggota $anggota): JsonResponse
    {
        return response()->json(
            $this->rekeningAnggotaQuery()->where('anggota_id', $anggota->id)->get(),
        );
    }

    // ---------------------------------------------------------------- //

    /** Query rekening aktif dengan nama jenisnya. */
    private function rekeningAnggotaQuery()
    {
        return Simpanan::query()
            ->join('simpanan_jenis', 'simpanan_jenis.id', '=', 'simpanan.jenis_id')
            ->where('simpanan.aktif', 1)
            ->selectRaw("simpanan.id, simpanan.no_rekening, simpanan_jenis.nama as jenis");
    }

    /** Data opsi untuk form: anggota, kantor, kode transaksi setoran. */
    private function formData(): array
    {
        return [
            'anggotas' => Anggota::orderBy('nama')->get(['id', 'no_anggota', 'nama']),
            'kantors' => Kantor::orderBy('kode')->get(['id', 'kode', 'nama_kantor']),
            'kodeTransaksis' => SimpananKode::query()
                ->where('setoran', true)
                ->orderBy('kode')
                ->get(['id', 'kode', 'nama']),
        ];
    }

    private function validateTransaksi(Request $request): array
    {
        return $request->validate([
            'tgl_transaksi' => ['required', 'date'],
            'anggota_id' => ['required', 'integer', 'exists:anggota,id'],
            'simpanan_id' => ['required', 'integer', 'exists:simpanan,id'],
            'kode_transaksi_id' => ['required', 'integer', 'exists:simpanan_kode,id'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'kantor_id' => ['required', 'integer', 'exists:kantor,id'],
            'status' => ['required', 'in:draft,posted,batal'],
        ], [
            'tgl_transaksi.required' => 'Tanggal transaksi wajib diisi.',
            'anggota_id.required' => 'Anggota wajib dipilih.',
            'anggota_id.exists' => 'Anggota tidak ditemukan.',
            'simpanan_id.required' => 'Rekening simpanan wajib dipilih.',
            'simpanan_id.exists' => 'Rekening simpanan tidak ditemukan.',
            'kode_transaksi_id.required' => 'Kode transaksi wajib dipilih.',
            'kode_transaksi_id.exists' => 'Kode transaksi tidak ditemukan.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.min' => 'Nominal tidak boleh negatif.',
            'kantor_id.required' => 'Kantor wajib dipilih.',
            'kantor_id.exists' => 'Kantor tidak ditemukan.',
            'status.in' => 'Status harus draft, posted, atau batal.',
        ]);
    }

    /** Susun atribut lengkap termasuk no_transaksi & user_id. */
    private function payload(Request $request, array $validated): array
    {
        return [
            ...$validated,
            'no_transaksi' => $this->generateNoTransaksi(),
            'user_id' => $request->user()->id,
        ];
    }

    /** Nomor unik format SET-YYYYMMDD-XXXX dengan retry saat tabrakan. */
    private function generateNoTransaksi(): string
    {
        do {
            $no = sprintf('SET-%s-%04d', now()->format('Ymd'), random_int(1, 9999));
        } while (SetoranSimpanan::where('no_transaksi', $no)->exists());

        return $no;
    }
}
