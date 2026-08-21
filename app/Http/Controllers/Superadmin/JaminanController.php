<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Jaminan;
use App\Models\JaminanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller CRUD Jaminan untuk frontend Inertia.
 * Menggantikan Livewire Superadmin\Jaminan — parent jaminan +
 * daftar detail dinamis (jaminan_detail).
 */
class JaminanController extends Controller
{
    public function index(Request $request)
    {
        $jaminan = Jaminan::query()
            ->with('details:id,jaminan_id,detail')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where('nama', 'LIKE', "%{$term}%");
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/Jaminan/Index', [
            'jaminan' => $jaminan,
            'filters' => ['search' => $request->input('search', '')],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/Jaminan/Create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateJaminan($request);

        DB::transaction(function () use ($request, $validated) {
            $parent = Jaminan::create([
                'nama' => $validated['nama'],
                'user_id' => $request->user()->id,
            ]);

            foreach ($validated['detail'] as $text) {
                JaminanDetail::create([
                    'jaminan_id' => $parent->id,
                    'detail' => $text,
                    'user_id' => $request->user()->id,
                ]);
            }
        });

        return redirect()
            ->route('superadmin.pinjaman.jaminan')
            ->with('flash.status', 'Data jaminan berhasil ditambahkan!');
    }

    public function show(Jaminan $jaminan)
    {
        $jaminan->load('details:id,jaminan_id,detail');

        return inertia('Superadmin/Jaminan/Show', ['jaminanData' => $jaminan]);
    }

    public function edit(Jaminan $jaminan)
    {
        $jaminan->load('details:id,jaminan_id,detail');

        return inertia('Superadmin/Jaminan/Edit', ['jaminanData' => $jaminan]);
    }

    public function update(Request $request, Jaminan $jaminan)
    {
        $validated = $this->validateJaminan($request, $jaminan->id);

        DB::transaction(function () use ($jaminan, $validated) {
            $jaminan->update(['nama' => $validated['nama']]);

            $jaminan->details()->delete();
            foreach ($validated['detail'] as $text) {
                JaminanDetail::create([
                    'jaminan_id' => $jaminan->id,
                    'detail' => $text,
                    'user_id' => $jaminan->user_id,
                ]);
            }
        });

        return redirect()
            ->route('superadmin.pinjaman.jaminan')
            ->with('flash.status', 'Data jaminan berhasil diperbarui!');
    }

    public function destroy(Jaminan $jaminan)
    {
        $jaminan->delete();

        return redirect()
            ->route('superadmin.pinjaman.jaminan')
            ->with('flash.status', 'Data jaminan berhasil dihapus!');
    }

    private function validateJaminan(Request $request, ?int $ignoreId = null): array
    {
        $suffix = is_null($ignoreId) ? '' : ','.$ignoreId;

        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:jaminan,nama'.$suffix,
            'detail' => 'required|array|min:1',
            'detail.*' => 'required|string|max:255',
        ], [
            'nama.required' => 'Nama jaminan wajib diisi.',
            'nama.unique' => 'Nama jaminan sudah digunakan.',
            'detail.required' => 'Minimal satu detail jaminan harus diisi.',
            'detail.*.required' => 'Detail jaminan tidak boleh kosong.',
        ]);

        // Buang baris kosong
        $validated['detail'] = collect($validated['detail'])
            ->map(fn ($d) => trim((string) $d))
            ->filter()
            ->values()
            ->all();

        abort_if(empty($validated['detail']), 422, 'Minimal satu detail jaminan harus diisi.');

        return $validated;
    }
}
