<?php

namespace App\Http\Controllers\Superadmin;

use App\Exports\TagihanPinjamanExport;
use App\Http\Controllers\Controller;
use App\Models\AngsuranPinjaman;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Controller daftar tagihan pinjaman (menu "Tagihan Pinjaman").
 *
 * Menampilkan pinjaman aktif beserta plafon, pokok yang sudah dibayar,
 * sisa pokok, angsuran per bulan, dan jatuh tempo (read-only).
 */
class TagihanPinjamanController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->string('search');
        $status = (string) $request->string('status');
        $mulai = $request->date('mulai');
        $sampai = $request->date('sampai');

        $tagihan = $this->buildQuery($search, $status, $mulai, $sampai)
            ->paginate((int) ($request->input('per_page') ?: 10))
            ->withQueryString()
            ->through(fn (Pinjaman $p) => $this->toRow($p));

        return inertia('Superadmin/TagihanPinjaman/Index', [
            'tagihan' => $tagihan,
            'filters' => $request->only(['search', 'status', 'mulai', 'sampai']),
            'rekap' => $this->rekap(),
        ]);
    }

    public function cetakTagihanPinjaman(Request $request)
    {
        $search = (string) $request->string('search');
        $status = (string) $request->string('status');
        $mulai = $request->date('mulai');
        $sampai = $request->date('sampai');

        $pinjaman = $this->buildQuery($search, $status, $mulai, $sampai)->get();

        return $this->streamPdf('pdf.tagihan-pinjaman', [
            'pinjaman' => $pinjaman,
            'filters' => $request->only(['search', 'status', 'mulai', 'sampai']),
            'rekap' => $this->rekap(),
        ], 'tagihan_pinjaman.pdf');
    }

    public function exportExcelTagihanPinjaman(Request $request)
    {
        $search = (string) $request->string('search');
        $status = (string) $request->string('status');
        $mulai = $request->date('mulai');
        $sampai = $request->date('sampai');

        $pinjaman = $this->buildQuery($search, $status, $mulai, $sampai)->get();

        return Excel::download(
            new TagihanPinjamanExport($pinjaman, $request->only(['search', 'status', 'mulai', 'sampai'])),
            'tagihan_pinjaman.xlsx'
        );
    }

    private function buildQuery(string $search, string $status, ?\DateTimeInterface $mulai = null, ?\DateTimeInterface $sampai = null)
    {
        $pokokTerbayar = 'COALESCE((SELECT SUM(ap.nominal_pokok) FROM angsuran_pinjaman ap WHERE ap.pinjaman_id = pinjaman.id), 0)';
        $tglBayar = '(SELECT MAX(ap.tgl_transaksi) FROM angsuran_pinjaman ap WHERE ap.pinjaman_id = pinjaman.id)';

        return Pinjaman::query()
            ->with([
                'jenisPinjaman:id,nama',
                'anggota:id,no_anggota,nama',
                'kantor:id,nama_kantor',
            ])
            ->where('aktif', '1')
            ->select('pinjaman.*')
            ->selectRaw("{$pokokTerbayar} AS pokok_terbayar")
            ->selectRaw("{$tglBayar} AS tgl_bayar")
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('no_pinjaman', 'ILIKE', "%{$search}%")
                        ->orWhereHas('anggota', fn ($a) => $a
                            ->where('nama', 'ILIKE', "%{$search}%")
                            ->orWhere('no_anggota', 'ILIKE', "%{$search}%"));
                });
            })
            ->when($status === 'lunas', fn ($q) => $q->whereRaw("{$pokokTerbayar} >= CAST(pinjaman.plafon AS NUMERIC)"))
            ->when($status === 'belum', fn ($q) => $q->whereRaw("{$pokokTerbayar} < CAST(pinjaman.plafon AS NUMERIC)"))
            ->when($mulai, fn ($q) => $q->whereDate('pinjaman.jatuh_tempo', '>=', $mulai))
            ->when($sampai, fn ($q) => $q->whereDate('pinjaman.jatuh_tempo', '<=', $sampai))
            ->orderBy('created_at', 'DESC');
    }

    private function toRow(Pinjaman $p): array
    {
        $plafon = (float) $p->plafon;
        $pokokTerbayar = (float) $p->pokok_terbayar;
        $sisaPokok = max(0, $plafon - $pokokTerbayar);

        return [
            'id' => $p->id,
            'tanggal' => $p->tanggal,
            'tgl_bayar' => $p->tgl_bayar,
            'no_pinjaman' => $p->no_pinjaman,
            'anggota' => $p->anggota ? [
                'id' => $p->anggota->id,
                'no_anggota' => $p->anggota->no_anggota,
                'nama' => $p->anggota->nama,
            ] : null,
            'jenisPinjaman' => $p->jenisPinjaman ? [
                'id' => $p->jenisPinjaman->id,
                'nama' => $p->jenisPinjaman->nama,
            ] : null,
            'plafon' => $plafon,
            'pokok_terbayar' => $pokokTerbayar,
            'sisa_pokok' => $sisaPokok,
            'tunggakan' => $sisaPokok,
            'nominal_angsuran' => (float) $p->nominal_angsuran,
            'bunga' => $p->bunga,
            'jangka_waktu' => $p->jangka_waktu,
            'satuan' => $p->satuan,
            'angsuranke' => (int) $p->angsuranke,
            'jatuh_tempo' => $p->jatuh_tempo,
            'lunas' => (float) $p->pokok_terbayar >= $plafon,
        ];
    }

    /** Ringkasan jumlah & nominal untuk header kartu. */
    private function rekap(): array
    {
        $totalPlafon = (float) Pinjaman::where('aktif', '1')->sum(DB::raw('CAST(plafon AS NUMERIC)'));
        $pokokTerbayar = (float) AngsuranPinjaman::query()
            ->whereRaw('EXISTS (SELECT 1 FROM pinjaman pp WHERE pp.id = angsuran_pinjaman.pinjaman_id AND pp.aktif = 1::text)')
            ->sum('nominal_pokok');

        return [
            'jumlah_pinjaman' => (int) Pinjaman::where('aktif', '1')->count(),
            'total_plafon' => $totalPlafon,
            'total_pokok_terbayar' => $pokokTerbayar,
            'total_sisa_pokok' => max(0, $totalPlafon - $pokokTerbayar),
        ];
    }

    private function streamPdf($view, array $data, string $filename, string $paper = 'A4', string $orientation = 'landscape')
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data)->setPaper($paper, $orientation);
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(420, 570, 'Halaman {PAGE_NUM} / {PAGE_COUNT}', null, 10, [0, 0, 0]);
        return response()->streamDownload(fn () => print($pdf->output()), $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
