<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\DepositoJenis;
use Illuminate\Http\Request;

/**
 * Controller CRUD Produk Simpanan Berjangka (tabel deposito_jenis)
 * untuk frontend Inertia (React + TypeScript).
 * Menggantikan Livewire Superadmin\Berjangkaproduk.
 */
class BerjangkaprodukController extends Controller
{
    public function index(Request $request)
    {
        $produk = DepositoJenis::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('nama', 'LIKE', "%{$term}%")
                    ->orWhere('kode', 'LIKE', "%{$term}%"));
            })
            ->orderBy('kode')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/Berjangkaproduk/Index', [
            'produk' => $produk,
            'filters' => ['search' => $request->input('search', '')],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/Berjangkaproduk/Create', [
            'accountOptions' => Account::orderBy('nama')->get(['id', 'no_account', 'nama']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduk($request);

        DepositoJenis::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('superadmin.simpanan-berjangka.produk')
            ->with('flash.status', 'Produk simpanan berjangka berhasil dibuat!');
    }

    public function show(DepositoJenis $produkBerjangka)
    {
        $produkBerjangka->load([
            'account:id,no_account,nama',
            'bunga:id,no_account,nama',
            'penalti:id,no_account,nama',
            'pajak:id,no_account,nama',
        ]);

        return inertia('Superadmin/Berjangkaproduk/Show', [
            'produkData' => $produkBerjangka,
        ]);
    }

    public function edit(DepositoJenis $produkBerjangka)
    {
        return inertia('Superadmin/Berjangkaproduk/Edit', [
            'produkData' => $produkBerjangka,
            'accountOptions' => Account::orderBy('nama')->get(['id', 'no_account', 'nama']),
        ]);
    }

    public function update(Request $request, DepositoJenis $produkBerjangka)
    {
        $validated = $this->validateProduk($request, $produkBerjangka->id);

        $produkBerjangka->update($validated);

        return redirect()
            ->route('superadmin.simpanan-berjangka.produk')
            ->with('flash.status', 'Produk simpanan berjangka berhasil diperbarui!');
    }

    public function destroy(DepositoJenis $produkBerjangka)
    {
        $produkBerjangka->delete();

        return redirect()
            ->route('superadmin.simpanan-berjangka.produk')
            ->with('flash.status', 'Produk simpanan berjangka berhasil dihapus!');
    }

    private function validateProduk(Request $request, ?int $ignoreId = null): array
    {
        $suffix = is_null($ignoreId) ? '' : ','.$ignoreId;

        return $request->validate([
            'kode' => 'required|string|max:255|unique:deposito_jenis,kode'.$suffix,
            'nama' => 'required|string|max:255|unique:deposito_jenis,nama'.$suffix,
            'account_id' => 'required|integer|exists:account,id',
            'jangka_waktu' => 'nullable|string|max:255',
            'bunga' => 'nullable|string|max:255',
            'account_bunga' => 'nullable|integer|exists:account,id',
            'rumus_bunga' => 'nullable|string|max:255',
            'penalti' => 'nullable|string|max:255',
            'account_penalti' => 'nullable|integer|exists:account,id',
            'pajak' => 'nullable|string|max:255',
            'account_pajak' => 'nullable|integer|exists:account,id',
            'saldo_pajak' => 'nullable|string|max:255',
            'insentif' => 'nullable|string|max:255',
        ], [
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode sudah digunakan.',
            'nama.required' => 'Nama produk wajib diisi.',
            'nama.unique' => 'Nama produk sudah digunakan.',
            'account_id.required' => 'Account wajib dipilih.',
        ]);
    }
}
