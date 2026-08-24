<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\KasHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class KasHarianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KasHarian::with('user')
            ->latest('tanggal')
            ->when($request->search, function ($q, $search) {
                $q->where('tanggal', 'like', "%{$search}%");
            });

        $kasHarian = $query->paginate(15)->withQueryString();

        return Inertia::render('Superadmin/KasHarian/Index', [
            'kasHarian' => $kasHarian,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Superadmin/KasHarian/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date|unique:kas_harians,tanggal',
            'kas_awal' => 'required|numeric|min:0',
            'kas_masuk' => 'required|numeric|min:0',
            'kas_keluar' => 'required|numeric|min:0',
        ]);

        $validated['kas_akhir'] = $validated['kas_awal'] + $validated['kas_masuk'] - $validated['kas_keluar'];
        $validated['user_id'] = Auth::id();

        KasHarian::create($validated);

        return redirect()
            ->route('superadmin.kas-harian.index')
            ->with('success', 'Data kas harian berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(KasHarian $kasHarian)
    {
        return Inertia::render('Superadmin/KasHarian/Show', [
            'kasHarian' => $kasHarian->load('user'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KasHarian $kasHarian)
    {
        return Inertia::render('Superadmin/KasHarian/Edit', [
            'kasHarian' => $kasHarian,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KasHarian $kasHarian)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date|unique:kas_harians,tanggal,' . $kasHarian->id,
            'kas_awal' => 'required|numeric|min:0',
            'kas_masuk' => 'required|numeric|min:0',
            'kas_keluar' => 'required|numeric|min:0',
        ]);

        $validated['kas_akhir'] = $validated['kas_awal'] + $validated['kas_masuk'] - $validated['kas_keluar'];

        $kasHarian->update($validated);

        return redirect()
            ->route('superadmin.kas-harian.index')
            ->with('success', 'Data kas harian berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KasHarian $kasHarian)
    {
        $kasHarian->delete();

        return redirect()
            ->route('superadmin.kas-harian.index')
            ->with('success', 'Data kas harian berhasil dihapus.');
    }
}
