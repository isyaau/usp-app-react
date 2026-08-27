<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\AngsuranKolektif;
use App\Models\AngsuranPinjaman;
use App\Models\Kantor;
use App\Models\Kelompok;
use App\Models\PenaltiPinjaman;
use App\Models\PencairanPinjaman;
use App\Models\Pinjaman;
use App\Models\PinjamanBiaya;
use App\Models\PinjamanJaminan;
use App\Models\PinjamanProduk;
use App\Models\PinjamanSaksi;
use App\Models\TarikanSimpanan;
use Illuminate\Http\Request;

class LaporanPinjamanController extends Controller
{
    private function applySearch($query, Request $request, array $searchColumns = ['no_pinjaman', 'anggota.nama']): void
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
        return $request->only(['search', 'kelompok_id', 'kantor_id', 'mulai', 'sampai']);
    }

    private function paginated($query, Request $request)
    {
        return $query->paginate($request->integer('per_page', 10))->withQueryString();
    }

    private function manualPaginate($items, Request $request)
    {
        $perPage = $request->integer('per_page', 10);
        $currentPage = $request->integer('page', 1);
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items->slice(($currentPage - 1) * $perPage, $perPage),
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
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

    // ==================== 1. DAFTAR PINJAMAN ====================

    public function daftarPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/DaftarPinjaman', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => PinjamanProduk::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Daftar Pinjaman',
        ]);
    }

    public function cetakDaftarPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.daftar-pinjaman', [
            'pinjaman' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'daftar_pinjaman.pdf');
    }

    // ==================== 2. DAFTAR NAMA PEMINJAM ====================

    public function daftarNamaPeminjam(Request $request)
    {
        $query = Anggota::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon,aktif,jenis_id',
            'pinjaman.jenisPinjaman:id,kode,nama',
            'kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->whereHas('pinjaman');

        $this->applySearch($query, $request, ['no_anggota', 'nama']);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Pinjaman/DaftarNamaPeminjam', [
            'data' => $this->paginated($query->orderBy('no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Daftar Nama Peminjam',
        ]);
    }

    public function cetakDaftarNamaPeminjam(Request $request)
    {
        $query = Anggota::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon,aktif,tanggal,jangka_waktu',
            'pinjaman.jenisPinjaman:id,kode,nama',
            'kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->whereHas('pinjaman');
        $this->applySearch($query, $request, ['no_anggota', 'nama']);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.daftar-nama-peminjam', [
            'anggota' => $query->orderBy('no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'daftar_nama_peminjam.pdf');
    }

    // ==================== 3. KARTU PINJAMAN ====================

    public function kartuPinjaman(Request $request)
    {
        $query = Anggota::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon,aktif',
            'kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->whereHas('pinjaman');
        $this->applySearch($query, $request, ['no_anggota', 'nama']);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Pinjaman/KartuPinjaman', [
            'data' => $this->paginated($query->orderBy('no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Kartu Pinjaman',
        ]);
    }

    public function cetakKartuPinjaman($id)
    {
        $anggota = Anggota::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon,nominal_angsuran,angsuranke,jangka_waktu,aktif,tanggal,bunga',
            'pinjaman.jenisPinjaman:id,kode,nama',
            'kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->whereHas('pinjaman')->findOrFail($id);

        return $this->streamPdf('pdf.laporan-cs.pinjaman.kartu-pinjaman', [
            'anggota' => $anggota,
        ], 'kartu_pinjaman_' . $anggota->no_anggota . '.pdf', 'A4', 'portrait');
    }

    // ==================== 4. KARTU PINJAMAN (DATA PINJAMAN) ====================

    public function kartuPinjamanData(Request $request)
    {
        $query = Anggota::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon,nominal_angsuran,angsuranke,jangka_waktu,aktif,tanggal,bunga,periode,satuan',
            'pinjaman.jenisPinjaman:id,kode,nama',
            'kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->whereHas('pinjaman');
        $this->applySearch($query, $request, ['no_anggota', 'nama']);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Pinjaman/KartuPinjamanData', [
            'data' => $this->paginated($query->orderBy('no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Kartu Pinjaman (Data Pinjaman)',
        ]);
    }

    public function cetakKartuPinjamanData($id)
    {
        $anggota = Anggota::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon,nominal_angsuran,angsuranke,jangka_waktu,aktif,tanggal,bunga,periode,satuan,pembayaran',
            'pinjaman.jenisPinjaman:id,kode,nama',
            'kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->whereHas('pinjaman')->findOrFail($id);

        return $this->streamPdf('pdf.laporan-cs.pinjaman.kartu-pinjaman-data', [
            'anggota' => $anggota,
        ], 'kartu_pinjaman_data_' . $anggota->no_anggota . '.pdf', 'A4', 'portrait');
    }

    // ==================== 5. LAPORAN ANGSURAN PINJAMAN ====================

    public function laporanAngsuranPinjaman(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanAngsuranPinjaman', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Angsuran Pinjaman',
        ]);
    }

    public function cetakLaporanAngsuranPinjaman(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-angsuran-pinjaman', [
            'transaksi' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_angsuran_pinjaman.pdf');
    }

    // ==================== 6. LAPORAN ANGSURAN PINJAMAN DETAIL ====================

    public function laporanAngsuranPinjamanDetail(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon,bunga,jangka_waktu,angsuranke',
            'pinjaman.anggota:id,no_anggota,nama,kelompok_id',
            'pinjaman.anggota.kelompok:id,kode,nama',
            'pinjaman.jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanAngsuranPinjamanDetail', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Angsuran Pinjaman Detail',
        ]);
    }

    public function cetakLaporanAngsuranPinjamanDetail(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon,bunga,jangka_waktu,angsuranke',
            'pinjaman.anggota:id,no_anggota,nama,kelompok_id',
            'pinjaman.anggota.kelompok:id,kode,nama',
            'pinjaman.jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-angsuran-pinjaman-detail', [
            'transaksi' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_angsuran_pinjaman_detail.pdf');
    }

    // ==================== 7. LAPORAN KOLEKTIBILITAS ====================

    public function laporanKolektibilitas(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'jenisPinjaman:id,kode,nama',
            'kolektabilitas',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('aktif'), fn ($q) => $q->where('aktif', $request->input('aktif')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanKolektibilitas', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => PinjamanProduk::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Kolektibilitas',
        ]);
    }

    public function cetakLaporanKolektibilitas(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'jenisPinjaman:id,kode,nama',
            'kolektabilitas',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('aktif'), fn ($q) => $q->where('aktif', $request->input('aktif')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-kolektibilitas', [
            'pinjaman' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_kolektibilitas.pdf');
    }

    // ==================== 8. LAPORAN MUTASI PINJAMAN ====================

    public function laporanMutasiPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanMutasiPinjaman', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => PinjamanProduk::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Mutasi Pinjaman',
        ]);
    }

    public function cetakLaporanMutasiPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-mutasi-pinjaman', [
            'pinjaman' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_mutasi_pinjaman.pdf');
    }

    // ==================== 9. LAPORAN NOMINATIF PINJAMAN (SISA) ====================

    public function laporanNominatifSisa(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('anggota.kelompok_id', $request->input('kelompok_id')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanNominatifSisa', [
            'data' => $this->paginated($query->orderBy('anggota.no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => PinjamanProduk::select('id', 'kode', 'nama')->get(),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Nominatif Pinjaman (Sisa)',
        ]);
    }

    public function cetakLaporanNominatifSisa(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-nominatif-sisa', [
            'pinjaman' => $query->orderBy('anggota.no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_nominatif_sisa.pdf');
    }

    // ==================== 10. LAPORAN NOMINATIF PINJAMAN (ANGSURAN) ====================

    public function laporanNominatifAngsuran(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('aktif'), fn ($q) => $q->where('aktif', $request->input('aktif')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanNominatifAngsuran', [
            'data' => $this->paginated($query->orderBy('anggota.no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => PinjamanProduk::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Nominatif Pinjaman (Angsuran)',
        ]);
    }

    public function cetakLaporanNominatifAngsuran(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('aktif'), fn ($q) => $q->where('aktif', $request->input('aktif')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-nominatif-angsuran', [
            'pinjaman' => $query->orderBy('anggota.no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_nominatif_angsuran.pdf');
    }

    // ==================== 11. LAPORAN NOMINATIF PINJAMAN (JAMINAN) ====================

    public function laporanNominatifJaminan(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanNominatifJaminan', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => PinjamanProduk::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Nominatif Pinjaman (Jaminan)',
        ]);
    }

    public function cetakLaporanNominatifJaminan(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-nominatif-jaminan', [
            'pinjaman' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_nominatif_jaminan.pdf');
    }

    // ==================== 12. LAPORAN NOMINATIF PINJAMAN (DENDA) ====================

    public function laporanNominatifDenda(Request $request)
    {
        $query = PenaltiPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanNominatifDenda', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Nominatif Pinjaman (Denda)',
        ]);
    }

    public function cetakLaporanNominatifDenda(Request $request)
    {
        $query = PenaltiPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-nominatif-denda', [
            'transaksi' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_nominatif_denda.pdf');
    }

    // ==================== 13. LAPORAN PINJAMAN CUSTOM ====================

    public function laporanPinjamanCustom(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('aktif'), fn ($q) => $q->where('aktif', $request->input('aktif')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanPinjamanCustom', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => PinjamanProduk::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Pinjaman Custom',
        ]);
    }

    public function cetakLaporanPinjamanCustom(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('aktif'), fn ($q) => $q->where('aktif', $request->input('aktif')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-pinjaman-custom', [
            'pinjaman' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_pinjaman_custom.pdf');
    }

    // ==================== 14. LAPORAN PINJAMAN JATUH TEMPO ====================

    public function laporanPinjamanJatuhTempo(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanPinjamanJatuhTempo', [
            'data' => $this->paginated($query->orderBy('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Pinjaman Jatuh Tempo',
        ]);
    }

    public function cetakLaporanPinjamanJatuhTempo(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-pinjaman-jatuh-tempo', [
            'pinjaman' => $query->orderBy('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_pinjaman_jatuh_tempo.pdf');
    }

    // ==================== 15. LAPORAN PINJAMAN LUNAS ====================

    public function laporanPinjamanLunas(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 0);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanPinjamanLunas', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Pinjaman Lunas',
        ]);
    }

    public function cetakLaporanPinjamanLunas(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 0);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-pinjaman-lunas', [
            'pinjaman' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_pinjaman_lunas.pdf');
    }

    // ==================== 16. LAPORAN PENDAPATAN BAGI HASIL PINJAMAN ====================

    public function laporanPendapatanBagiHasil(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,bunga',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanPendapatanBagiHasil', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Pendapatan Bagi Hasil Pinjaman',
        ]);
    }

    public function cetakLaporanPendapatanBagiHasil(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,bunga',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-pendapatan-bagi-hasil', [
            'transaksi' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_pendapatan_bagi_hasil.pdf');
    }

    // ==================== 17. LAPORAN PENGEMBALIAN JAMINAN ====================

    public function laporanPengembalianJaminan(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanPengembalianJaminan', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Pengembalian Jaminan',
        ]);
    }

    public function cetakLaporanPengembalianJaminan(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-pengembalian-jaminan', [
            'pinjaman' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_pengembalian_jaminan.pdf');
    }

    // ==================== 18. PROPOSAL PINJAMAN ====================

    public function proposalPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/ProposalPinjaman', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => PinjamanProduk::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Proposal Pinjaman',
        ]);
    }

    public function cetakProposalPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.proposal-pinjaman', [
            'pinjaman' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'proposal_pinjaman.pdf');
    }

    // ==================== 19. PROPOSAL PENALTI PINJAMAN ====================

    public function proposalPenaltiPinjaman(Request $request)
    {
        $query = PenaltiPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/ProposalPenaltiPinjaman', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Proposal Penalti Pinjaman',
        ]);
    }

    public function cetakProposalPenaltiPinjaman(Request $request)
    {
        $query = PenaltiPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.proposal-penalti-pinjaman', [
            'transaksi' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
        ], 'proposal_penalti_pinjaman.pdf');
    }

    // ==================== 20. LAPORAN PROPOSAL PINJAMAN MOBILE ====================

    public function laporanProposalPinjamanMobile(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanProposalPinjamanMobile', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Proposal Pinjaman Mobile',
        ]);
    }

    public function cetakLaporanProposalPinjamanMobile(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-proposal-pinjaman-mobile', [
            'pinjaman' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_proposal_pinjaman_mobile.pdf');
    }

    // ==================== 21. TABEL ANGSURAN PINJAMAN ====================

    public function tabelAngsuranPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return inertia('Superadmin/LaporanCS/Pinjaman/TabelAngsuranPinjaman', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => PinjamanProduk::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Tabel Angsuran Pinjaman',
        ]);
    }

    public function cetakTabelAngsuranPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.tabel-angsuran-pinjaman', [
            'pinjaman' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'tabel_angsuran_pinjaman.pdf');
    }

    // ==================== 22. TABEL ANGSURAN PINJAMAN (KOSONG) ====================

    public function tabelAngsuranPinjamanKosong(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return inertia('Superadmin/LaporanCS/Pinjaman/TabelAngsuranPinjamanKosong', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'jenisList' => PinjamanProduk::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Tabel Angsuran Pinjaman (Kosong)',
        ]);
    }

    public function cetakTabelAngsuranPinjamanKosong(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.tabel-angsuran-pinjaman-kosong', [
            'pinjaman' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'tabel_angsuran_pinjaman_kosong.pdf');
    }

    // ==================== 23. TRANSAKSI PINJAMAN ====================

    public function transaksiPinjaman(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/TransaksiPinjaman', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Transaksi Pinjaman',
        ]);
    }

    public function cetakTransaksiPinjaman(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.transaksi-pinjaman', [
            'transaksi' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
        ], 'transaksi_pinjaman.pdf');
    }

    // ==================== 24. LAPORAN TUNGGAKAN PINJAMAN ====================

    public function laporanTunggakanPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('anggota.kelompok_id', $request->input('kelompok_id')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanTunggakanPinjaman', [
            'data' => $this->paginated($query->orderBy('anggota.no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Tunggakan Pinjaman',
        ]);
    }

    public function cetakLaporanTunggakanPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('anggota.kelompok_id', $request->input('kelompok_id')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-tunggakan-pinjaman', [
            'pinjaman' => $query->orderBy('anggota.no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_tunggakan_pinjaman.pdf');
    }

    // ==================== 25. LAPORAN TUNGGAKAN PINJAMAN PER KOTA ====================

    public function laporanTunggakanPinjamanPerKota(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanTunggakanPinjamanPerKota', [
            'data' => $this->paginated($query->orderBy('kantor_id')->orderBy('anggota.no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Tunggakan Pinjaman Per Kota',
        ]);
    }

    public function cetakLaporanTunggakanPinjamanPerKota(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-tunggakan-pinjaman-per-kota', [
            'pinjaman' => $query->orderBy('kantor_id')->orderBy('anggota.no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_tunggakan_pinjaman_per_kota.pdf');
    }

    // ==================== 26. LAPORAN PENCAIRAN PINJAMAN ====================

    public function laporanPencairanPinjaman(Request $request)
    {
        $query = PencairanPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal_cair', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal_cair', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanPencairanPinjaman', [
            'data' => $this->paginated($query->orderByDesc('tanggal_cair'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Pencairan Pinjaman',
        ]);
    }

    public function cetakLaporanPencairanPinjaman(Request $request)
    {
        $query = PencairanPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal_cair', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal_cair', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-pencairan-pinjaman', [
            'transaksi' => $query->orderByDesc('tanggal_cair')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_pencairan_pinjaman.pdf');
    }

    // ==================== 27. LAPORAN TRANSAKSI HARIAN PINJAMAN ====================

    public function laporanTransaksiHarianPinjaman(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanTransaksiHarianPinjaman', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Transaksi Harian Pinjaman',
        ]);
    }

    public function cetakLaporanTransaksiHarianPinjaman(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-transaksi-harian-pinjaman', [
            'transaksi' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_transaksi_harian_pinjaman.pdf');
    }

    // ==================== 28. LAPORAN KONTROL ANGSURAN ====================

    public function laporanKontrolAngsuran(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon,nominal_angsuran,angsuranke,jangka_waktu',
            'pinjaman.anggota:id,no_anggota,nama,kelompok_id',
            'pinjaman.anggota.kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanKontrolAngsuran', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Kontrol Angsuran',
        ]);
    }

    public function cetakLaporanKontrolAngsuran(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon,nominal_angsuran,angsuranke,jangka_waktu',
            'pinjaman.anggota:id,no_anggota,nama,kelompok_id',
            'pinjaman.anggota.kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-kontrol-angsuran', [
            'transaksi' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_kontrol_angsuran.pdf');
    }

    // ==================== 29. REKAPITULASI PINJAMAN ====================

    public function rekapitulasiPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/RekapitulasiPinjaman', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Rekapitulasi Pinjaman',
        ]);
    }

    public function cetakRekapitulasiPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.rekapitulasi-pinjaman', [
            'pinjaman' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'rekapitulasi_pinjaman.pdf');
    }

    // ==================== 30. REKAPITULASI PINJAMAN (SEKTOR) ====================

    public function rekapitulasiPinjamanSektor(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/RekapitulasiPinjamanSektor', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Rekapitulasi Pinjaman (Sektor)',
        ]);
    }

    public function cetakRekapitulasiPinjamanSektor(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.rekapitulasi-pinjaman-sektor', [
            'pinjaman' => $query->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'rekapitulasi_pinjaman_sektor.pdf');
    }

    // ==================== 31. REKAPITULASI PENDAPATAN BAGI HASIL PINJAMAN ====================

    public function rekapitulasiPendapatanBagiHasil(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,bunga',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/RekapitulasiPendapatanBagiHasil', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Rekapitulasi Pendapatan Bagi Hasil Pinjaman',
        ]);
    }

    public function cetakRekapitulasiPendapatanBagiHasil(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,bunga',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.rekapitulasi-pendapatan-bagi-hasil', [
            'transaksi' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
        ], 'rekapitulasi_pendapatan_bagi_hasil.pdf');
    }

    // ==================== 32. LAPORAN REKAP NOMINATIF PINJAMAN (PRODUK) ====================

    public function laporanRekapNominatifProduk(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanRekapNominatifProduk', [
            'data' => $this->paginated($query->orderBy('jenis_id')->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => PinjamanProduk::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Rekap Nominatif Pinjaman (Produk)',
        ]);
    }

    public function cetakLaporanRekapNominatifProduk(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-rekap-nominatif-produk', [
            'pinjaman' => $query->orderBy('jenis_id')->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_rekap_nominatif_produk.pdf');
    }

    // ==================== 33. LAPORAN REKAP NOMINATIF PINJAMAN (MARKETING) ====================

    public function laporanRekapNominatifMarketing(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanRekapNominatifMarketing', [
            'data' => $this->paginated($query->orderBy('marketing_id')->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Rekap Nominatif Pinjaman (Marketing)',
        ]);
    }

    public function cetakLaporanRekapNominatifMarketing(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-rekap-nominatif-marketing', [
            'pinjaman' => $query->orderBy('marketing_id')->orderByDesc('tanggal')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_rekap_nominatif_marketing.pdf');
    }

    // ==================== 34. PASAL SURAT PERJANJIAN PINJAMAN ====================

    public function pasalSuratPerjanjianPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);

        return inertia('Superadmin/LaporanCS/Pinjaman/PasalSuratPerjanjianPinjaman', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'variantTitle' => 'Pasal Surat Perjanjian Pinjaman',
        ]);
    }

    public function cetakPasalSuratPerjanjianPinjaman($id)
    {
        $pinjaman = Pinjaman::with([
            'anggota:id,no_anggota,nama,alamat,tempat_lahir,tgl_lahir,jenis_identitas,no_identitas',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->findOrFail($id);

        return $this->streamPdf('pdf.laporan-cs.pinjaman.pasal-surat-perjanjian-pinjaman', [
            'pinjaman' => $pinjaman,
        ], 'pasal_surat_perjanjian_pinjaman.pdf');
    }

    // ==================== 35. SURAT PERJANJIAN PINJAMAN ====================

    public function suratPerjanjianPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);

        return inertia('Superadmin/LaporanCS/Pinjaman/SuratPerjanjianPinjaman', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'variantTitle' => 'Surat Perjanjian Pinjaman',
        ]);
    }

    public function cetakSuratPerjanjianPinjaman($id)
    {
        $pinjaman = Pinjaman::with([
            'anggota:id,no_anggota,nama,alamat,tempat_lahir,tgl_lahir,jenis_identitas,no_identitas,telepon,no_hp',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->findOrFail($id);

        return $this->streamPdf('pdf.laporan-cs.pinjaman.surat-perjanjian-pinjaman', [
            'pinjaman' => $pinjaman,
        ], 'surat_perjanjian_pinjaman.pdf');
    }

    // ==================== 36. SURAT KUASA ====================

    public function suratKuasa(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);

        return inertia('Superadmin/LaporanCS/Pinjaman/SuratKuasa', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'variantTitle' => 'Surat Kuasa',
        ]);
    }

    public function cetakSuratKuasa($id)
    {
        $pinjaman = Pinjaman::with([
            'anggota:id,no_anggota,nama,alamat,tempat_lahir,tgl_lahir,jenis_identitas,no_identitas',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->findOrFail($id);

        return $this->streamPdf('pdf.laporan-cs.pinjaman.surat-kuasa', [
            'pinjaman' => $pinjaman,
        ], 'surat_kuasa.pdf');
    }

    // ==================== 37. SURAT PERNYATAAN ====================

    public function suratPernyataan(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);

        return inertia('Superadmin/LaporanCS/Pinjaman/SuratPernyataan', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'variantTitle' => 'Surat Pernyataan',
        ]);
    }

    public function cetakSuratPernyataan($id)
    {
        $pinjaman = Pinjaman::with([
            'anggota:id,no_anggota,nama,alamat,tempat_lahir,tgl_lahir,jenis_identitas,no_identitas',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->findOrFail($id);

        return $this->streamPdf('pdf.laporan-cs.pinjaman.surat-pernyataan', [
            'pinjaman' => $pinjaman,
        ], 'surat_pernyataan.pdf');
    }

    // ==================== 38. TANDA TERIMA JAMINAN ====================

    public function tandaTerimaJaminan(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
        ]);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);

        return inertia('Superadmin/LaporanCS/Pinjaman/TandaTerimaJaminan', [
            'data' => $this->paginated($query->orderByDesc('tanggal'), $request),
            'filters' => $this->baseFilters($request),
            'variantTitle' => 'Tanda Terima Jaminan',
        ]);
    }

    public function cetakTandaTerimaJaminan($id)
    {
        $pinjaman = Pinjaman::with([
            'anggota:id,no_anggota,nama,alamat',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->findOrFail($id);

        $jaminan = PinjamanJaminan::where('pinjaman_id', $id)->get();

        return $this->streamPdf('pdf.laporan-cs.pinjaman.tanda-terima-jaminan', [
            'pinjaman' => $pinjaman,
            'jaminan' => $jaminan,
        ], 'tanda_terima_jaminan.pdf');
    }

    // ==================== 39. SIMULASI TAGIHAN PINJAMAN ====================

    public function simulasiTagihanPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return inertia('Superadmin/LaporanCS/Pinjaman/SimulasiTagihanPinjaman', [
            'data' => $this->paginated($query->orderBy('anggota.no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => PinjamanProduk::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Simulasi Tagihan Pinjaman',
        ]);
    }

    public function cetakSimulasiTagihanPinjaman(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.simulasi-tagihan-pinjaman', [
            'pinjaman' => $query->orderBy('anggota.no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'simulasi_tagihan_pinjaman.pdf');
    }

    // ==================== 40. LAPORAN ANGSURAN PER ANGGOTA ====================

    public function laporanAngsuranPerAnggota(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon',
            'pinjaman.anggota:id,no_anggota,nama,kelompok_id',
            'pinjaman.anggota.kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['pinjaman.anggota.nama', 'pinjaman.anggota.no_anggota']);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->whereHas('pinjaman.anggota', fn ($a) => $a->where('kelompok_id', $request->input('kelompok_id'))));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanAngsuranPerAnggota', [
            'data' => $this->paginated($query->orderBy('pinjaman.anggota.no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Angsuran Per Anggota',
        ]);
    }

    public function cetakLaporanAngsuranPerAnggota(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon',
            'pinjaman.anggota:id,no_anggota,nama,kelompok_id',
            'pinjaman.anggota.kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['pinjaman.anggota.nama', 'pinjaman.anggota.no_anggota']);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->whereHas('pinjaman.anggota', fn ($a) => $a->where('kelompok_id', $request->input('kelompok_id'))));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-angsuran-per-anggota', [
            'transaksi' => $query->orderBy('pinjaman.anggota.no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_angsuran_per_anggota.pdf');
    }

    // ==================== 41. LAPORAN REKAPAN PEMASUKAN DETAIL ====================

    public function laporanRekapanPemasukanDetail(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon,bunga',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanRekapanPemasukanDetail', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Rekapan Pemasukan Detail',
        ]);
    }

    public function cetakLaporanRekapanPemasukanDetail(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon,bunga',
            'pinjaman.anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'pinjaman.no_pinjaman', 'pinjaman.anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-rekapan-pemasukan-detail', [
            'transaksi' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_rekapan_pemasukan_detail.pdf');
    }

    // ==================== 42. LAPORAN JATUH TEMPO ANGSURAN ====================

    public function laporanJatuhTempoAngsuran(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanJatuhTempoAngsuran', [
            'data' => $this->paginated($query->orderBy('anggota.no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Jatuh Tempo Angsuran',
        ]);
    }

    public function cetakLaporanJatuhTempoAngsuran(Request $request)
    {
        $query = Pinjaman::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);
        $this->applySearch($query, $request, ['no_pinjaman', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-jatuh-tempo-angsuran', [
            'pinjaman' => $query->orderBy('anggota.no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_jatuh_tempo_angsuran.pdf');
    }

    // ==================== 43. LAPORAN PENILAIAN ANGGOTA YANG ANGSURAN SERING TERLAMBAT ====================

    public function laporanPenilaianAnggotaTerlambat(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon,nominal_angsuran,angsuranke,jangka_waktu',
            'pinjaman.anggota:id,no_anggota,nama,kelompok_id',
            'pinjaman.anggota.kelompok:id,kode,nama',
            'pinjaman.jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['pinjaman.anggota.nama', 'pinjaman.anggota.no_anggota']);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->whereHas('pinjaman.anggota', fn ($a) => $a->where('kelompok_id', $request->input('kelompok_id'))));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Pinjaman/LaporanPenilaianAnggotaTerlambat', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Penilaian Anggota Yang Angsuran Sering Terlambat',
        ]);
    }

    public function cetakLaporanPenilaianAnggotaTerlambat(Request $request)
    {
        $query = AngsuranPinjaman::with([
            'pinjaman:id,anggota_id,no_pinjaman,plafon,nominal_angsuran,angsuranke,jangka_waktu',
            'pinjaman.anggota:id,no_anggota,nama,kelompok_id',
            'pinjaman.anggota.kelompok:id,kode,nama',
            'pinjaman.jenisPinjaman:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['pinjaman.anggota.nama', 'pinjaman.anggota.no_anggota']);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->whereHas('pinjaman.anggota', fn ($a) => $a->where('kelompok_id', $request->input('kelompok_id'))));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.pinjaman.laporan-penilaian-anggota-terlambat', [
            'transaksi' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_penilaian_anggota_terlambat.pdf');
    }
}
