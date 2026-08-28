<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\Pinjaman;
use App\Models\SuratPeringatan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SuratPeringatanController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratPeringatan::query()
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
            ->when($request->filled('tahap'), fn ($q) => $q->where('tahap', $request->string('tahap')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')))
            ->orderByDesc('tgl_transaksi')
            ->orderByDesc('id')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString()
            ->through(fn (SuratPeringatan $sp) => $this->toRow($sp));

        return inertia('Superadmin/SuratPeringatan/Index', [
            'transaksi' => $query,
            'filters' => $request->only(['search', 'status', 'tahap', 'mulai', 'sampai']),
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/SuratPeringatan/Create', [
            'anggotas' => Anggota::select('id', 'no_anggota', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl_transaksi' => 'required|date',
            'pinjaman_id' => 'required|exists:pinjaman,id',
            'tahap' => 'in:SP-1,SP-2,SP-3',
            'isi' => 'nullable|string',
            'kantor_id' => 'required|exists:kantor,id',
        ]);

        $validated['no_transaksi'] = 'SP-' . date('YmdHis') . rand(10, 99);
        $validated['user_id'] = auth()->id();
        $validated['tahap'] = $validated['tahap'] ?? 'SP-1';
        $validated['status'] = 'draft';
        $validated['isi'] = $validated['isi'] ?? null;

        SuratPeringatan::create($validated);

        return redirect()->route('superadmin.pinjaman.surat-peringatan')
            ->with('success', 'Surat peringatan berhasil ditambahkan.');
    }

    public function show(SuratPeringatan $suratPeringatan)
    {
        $suratPeringatan->load(['pinjaman.anggota', 'user', 'kantor']);

        return inertia('Superadmin/SuratPeringatan/Show', [
            'transaksi' => $suratPeringatan,
        ]);
    }

    public function edit(SuratPeringatan $suratPeringatan)
    {
        $suratPeringatan->load('pinjaman.anggota');

        return inertia('Superadmin/SuratPeringatan/Edit', [
            'transaksi' => $suratPeringatan,
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
        ]);
    }

    public function update(Request $request, SuratPeringatan $suratPeringatan)
    {
        $validated = $request->validate([
            'tgl_transaksi' => 'sometimes|date',
            'tahap' => 'in:SP-1,SP-2,SP-3',
            'isi' => 'nullable|string',
            'kantor_id' => 'sometimes|exists:kantor,id',
            'status' => 'in:draft,posted,batal',
        ]);

        $suratPeringatan->update($validated);

        return redirect()->route('superadmin.pinjaman.surat-peringatan')
            ->with('success', 'Surat peringatan berhasil diupdate.');
    }

    public function destroy(SuratPeringatan $suratPeringatan)
    {
        $suratPeringatan->delete();

        return redirect()->route('superadmin.pinjaman.surat-peringatan')
            ->with('success', 'Surat peringatan berhasil dihapus.');
    }

    /** Cetak surat peringatan dalam format PDF (kertas A4 portrait). */
    public function cetak(SuratPeringatan $suratPeringatan)
    {
        $suratPeringatan->load([
            'pinjaman.jenisPinjaman',
            'pinjaman.anggota',
            'user',
            'kantor',
        ]);

        $pdf = Pdf::loadView('pdf.surat-peringatan', [
            'surat' => $suratPeringatan,
        ])->setPaper('A4', 'portrait');

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'surat_peringatan_' . $suratPeringatan->no_transaksi . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    /** Daftar pinjaman aktif milik anggota + sisa pokok untuk dropdown form. */
    public function pinjamanByAnggota(Anggota $anggota)
    {
        $pokokTerbayar = 'COALESCE((SELECT SUM(ap.nominal_pokok) FROM angsuran_pinjaman ap WHERE ap.pinjaman_id = pinjaman.id), 0)';

        return response()->json(
            Pinjaman::where('anggota_id', $anggota->id)
                ->where('aktif', '1')
                ->select('pinjaman.id', 'pinjaman.no_pinjaman', 'pinjaman.plafon')
                ->selectRaw("{$pokokTerbayar} AS pokok_terbayar")
                ->get()
                ->map(fn (Pinjaman $p) => [
                    'id' => $p->id,
                    'no_pinjaman' => $p->no_pinjaman,
                    'plafon' => (float) $p->plafon,
                    'sisa_pokok' => max(0, (float) $p->plafon - (float) $p->pokok_terbayar),
                ])
        );
    }

    private function toRow(SuratPeringatan $sp): array
    {
        return [
            'id' => $sp->id,
            'no_transaksi' => $sp->no_transaksi,
            'tgl_transaksi' => optional($sp->tgl_transaksi)->toDateString(),
            'tahap' => $sp->tahap,
            'isi' => $sp->isi,
            'status' => $sp->status,
            'pinjaman' => $sp->pinjaman ? [
                'id' => $sp->pinjaman->id,
                'no_pinjaman' => $sp->pinjaman->no_pinjaman,
                'plafon' => (float) $sp->pinjaman->plafon,
                'anggota' => $sp->pinjaman->anggota ? [
                    'id' => $sp->pinjaman->anggota->id,
                    'no_anggota' => $sp->pinjaman->anggota->no_anggota,
                    'nama' => $sp->pinjaman->anggota->nama,
                ] : null,
            ] : null,
            'user' => $sp->user ? ['id' => $sp->user->id, 'nama' => $sp->user->nama] : null,
            'kantor' => $sp->kantor ? ['id' => $sp->kantor->id, 'nama_kantor' => $sp->kantor->nama_kantor] : null,
        ];
    }
}