<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\SimpananKode;
use Illuminate\Http\Request;

/**
 * Controller CRUD Kode Transaksi (simpanan_kode) untuk frontend Inertia.
 * Menggantikan Livewire Superadmin\Kodetransaksi — 10 flag boolean dipertahankan.
 */
class KodetransaksiController extends Controller
{
    /** Daftar flag boolean yang mengikuti form lama. */
    private const FLAGS = [
        'setoran', 'tarikan', 'transfer', 'pokok', 'wajib',
        'sukarela', 'pinjaman', 'saham', 'pokok_pinjaman', 'rencana',
    ];

    public function index(Request $request)
    {
        $kode = SimpananKode::query()
            ->with(['debetAccount:id,no_account,nama,tipe', 'kreditAccount:id,no_account,nama,tipe'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('nama', 'LIKE', "%{$term}%")
                    ->orWhere('kode', 'LIKE', "%{$term}%"));
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/Kodetransaksi/Index', [
            'kodeTransaksi' => $kode,
            'filters' => ['search' => $request->input('search', '')],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/Kodetransaksi/Create', [
            'debetAccounts' => Account::where('tipe', 'Debet')->orderBy('no_account')->get(['id', 'no_account', 'nama']),
            'kreditAccounts' => Account::where('tipe', 'Kredit')->orderBy('no_account')->get(['id', 'no_account', 'nama']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateKode($request);

        SimpananKode::create([
            ...$validated,
            ...collect(self::FLAGS)->mapWithKeys(fn ($f) => [$f => $request->boolean($f)])->all(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('superadmin.simpanan.kode-transaksi')
            ->with('flash.status', 'Kode Transaksi berhasil dibuat!');
    }

    public function show(SimpananKode $kodetransaksi)
    {
        $kodetransaksi->load(['debetAccount:id,no_account,nama,tipe', 'kreditAccount:id,no_account,nama,tipe']);

        return inertia('Superadmin/Kodetransaksi/Show', ['kodeData' => $kodetransaksi]);
    }

    public function edit(SimpananKode $kodetransaksi)
    {
        return inertia('Superadmin/Kodetransaksi/Edit', [
            'kodeData' => $kodetransaksi,
            'debetAccounts' => Account::where('tipe', 'Debet')->orderBy('no_account')->get(['id', 'no_account', 'nama']),
            'kreditAccounts' => Account::where('tipe', 'Kredit')->orderBy('no_account')->get(['id', 'no_account', 'nama']),
        ]);
    }

    public function update(Request $request, SimpananKode $kodetransaksi)
    {
        $validated = $this->validateKode($request, $kodetransaksi->id);

        $kodetransaksi->update([
            ...$validated,
            ...collect(self::FLAGS)->mapWithKeys(fn ($f) => [$f => $request->boolean($f)])->all(),
        ]);

        return redirect()
            ->route('superadmin.simpanan.kode-transaksi')
            ->with('flash.status', 'Kode Transaksi berhasil diperbarui!');
    }

    public function destroy(SimpananKode $kodetransaksi)
    {
        $kodetransaksi->delete();

        return redirect()
            ->route('superadmin.simpanan.kode-transaksi')
            ->with('flash.status', 'Kode Transaksi berhasil dihapus!');
    }

    private function validateKode(Request $request, ?int $ignoreId = null): array
    {
        $suffix = is_null($ignoreId) ? '' : ','.$ignoreId;

        return $request->validate([
            'kode' => 'required|string|max:255|unique:simpanan_kode,kode'.$suffix,
            'nama' => 'required|string|max:255|unique:simpanan_kode,nama'.$suffix,
            'account_debet' => 'required|integer|exists:account,id',
            'account_kredit' => 'required|integer|exists:account,id',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode sudah digunakan.',
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique' => 'Nama sudah digunakan.',
            'account_debet.required' => 'Account Debet wajib dipilih.',
            'account_debet.exists' => 'Account Debet tidak valid.',
            'account_kredit.required' => 'Account Kredit wajib dipilih.',
            'account_kredit.exists' => 'Account Kredit tidak valid.',
        ]);
    }
}
