<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kantor;
use Illuminate\Http\Request;

/**
 * Controller CRUD Kantor untuk frontend Inertia (React + TypeScript).
 * Menggantikan Livewire Superadmin\Kantor.
 */
class KantorController extends Controller
{
    public function index(Request $request)
    {
        $kantor = Kantor::query()
            ->with(['provinsi:code,name', 'kota:code,name'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('nama_kantor', 'LIKE', "%{$term}%")
                    ->orWhere('kode', 'LIKE', "%{$term}%"));
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/Kantor/Index', [
            'kantor' => $kantor,
            'filters' => ['search' => $request->input('search', '')],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/Kantor/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:kantor,kode',
            'nama_kantor' => 'required|string|max:255',
            'alamat_kantor' => 'required|string|max:500',
            'provinsi_id' => 'required|string|exists:indonesia_provinces,code',
            'kota_id' => 'required|string|exists:indonesia_cities,code',
            'kecamatan_id' => 'required|string|exists:indonesia_districts,code',
            'kelurahan_id' => 'required|string|exists:indonesia_villages,code',
            'pejabat' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'bendahara' => 'required|string|max:255',
        ], [
            'kode.required' => 'Kode Kantor wajib diisi.',
            'kode.unique' => 'Kode Kantor sudah digunakan.',
            'nama_kantor.required' => 'Nama Kantor wajib diisi.',
            'alamat_kantor.required' => 'Alamat Kantor wajib diisi.',
            'provinsi_id.required' => 'Provinsi wajib dipilih.',
            'kota_id.required' => 'Kota/Kabupaten wajib dipilih.',
            'kecamatan_id.required' => 'Kecamatan wajib dipilih.',
            'kelurahan_id.required' => 'Kelurahan wajib dipilih.',
            'pejabat.required' => 'Nama Pejabat wajib diisi.',
            'jabatan.required' => 'Jabatan Pejabat wajib diisi.',
            'bendahara.required' => 'Nama Bendahara wajib diisi.',
        ]);

        Kantor::create([...$validated, 'user_id' => $request->user()->id]);

        return redirect()
            ->route('superadmin.kantor')
            ->with('flash.status', 'Kantor berhasil dibuat!');
    }

    public function show(Kantor $kantor)
    {
        $kantor->load(['provinsi:code,name', 'kota:code,name', 'kecamatan:code,name', 'kelurahan:code,name']);

        return inertia('Superadmin/Kantor/Show', ['kantorData' => $kantor]);
    }

    public function edit(Kantor $kantor)
    {
        return inertia('Superadmin/Kantor/Edit', ['kantorData' => $kantor]);
    }

    public function update(Request $request, Kantor $kantor)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:kantor,kode,'.$kantor->id,
            'nama_kantor' => 'required|string|max:255',
            'alamat_kantor' => 'required|string|max:500',
            'provinsi_id' => 'required|string|exists:indonesia_provinces,code',
            'kota_id' => 'required|string|exists:indonesia_cities,code',
            'kecamatan_id' => 'required|string|exists:indonesia_districts,code',
            'kelurahan_id' => 'required|string|exists:indonesia_villages,code',
            'pejabat' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'bendahara' => 'required|string|max:255',
        ]);

        $kantor->update($validated);

        return redirect()
            ->route('superadmin.kantor')
            ->with('flash.status', 'Kantor berhasil diperbarui!');
    }

    public function destroy(Kantor $kantor)
    {
        $kantor->delete();

        return redirect()
            ->route('superadmin.kantor')
            ->with('flash.status', 'Kantor berhasil dihapus!');
    }
}
