<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\Kelompok;
use App\Models\SetoranSimpanan;
use App\Models\Simpanan;
use App\Models\SimpananJenis;
use App\Models\SimpananKode;
use App\Models\TarikanSimpanan;
use App\Models\PenutupanSimpanan;
use App\Models\PemindahbukuanSimpanan;
use Illuminate\Http\Request;

class LaporanSimpananController extends Controller
{
    private function applySearch($query, Request $request, array $searchColumns = ['no_rekening', 'anggota.nama']): void
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

    private function streamPdf($view, array $data, string $filename, string $paper = 'A4', string $orientation = 'landscape')
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data)->setPaper($paper, $orientation);
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(420, 570, 'Halaman {PAGE_NUM} / {PAGE_COUNT}', null, 10, [0, 0, 0]);
        return response()->streamDownload(fn () => print($pdf->output()), $filename);
    }

    // ==================== 1. KARTU SIMPANAN BAGIAN DEPAN ====================

    public function kartuSimpananDepan(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama,alamat,tempat_lahir,tgl_lahir,jenis_kelamin,telepon,no_hp,kelurahan_id,kota_id,provinsi_id,kecamatan_id',
            'anggota.kelompok:id,kode,nama',
            'anggota.kelurahan:code,name',
            'anggota.kota:code,name',
            'anggota.provinsi:code,name',
            'anggota.kecamatan:code,name',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return inertia('Superadmin/LaporanCS/Simpanan/KartuSimpananDepan', [
            'data' => $this->paginated($query->orderBy('no_rekening'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => SimpananJenis::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Kartu Simpanan Bagian Depan',
        ]);
    }

    public function cetakKartuSimpananDepan(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama,alamat,tempat_lahir,tgl_lahir,jenis_kelamin,telepon,no_hp,kelurahan_id,kota_id,provinsi_id,kecamatan_id',
            'anggota.kelompok:id,kode,nama',
            'anggota.kelurahan:code,name',
            'anggota.kota:code,name',
            'anggota.provinsi:code,name',
            'anggota.kecamatan:code,name',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.kartu-depan', [
            'items' => $query->orderBy('no_rekening')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'kartu_simpanan_depan.pdf', 'A4', 'portrait');
    }

    // ==================== 2. KARTU SIMPANAN BAGIAN BELAKANG ====================

    public function kartuSimpananBelakang(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama,alamat,tempat_lahir,tgl_lahir,jenis_kelamin,telepon,no_hp,kelurahan_id,kota_id,provinsi_id,kecamatan_id',
            'anggota.kelompok:id,kode,nama',
            'anggota.kelurahan:code,name',
            'anggota.kota:code,name',
            'anggota.provinsi:code,name',
            'anggota.kecamatan:code,name',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return inertia('Superadmin/LaporanCS/Simpanan/KartuSimpananBelakang', [
            'data' => $this->paginated($query->orderBy('no_rekening'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => SimpananJenis::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Kartu Simpanan Bagian Belakang',
        ]);
    }

    public function cetakKartuSimpananBelakang(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama,alamat,tempat_lahir,tgl_lahir,jenis_kelamin,telepon,no_hp,kelurahan_id,kota_id,provinsi_id,kecamatan_id',
            'anggota.kelompok:id,kode,nama',
            'anggota.kelurahan:code,name',
            'anggota.kota:code,name',
            'anggota.provinsi:code,name',
            'anggota.kecamatan:code,name',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.kartu-belakang', [
            'items' => $query->orderBy('no_rekening')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'kartu_simpanan_belakang.pdf', 'A4', 'portrait');
    }

    // ==================== 3. KARTU SIMPANAN BAGIAN BELAKANG DATA ANGGOTA ====================

    public function kartuSimpananBelakangData(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama,alamat,tempat_lahir,tgl_lahir,jenis_kelamin,telepon,no_hp,kelurahan_id,kota_id,provinsi_id,kecamatan_id',
            'anggota.kelompok:id,kode,nama',
            'anggota.kelurahan:code,name',
            'anggota.kota:code,name',
            'anggota.provinsi:code,name',
            'anggota.kecamatan:code,name',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return inertia('Superadmin/LaporanCS/Simpanan/KartuSimpananBelakangData', [
            'data' => $this->paginated($query->orderBy('no_rekening'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => SimpananJenis::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Kartu Simpanan Bagian Belakang Data Anggota',
        ]);
    }

    public function cetakKartuSimpananBelakangData(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama,alamat,tempat_lahir,tgl_lahir,jenis_kelamin,telepon,no_hp,kelurahan_id,kota_id,provinsi_id,kecamatan_id',
            'anggota.kelompok:id,kode,nama',
            'anggota.kelurahan:code,name',
            'anggota.kota:code,name',
            'anggota.provinsi:code,name',
            'anggota.kecamatan:code,name',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.kartu-belakang-data', [
            'items' => $query->orderBy('no_rekening')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'kartu_simpanan_belakang_data.pdf', 'A4', 'portrait');
    }

    // ==================== 4. PASAL KARTU SIMPANAN ====================

    public function pasalKartuSimpanan(Request $request)
    {
        return inertia('Superadmin/LaporanCS/Simpanan/PasalKartuSimpanan', [
            'data' => collect(),
            'filters' => $this->baseFilters($request),
            'variantTitle' => 'Pasal Kartu Simpanan',
        ]);
    }

    public function cetakPasalKartuSimpanan(Request $request)
    {
        return $this->streamPdf('pdf.laporan-cs.simpanan.pasal-kartu', [
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'pasal_kartu_simpanan.pdf', 'A4', 'portrait');
    }

    // ==================== 5. REKENING KORAN ====================

    public function rekeningKoran(Request $request)
    {
        $query = SetoranSimpanan::with([
            'anggota:id,no_anggota,nama',
            'simpanan:id,no_rekening',
            'kodeTransaksi:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('simpanan_id'), fn ($q) => $q->where('simpanan_id', $request->input('simpanan_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Simpanan/RekeningKoran', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Rekening Koran',
        ]);
    }

    public function cetakRekeningKoran(Request $request)
    {
        $query = SetoranSimpanan::with([
            'anggota:id,no_anggota,nama',
            'simpanan:id,no_rekening',
            'kodeTransaksi:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('simpanan_id'), fn ($q) => $q->where('simpanan_id', $request->input('simpanan_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.rekening-koran', [
            'items' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'rekening_koran.pdf');
    }

    // ==================== 6. REKENING KORAN KOLEKTIF ====================

    public function rekeningKoranKolektif(Request $request)
    {
        $query = SetoranSimpanan::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'simpanan:id,no_rekening',
            'kodeTransaksi:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('anggota.kelompok_id', $request->input('kelompok_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Simpanan/RekeningKoranKolektif', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Rekening Koran Kolektif',
        ]);
    }

    public function cetakRekeningKoranKolektif(Request $request)
    {
        $query = SetoranSimpanan::with([
            'anggota:id,no_anggota,nama,kelompok_id',
            'anggota.kelompok:id,kode,nama',
            'simpanan:id,no_rekening',
            'kodeTransaksi:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('anggota.kelompok_id', $request->input('kelompok_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.rekening-koran-kolektif', [
            'items' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'rekening_koran_kolektif.pdf');
    }

    // ==================== 7. TRANSAKSI SIMPANAN ====================

    public function transaksiSimpanan(Request $request)
    {
        $query = SetoranSimpanan::with([
            'anggota:id,no_anggota,nama',
            'simpanan:id,no_rekening',
            'simpanan.jenis_simpanan:id,kode,nama',
            'kodeTransaksi:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Simpanan/TransaksiSimpanan', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'simpananList' => Simpanan::with('anggota:id,no_anggota,nama')
                ->select('id', 'no_rekening', 'anggota_id')
                ->orderBy('no_rekening')->get(),
            'variantTitle' => 'Transaksi Simpanan',
        ]);
    }

    public function cetakTransaksiSimpanan(Request $request)
    {
        $query = SetoranSimpanan::with([
            'anggota:id,no_anggota,nama',
            'simpanan:id,no_rekening',
            'simpanan.jenis_simpanan:id,kode,nama',
            'kodeTransaksi:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.transaksi-simpanan', [
            'items' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'transaksi_simpanan.pdf');
    }

    public function cetakBukuTabungan(Request $request)
    {
        $request->validate([
            'simpanan_id' => 'required|exists:simpanan,id',
        ], ['simpanan_id.required' => 'No rekening harus dipilih.']);

        $simpanan = Simpanan::with([
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->findOrFail($request->input('simpanan_id'));

        $query = SetoranSimpanan::with([
            'kodeTransaksi:id,kode,nama,setoran,tarikan',
            'user:id,username,nama',
        ])->where('simpanan_id', $simpanan->id);
        $allItems = $query->orderBy('tgl_transaksi')->orderBy('id')->get();

        // Start from transaction number (skip first N-1 transactions)
        $startFrom = max(1, (int) $request->input('start_from', 1));

        // Total lines per passbook page
        $totalLines = max(1, (int) $request->input('total_lines', 30));

        // Skip lines at top (for passbook already partially printed on this page)
        $skipLines = max(0, (int) $request->input('skip_lines', 0));

        // Paper dimensions
        $paperWidth = (float) $request->input('paper_width', 140);
        $paperHeight = (float) $request->input('paper_height', 200);
        $marginTop = (float) $request->input('margin_top', 15);
        $marginLeft = (float) $request->input('margin_left', 5);
        $marginRight = (float) $request->input('margin_right', 5);
        $fontSize = (int) $request->input('font_size', 8);
        $lineHeight = (float) $request->input('line_height', 4);

        // Column widths in mm (saldo takes the remaining width)
        $colNo = (float) $request->input('col_no', 8);
        $colTanggal = (float) $request->input('col_tanggal', 22);
        $colKode = (float) $request->input('col_kode', 12);
        $colDebet = (float) $request->input('col_debet', 22);
        $colKredit = (float) $request->input('col_kredit', 22);
        $colOpt = (float) $request->input('col_opt', 14);

        // Calculate running balance from all transactions before startFrom
        $preBalance = 0;
        foreach ($allItems->slice(0, $startFrom - 1) as $prevItem) {
            $nominal = (float) ($prevItem->nominal ?? 0);
            if ($prevItem->kodeTransaksi->setoran ?? false) {
                $preBalance += $nominal;
            } else {
                $preBalance -= $nominal;
            }
        }

        // Slice items starting from startFrom
        $items = $allItems->slice($startFrom - 1)->values();

        $data = [
            'items' => $items,
            'skipLines' => $skipLines,
            'totalLines' => $totalLines,
            'preBalance' => $preBalance,
            'paperWidth' => $paperWidth,
            'paperHeight' => $paperHeight,
            'marginTop' => $marginTop,
            'marginLeft' => $marginLeft,
            'marginRight' => $marginRight,
            'fontSize' => $fontSize,
            'lineHeight' => $lineHeight,
            'startFrom' => $startFrom,
            'colNo' => $colNo,
            'colTanggal' => $colTanggal,
            'colKode' => $colKode,
            'colDebet' => $colDebet,
            'colKredit' => $colKredit,
            'colOpt' => $colOpt,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.laporan-cs.simpanan.buku-tabungan', $data)
            ->setPaper([0, 0, $paperWidth * 2.83465, $paperHeight * 2.83465], 'portrait')
            ->setOption('isRemoteEnabled', true);

        return response()->stream(fn () => print($pdf->output()), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="buku_tabungan.pdf"',
        ]);
    }

    public function bukuTabunganData(Request $request)
    {
        $request->validate([
            'simpanan_id' => 'required|exists:simpanan,id',
        ]);

        $items = SetoranSimpanan::with([
            'kodeTransaksi:id,kode,nama,setoran,tarikan',
            'user:id,username',
        ])->where('simpanan_id', $request->input('simpanan_id'))
            ->orderBy('tgl_transaksi')->orderBy('id')->get()
            ->map(fn ($item) => [
                'no_transaksi' => $item->no_transaksi,
                'tgl_transaksi' => $item->tgl_transaksi,
                'kode' => $item->kodeTransaksi->kode ?? '—',
                'setoran' => (bool) ($item->kodeTransaksi->setoran ?? false),
                'nominal' => (float) ($item->nominal ?? 0),
                'keterangan' => $item->keterangan,
                'opt' => $item->user->username ?? '—',
            ]);

        return response()->json([
            'status' => 'ok',
            'items' => $items,
        ]);
    }

    // ==================== 8. DAFTAR SIMPANAN ====================

    public function daftarSimpanan(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'marketing:id,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return inertia('Superadmin/LaporanCS/Simpanan/DaftarSimpanan', [
            'data' => $this->paginated($query->orderBy('no_rekening'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => SimpananJenis::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Daftar Simpanan',
        ]);
    }

    public function cetakDaftarSimpanan(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'marketing:id,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.daftar-simpanan', [
            'items' => $query->orderBy('no_rekening')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'daftar_simpanan.pdf');
    }

    // ==================== 9. MUTASI SIMPANAN ====================

    public function mutasiSimpanan(Request $request)
    {
        $query = SetoranSimpanan::with([
            'anggota:id,no_anggota,nama',
            'simpanan:id,no_rekening',
            'simpanan.jenis_simpanan:id,kode,nama',
            'kodeTransaksi:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'anggota.nama', 'simpanan.no_rekening']);
        $query->when($request->filled('simpanan_id'), fn ($q) => $q->where('simpanan_id', $request->input('simpanan_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Simpanan/MutasiSimpanan', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Mutasi Simpanan',
        ]);
    }

    public function cetakMutasiSimpanan(Request $request)
    {
        $query = SetoranSimpanan::with([
            'anggota:id,no_anggota,nama',
            'simpanan:id,no_rekening',
            'simpanan.jenis_simpanan:id,kode,nama',
            'kodeTransaksi:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'anggota.nama', 'simpanan.no_rekening']);
        $query->when($request->filled('simpanan_id'), fn ($q) => $q->where('simpanan_id', $request->input('simpanan_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.mutasi-simpanan', [
            'items' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'mutasi_simpanan.pdf');
    }

    // ==================== 10. MUTASI HARIAN SIMPANAN ====================

    public function mutasiHarianSimpanan(Request $request)
    {
        $query = SetoranSimpanan::with([
            'kantor:id,kode,nama_kantor',
        ]);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        $items = $query->orderByDesc('tgl_transaksi')->get()
            ->groupBy(fn ($t) => $t->tgl_transaksi ? date('Y-m-d', strtotime($t->tgl_transaksi)) : 'tanpa tanggal')
            ->map(fn ($group, $date) => [
                'tanggal' => $date,
                'total' => $group->sum('nominal'),
                'jumlah_transaksi' => $group->count(),
                'items' => $group,
            ])->values();

        $perPage = $request->integer('per_page', 10);
        $currentPage = $request->integer('page', 1);
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items->slice(($currentPage - 1) * $perPage, $perPage),
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return inertia('Superadmin/LaporanCS/Simpanan/MutasiHarianSimpanan', [
            'data' => $paginated,
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Mutasi Harian Simpanan',
        ]);
    }

    public function cetakMutasiHarianSimpanan(Request $request)
    {
        $query = SetoranSimpanan::with([
            'anggota:id,no_anggota,nama',
            'simpanan:id,no_rekening',
            'kodeTransaksi:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.mutasi-harian-simpanan', [
            'items' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'mutasi_harian_simpanan.pdf');
    }

    // ==================== 11. LAPORAN BAGI HASIL SIMPANAN ====================

    public function bagiHasilSimpanan(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama,bunga',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Simpanan/BagiHasilSimpanan', [
            'data' => $this->paginated($query->orderBy('no_rekening'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => SimpananJenis::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Bagi Hasil Simpanan',
        ]);
    }

    public function cetakBagiHasilSimpanan(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama,bunga',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.bagi-hasil-simpanan', [
            'items' => $query->orderBy('no_rekening')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'bagi_hasil_simpanan.pdf');
    }

    // ==================== 12. LAPORAN BAGI HASIL SIMPANAN 2 ====================

    public function bagiHasilSimpanan2(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama,bunga',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Simpanan/BagiHasilSimpanan2', [
            'data' => $this->paginated($query->orderBy('no_rekening'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => SimpananJenis::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Bagi Hasil Simpanan 2',
        ]);
    }

    public function cetakBagiHasilSimpanan2(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama,bunga',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.bagi-hasil-simpanan-2', [
            'items' => $query->orderBy('no_rekening')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'bagi_hasil_simpanan_2.pdf');
    }

    // ==================== 13. LAPORAN NOMINATIF SIMPANAN ====================

    public function nominatifSimpanan(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return inertia('Superadmin/LaporanCS/Simpanan/NominatifSimpanan', [
            'data' => $this->paginated($query->orderBy('anggota.no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => SimpananJenis::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Nominatif Simpanan',
        ]);
    }

    public function cetakNominatifSimpanan(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.nominatif-simpanan', [
            'items' => $query->orderBy('anggota.no_anggota')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'nominatif_simpanan.pdf');
    }

    // ==================== 14. LAPORAN NOMINATIF SIMPANAN DETAIL ====================

    public function nominatifSimpananDetail(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return inertia('Superadmin/LaporanCS/Simpanan/NominatifSimpananDetail', [
            'data' => $this->paginated($query->orderBy('anggota.no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => SimpananJenis::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Nominatif Simpanan Detail',
        ]);
    }

    public function cetakNominatifSimpananDetail(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.nominatif-simpanan-detail', [
            'items' => $query->orderBy('anggota.no_anggota')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'nominatif_simpanan_detail.pdf');
    }

    // ==================== 15. LAPORAN SALDO SIMPANAN ====================

    public function saldoSimpanan(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return inertia('Superadmin/LaporanCS/Simpanan/SaldoSimpanan', [
            'data' => $this->paginated($query->orderBy('no_rekening'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => SimpananJenis::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Saldo Simpanan',
        ]);
    }

    public function cetakSaldoSimpanan(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.saldo-simpanan', [
            'items' => $query->orderBy('no_rekening')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'saldo_simpanan.pdf');
    }

    // ==================== 16. LAPORAN SIMPANAN BARU ====================

    public function simpananBaru(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Simpanan/SimpananBaru', [
            'data' => $this->paginated($query->orderByDesc('created_at'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => SimpananJenis::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Simpanan Baru',
        ]);
    }

    public function cetakSimpananBaru(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.simpanan-baru', [
            'items' => $query->orderByDesc('created_at')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'simpanan_baru.pdf');
    }

    // ==================== 17. LAPORAN PENUTUPAN SIMPANAN ====================

    public function penutupanSimpanan(Request $request)
    {
        $query = PenutupanSimpanan::with([
            'anggota:id,no_anggota,nama',
            'simpanan:id,no_rekening',
            'simpanan.jenis_simpanan:id,kode,nama',
            'kodeTransaksi:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Simpanan/PenutupanSimpanan', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Penutupan Simpanan',
        ]);
    }

    public function cetakPenutupanSimpanan(Request $request)
    {
        $query = PenutupanSimpanan::with([
            'anggota:id,no_anggota,nama',
            'simpanan:id,no_rekening',
            'simpanan.jenis_simpanan:id,kode,nama',
            'kodeTransaksi:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.penutupan-simpanan', [
            'items' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'penutupan_simpanan.pdf');
    }

    // ==================== 18. LAPORAN TUNGGAKAN SETORAN SIMPANAN WAJIB ====================

    public function tunggakanSetoranWajib(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->whereHas('jenis_simpanan', fn ($q) => $q->where('setor_id', '!=', null));

        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Simpanan/TunggakanSetoranWajib', [
            'data' => $this->paginated($query->orderBy('no_rekening'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Tunggakan Setoran Simpanan Wajib',
        ]);
    }

    public function cetakTunggakanSetoranWajib(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->whereHas('jenis_simpanan', fn ($q) => $q->where('setor_id', '!=', null));

        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.tunggakan-setoran-wajib', [
            'items' => $query->orderBy('no_rekening')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'tunggakan_setoran_wajib.pdf');
    }

    // ==================== 19. LAPORAN SIMPANAN TIDAK AKTIF ====================

    public function tidakAktif(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 0);

        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return inertia('Superadmin/LaporanCS/Simpanan/SimpananTidakAktif', [
            'data' => $this->paginated($query->orderBy('no_rekening'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => SimpananJenis::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Simpanan Tidak Aktif',
        ]);
    }

    public function cetakTidakAktif(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 0);

        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.tidak-aktif', [
            'items' => $query->orderBy('no_rekening')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'simpanan_tidak_aktif.pdf');
    }

    // ==================== 20. LAPORAN SIMPANAN JATUH TEMPO ====================

    public function simpananJatuhTempo(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);

        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Simpanan/SimpananJatuhTempo', [
            'data' => $this->paginated($query->orderBy('no_rekening'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => SimpananJenis::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Laporan Simpanan Jatuh Tempo',
        ]);
    }

    public function cetakSimpananJatuhTempo(Request $request)
    {
        $query = Simpanan::with([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ])->where('aktif', 1);

        $this->applySearch($query, $request, ['no_rekening', 'anggota.nama', 'anggota.no_anggota']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.jatuh-tempo-simpanan', [
            'items' => $query->orderBy('no_rekening')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'simpanan_jatuh_tempo.pdf');
    }

    // ==================== 21. REKAPITULASI PRODUK SIMPANAN ====================

    public function rekapitulasiProduk(Request $request)
    {
        $query = SimpananJenis::withCount('simpanan');
        $query->when($request->filled('kantor_id'), function ($q) use ($request) {
            $q->whereHas('simpanan', fn ($sq) => $sq->where('kantor_id', $request->input('kantor_id')));
        });

        return inertia('Superadmin/LaporanCS/Simpanan/RekapitulasiProdukSimpanan', [
            'data' => $this->paginated($query->orderBy('kode'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Rekapitulasi Produk Simpanan',
        ]);
    }

    public function cetakRekapitulasiProduk(Request $request)
    {
        $query = SimpananJenis::withCount('simpanan');
        $query->when($request->filled('kantor_id'), function ($q) use ($request) {
            $q->whereHas('simpanan', fn ($sq) => $sq->where('kantor_id', $request->input('kantor_id')));
        });

        return $this->streamPdf('pdf.laporan-cs.simpanan.rekapitulasi-produk', [
            'items' => $query->orderBy('kode')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'rekapitulasi_produk_simpanan.pdf');
    }

    // ==================== 22. REKAPITULASI SIMPANAN GRAFIK ====================

    public function rekapitulasiGrafik(Request $request)
    {
        $query = SetoranSimpanan::with([
            'simpanan:id,jenis_id',
            'simpanan.jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        $items = $query->orderByDesc('tgl_transaksi')->get();

        return inertia('Superadmin/LaporanCS/Simpanan/RekapitulasiSimpananGrafik', [
            'data' => $items,
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Rekapitulasi Simpanan Grafik',
        ]);
    }

    public function cetakRekapitulasiGrafik(Request $request)
    {
        $query = SetoranSimpanan::with([
            'simpanan:id,jenis_id',
            'simpanan.jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.rekapitulasi-grafik', [
            'items' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'rekapitulasi_simpanan_grafik.pdf');
    }

    // ==================== 23. REKAPITULASI PENGELUARAN BAGI HASIL SIMPANAN ====================

    public function rekapitulasiBagiHasil(Request $request)
    {
        $query = SetoranSimpanan::with([
            'anggota:id,no_anggota,nama',
            'simpanan:id,no_rekening',
            'simpanan.jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('simpanan.jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Simpanan/RekapitulasiBagiHasilSimpanan', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'jenisList' => SimpananJenis::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Rekapitulasi Pengeluaran Bagi Hasil Simpanan',
        ]);
    }

    public function cetakRekapitulasiBagiHasil(Request $request)
    {
        $query = SetoranSimpanan::with([
            'anggota:id,no_anggota,nama',
            'simpanan:id,no_rekening',
            'simpanan.jenis_simpanan:id,kode,nama',
            'kantor:id,kode,nama_kantor',
        ]);
        $this->applySearch($query, $request, ['no_transaksi', 'anggota.nama']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('jenis_id'), fn ($q) => $q->where('simpanan.jenis_id', $request->input('jenis_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.simpanan.rekapitulasi-bagi-hasil', [
            'items' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ], 'rekapitulasi_pengeluaran_bagi_hasil_simpanan.pdf');
    }
}
