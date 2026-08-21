<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kantor;
use App\Models\Marketing;
use Illuminate\Http\Request;

/**
 * Controller CRUD Marketing untuk frontend Inertia (React + TypeScript).
 * Menggantikan Livewire Superadmin\Marketing.
 */
class MarketingController extends Controller
{
    public function index(Request $request)
    {
        $marketing = Marketing::query()
            ->with('kantor:id,nama_kantor')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('nama', 'LIKE', "%{$term}%")
                    ->orWhere('kode', 'LIKE', "%{$term}%")
                    ->orWhere('no_ktp', 'LIKE', "%{$term}%"));
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/Marketing/Index', [
            'marketing' => $marketing,
            'filters' => ['search' => $request->input('search', '')],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/Marketing/Create', [
            'kantorOptions' => Kantor::orderBy('nama_kantor')->get(['id', 'nama_kantor']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateMarketing($request);

        Marketing::create([
            ...$validated,
            'aktif' => $request->boolean('aktif'),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('superadmin.marketing')
            ->with('flash.status', 'Marketing berhasil dibuat!');
    }

    public function show(Marketing $marketing)
    {
        $marketing->load('kantor:id,nama_kantor');

        return inertia('Superadmin/Marketing/Show', ['marketingData' => $marketing]);
    }

    public function edit(Marketing $marketing)
    {
        return inertia('Superadmin/Marketing/Edit', [
            'marketingData' => $marketing,
            'kantorOptions' => Kantor::orderBy('nama_kantor')->get(['id', 'nama_kantor']),
        ]);
    }

    public function update(Request $request, Marketing $marketing)
    {
        $validated = $this->validateMarketing($request, $marketing->id);

        $marketing->update([
            ...$validated,
            'aktif' => $request->boolean('aktif'),
        ]);

        return redirect()
            ->route('superadmin.marketing')
            ->with('flash.status', 'Marketing berhasil diperbarui!');
    }

    public function destroy(Marketing $marketing)
    {
        $marketing->delete();

        return redirect()
            ->route('superadmin.marketing')
            ->with('flash.status', 'Marketing berhasil dihapus!');
    }

    private function validateMarketing(Request $request, ?int $ignoreId = null): array
    {
        $uniqueKtp = is_null($ignoreId)
            ? 'unique:marketing,no_ktp'
            : 'unique:marketing,no_ktp,'.$ignoreId;

        return $request->validate([
            'kode' => 'required|string|max:50|unique:marketing,kode'.(is_null($ignoreId) ? '' : ','.$ignoreId),
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'no_ktp' => 'required|string|max:30|'.$uniqueKtp,
            'telepon' => 'nullable|string|max:20',
            'no_hp' => 'nullable|string|max:20',
            'kantor_id' => 'required|integer|exists:kantor,id',
        ], [
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode sudah digunakan.',
            'nama.required' => 'Nama wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_ktp.required' => 'No. KTP wajib diisi.',
            'no_ktp.unique' => 'No. KTP sudah terdaftar.',
            'kantor_id.required' => 'Kantor wajib dipilih.',
            'kantor_id.exists' => 'Kantor tidak ditemukan.',
        ]);
    }
}
