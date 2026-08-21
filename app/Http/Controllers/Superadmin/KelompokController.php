<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kelompok;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Controller CRUD Kelompok untuk frontend Inertia (React + TypeScript).
 * Menggantikan Livewire Superadmin\Kelompok.
 */
class KelompokController extends Controller
{
    public function index(Request $request)
    {
        $kelompok = Kelompok::query()
            ->with('ketua:id,nama')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('nama', 'LIKE', "%{$term}%")
                    ->orWhere('kode', 'LIKE', "%{$term}%"));
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/Kelompok/Index', [
            'kelompok' => $kelompok,
            'filters' => ['search' => $request->input('search', '')],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/Kelompok/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:kelompok,kode',
            'nama' => 'required|string|max:255|unique:kelompok,nama',
            'ketua_id' => 'nullable|integer|exists:users,id',
        ], [
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode sudah digunakan.',
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique' => 'Nama sudah digunakan.',
            'ketua_id.exists' => 'User ketua tidak ditemukan.',
        ]);

        Kelompok::create([
            ...$validated,
            'ketua_id' => $validated['ketua_id'] ?: null,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('superadmin.kelompok')
            ->with('flash.status', 'Kelompok berhasil dibuat!');
    }

    public function show(Kelompok $kelompok)
    {
        $kelompok->load('ketua:id,nama');

        return inertia('Superadmin/Kelompok/Show', ['kelompokData' => $kelompok]);
    }

    public function edit(Kelompok $kelompok)
    {
        return inertia('Superadmin/Kelompok/Edit', ['kelompokData' => $kelompok]);
    }

    public function update(Request $request, Kelompok $kelompok)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:kelompok,kode,'.$kelompok->id,
            'nama' => 'required|string|max:255|unique:kelompok,nama,'.$kelompok->id,
            'ketua_id' => 'nullable|integer|exists:users,id',
        ], [
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode sudah digunakan.',
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique' => 'Nama sudah digunakan.',
            'ketua_id.exists' => 'User ketua tidak ditemukan.',
        ]);

        $kelompok->update([
            ...$validated,
            'ketua_id' => $validated['ketua_id'] ?: null,
        ]);

        return redirect()
            ->route('superadmin.kelompok')
            ->with('flash.status', 'Kelompok berhasil diperbarui!');
    }

    public function destroy(Kelompok $kelompok)
    {
        $kelompok->delete();

        return redirect()
            ->route('superadmin.kelompok')
            ->with('flash.status', 'Kelompok berhasil dihapus!');
    }

    /**
     * Endpoint pencarian user untuk combobox ketua (dipakai Create/Edit).
     */
    public function searchUsers(Request $request)
    {
        $term = $request->string('q');

        return response()->json(
            User::where('nama', 'LIKE', "%{$term}%")
                ->limit(8)
                ->get(['id', 'nama'])
        );
    }
}
