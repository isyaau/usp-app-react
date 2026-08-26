<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\SimpananKode;
use App\Models\TarikanSimpanan;
use Illuminate\Http\Request;

/**
 * Controller CRUD Tarikan Simpanan untuk frontend Inertia (React + TypeScript).
 * Menggantikan Livewire Superadmin\Tarikansimpanan (stub).
 */
class TarikanSimpananController extends Controller
{
    public function index(Request $request)
    {
        $tarikan = TarikanSimpanan::query()
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

        return inertia('Superadmin/TarikanSimpanan/Index', [
            'transaksi' => $tarikan,
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
        return inertia('Superadmin/TarikanSimpanan/Create', [
            ...$this->formData(),
            'simpananUrl' => '/superadmin/transaksi-simpanan/simpanan-by-anggota',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateTransaksi($request);

        TarikanSimpanan::create($this->payload($request, $validated));

        return redirect()
            ->route('superadmin.transaksi-simpanan.tarikan-simpanan')
            ->with('flash.status', 'Tarikan simpanan berhasil dicatat!');
    }

    public function show(TarikanSimpanan $tarikanSimpanan)
    {
        $tarikanSimpanan->load([
            'anggota',
            'simpanan.jenis_simpanan:id,nama,jenis',
            'kodeTransaksi',
            'user:id,nama',
            'kantor:id,nama_kantor',
        ]);

        return inertia('Superadmin/TarikanSimpanan/Show', [
            'transaksiData' => $tarikanSimpanan,
        ]);
    }

    public function edit(TarikanSimpanan $tarikanSimpanan)
    {
        return inertia('Superadmin/TarikanSimpanan/Edit', [
            ...$this->formData(),
            'simpananUrl' => '/superadmin/transaksi-simpanan/simpanan-by-anggota',
            'transaksiData' => $tarikanSimpanan,
        ]);
    }

    public function update(Request $request, TarikanSimpanan $tarikanSimpanan)
    {
        $validated = $this->validateTransaksi($request);

        $tarikanSimpanan->update($this->payload($request, $validated));

        return redirect()
            ->route('superadmin.transaksi-simpanan.tarikan-simpanan')
            ->with('flash.status', 'Tarikan simpanan berhasil diperbarui!');
    }

    public function destroy(TarikanSimpanan $tarikanSimpanan)
    {
        $tarikanSimpanan->delete();

        return redirect()
            ->route('superadmin.transaksi-simpanan.tarikan-simpanan')
            ->with('flash.status', 'Tarikan simpanan berhasil dihapus!');
    }

    // ---------------------------------------------------------------- //

    private function formData(): array
    {
        return [
            'anggotas' => Anggota::orderBy('nama')->get(['id', 'no_anggota', 'nama']),
            'kantors' => Kantor::orderBy('kode')->get(['id', 'kode', 'nama_kantor']),
            'kodeTransaksis' => SimpananKode::query()
                ->where('tarikan', true)
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

    private function payload(Request $request, array $validated): array
    {
        return [
            ...$validated,
            'no_transaksi' => $this->generateNoTransaksi(),
            'user_id' => $request->user()->id,
        ];
    }

    /** Nomor unik format TRK-YYYYMMDD-XXXX dengan retry saat tabrakan. */
    private function generateNoTransaksi(): string
    {
        do {
            $no = sprintf('TRK-%s-%04d', now()->format('Ymd'), random_int(1, 9999));
        } while (TarikanSimpanan::where('no_transaksi', $no)->exists());

        return $no;
    }
}
