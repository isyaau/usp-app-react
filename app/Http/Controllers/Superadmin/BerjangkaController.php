<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Anggota;
use App\Models\Deposito;
use App\Models\DepositoJenis;
use App\Models\Kantor;
use App\Models\Marketing;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller CRUD Simpanan Berjangka (tabel deposito)
 * untuk frontend Inertia (React + TypeScript).
 * Menggantikan Livewire Superadmin\Berjangka.
 */
class BerjangkaController extends Controller
{
    public function index(Request $request)
    {
        $berjangka = Deposito::query()
            ->with([
                'anggota:id,no_anggota,nama',
                'produk:id,kode,nama,jangka_waktu,bunga',
                'marketing:id,nama',
                'kantor:id,nama_kantor',
            ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('no_deposito', 'LIKE', "%{$term}%")
                    ->orWhereHas('anggota', fn ($a) => $a
                        ->where('nama', 'LIKE', "%{$term}%")
                        ->orWhere('no_anggota', 'LIKE', "%{$term}%")));
            })
            ->orderByDesc('tanggal')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/Berjangka/Index', [
            'berjangka' => $berjangka,
            'filters' => ['search' => $request->input('search', '')],
        ]);
    }

    public function create(Request $request)
    {
        return inertia('Superadmin/Berjangka/Create', [
            ...$this->formOptions(),
            'suggestedNoDeposito' => $this->generateNoDeposito(now()),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateDeposito($request);
        $validated['no_deposito'] = $this->generateNoDeposito($validated['tanggal']);
        $validated['qq'] ??= '-';

        Deposito::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('superadmin.simpanan-berjangka')
            ->with('flash.status', 'Simpanan berjangka berhasil dibuat!');
    }

    public function show(Deposito $berjangka)
    {
        $berjangka->load([
            'anggota:id,no_anggota,nama',
            'produk',
            'marketing:id,nama',
            'kantor:id,nama_kantor',
            'tabunganBunga:id,no_rekening',
            'tabunganTempo:id,no_rekening',
        ]);

        return inertia('Superadmin/Berjangka/Show', [
            'berjangkaData' => $berjangka,
        ]);
    }

    public function edit(Deposito $berjangka)
    {
        return inertia('Superadmin/Berjangka/Edit', [
            'berjangkaData' => $berjangka,
            ...$this->formOptions(),
        ]);
    }

    public function update(Request $request, Deposito $berjangka)
    {
        $validated = $this->validateDeposito($request);

        // No. deposito tidak berubah saat update.
        unset($validated['no_deposito']);

        $berjangka->update($validated);

        return redirect()
            ->route('superadmin.simpanan-berjangka')
            ->with('flash.status', 'Simpanan berjangka berhasil diperbarui!');
    }

    public function destroy(Deposito $berjangka)
    {
        $berjangka->delete();

        return redirect()
            ->route('superadmin.simpanan-berjangka')
            ->with('flash.status', 'Simpanan berjangka berhasil dihapus!');
    }

    /** Data pendukung form: anggota, produk, marketing, kantor, account, simpanan. */
    private function formOptions(): array
    {
        return [
            'anggotaOptions' => Anggota::orderBy('nama')
                ->get(['id', 'no_anggota', 'nama']),
            'produkOptions' => DepositoJenis::orderBy('nama')
                ->get(['id', 'kode', 'nama', 'jangka_waktu', 'bunga']),
            'marketingOptions' => Marketing::orderBy('nama')->get(['id', 'nama']),
            'kantorOptions' => Kantor::orderBy('nama_kantor')->get(['id', 'nama_kantor']),
            'accountOptions' => Account::orderBy('nama')->get(['id', 'no_account', 'nama']),
            'simpananOptions' => Simpanan::with('anggota:id,nama')
                ->orderBy('no_rekening')
                ->get(['id', 'no_rekening', 'anggota_id']),
        ];
    }

    /**
     * Nomor deposito otomatis dengan pola 55.yymm.nnnn (urut per bulan).
     */
    private function generateNoDeposito(string $tanggal): string
    {
        $tgl = Carbon::parse($tanggal);
        $kodeAwal = sprintf('55.%s%s', $tgl->format('y'), $tgl->format('m'));

        return DB::transaction(function () use ($kodeAwal) {
            $last = Deposito::where('no_deposito', 'LIKE', $kodeAwal.'%')
                ->lockForUpdate()
                ->orderByDesc('no_deposito')
                ->first();

            $next = $last
                ? ((int) substr($last->no_deposito, strlen($kodeAwal))) + 1
                : 1;

            return $kodeAwal.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
        });
    }

    private function validateDeposito(Request $request): array
    {
        return $request->validate([
            'tanggal' => 'required|date',
            'anggota_id' => 'required|integer|exists:anggota,id',
            'jenis_id' => 'required|integer|exists:deposito_jenis,id',
            'marketing_id' => 'nullable|integer|exists:marketing,id',
            'qq' => 'nullable|string|max:255',
            'jangka_waktu' => 'required|string|max:255',
            'bunga' => 'required|string|max:255',
            'nominal' => 'required|string|max:255',
            'otomatis' => 'boolean',
            'bayar_bunga' => 'required|string|max:255',
            'diawal' => 'required|string|max:255',
            'bunga_accrual' => 'boolean',
            'account_bungaaccrual' =>
                'nullable|required_if:bunga_accrual,1,true|integer|exists:account,id',
            'tabunganbunga_id' => 'nullable|integer|exists:simpanan,id',
            'tabungantempo_id' => 'nullable|integer|exists:simpanan,id',
            'bayar_jatuhtempo' => 'required|string|max:255',
            'blokir' => 'boolean',
            'kantor_id' => 'required|integer|exists:kantor,id',
        ], [
            'anggota_id.required' => 'Anggota wajib dipilih.',
            'jenis_id.required' => 'Produk deposito wajib dipilih.',
            'nominal.required' => 'Nominal wajib diisi.',
            'diawal.required' => 'Cara pembayaran wajib diisi.',
            'bayar_bunga.required' => 'Jenis pembayaran bunga wajib diisi.',
            'bayar_jatuhtempo.required' => 'Pembayaran jatuh tempo wajib diisi.',
            'kantor_id.required' => 'Kantor wajib dipilih.',
        ]);
    }
}
