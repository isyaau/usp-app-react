<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AccGroup;
use Illuminate\Http\Request;

/**
 * Controller Grup Akun (COA level 1) — dipakai dialog "Kelola Grup"
 * di halaman Account Header.
 */
class AccGroupController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:acc_group,nama',
        ], [
            'nama.required' => 'Nama grup wajib diisi.',
            'nama.unique' => 'Nama grup sudah digunakan.',
        ]);

        AccGroup::create(['nama' => $validated['nama'], 'user_id' => $request->user()->id]);

        return redirect()
            ->route('superadmin.account-header')
            ->with('flash.status', "Grup \"{$validated['nama']}\" berhasil dibuat!");
    }
}
