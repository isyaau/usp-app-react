<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Deposito;
use App\Models\DepositoJenis;
use App\Models\Kantor;
use App\Models\PencairanSimpananBerjangka;
use App\Models\PenaltiSimpananBerjangka;
use App\Models\SetoranSimpananBerjangka;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Laporan Simpanan Berjangka (tabel deposito) untuk frontend Inertia (React + TypeScript).
 * Mengikuti pola LaporanSimpananController: index + cetak, data diratakan (flat string) agar
 * tidak memicu error "Objects are not valid as a React child".
 */
class LaporanSimpananBerjangkaController extends Controller
{
    private function applySearch($query, Request $request, array $searchColumns = ['no_deposito', 'anggota.nama']): void
    {
        if ($request->filled('search')) {
            $term = $request->string('search');
            $query->where(fn ($q) => collect($searchColumns)->each(
                fn ($col) => $q->orWhere($col, 'LIKE', "%{$term}%")
            ));
        }
    }

    private function baseFilters(Request $request): array
    {
        return $request->only(['search', 'kantor_id', 'mulai', 'sampai']);
    }

    private function paginateFlat($query, Request $request, callable $row)
    {
        $pag = $query->paginate($request->integer('per_page', 10))->withQueryString();
        $pag->setCollection($pag->getCollection()->map($row));
        return $pag;
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

    private function depositoBase(Deposito $d): array
    {
        return [
            'id' => $d->id,
            'no_deposito' => (string) $d->no_deposito,
            'tanggal' => $d->tanggal ? Carbon::parse($d->tanggal)->toDateString() : null,
            'no_anggota' => (string) ($d->anggota?->no_anggota ?? ''),
            'nama_anggota' => (string) ($d->anggota?->nama ?? ''),
            'produk' => (string) ($d->produk?->nama ?? ''),
            'kode_produk' => (string) ($d->produk?->kode ?? ''),
            'jangka_waktu' => (string) ($d->jangka_waktu ?? ''),
            'bunga' => (string) ($d->bunga ?? ''),
            'nominal' => (float) ($d->nominal ?? 0),
            'kantor' => (string) ($d->kantor?->nama_kantor ?? ''),
            'marketing' => (string) ($d->marketing?->nama ?? ''),
        ];
    }

    private function depositoQuery()
    {
        return Deposito::with([
            'anggota:id,no_anggota,nama',
            'produk:id,kode,nama,jangka_waktu,bunga',
            'marketing:id,nama',
            'kantor:id,nama_kantor',
        ]);
    }

    // 1. DAFTAR SIMPANAN BERJANGKA
    public function daftarBerjangka(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Berjangka/DaftarBerjangka', [
            'data' => $this->paginateFlat($query->orderByDesc('tanggal'), $request, fn ($d) => $this->depositoBase($d)),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Daftar Simpanan Berjangka',
        ]);
    }

    public function cetakDaftarBerjangka(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return $this->streamPdf('pdf.laporan-cs.berjangka.daftar-berjangka', [
            'items' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'daftar_simpanan_berjangka.pdf');
    }

    // 2. BILYET SIMPANAN BERJANGKA
    public function bilyetBerjangka(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Berjangka/BilyetBerjangka', [
            'data' => $this->paginateFlat($query->orderByDesc('tanggal'), $request, fn ($d) => $this->depositoBase($d)),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Bilyet Simpanan Berjangka',
        ]);
    }

    public function cetakBilyetBerjangka(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return $this->streamPdf('pdf.laporan-cs.berjangka.bilyet-berjangka', [
            'items' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'bilyet_simpanan_berjangka.pdf', 'A4', 'portrait');
    }

    // 3. KARTU SIMPANAN BERJANGKA
    public function kartuBerjangka(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Berjangka/KartuBerjangka', [
            'data' => $this->paginateFlat($query->orderByDesc('tanggal'), $request, fn ($d) => $this->depositoBase($d)),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Kartu Simpanan Berjangka',
        ]);
    }

    public function cetakKartuBerjangka(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return $this->streamPdf('pdf.laporan-cs.berjangka.kartu-berjangka', [
            'items' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'kartu_simpanan_berjangka.pdf');
    }

    // 4. KONFIRMASI PERUBAHAN BAGI HASIL SIMPANAN BERJANGKA
    public function konfirmasiPerubahanBagiHasil(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Berjangka/KonfirmasiPerubahanBagiHasil', [
            'data' => $this->paginateFlat($query->orderByDesc('tanggal'), $request, fn ($d) => $this->depositoBase($d)),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Konfirmasi Perubahan Bagi Hasil Simpanan Berjangka',
        ]);
    }

    public function cetakKonfirmasiPerubahanBagiHasil(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return $this->streamPdf('pdf.laporan-cs.berjangka.konfirmasi-perubahan-bagi-hasil', [
            'items' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'konfirmasi_perubahan_bagi_hasil_simpanan_berjangka.pdf', 'A4', 'portrait');
    }

    // 5. LAPORAN SIMPANAN BERJANGKA BARU
    public function simpananBerjangkaBaru(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Berjangka/SimpananBerjangkaBaru', [
            'data' => $this->paginateFlat($query->orderByDesc('tanggal'), $request, fn ($d) => $this->depositoBase($d)),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Simpanan Berjangka Baru',
        ]);
    }

    public function cetakSimpananBerjangkaBaru(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.berjangka.simpanan-berjangka-baru', [
            'items' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_simpanan_berjangka_baru.pdf');
    }

    // 6. LAPORAN PENCAIRAN SIMPANAN BERJANGKA
    public function pencairanBerjangka(Request $request)
    {
        $query = PencairanSimpananBerjangka::with([
            'anggota:id,no_anggota,nama',
            'deposito:id,no_deposito,tanggal',
            'deposito.produk:id,kode,nama',
            'kantor:id,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Berjangka/PencairanBerjangka', [
            'data' => $this->paginateFlat($query->orderByDesc('tgl_transaksi'), $request, function ($t) {
                return [
                    'id' => $t->id,
                    'no_transaksi' => (string) $t->no_transaksi,
                    'tanggal' => $t->tgl_transaksi ? $t->tgl_transaksi->toDateString() : null,
                    'no_deposito' => (string) ($t->deposito?->no_deposito ?? ''),
                    'no_anggota' => (string) ($t->anggota?->no_anggota ?? ''),
                    'nama_anggota' => (string) ($t->anggota?->nama ?? ''),
                    'produk' => (string) ($t->deposito?->produk?->nama ?? ''),
                    'nominal_pokok' => (float) ($t->nominal_pokok ?? 0),
                    'nominal_bunga' => (float) ($t->nominal_bunga ?? 0),
                    'nominal_pajak' => (float) ($t->nominal_pajak ?? 0),
                    'nominal_penalti' => (float) ($t->nominal_penalti ?? 0),
                    'nominal_diterima' => (float) ($t->nominal_diterima ?? 0),
                    'kantor' => (string) ($t->kantor?->nama_kantor ?? ''),
                ];
            }),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Pencairan Simpanan Berjangka',
        ]);
    }

    public function cetakPencairanBerjangka(Request $request)
    {
        $query = PencairanSimpananBerjangka::with([
            'anggota:id,no_anggota,nama',
            'deposito:id,no_deposito,tanggal',
            'deposito.produk:id,kode,nama',
            'kantor:id,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.berjangka.pencairan-berjangka', [
            'items' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_pencairan_simpanan_berjangka.pdf');
    }

    // 7. LAPORAN BAGI HASIL SIMPANAN BERJANGKA
    public function bagiHasilBerjangka(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Berjangka/BagiHasilBerjangka', [
            'data' => $this->paginateFlat($query->orderByDesc('tanggal'), $request, function ($d) {
                $row = $this->depositoBase($d);
                $rate = (float) str_replace([',', '%'], ['.', ''], (string) ($d->bunga ?? 0));
                $row['nominal_bunga'] = round($row['nominal'] * $rate / 100);
                return $row;
            }),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Bagi Hasil Simpanan Berjangka',
        ]);
    }

    public function cetakBagiHasilBerjangka(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.berjangka.bagi-hasil-berjangka', [
            'items' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_bagi_hasil_simpanan_berjangka.pdf');
    }

    // 8. LAPORAN BAGI HASIL SIMPANAN BERJANGKA 2
    public function bagiHasilBerjangka2(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Berjangka/BagiHasilBerjangka2', [
            'data' => $this->paginateFlat($query->orderByDesc('tanggal'), $request, function ($d) {
                $row = $this->depositoBase($d);
                $rate = (float) str_replace([',', '%'], ['.', ''], (string) ($d->bunga ?? 0));
                $row['nominal_bunga'] = round($row['nominal'] * $rate / 100);
                return $row;
            }),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Bagi Hasil Simpanan Berjangka 2',
        ]);
    }

    public function cetakBagiHasilBerjangka2(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.berjangka.bagi-hasil-berjangka-2', [
            'items' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_bagi_hasil_simpanan_berjangka_2.pdf');
    }

    // 9. LAPORAN POSTING BAGI HASIL SIMPANAN BERJANGKA
    public function postingBagiHasil(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Berjangka/PostingBagiHasil', [
            'data' => $this->paginateFlat($query->orderByDesc('tanggal'), $request, function ($d) {
                $row = $this->depositoBase($d);
                $row['status_bunga'] = (int) ($d->bunga_accrual ?? 0);
                return $row;
            }),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Posting Bagi Hasil Simpanan Berjangka',
        ]);
    }

    public function cetakPostingBagiHasil(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return $this->streamPdf('pdf.laporan-cs.berjangka.posting-bagi-hasil', [
            'items' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_posting_bagi_hasil_simpanan_berjangka.pdf');
    }

    // 10. LAPORAN NOMINATIF SIMPANAN BERJANGKA
    public function nominatifBerjangka(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Berjangka/NominatifBerjangka', [
            'data' => $this->paginateFlat($query->orderBy('anggota.no_anggota'), $request, fn ($d) => $this->depositoBase($d)),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Nominatif Simpanan Berjangka',
        ]);
    }

    public function cetakNominatifBerjangka(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return $this->streamPdf('pdf.laporan-cs.berjangka.nominatif-berjangka', [
            'items' => $query->orderBy('anggota.no_anggota')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_nominatif_simpanan_berjangka.pdf');
    }

    // 11. LAPORAN SIMPANAN BERJANGKA JATUH TEMPO
    public function jatuhTempoBerjangka(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        $items = $query->orderByDesc('tanggal')->get()->map(function (Deposito $d) {
            $row = $this->depositoBase($d);
            $tgl = Carbon::parse($d->tanggal ?? now());
            $months = max(0, (int) preg_replace('/[^0-9]/', '', (string) $d->jangka_waktu));
            $row['tanggal_jatuh_tempo'] = $tgl->addMonths($months ?: 1)->toDateString();
            return $row;
        });

        $perPage = $request->integer('per_page', 10);
        $currentPage = $request->integer('page', 1);
        $all = collect($items)->values();
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $all->slice(($currentPage - 1) * $perPage, $perPage),
            $all->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return inertia('Superadmin/LaporanCS/Berjangka/JatuhTempoBerjangka', [
            'data' => $paginated,
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Simpanan Berjangka Jatuh Tempo',
        ]);
    }

    public function cetakJatuhTempoBerjangka(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        $items = $query->orderByDesc('tanggal')->get()->map(function (Deposito $d) {
            $row = $this->depositoBase($d);
            $tgl = Carbon::parse($d->tanggal ?? now());
            $months = max(0, (int) preg_replace('/[^0-9]/', '', (string) $d->jangka_waktu));
            $row['tanggal_jatuh_tempo'] = $tgl->addMonths($months ?: 1)->toDateString();
            return $row;
        });

        return $this->streamPdf('pdf.laporan-cs.berjangka.jatuh-tempo-berjangka', [
            'items' => $items->values(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'laporan_simpanan_berjangka_jatuh_tempo.pdf');
    }

    // 12. REKAPITULASI PENGELUARAN BAGI HASIL SIMPANAN BERJANGKA
    public function rekapitulasiBagiHasil(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        $items = $query->get()->groupBy(function (Deposito $d) {
            return ($d->produk?->nama ?? 'Lainnya').'|'.($d->kantor?->nama_kantor ?? '');
        })->map(function ($group) {
            $first = $group->first();
            $nominal = (float) $group->sum('nominal');
            $rate = (float) str_replace([',', '%'], ['.', ''], (string) ($first->bunga ?? 0));
            return [
                'id' => $first->id,
                'produk' => (string) ($first->produk?->nama ?? 'Lainnya'),
                'kantor' => (string) ($first->kantor?->nama_kantor ?? ''),
                'jumlah' => $group->count(),
                'total_nominal' => $nominal,
                'total_bagi_hasil' => round($nominal * $rate / 100),
            ];
        })->values();

        $perPage = $request->integer('per_page', 10);
        $currentPage = $request->integer('page', 1);
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items->slice(($currentPage - 1) * $perPage, $perPage),
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return inertia('Superadmin/LaporanCS/Berjangka/RekapitulasiBagiHasil', [
            'data' => $paginated,
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Rekapitulasi Pengeluaran Bagi Hasil Simpanan Berjangka',
        ]);
    }

    public function cetakRekapitulasiBagiHasil(Request $request)
    {
        $query = $this->depositoQuery();
        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        $items = $query->get()->groupBy(function (Deposito $d) {
            return ($d->produk?->nama ?? 'Lainnya').'|'.($d->kantor?->nama_kantor ?? '');
        })->map(function ($group) {
            $first = $group->first();
            $nominal = (float) $group->sum('nominal');
            $rate = (float) str_replace([',', '%'], ['.', ''], (string) ($first->bunga ?? 0));
            return [
                'id' => $first->id,
                'produk' => (string) ($first->produk?->nama ?? 'Lainnya'),
                'kantor' => (string) ($first->kantor?->nama_kantor ?? ''),
                'jumlah' => $group->count(),
                'total_nominal' => $nominal,
                'total_bagi_hasil' => round($nominal * $rate / 100),
            ];
        })->values();

        return $this->streamPdf('pdf.laporan-cs.berjangka.rekapitulasi-bagi-hasil', [
            'items' => $items,
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'rekapitulasi_pengeluaran_bagi_hasil_simpanan_berjangka.pdf');
    }
}
