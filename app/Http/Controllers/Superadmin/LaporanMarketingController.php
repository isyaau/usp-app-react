<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AngsuranPinjaman;
use App\Models\Deposito;
use App\Models\Kantor;
use App\Models\Marketing;
use App\Models\Pinjaman;
use App\Models\PinjamanProduk;
use App\Models\SetoranSimpanan;
use App\Models\Simpanan;
use App\Models\TarikanSimpanan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanMarketingController extends Controller
{
    private function marketings()
    {
        return Marketing::select('id', 'kode', 'nama')->get();
    }

    private function kantors()
    {
        return Kantor::select('id', 'kode', 'nama_kantor')->get();
    }

    private function filters(Request $request): array
    {
        return $request->only(['search', 'marketing_id', 'kantor_id', 'jenis_id', 'mulai', 'sampai']);
    }

    private function applySearch($query, Request $request, array $columns): void
    {
        if ($request->filled('search')) {
            $term = $request->string('search');
            $query->where(fn ($q) => collect($columns)->each(
                fn ($col) => $q->orWhere($col, 'LIKE', "%{$term}%")
            ));
        }
    }

    private function applyMarketingKantor($query, Request $request, string $marketingCol = 'marketing_id', string $kantorCol = 'kantor_id'): void
    {
        $query->when($request->filled('marketing_id'), fn ($q) => $q->where($marketingCol, $request->input('marketing_id')));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where($kantorCol, $request->input('kantor_id')));
    }

    private function paginateFlat($query, Request $request, callable $row)
    {
        $pag = $query->paginate($request->integer('per_page', 10))->withQueryString();
        $pag->setCollection($pag->getCollection()->map($row));
        return $pag;
    }

    private function indexProps(Request $request, $pag, string $title, array $extra = [])
    {
        return array_merge([
            'data' => $pag,
            'filters' => $this->filters($request),
            'kantors' => $this->kantors(),
            'marketings' => $this->marketings(),
            'variantTitle' => $title,
        ], $extra);
    }

    private function streamPdf($view, array $data, string $filename, string $paper = 'A4', string $orientation = 'landscape')
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data)->setPaper($paper, $orientation);
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(420, 570, 'Halaman {PAGE_NUM} / {PAGE_COUNT}', null, 10, [0, 0, 0]);
        return response()->streamDownload(fn () => print($pdf->output()), $filename);
    }

    private function angsuranQuery()
    {
        return AngsuranPinjaman::with([
            'pinjaman:id,no_pinjaman,anggota_id,jenis_id,marketing_id,tanggal,plafon,angsuran,jangka_waktu',
            'pinjaman.anggota:id,no_anggota,nama',
            'pinjaman.jenisPinjaman:id,kode,nama',
        ]);
    }

    private function angsuranRow(AngsuranPinjaman $t): array
    {
        return [
            'id' => $t->id,
            'no_transaksi' => (string) $t->no_transaksi,
            'tanggal' => $t->tgl_transaksi ? Carbon::parse($t->tgl_transaksi)->toDateString() : null,
            'no_pinjaman' => (string) ($t->pinjaman?->no_pinjaman ?? ''),
            'no_anggota' => (string) ($t->pinjaman?->anggota?->no_anggota ?? ''),
            'nama_anggota' => (string) ($t->pinjaman?->anggota?->nama ?? ''),
            'produk' => (string) ($t->pinjaman?->jenisPinjaman?->nama ?? ''),
            'angsuran_ke' => (int) ($t->angsuran_ke ?? 0),
            'nominal_pokok' => (float) ($t->nominal_pokok ?? 0),
            'nominal_bunga' => (float) ($t->nominal_bunga ?? 0),
            'total_angsuran' => (float) ($t->total_angsuran ?? 0),
            'denda' => (float) ($t->denda ?? 0),
        ];
    }

    // 1. DAFTAR MARKETING
    public function daftarMarketing(Request $request)
    {
        $query = Marketing::with('kantor:id,nama_kantor');
        $this->applySearch($query, $request, ['kode', 'nama', 'no_hp']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Marketing/DaftarMarketing', $this->indexProps(
            $request,
            $this->paginateFlat($query->orderBy('kode'), $request, fn ($m) => [
                'id' => $m->id,
                'kode' => (string) $m->kode,
                'nama' => (string) $m->nama,
                'alamat' => (string) $m->alamat,
                'no_hp' => (string) $m->no_hp,
                'aktif' => (bool) $m->aktif,
                'kantor' => (string) ($m->kantor?->nama_kantor ?? ''),
            ]),
            'Daftar Marketing'
        ));
    }

    public function cetakDaftarMarketing(Request $request)
    {
        $query = Marketing::with('kantor:id,nama_kantor');
        $this->applySearch($query, $request, ['kode', 'nama', 'no_hp']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return $this->streamPdf('pdf.laporan-cs.marketing.daftar-marketing', [
            'items' => $query->orderBy('kode')->get(),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'daftar_marketing.pdf');
    }

    // 2. LAPORAN ANGSURAN PINJAMAN MARKETING
    public function angsuranPinjamanMarketing(Request $request)
    {
        $query = $this->angsuranQuery();
        $this->applySearch($query, $request, ['no_transaksi', 'no_pinjaman', 'pinjaman.anggota.nama']);
        $query->whereHas('pinjaman');
        $query->whereHas('pinjaman', fn ($q) => $q->when($request->filled('marketing_id'), fn ($p) => $p->where('marketing_id', $request->input('marketing_id'))));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Marketing/AngsuranPinjamanMarketing', $this->indexProps(
            $request,
            $this->paginateFlat($query->orderByDesc('tgl_transaksi'), $request, fn ($t) => $this->angsuranRow($t)),
            'Laporan Angsuran Pinjaman Marketing',
            ['showDate' => true]
        ));
    }

    public function cetakAngsuranPinjamanMarketing(Request $request)
    {
        $query = $this->angsuranQuery();
        $this->applySearch($query, $request, ['no_transaksi', 'no_pinjaman', 'pinjaman.anggota.nama']);
        $query->whereHas('pinjaman');
        $query->whereHas('pinjaman', fn ($q) => $q->when($request->filled('marketing_id'), fn ($p) => $p->where('marketing_id', $request->input('marketing_id'))));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.marketing.angsuran-pinjaman-marketing', [
            'items' => $query->orderByDesc('tgl_transaksi')->get(),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_angsuran_pinjaman_marketing.pdf');
    }

    // 3. LAPORAN ANGSURAN PINJAMAN MARKETING DETAIL
    public function angsuranPinjamanMarketingDetail(Request $request)
    {
        $query = $this->angsuranQuery();
        $this->applySearch($query, $request, ['no_transaksi', 'no_pinjaman', 'pinjaman.anggota.nama']);
        $query->whereHas('pinjaman');
        $query->whereHas('pinjaman', fn ($q) => $q->when($request->filled('marketing_id'), fn ($p) => $p->where('marketing_id', $request->input('marketing_id'))));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Marketing/AngsuranPinjamanMarketingDetail', $this->indexProps(
            $request,
            $this->paginateFlat($query->orderByDesc('tgl_transaksi'), $request, function ($t) {
                $row = $this->angsuranRow($t);
                $row['marketing'] = (string) ($t->pinjaman?->marketing_id ? (Marketing::find($t->pinjaman->marketing_id)?->nama ?? '') : '');
                return $row;
            }),
            'Laporan Angsuran Pinjaman Marketing Detail',
            ['showDate' => true]
        ));
    }

    public function cetakAngsuranPinjamanMarketingDetail(Request $request)
    {
        $query = $this->angsuranQuery();
        $this->applySearch($query, $request, ['no_transaksi', 'no_pinjaman', 'pinjaman.anggota.nama']);
        $query->whereHas('pinjaman');
        $query->whereHas('pinjaman', fn ($q) => $q->when($request->filled('marketing_id'), fn ($p) => $p->where('marketing_id', $request->input('marketing_id'))));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        $items = $query->orderByDesc('tgl_transaksi')->get()->map(fn ($t) => $this->angsuranRow($t));

        return $this->streamPdf('pdf.laporan-cs.marketing.angsuran-pinjaman-marketing-detail', [
            'items' => $items,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_angsuran_pinjaman_marketing_detail.pdf');
    }

    // 4. LAPORAN INSENTIF MARKETING
    public function insentifMarketing(Request $request)
    {
        $this->applyMarketingKantor($query = Marketing::query(), $request);
        $this->applySearch($query, $request, ['kode', 'nama']);

        return inertia('Superadmin/LaporanCS/Marketing/InsentifMarketing', $this->indexProps(
            $request,
            $this->paginateFlat($query->orderBy('kode'), $request, function ($m) {
                $totalPokok = AngsuranPinjaman::whereHas('pinjaman', fn ($q) => $q->where('marketing_id', $m->id))->sum('nominal_pokok');
                $totalBunga = AngsuranPinjaman::whereHas('pinjaman', fn ($q) => $q->where('marketing_id', $m->id))->sum('nominal_bunga');
                $insentif = (float) ($m->rate_insentif ?? 0);
                $nilaiInsentif = $insentif > 0 ? round($totalPokok * $insentif / 100) : 0;
                return [
                    'id' => $m->id,
                    'kode' => (string) $m->kode,
                    'nama' => (string) $m->nama,
                    'total_angsuran' => round($totalPokok + $totalBunga),
                    'total_pokok' => round($totalPokok),
                    'total_bunga' => round($totalBunga),
                    'rate_insentif' => (float) $insentif,
                    'nilai_insentif' => $nilaiInsentif,
                ];
            }),
            'Laporan Insentif Marketing'
        ));
    }

    public function cetakInsentifMarketing(Request $request)
    {
        $this->applyMarketingKantor($query = Marketing::query(), $request);
        $this->applySearch($query, $request, ['kode', 'nama']);
        $items = $query->orderBy('kode')->get()->map(function ($m) {
            $totalPokok = AngsuranPinjaman::whereHas('pinjaman', fn ($q) => $q->where('marketing_id', $m->id))->sum('nominal_pokok');
            $totalBunga = AngsuranPinjaman::whereHas('pinjaman', fn ($q) => $q->where('marketing_id', $m->id))->sum('nominal_bunga');
            $insentif = (float) ($m->rate_insentif ?? 0);
            return [
                'kode' => (string) $m->kode,
                'nama' => (string) $m->nama,
                'total_angsuran' => round($totalPokok + $totalBunga),
                'total_pokok' => round($totalPokok),
                'total_bunga' => round($totalBunga),
                'rate_insentif' => (float) $insentif,
                'nilai_insentif' => $insentif > 0 ? round($totalPokok * $insentif / 100) : 0,
            ];
        });

        return $this->streamPdf('pdf.laporan-cs.marketing.insentif-marketing', [
            'items' => $items,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_insentif_marketing.pdf');
    }

    // 5. LAPORAN INSENTIF MARKETING ANGSURAN PINJAMAN
    public function insentifMarketingAngsuranPinjaman(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,no_pinjaman,anggota_id,marketing_id,tanggal,plafon,jenis_id',
            'pinjaman.anggota:id,no_anggota,nama',
            'pinjaman.jenisPinjaman:id,kode,nama,insentif',
        ]);
        $query->whereHas('pinjaman');
        $query->whereHas('pinjaman', fn ($q) => $q->when($request->filled('marketing_id'), fn ($p) => $p->where('marketing_id', $request->input('marketing_id'))));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Marketing/InsentifMarketingAngsuranPinjaman', $this->indexProps(
            $request,
            $this->paginateFlat($query->orderByDesc('tgl_transaksi'), $request, function ($t) {
                $row = $this->angsuranRow($t);
                $rate = (float) ($t->pinjaman?->jenisPinjaman?->insentif ?? 0);
                $row['rate_insentif'] = $rate;
                $row['insentif'] = $rate > 0 ? round(($t->nominal_pokok ?? 0) * $rate / 100) : 0;
                $row['marketing'] = (string) ($t->pinjaman?->marketing_id ? (Marketing::find($t->pinjaman->marketing_id)?->nama ?? '') : '');
                return $row;
            }),
            'Laporan Insentif Marketing Angsuran Pinjaman',
            ['showDate' => true]
        ));
    }

    public function cetakInsentifMarketingAngsuranPinjaman(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,no_pinjaman,anggota_id,marketing_id,tanggal,plafon,jenis_id',
            'pinjaman.anggota:id,no_anggota,nama',
            'pinjaman.jenisPinjaman:id,kode,nama,insentif',
        ]);
        $query->whereHas('pinjaman');
        $query->whereHas('pinjaman', fn ($q) => $q->when($request->filled('marketing_id'), fn ($p) => $p->where('marketing_id', $request->input('marketing_id'))));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.marketing.insentif-marketing-angsuran-pinjaman', [
            'items' => $query->orderByDesc('tgl_transaksi')->get(),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_insentif_marketing_angsuran_pinjaman.pdf');
    }

    // 6. LAPORAN PINJAMAN MARKETING
    public function pinjamanMarketing(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'marketing:id,kode,nama',
            'kantor:id,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $this->applyMarketingKantor($query, $request);

        return inertia('Superadmin/LaporanCS/Marketing/PinjamanMarketing', $this->indexProps(
            $request,
            $this->paginateFlat($query->orderByDesc('tanggal'), $request, function ($p) {
                return [
                    'id' => $p->id,
                    'no_pinjaman' => (string) $p->no_pinjaman,
                    'tanggal' => $p->tanggal ? Carbon::parse($p->tanggal)->toDateString() : null,
                    'no_anggota' => (string) ($p->anggota?->no_anggota ?? ''),
                    'nama_anggota' => (string) ($p->anggota?->nama ?? ''),
                    'produk' => (string) ($p->jenisPinjaman?->nama ?? ''),
                    'plafon' => (float) ($p->plafon ?? 0),
                    'bunga' => (string) ($p->bunga ?? ''),
                    'jangka_waktu' => (string) ($p->jangka_waktu ?? ''),
                    'marketing' => (string) ($p->marketing?->nama ?? ''),
                    'kantor' => (string) ($p->kantor?->nama_kantor ?? ''),
                ];
            }),
            'Laporan Pinjaman Marketing'
        ));
    }

    public function cetakPinjamanMarketing(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'marketing:id,kode,nama',
            'kantor:id,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $this->applyMarketingKantor($query, $request);

        return $this->streamPdf('pdf.laporan-cs.marketing.pinjaman-marketing', [
            'items' => $query->orderByDesc('tanggal')->get(),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_pinjaman_marketing.pdf');
    }

    private function tagihanRows(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'marketing:id,kode,nama',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $this->applyMarketingKantor($query, $request);
        $query->where('aktif', 1);

        $pinjamans = $query->get();
        $paidMap = AngsuranPinjaman::whereIn('pinjaman_id', $pinjamans->pluck('id'))
            ->selectRaw('pinjaman_id, SUM(nominal_pokok) as pokok, SUM(nominal_bunga) as bunga, COUNT(*) as jml')
            ->groupBy('pinjaman_id')->get()->keyBy('pinjaman_id');

        return $pinjamans->map(function ($p) use ($paidMap) {
            $paid = $paidMap->get($p->id);
            $pokok = (float) ($paid->pokok ?? 0);
            $bunga = (float) ($paid->bunga ?? 0);
            $sisa = round($p->plafon - $pokok);
            $diangsur = round($pokok + $bunga);
            return [
                'id' => $p->id,
                'no_pinjaman' => (string) $p->no_pinjaman,
                'tanggal' => $p->tanggal ? Carbon::parse($p->tanggal)->toDateString() : null,
                'no_anggota' => (string) ($p->anggota?->no_anggota ?? ''),
                'nama_anggota' => (string) ($p->anggota?->nama ?? ''),
                'produk' => (string) ($p->jenisPinjaman?->nama ?? ''),
                'marketing' => (string) ($p->marketing?->nama ?? ''),
                'plafon' => (float) ($p->plafon ?? 0),
                'terbayar' => round($diangsur),
                'sisa_pokok' => max(0, $sisa),
                'angsuran_ke' => (int) ($paid->jml ?? 0),
                'jangka_waktu' => (int) ($p->jangka_waktu ?? 0),
            ];
        })->values();
    }

    // 7. LAPORAN TAGIHAN MARKETING
    public function tagihanMarketing(Request $request)
    {
        $rows = $this->tagihanRows($request);

        return inertia('Superadmin/LaporanCS/Marketing/TagihanMarketing', $this->indexProps(
            $request,
            $this->paginateCollection($request, $rows),
            'Laporan Tagihan Marketing'
        ));
    }

    public function cetakTagihanMarketing(Request $request)
    {
        return $this->streamPdf('pdf.laporan-cs.marketing.tagihan-marketing', [
            'items' => $this->tagihanRows($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_tagihan_marketing.pdf');
    }

    // 8. LAPORAN TAGIHAN MARKETING DETAIL
    public function tagihanMarketingDetail(Request $request)
    {
        $rows = $this->tagihanRows($request);

        return inertia('Superadmin/LaporanCS/Marketing/TagihanMarketingDetail', $this->indexProps(
            $request,
            $this->paginateCollection($request, $rows),
            'Laporan Tagihan Marketing Detail'
        ));
    }

    public function cetakTagihanMarketingDetail(Request $request)
    {
        return $this->streamPdf('pdf.laporan-cs.marketing.tagihan-marketing-detail', [
            'items' => $this->tagihanRows($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_tagihan_marketing_detail.pdf');
    }

    // 9. LAPORAN TAGIHAN MARKETING (STATUS)
    public function tagihanMarketingStatus(Request $request)
    {
        $rows = $this->tagihanRows($request)->map(function ($r) {
            $r['status'] = $r['sisa_pokok'] <= 0 ? 'Lunas' : ($r['angkauran_tunggakan'] ?? 'Berjalan');
            return $r;
        });

        return inertia('Superadmin/LaporanCS/Marketing/TagihanMarketingStatus', $this->indexProps(
            $request,
            $this->paginateCollection($request, $rows),
            'Laporan Tagihan Marketing (Status)'
        ));
    }

    public function cetakTagihanMarketingStatus(Request $request)
    {
        $rows = $this->tagihanRows($request)->map(function ($r) {
            $r['status'] = $r['sisa_pokok'] <= 0 ? 'Lunas' : ($r['angkauran_tunggakan'] ?? 'Berjalan');
            return $r;
        });

        return $this->streamPdf('pdf.laporan-cs.marketing.tagihan-marketing-status', [
            'items' => $rows,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_tagihan_marketing_status.pdf');
    }

    // 10. LAPORAN SIMPANAN MARKETING
    public function simpananMarketing(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'marketing:id,kode,nama',
            'kantor:id,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama']);
        $this->applyMarketingKantor($query, $request);

        return inertia('Superadmin/LaporanCS/Marketing/SimpananMarketing', $this->indexProps(
            $request,
            $this->paginateFlat($query->orderBy('no_rekening'), $request, function ($s) {
                return [
                    'id' => $s->id,
                    'no_rekening' => (string) $s->no_rekening,
                    'no_anggota' => (string) ($s->anggota?->no_anggota ?? ''),
                    'nama_anggota' => (string) ($s->anggota?->nama ?? ''),
                    'jenis_simpanan' => (string) ($s->jenis_simpanan?->nama ?? ''),
                    'nominal_setor' => (float) ($s->nominal_setor ?? 0),
                    'aktif' => (bool) $s->aktif,
                    'marketing' => (string) ($s->marketing?->nama ?? ''),
                    'kantor' => (string) ($s->kantor?->nama_kantor ?? ''),
                ];
            }),
            'Laporan Simpanan Marketing'
        ));
    }

    public function cetakSimpananMarketing(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'marketing:id,kode,nama',
            'kantor:id,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama']);
        $this->applyMarketingKantor($query, $request);

        return $this->streamPdf('pdf.laporan-cs.marketing.simpanan-marketing', [
            'items' => $query->orderBy('no_rekening')->get(),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_simpanan_marketing.pdf');
    }

    private function transaksiSimpananQuery()
    {
        return SetoranSimpanan::with([
            'anggota:id,no_anggota,nama',
            'simpanan:id,no_rekening,jenis_id,marketing_id',
            'simpanan.jenis_simpanan:id,kode,nama',
            'simpanan.marketing:id,kode,nama',
        ]);
    }

    private function transaksiSimpananRow(SetoranSimpanan $t): array
    {
        return [
            'id' => $t->id,
            'no_transaksi' => (string) $t->no_transaksi,
            'tanggal' => $t->tgl_transaksi ? Carbon::parse($t->tgl_transaksi)->toDateString() : null,
            'no_rekening' => (string) ($t->simpanan?->no_rekening ?? ''),
            'no_anggota' => (string) ($t->anggota?->no_anggota ?? ''),
            'nama_anggota' => (string) ($t->anggota?->nama ?? ''),
            'jenis_simpanan' => (string) ($t->simpanan?->jenis_simpanan?->nama ?? ''),
            'marketing' => (string) ($t->simpanan?->marketing?->nama ?? ''),
            'nominal' => (float) ($t->nominal ?? 0),
        ];
    }

    // 11. LAPORAN TRANSAKSI SIMPANAN MARKETING
    public function transaksiSimpananMarketing(Request $request)
    {
        $query = $this->transaksiSimpananQuery();
        $this->applySearch($query, $request, ['no_transaksi', 'simpanan.no_rekening', 'anggota.nama']);
        $query->whereHas('simpanan', fn ($q) => $q->when($request->filled('marketing_id'), fn ($p) => $p->where('marketing_id', $request->input('marketing_id'))));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Marketing/TransaksiSimpananMarketing', $this->indexProps(
            $request,
            $this->paginateFlat($query->orderByDesc('tgl_transaksi'), $request, fn ($t) => $this->transaksiSimpananRow($t)),
            'Laporan Transaksi Simpanan Marketing',
            ['showDate' => true]
        ));
    }

    public function cetakTransaksiSimpananMarketing(Request $request)
    {
        $query = $this->transaksiSimpananQuery();
        $this->applySearch($query, $request, ['no_transaksi', 'simpanan.no_rekening', 'anggota.nama']);
        $query->whereHas('simpanan', fn ($q) => $q->when($request->filled('marketing_id'), fn ($p) => $p->where('marketing_id', $request->input('marketing_id'))));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.marketing.transaksi-simpanan-marketing', [
            'items' => $query->orderByDesc('tgl_transaksi')->get(),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_transaksi_simpanan_marketing.pdf');
    }

    // 12. LAPORAN TRANSAKSI SIMPANAN MARKETING DETAIL
    public function transaksiSimpananMarketingDetail(Request $request)
    {
        $query = $this->transaksiSimpananQuery();
        $this->applySearch($query, $request, ['no_transaksi', 'simpanan.no_rekening', 'anggota.nama']);
        $query->whereHas('simpanan', fn ($q) => $q->when($request->filled('marketing_id'), fn ($p) => $p->where('marketing_id', $request->input('marketing_id'))));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Marketing/TransaksiSimpananMarketingDetail', $this->indexProps(
            $request,
            $this->paginateFlat($query->orderByDesc('tgl_transaksi'), $request, function ($t) {
                $row = $this->transaksiSimpananRow($t);
                $row['nominal_tarik'] = 0;
                return $row;
            }),
            'Laporan Transaksi Simpanan Marketing Detail',
            ['showDate' => true]
        ));
    }

    public function cetakTransaksiSimpananMarketingDetail(Request $request)
    {
        $query = $this->transaksiSimpananQuery();
        $this->applySearch($query, $request, ['no_transaksi', 'simpanan.no_rekening', 'anggota.nama']);
        $query->whereHas('simpanan', fn ($q) => $q->when($request->filled('marketing_id'), fn ($p) => $p->where('marketing_id', $request->input('marketing_id'))));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.marketing.transaksi-simpanan-marketing-detail', [
            'items' => $query->orderByDesc('tgl_transaksi')->get(),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_transaksi_simpanan_marketing_detail.pdf');
    }

    // 13. LAPORAN NPL MARKETING
    public function nplMarketing(Request $request)
    {
        $rows = $this->tagihanRows($request)->map(function ($r) {
            $r['tanggal_tunggakan'] = $r['sisa_pokok'] > 0 ? Carbon::parse($r['tanggal'] ?? now())->addDays(1)->toDateString() : null;
            $r['hari_tunggakan'] = $r['sisa_pokok'] > 0 ? max(0, Carbon::today()->diffInDays(Carbon::parse($r['tanggal'] ?? now()))) : 0;
            return $r;
        })->filter(fn ($r) => $r['sisa_pokok'] > 0)->values();

        return inertia('Superadmin/LaporanCS/Marketing/NplMarketing', $this->indexProps(
            $request,
            $this->paginateCollection($request, $rows),
            'Laporan NPL Marketing'
        ));
    }

    public function cetakNplMarketing(Request $request)
    {
        $rows = $this->tagihanRows($request)->map(function ($r) {
            $r['tanggal_tunggakan'] = $r['sisa_pokok'] > 0 ? Carbon::parse($r['tanggal'] ?? now())->addDays(1)->toDateString() : null;
            $r['hari_tunggakan'] = $r['sisa_pokok'] > 0 ? max(0, Carbon::today()->diffInDays(Carbon::parse($r['tanggal'] ?? now()))) : 0;
            return $r;
        })->filter(fn ($r) => $r['sisa_pokok'] > 0)->values();

        return $this->streamPdf('pdf.laporan-cs.marketing.npl-marketing', [
            'items' => $rows,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_npl_marketing.pdf');
    }

    private function pencapaianRows(Request $request, int $days)
    {
        $mulai = $request->date('mulai') ? Carbon::parse($request->date('mulai'))->startOfDay() : Carbon::today()->subDays($days - 1)->startOfDay();
        $sampai = $request->date('sampai') ? Carbon::parse($request->date('sampai'))->endOfDay() : Carbon::today()->endOfDay();

        $marketings = Marketing::query();
        $this->applySearch($marketings, $request, ['kode', 'nama']);
        $marketings->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return $marketings->get()->map(function ($m) use ($mulai, $sampai, $days) {
            $jml = AngsuranPinjaman::whereHas('pinjaman', fn ($q) => $q->where('marketing_id', $m->id))
                ->whereBetween('tgl_transaksi', [$mulai, $sampai])->count();
            $total = AngsuranPinjaman::whereHas('pinjaman', fn ($q) => $q->where('marketing_id', $m->id))
                ->whereBetween('tgl_transaksi', [$mulai, $sampai])->sum('total_angsuran');
            $target = (float) ($m->target_angsuran ?? 0);
            $persen = $target > 0 ? round(($total / $target) * 100, 2) : 0;
            return [
                'id' => $m->id,
                'kode' => (string) $m->kode,
                'nama' => (string) $m->nama,
                'jml_transaksi' => (int) $jml,
                'total_terkumpul' => round($total),
                'target' => $target,
                'persentase' => $persen,
                'periode' => $mulai->format('d/m/Y').' - '.$sampai->format('d/m/Y'),
            ];
        })->values();
    }

    // 14. PERSENTASE PENCAPAIAN ANGSURAN HARIAN
    public function pencapaianAngsuranHarian(Request $request)
    {
        return inertia('Superadmin/LaporanCS/Marketing/PencapaianAngsuranHarian', $this->indexProps(
            $request,
            $this->paginateCollection($request, $this->pencapaianRows($request, 1)),
            'Laporan Persentase Pencapaian Angsuran Harian'
        ));
    }

    public function cetakPencapaianAngsuranHarian(Request $request)
    {
        return $this->streamPdf('pdf.laporan-cs.marketing.pencapaian-angsuran-harian', [
            'items' => $this->pencapaianRows($request, 1),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_pencapaian_angsuran_harian.pdf');
    }

    // 15. PERSENTASE PENCAPAIAN ANGSURAN MINGGUAN
    public function pencapaianAngsuranMingguan(Request $request)
    {
        return inertia('Superadmin/LaporanCS/Marketing/PencapaianAngsuranMingguan', $this->indexProps(
            $request,
            $this->paginateCollection($request, $this->pencapaianRows($request, 7)),
            'Laporan Persentase Pencapaian Angsuran Mingguan'
        ));
    }

    public function cetakPencapaianAngsuranMingguan(Request $request)
    {
        return $this->streamPdf('pdf.laporan-cs.marketing.pencapaian-angsuran-mingguan', [
            'items' => $this->pencapaianRows($request, 7),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_pencapaian_angsuran_mingguan.pdf');
    }

    // 16. PERSENTASE PENCAPAIAN ANGSURAN BULANAN
    public function pencapaianAngsuranBulanan(Request $request)
    {
        return inertia('Superadmin/LaporanCS/Marketing/PencapaianAngsuranBulanan', $this->indexProps(
            $request,
            $this->paginateCollection($request, $this->pencapaianRows($request, 30)),
            'Laporan Persentase Pencapaian Angsuran Bulanan'
        ));
    }

    public function cetakPencapaianAngsuranBulanan(Request $request)
    {
        return $this->streamPdf('pdf.laporan-cs.marketing.pencapaian-angsuran-bulanan', [
            'items' => $this->pencapaianRows($request, 30),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_pencapaian_angsuran_bulanan.pdf');
    }

    // 17. REKAPITULASI PINJAMAN MARKETING
    public function rekapitulasiPinjamanMarketing(Request $request)
    {
        $query = Pinjaman::with('marketing:id,kode,nama');
        $this->applySearch($query, $request, ['no_pinjaman']);
        $this->applyMarketingKantor($query, $request);

        $rows = $query->get()->groupBy('marketing_id')->map(function ($group) {
            $m = $group->first()?->marketing;
            $plafon = (float) $group->sum('plafon');
            return [
                'id' => $group->first()->id,
                'kode' => (string) ($m->kode ?? ''),
                'nama' => (string) ($m->nama ?? 'Tanpa Marketing'),
                'jumlah_pinjaman' => $group->count(),
                'total_plafon' => $plafon,
                'rata_rata' => $group->count() ? round($plafon / $group->count()) : 0,
            ];
        })->values();

        return inertia('Superadmin/LaporanCS/Marketing/RekapitulasiPinjamanMarketing', $this->indexProps(
            $request,
            $this->paginateCollection($request, $rows),
            'Rekapitulasi Pinjaman Marketing'
        ));
    }

    public function cetakRekapitulasiPinjamanMarketing(Request $request)
    {
        $query = Pinjaman::with('marketing:id,kode,nama');
        $this->applySearch($query, $request, ['no_pinjaman']);
        $this->applyMarketingKantor($query, $request);

        $items = $query->get()->groupBy('marketing_id')->map(function ($group) {
            $m = $group->first()?->marketing;
            $plafon = (float) $group->sum('plafon');
            return [
                'kode' => (string) ($m->kode ?? ''),
                'nama' => (string) ($m->nama ?? 'Tanpa Marketing'),
                'jumlah_pinjaman' => $group->count(),
                'total_plafon' => $plafon,
                'rata_rata' => $group->count() ? round($plafon / $group->count()) : 0,
            ];
        })->values();

        return $this->streamPdf('pdf.laporan-cs.marketing.rekapitulasi-pinjaman-marketing', [
            'items' => $items,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'rekapitulasi_pinjaman_marketing.pdf');
    }

    private function paginateCollection(Request $request, $items)
    {
        $perPage = $request->integer('per_page', 10);
        $currentPage = $request->integer('page', 1);
        $all = collect($items)->values();
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $all->slice(($currentPage - 1) * $perPage, $perPage),
            $all->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
