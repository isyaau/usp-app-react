<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\PenaltiPinjaman;
use App\Models\Pinjaman;
use Illuminate\Http\Request;

class PenaltiPinjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = PenaltiPinjaman::query()
            ->with([
                'pinjaman:id,no_pinjaman,plafon,anggota_id',
                'pinjaman.anggota:id,no_anggota,nama',
                'user:id,nama',
                'kantor:id,nama_kantor',
            ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('no_transaksi', 'LIKE', "%{$term}%")
                    ->orWhereHas('pinjaman', fn ($p) => $p
                        ->where('no_pinjaman', 'LIKE', "%{$term}%")
                        ->orWhereHas('anggota', fn ($a) => $a->where('nama', 'LIKE', "%{$term}%"))));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')))
            ->orderByDesc('tgl_transaksi')
            ->orderByDesc('id')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/PenaltiPinjaman/Index', [
            'transaksi' => $query,
            'filters' => $request->only(['search', 'status', 'mulai', 'sampai']),
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/PenaltiPinjaman/Create', [
            'anggotas' => Anggota::select('id', 'no_anggota', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl_transaksi' => 'required|date',
            'pinjaman_id' => 'required|exists:pinjaman,id',
            'nominal_penalti' => 'required|numeric|min:0',
            'denda' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kantor_id' => 'required|exists:kantor,id',
            'status' => 'in:draft,posted,batal',
        ]);

        $validated['no_transaksi'] = 'PP-' . date('YmdHis') . rand(10, 99);
        $validated['user_id'] = auth()->id();
        $validated['status'] = $validated['status'] ?? 'draft';
        $validated['denda'] = $validated['denda'] ?? 0;

        PenaltiPinjaman::create($validated);

        return redirect()->route('superadmin.transaksi-pinjaman.penalti-pinjaman')
            ->with('success', 'Penalti pinjaman berhasil ditambahkan.');
    }

    public function show(PenaltiPinjaman $penaltiPinjaman)
    {
        $penaltiPinjaman->load(['pinjaman.anggota', 'user', 'kantor']);

        return inertia('Superadmin/PenaltiPinjaman/Show', [
            'transaksi' => $penaltiPinjaman,
        ]);
    }

    public function edit(PenaltiPinjaman $penaltiPinjaman)
    {
        $penaltiPinjaman->load('pinjaman.anggota');

        return inertia('Superadmin/PenaltiPinjaman/Edit', [
            'transaksi' => $penaltiPinjaman,
            'anggotas' => Anggota::select('id', 'no_anggota', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
        ]);
    }

    public function update(Request $request, PenaltiPinjaman $penaltiPinjaman)
    {
        $validated = $request->validate([
            'tgl_transaksi' => 'sometimes|date',
            'nominal_penalti' => 'sometimes|numeric|min:0',
            'denda' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kantor_id' => 'sometimes|exists:kantor,id',
            'status' => 'in:draft,posted,batal',
        ]);

        $penaltiPinjaman->update($validated);

        return redirect()->route('superadmin.transaksi-pinjaman.penalti-pinjaman')
            ->with('success', 'Penalti pinjaman berhasil diupdate.');
    }

    public function destroy(PenaltiPinjaman $penaltiPinjaman)
    {
        $penaltiPinjaman->delete();

        return redirect()->route('superadmin.transaksi-pinjaman.penalti-pinjaman')
            ->with('success', 'Penalti pinjaman berhasil dihapus.');
    }

    public function pinjamanByAnggota(Anggota $anggota)
    {
        $pinjaman = Pinjaman::where('anggota_id', $anggota->id)
            ->where('aktif', '1')
            ->select('id', 'no_pinjaman', 'plafon')
            ->get();

        return response()->json($pinjaman);
    }
}
