<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\PengembalianJaminan;
use App\Models\Pinjaman;
use Illuminate\Http\Request;

class PengembalianJaminanController extends Controller
{
    public function index(Request $request)
    {
        $query = PengembalianJaminan::query()
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
            ->withQueryString()
            ->through(fn (PengembalianJaminan $k) => $this->toRow($k));

        return inertia('Superadmin/PengembalianJaminan/Index', [
            'transaksi' => $query,
            'filters' => $request->only(['search', 'status', 'mulai', 'sampai']),
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/PengembalianJaminan/Create', [
            'anggotas' => Anggota::select('id', 'no_anggota', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl_transaksi' => 'required|date',
            'pinjaman_id' => 'required|exists:pinjaman,id',
            'sisa_pokok' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kantor_id' => 'required|exists:kantor,id',
        ]);

        $validated['no_transaksi'] = 'PJ-' . date('YmdHis') . rand(10, 99);
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'draft';
        $validated['keterangan'] = $validated['keterangan'] ?? null;

        PengembalianJaminan::create($validated);

        return redirect()->route('superadmin.pinjaman.pengembalian-jaminan')
            ->with('success', 'Pengembalian jaminan berhasil ditambahkan.');
    }

    public function show(PengembalianJaminan $pengembalianJaminan)
    {
        $pengembalianJaminan->load(['pinjaman.anggota', 'pinjaman.jaminan', 'user', 'kantor']);

        return inertia('Superadmin/PengembalianJaminan/Show', [
            'transaksi' => $pengembalianJaminan,
        ]);
    }

    public function edit(PengembalianJaminan $pengembalianJaminan)
    {
        $pengembalianJaminan->load(['pinjaman.anggota', 'pinjaman.jaminan']);

        return inertia('Superadmin/PengembalianJaminan/Edit', [
            'transaksi' => $pengembalianJaminan,
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
        ]);
    }

    public function update(Request $request, PengembalianJaminan $pengembalianJaminan)
    {
        $validated = $request->validate([
            'tgl_transaksi' => 'sometimes|date',
            'sisa_pokok' => 'sometimes|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kantor_id' => 'sometimes|exists:kantor,id',
            'status' => 'in:draft,posted,batal',
        ]);

        $pengembalianJaminan->update($validated);

        return redirect()->route('superadmin.pinjaman.pengembalian-jaminan')
            ->with('success', 'Pengembalian jaminan berhasil diupdate.');
    }

    public function destroy(PengembalianJaminan $pengembalianJaminan)
    {
        $pengembalianJaminan->delete();

        return redirect()->route('superadmin.pinjaman.pengembalian-jaminan')
            ->with('success', 'Pengembalian jaminan berhasil dihapus.');
    }

    /** Cetak bukti pengembalian jaminan dalam format PDF (kertas A4 portrait). */
    public function cetak(PengembalianJaminan $pengembalianJaminan)
    {
        $pengembalianJaminan->load([
            'pinjaman.anggota',
            'pinjaman.jaminan',
            'user',
            'kantor',
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.pengembalian-jaminan', [
            'transaksi' => $pengembalianJaminan,
        ])->setPaper('A4', 'portrait');

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'pengembalian_jaminan_' . $pengembalianJaminan->no_transaksi . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    /** Daftar pinjaman aktif milik anggota + sisa pokok & jaminan untuk dropdown form. */
    public function pinjamanByAnggota(Anggota $anggota)
    {
        $pokokTerbayar = 'COALESCE((SELECT SUM(ap.nominal_pokok) FROM angsuran_pinjaman ap WHERE ap.pinjaman_id = pinjaman.id), 0)';

        return response()->json(
            Pinjaman::where('anggota_id', $anggota->id)
                ->where('aktif', '1')
                ->with('jaminan:id,pinjaman_id,nama,keterangan,nominal')
                ->select('pinjaman.id', 'pinjaman.no_pinjaman', 'pinjaman.plafon')
                ->selectRaw("{$pokokTerbayar} AS pokok_terbayar")
                ->get()
                ->map(fn (Pinjaman $p) => [
                    'id' => $p->id,
                    'no_pinjaman' => $p->no_pinjaman,
                    'plafon' => (float) $p->plafon,
                    'sisa_pokok' => max(0, (float) $p->plafon - (float) $p->pokok_terbayar),
                    'jaminan' => $p->jaminan->map(fn ($j) => [
                        'nama' => $j->nama,
                        'keterangan' => $j->keterangan,
                        'nominal' => (float) $j->nominal,
                    ])->values(),
                ])
        );
    }

    private function toRow(PengembalianJaminan $k): array
    {
        return [
            'id' => $k->id,
            'no_transaksi' => $k->no_transaksi,
            'tgl_transaksi' => optional($k->tgl_transaksi)->toDateString(),
            'sisa_pokok' => (float) $k->sisa_pokok,
            'keterangan' => $k->keterangan,
            'status' => $k->status,
            'pinjaman' => $k->pinjaman ? [
                'id' => $k->pinjaman->id,
                'no_pinjaman' => $k->pinjaman->no_pinjaman,
                'plafon' => (float) $k->pinjaman->plafon,
                'anggota' => $k->pinjaman->anggota ? [
                    'id' => $k->pinjaman->anggota->id,
                    'no_anggota' => $k->pinjaman->anggota->no_anggota,
                    'nama' => $k->pinjaman->anggota->nama,
                ] : null,
            ] : null,
            'user' => $k->user ? ['id' => $k->user->id, 'nama' => $k->user->nama] : null,
            'kantor' => $k->kantor ? ['id' => $k->kantor->id, 'nama_kantor' => $k->kantor->nama_kantor] : null,
        ];
    }
}