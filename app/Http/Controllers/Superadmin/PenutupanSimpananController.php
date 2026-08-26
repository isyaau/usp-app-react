<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\SimpananKode;
use Illuminate\Http\Request;
use App\Models\PenutupanSimpanan;

/**
 * Controller CRUD Penutupan Simpanan untuk frontend Inertia (React + TypeScript).
 * Menggantikan Livewire Superadmin\Penutupansimpanan (stub).
 */
class PenutupanSimpananController extends Controller
{
    public function index(Request $request)
    {
        $penutupan = PenutupanSimpanan::query()
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

        return inertia('Superadmin/PenutupanSimpanan/Index', [
            'transaksi' => $penutupan,
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
        return inertia('Superadmin/PenutupanSimpanan/Create', [
            ...$this->formData(),
            'simpananUrl' => '/superadmin/transaksi-simpanan/simpanan-by-anggota',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateTransaksi($request);

        PenutupanSimpanan::create($this->payload($request, $validated));

        return redirect()
            ->route('superadmin.transaksi-simpanan.penutupan-simpanan')
            ->with('flash.status', 'Penutupan simpanan berhasil dicatat!');
    }

    public function show(PenutupanSimpanan $penutupanSimpanan)
    {
        $penutupanSimpanan->load([
            'anggota',
            'simpanan.jenis_simpanan:id,nama,jenis',
            'kodeTransaksi',
            'user:id,nama',
            'kantor:id,nama_kantor',
        ]);

        return inertia('Superadmin/PenutupanSimpanan/Show', [
            'transaksiData' => $penutupanSimpanan,
        ]);
    }

    public function edit(PenutupanSimpanan $penutupanSimpanan)
    {
        return inertia('Superadmin/PenutupanSimpanan/Edit', [
            ...$this->formData(),
            'simpananUrl' => '/superadmin/transaksi-simpanan/simpanan-by-anggota',
            'transaksiData' => $penutupanSimpanan,
        ]);
    }

    public function update(Request $request, PenutupanSimpanan $penutupanSimpanan)
    {
        $validated = $this->validateTransaksi($request);

        $penutupanSimpanan->update($this->payload($request, $validated));

        return redirect()
            ->route('superadmin.transaksi-simpanan.penutupan-simpanan')
            ->with('flash.status', 'Penutupan simpanan berhasil diperbarui!');
    }

    public function destroy(PenutupanSimpanan $penutupanSimpanan)
    {
        $penutupanSimpanan->delete();

        return redirect()
            ->route('superadmin.transaksi-simpanan.penutupan-simpanan')
            ->with('flash.status', 'Penutupan simpanan berhasil dihapus!');
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
            'nominal_bunga' => ['nullable', 'numeric', 'min:0'],
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
            'nominal.required' => 'Nominal pokok wajib diisi.',
            'nominal.min' => 'Nominal tidak boleh negatif.',
            'nominal_bunga.min' => 'Nominal bunga tidak boleh negatif.',
            'kantor_id.required' => 'Kantor wajib dipilih.',
            'kantor_id.exists' => 'Kantor tidak ditemukan.',
            'status.in' => 'Status harus draft, posted, atau batal.',
        ]);
    }

    private function payload(Request $request, array $validated): array
    {
        return [
            ...$validated,
            'nominal_bunga' => $validated['nominal_bunga'] ?? 0,
            'no_transaksi' => $this->generateNoTransaksi(),
            'user_id' => $request->user()->id,
        ];
    }

    /** Nomor unik format TNP-YYYYMMDD-XXXX dengan retry saat tabrakan. */
    private function generateNoTransaksi(): string
    {
        do {
            $no = sprintf('TNP-%s-%04d', now()->format('Ymd'), random_int(1, 9999));
        } while (PenutupanSimpanan::where('no_transaksi', $no)->exists());

        return $no;
    }
}
