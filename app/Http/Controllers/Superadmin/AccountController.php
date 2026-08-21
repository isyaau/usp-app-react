<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AccHeader;
use App\Models\Account;
use Illuminate\Http\Request;

/**
 * Controller CRUD Account (COA) untuk frontend Inertia (React + TypeScript).
 * Menggantikan Livewire Superadmin\Account.
 */
class AccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::query()
            ->with('header:id,nama,no_header')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('nama', 'LIKE', "%{$term}%")
                    ->orWhere('no_account', 'LIKE', "%{$term}%"));
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/Account/Index', [
            'accounts' => $accounts,
            'filters' => ['search' => $request->input('search', '')],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/Account/Create', [
            'headers' => AccHeader::orderBy('nama')->get(['id', 'nama', 'no_header']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateAccount($request);

        Account::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('superadmin.account')
            ->with('flash.status', 'Account berhasil dibuat!');
    }

    public function show(Account $account)
    {
        $account->load('header:id,nama,no_header');

        return inertia('Superadmin/Account/Show', ['accountData' => $account]);
    }

    public function edit(Account $account)
    {
        return inertia('Superadmin/Account/Edit', [
            'accountData' => $account,
            'headers' => AccHeader::orderBy('nama')->get(['id', 'nama', 'no_header']),
        ]);
    }

    public function update(Request $request, Account $account)
    {
        $validated = $this->validateAccount($request, $account->id);

        $account->update($validated);

        return redirect()
            ->route('superadmin.account')
            ->with('flash.status', 'Account berhasil diperbarui!');
    }

    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()
            ->route('superadmin.account')
            ->with('flash.status', 'Account berhasil dihapus!');
    }

    private function validateAccount(Request $request, ?int $ignoreId = null): array
    {
        $suffix = is_null($ignoreId) ? '' : ','.$ignoreId;

        return $request->validate([
            'no_account' => 'required|string|max:50|unique:account,no_account'.$suffix,
            'nama' => 'required|string|max:255',
            'header_id' => 'required|integer|exists:acc_header,id',
            'tipe' => 'required|in:Debet,Kredit',
        ], [
            'no_account.required' => 'Nomor Account wajib diisi.',
            'no_account.unique' => 'Nomor Account sudah digunakan.',
            'nama.required' => 'Nama Account wajib diisi.',
            'header_id.required' => 'Header wajib dipilih.',
            'header_id.exists' => 'Header tidak ditemukan.',
            'tipe.required' => 'Tipe wajib dipilih.',
            'tipe.in' => 'Tipe harus Debet atau Kredit.',
        ]);
    }
}
