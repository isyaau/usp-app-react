<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\AngsuranPinjaman;
use App\Models\Kantor;
use App\Models\Pinjaman;
use App\Models\User;
use Illuminate\Http\Request;

class AngsuranPinjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = AngsuranPinjaman::query()
            ->with([
                'pinjaman:id,no_pinjaman,plafon,anggota_id,jenis_id',
                'pinjaman.anggota:id,no_anggota,nama',
                'pinjaman.jenisPinjaman:id,nama',
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

        return inertia('Superadmin/AngsuranPinjaman/Index', [
            'transaksi' => $query,
            'filters' => $request->only(['search', 'status', 'mulai', 'sampai']),
        ]);
    }

    public function create(Request $request)
    {
        return inertia('Superadmin/AngsuranPinjaman/Create', [
            'anggotas' => Anggota::select('id', 'no_anggota', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl_transaksi' => 'required|date',
            'pinjaman_id' => 'required|exists:pinjaman,id',
            'angsuran_ke' => 'required|integer|min:1',
            'nominal_pokok' => 'required|numeric|min:0',
            'nominal_bunga' => 'required|numeric|min:0',
            'total_angsuran' => 'required|numeric|min:0',
            'denda' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kantor_id' => 'required|exists:kantor,id',
            'status' => 'in:draft,posted,batal',
        ]);

        $validated['no_transaksi'] = 'AP-' . date('YmdHis') . rand(10, 99);
        $validated['user_id'] = auth()->id();
        $validated['status'] = $validated['status'] ?? 'draft';
        $validated['denda'] = $validated['denda'] ?? 0;

        AngsuranPinjaman::create($validated);

        return redirect()->route('superadmin.transaksi-pinjaman.angsuran-pinjaman')
            ->with('success', 'Angsuran pinjaman berhasil ditambahkan.');
    }

    public function show(AngsuranPinjaman $angsuranPinjaman)
    {
        $angsuranPinjaman->load([
            'pinjaman.anggota', 'pinjaman.jenisPinjaman', 'user', 'kantor',
        ]);

        return inertia('Superadmin/AngsuranPinjaman/Show', [
            'transaksi' => $angsuranPinjaman,
        ]);
    }

    public function edit(AngsuranPinjaman $angsuranPinjaman)
    {
        $angsuranPinjaman->load('pinjaman.anggota');

        return inertia('Superadmin/AngsuranPinjaman/Edit', [
            'transaksi' => $angsuranPinjaman,
            'anggotas' => Anggota::select('id', 'no_anggota', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
        ]);
    }

    public function update(Request $request, AngsuranPinjaman $angsuranPinjaman)
    {
        $validated = $request->validate([
            'tgl_transaksi' => 'sometimes|date',
            'angsuran_ke' => 'sometimes|integer|min:1',
            'nominal_pokok' => 'sometimes|numeric|min:0',
            'nominal_bunga' => 'sometimes|numeric|min:0',
            'total_angsuran' => 'sometimes|numeric|min:0',
            'denda' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kantor_id' => 'sometimes|exists:kantor,id',
            'status' => 'in:draft,posted,batal',
        ]);

        $angsuranPinjaman->update($validated);

        return redirect()->route('superadmin.transaksi-pinjaman.angsuran-pinjaman')
            ->with('success', 'Angsuran pinjaman berhasil diupdate.');
    }

    public function destroy(AngsuranPinjaman $angsuranPinjaman)
    {
        $angsuranPinjaman->delete();

        return redirect()->route('superadmin.transaksi-pinjaman.angsuran-pinjaman')
            ->with('success', 'Angsuran pinjaman berhasil dihapus.');
    }

    public function pinjamanByAnggota(Anggota $anggota)
    {
        $pinjaman = Pinjaman::where('anggota_id', $anggota->id)
            ->where('aktif', '1')
            ->select('id', 'no_pinjaman', 'plafon', 'angsuranke', 'jangka_waktu')
            ->get();

        return response()->json($pinjaman);
    }
}
