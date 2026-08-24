<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\PemindahbukuanSimpanan;
use App\Models\SimpananKode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Controller CRUD Pemindahbukuan Simpanan untuk frontend Inertia.
 * Menggantikan Livewire Superadmin\Pemindahbukuansimpanan (stub).
 *
 * Memindahkan dana antar dua rekening milik anggota yang sama
 * (rekening asal ≠ rekening tujuan).
 */
class PemindahbukuanSimpananController extends Controller
{
    public function index(Request $request)
    {
        $pemindahbukuan = PemindahbukuanSimpanan::query()
            ->with([
                'anggota:id,nama,no_anggota',
                'simpananDari:id,no_rekening,anggota_id',
                'simpananKe:id,no_rekening,anggota_id',
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

        return inertia('Superadmin/PemindahbukuanSimpanan/Index', [
            'transaksi' => $pemindahbukuan,
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
        return inertia('Superadmin/PemindahbukuanSimpanan/Create', [
            ...$this->formData(),
            'simpananUrl' => route('superadmin.transaksi-simpanan.simpanan-by-anggota'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateTransaksi($request);

        PemindahbukuanSimpanan::create($this->payload($request, $validated));

        return redirect()
            ->route('superadmin.transaksi-simpanan.pemindahbukuan-simpanan')
            ->with('flash.status', 'Pemindahbukuan simpanan berhasil dicatat!');
    }

    public function show(PemindahbukuanSimpanan $pemindahbukuanSimpanan)
    {
        $pemindahbukuanSimpanan->load([
            'anggota',
            'simpananDari.jenis_simpanan:id,nama,jenis',
            'simpananKe.jenis_simpanan:id,nama,jenis',
            'kodeTransaksi',
            'user:id,nama',
            'kantor:id,nama_kantor',
        ]);

        return inertia('Superadmin/PemindahbukuanSimpanan/Show', [
            'transaksiData' => $pemindahbukuanSimpanan,
        ]);
    }

    public function edit(PemindahbukuanSimpanan $pemindahbukuanSimpanan)
    {
        return inertia('Superadmin/PemindahbukuanSimpanan/Edit', [
            ...$this->formData(),
            'simpananUrl' => route('superadmin.transaksi-simpanan.simpanan-by-anggota'),
            'transaksiData' => $pemindahbukuanSimpanan,
        ]);
    }

    public function update(Request $request, PemindahbukuanSimpanan $pemindahbukuanSimpanan)
    {
        $validated = $this->validateTransaksi($request);

        $pemindahbukuanSimpanan->update($this->payload($request, $validated));

        return redirect()
            ->route('superadmin.transaksi-simpanan.pemindahbukuan-simpanan')
            ->with('flash.status', 'Pemindahbukuan simpanan berhasil diperbarui!');
    }

    public function destroy(PemindahbukuanSimpanan $pemindahbukuanSimpanan)
    {
        $pemindahbukuanSimpanan->delete();

        return redirect()
            ->route('superadmin.transaksi-simpanan.pemindahbukuan-simpanan')
            ->with('flash.status', 'Pemindahbukuan simpanan berhasil dihapus!');
    }

    // ---------------------------------------------------------------- //

    private function formData(): array
    {
        return [
            'anggotas' => Anggota::orderBy('nama')->get(['id', 'no_anggota', 'nama']),
            'kantors' => Kantor::orderBy('kode')->get(['id', 'kode', 'nama_kantor']),
            'kodeTransaksis' => SimpananKode::query()
                ->where('transfer', true)
                ->orderBy('kode')
                ->get(['id', 'kode', 'nama']),
        ];
    }

    private function validateTransaksi(Request $request): array
    {
        return $request->validate([
            'tgl_transaksi' => ['required', 'date'],
            'anggota_id' => ['required', 'integer', 'exists:anggota,id'],
            'simpanan_dari_id' => ['required', 'integer', 'exists:simpanan,id'],
            'simpanan_ke_id' => [
                'required', 'integer', 'exists:simpanan,id',
                Rule::notIn([$request->input('simpanan_dari_id')]),
            ],
            'kode_transaksi_id' => ['required', 'integer', 'exists:simpanan_kode,id'],
            'nominal' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'kantor_id' => ['required', 'integer', 'exists:kantor,id'],
            'status' => ['required', 'in:draft,posted,batal'],
        ], [
            'tgl_transaksi.required' => 'Tanggal transaksi wajib diisi.',
            'anggota_id.required' => 'Anggota wajib dipilih.',
            'anggota_id.exists' => 'Anggota tidak ditemukan.',
            'simpanan_dari_id.required' => 'Rekening asal wajib dipilih.',
            'simpanan_dari_id.exists' => 'Rekening asal tidak ditemukan.',
            'simpanan_ke_id.required' => 'Rekening tujuan wajib dipilih.',
            'simpanan_ke_id.exists' => 'Rekening tujuan tidak ditemukan.',
            'simpanan_ke_id.not_in' => 'Rekening tujuan harus berbeda dari rekening asal.',
            'kode_transaksi_id.required' => 'Kode transaksi wajib dipilih.',
            'kode_transaksi_id.exists' => 'Kode transaksi tidak ditemukan.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.min' => 'Nominal tidak boleh negatif.',
            'kantor_id.required' => 'Kantor wajib dipilih.',
            'kantor_id.exists' => 'Kantor tidak ditemukan.',
            'status.in' => 'Status harus draft, posted, atau batal.',
        ]);
    }

    private function payload(Request $request, array $validated): array
    {
        return [
            ...$validated,
            'no_transaksi' => $this->generateNoTransaksi(),
            'user_id' => $request->user()->id,
        ];
    }

    /** Nomor unik format PMB-YYYYMMDD-XXXX dengan retry saat tabrakan. */
    private function generateNoTransaksi(): string
    {
        do {
            $no = sprintf('PMB-%s-%04d', now()->format('Ymd'), random_int(1, 9999));
        } while (PemindahbukuanSimpanan::where('no_transaksi', $no)->exists());

        return $no;
    }
}
