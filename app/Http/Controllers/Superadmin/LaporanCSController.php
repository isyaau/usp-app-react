<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\Kelompok;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\SimpananJenis;
use App\Models\TarikanSimpanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanCSController extends Controller
{
    private function applySearch($query, Request $request, array $searchColumns = ['nama', 'no_anggota']): void
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

    // ==================== INDEX METHODS ====================

    public function daftarAnggota(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('status', 1);

        $this->applySearch($query, $request);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Anggota/DaftarAnggota', [
            'data' => $this->paginated($query->orderBy('no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Daftar Anggota',
        ]);
    }

    public function daftarNonAnggota(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('status', 0);

        $this->applySearch($query, $request);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Anggota/DaftarNonAnggota', [
            'data' => $this->paginated($query->orderBy('no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Daftar Non Anggota',
        ]);
    }

    public function daftarPengurus(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('pengurus', 1);

        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Anggota/DaftarPengurus', [
            'data' => $this->paginated($query->orderBy('no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Daftar Pengurus',
        ]);
    }

    public function daftarPengawas(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('pengawas', 1);

        $this->applySearch($query, $request);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Anggota/DaftarPengawas', [
            'data' => $this->paginated($query->orderBy('no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Daftar Pengawas',
        ]);
    }

    public function anggotaPerKelompok(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('status', 1);

        $this->applySearch($query, $request);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));

        return inertia('Superadmin/LaporanCS/Anggota/AnggotaPerKelompok', [
            'data' => $this->paginated($query->orderBy('kelompok_id')->orderBy('no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Anggota per Kelompok',
        ]);
    }

    public function kartuAnggota(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('status', 1);

        $this->applySearch($query, $request);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return inertia('Superadmin/LaporanCS/Anggota/KartuAnggota', [
            'data' => $this->paginated($query->orderBy('no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Kartu Anggota',
        ]);
    }

    public function laporanAnggota(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('status', 1);

        $this->applySearch($query, $request);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Anggota/LaporanAnggota', [
            'data' => $this->paginated($query->orderBy('no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Laporan Anggota',
        ]);
    }

    public function penarikanAnggota(Request $request)
    {
        $query = TarikanSimpanan::with([
            'anggota:id,no_anggota,nama',
            'kantor:id,kode,nama_kantor',
        ]);

        $this->applySearch($query, $request, ['anggota.no_anggota', 'anggota.nama', 'no_transaksi']);
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return inertia('Superadmin/LaporanCS/Anggota/PenarikanAnggota', [
            'data' => $this->paginated($query->orderByDesc('tgl_transaksi'), $request),
            'filters' => $this->baseFilters($request),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Penarikan Anggota',
        ]);
    }

    public function sisaPenarikanDana(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('status', 1)
            ->whereHas('simpanan');

        $this->applySearch($query, $request);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        $anggotas = $query->orderBy('no_anggota')->get()->map(function ($a) {
            $totalSetor = $a->simpanan->sum(fn ($s) => (float) ($s->nominal_setor ?? 0));
            $totalTarik = TarikanSimpanan::where('anggota_id', $a->id)->where('status', 'posted')->sum('nominal');
            $sisa = $totalSetor - $totalTarik;
            $a->total_setor = $totalSetor;
            $a->total_tarik = $totalTarik;
            $a->sisa_saldo = $sisa;
            return $a;
        });

        $perPage = $request->integer('per_page', 10);
        $currentPage = $request->integer('page', 1);
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $anggotas->slice(($currentPage - 1) * $perPage, $perPage),
            $anggotas->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return inertia('Superadmin/LaporanCS/Anggota/SisaPenarikanDana', [
            'data' => $paginated,
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Sisa Penarikan Dana',
        ]);
    }

    public function simpananPinjaman(Request $request)
    {
        $query = Anggota::with([
            'kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
            'simpanan:id,anggota_id,no_rekening,aktif',
            'pinjaman:id,anggota_id,no_pinjaman,plafon,aktif',
        ])->where('status', 1);

        $this->applySearch($query, $request);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        $anggotas = $query->orderBy('no_anggota')->get()->map(function ($a) {
            $a->jumlah_simpanan = $a->simpanan->count();
            $a->jumlah_pinjaman = $a->pinjaman->count();
            $a->total_plafon = $a->pinjaman->sum(fn ($p) => (float) ($p->plafon ?? 0));
            return $a;
        });

        $perPage = $request->integer('per_page', 10);
        $currentPage = $request->integer('page', 1);
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $anggotas->slice(($currentPage - 1) * $perPage, $perPage),
            $anggotas->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return inertia('Superadmin/LaporanCS/Anggota/SimpananPinjaman', [
            'data' => $paginated,
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
            'variantTitle' => 'Simpanan & Pinjaman Anggota',
        ]);
    }

    public function simpanPinjamDetail(Request $request)
    {
        $query = Anggota::with([
            'kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
            'simpanan:id,anggota_id,no_rekening,jenis_id,aktif,nominal_setor',
            'simpanan.jenis_simpanan:id,kode,nama,jenis',
            'pinjaman:id,anggota_id,no_pinjaman,plafon,bunga,jangka_waktu,angsuranke,aktif',
            'pinjaman.jenisPinjaman:id,nama',
        ])->where('status', 1);

        $this->applySearch($query, $request);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));

        return inertia('Superadmin/LaporanCS/Anggota/SimpanPinjamDetail', [
            'data' => $this->paginated($query->orderBy('no_anggota'), $request),
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Simpan & Pinjam Detail',
        ]);
    }

    public function hutangKewajiban(Request $request)
    {
        $query = Anggota::with([
            'kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
            'pinjaman:id,anggota_id,no_pinjaman,plafon,nominal_angsuran,angsuranke,jangka_waktu,aktif',
            'pinjaman.jenisPinjaman:id,nama',
        ])->where('status', 1)
            ->whereHas('pinjaman', fn ($q) => $q->where('aktif', 1));

        $this->applySearch($query, $request);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));

        $anggotas = $query->orderBy('no_anggota')->get()->map(function ($a) {
            $a->pinjaman_aktif = $a->pinjaman->filter(fn ($p) => $p->aktif == 1);
            $a->total_hutang = $a->pinjaman_aktif->sum(fn ($p) => (float) ($p->plafon ?? 0));
            $a->total_angsuran_bulan = $a->pinjaman_aktif->sum(fn ($p) => (float) ($p->nominal_angsuran ?? 0));
            return $a;
        });

        $perPage = $request->integer('per_page', 10);
        $currentPage = $request->integer('page', 1);
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $anggotas->slice(($currentPage - 1) * $perPage, $perPage),
            $anggotas->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return inertia('Superadmin/LaporanCS/Anggota/HutangKewajiban', [
            'data' => $paginated,
            'filters' => $this->baseFilters($request),
            'kelompoks' => Kelompok::select('id', 'kode', 'nama')->get(),
            'variantTitle' => 'Hutang & Kewajiban Anggota',
        ]);
    }

    // ==================== CETAK PDF METHODS ====================

    private function streamPdf($view, array $data, string $filename, string $paper = 'A4', string $orientation = 'landscape')
    {
        $pdf = Pdf::loadView($view, $data)->setPaper($paper, $orientation);
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(420, 570, 'Halaman {PAGE_NUM} / {PAGE_COUNT}', null, 10, [0, 0, 0]);

        return response()->streamDownload(fn () => print ($pdf->output()), $filename);
    }

    public function cetakDaftarAnggota(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('status', 1);
        $this->applySearch($query, $request);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));
        $query->when($request->filled('kantor_id'), fn ($q) => $q->where('kantor_id', $request->input('kantor_id')));

        return $this->streamPdf('pdf.laporan-cs.anggota.daftar-anggota', [
            'anggota' => $query->orderBy('no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'daftar_anggota.pdf');
    }

    public function cetakDaftarNonAnggota(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('status', 0);
        $this->applySearch($query, $request);

        return $this->streamPdf('pdf.laporan-cs.anggota.non-anggota', [
            'anggota' => $query->orderBy('no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'daftar_non_anggota.pdf');
    }

    public function cetakDaftarPengurus(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('pengurus', 1);
        $this->applySearch($query, $request);

        return $this->streamPdf('pdf.laporan-cs.anggota.pengurus', [
            'anggota' => $query->orderBy('no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'daftar_pengurus.pdf');
    }

    public function cetakDaftarPengawas(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('pengawas', 1);
        $this->applySearch($query, $request);

        return $this->streamPdf('pdf.laporan-cs.anggota.pengawas', [
            'anggota' => $query->orderBy('no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'daftar_pengawas.pdf');
    }

    public function cetakAnggotaPerKelompok(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('status', 1);
        $this->applySearch($query, $request);
        $query->when($request->filled('kelompok_id'), fn ($q) => $q->where('kelompok_id', $request->input('kelompok_id')));

        return $this->streamPdf('pdf.laporan-cs.anggota.per-kelompok', [
            'anggota' => $query->orderBy('kelompok_id')->orderBy('no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'anggota_per_kelompok.pdf');
    }

    public function cetakKartuAnggota($id)
    {
        $anggota = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->findOrFail($id);

        return $this->streamPdf('pdf.laporan-cs.anggota.kartu-anggota', [
            'anggota' => $anggota,
        ], 'kartu_anggota_'.$anggota->no_anggota.'.pdf', 'A4', 'portrait');
    }

    public function cetakLaporanAnggota(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('status', 1);
        $this->applySearch($query, $request);
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.anggota.laporan-anggota', [
            'anggota' => $query->orderBy('no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'laporan_anggota.pdf');
    }

    public function cetakPenarikanAnggota(Request $request)
    {
        $query = TarikanSimpanan::with(['anggota:id,no_anggota,nama', 'kantor:id,kode,nama_kantor']);
        $this->applySearch($query, $request, ['anggota.no_anggota', 'anggota.nama', 'no_transaksi']);
        $query->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')));
        $query->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')));

        return $this->streamPdf('pdf.laporan-cs.anggota.penarikan', [
            'transaksi' => $query->orderByDesc('tgl_transaksi')->get(),
            'filters' => $this->baseFilters($request),
        ], 'penarikan_anggota.pdf');
    }

    public function cetakSisaPenarikanDana(Request $request)
    {
        $query = Anggota::with(['kelompok:id,kode,nama', 'kantor:id,kode,nama_kantor'])
            ->where('status', 1)
            ->whereHas('simpanan');
        $this->applySearch($query, $request);

        $anggotas = $query->orderBy('no_anggota')->get()->map(function ($a) {
            $totalSetor = $a->simpanan->sum(fn ($s) => (float) ($s->nominal_setor ?? 0));
            $totalTarik = TarikanSimpanan::where('anggota_id', $a->id)->where('status', 'posted')->sum('nominal');
            $a->total_setor = $totalSetor;
            $a->total_tarik = $totalTarik;
            $a->sisa_saldo = $totalSetor - $totalTarik;
            return $a;
        });

        return $this->streamPdf('pdf.laporan-cs.anggota.sisa-penarikan', [
            'anggota' => $anggotas,
            'filters' => $this->baseFilters($request),
        ], 'sisa_penarikan_dana.pdf');
    }

    public function cetakSimpananPinjaman(Request $request)
    {
        $query = Anggota::with([
            'kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
            'simpanan:id,anggota_id,no_rekening,aktif',
            'pinjaman:id,anggota_id,no_pinjaman,plafon,aktif',
        ])->where('status', 1);
        $this->applySearch($query, $request);

        $anggotas = $query->orderBy('no_anggota')->get()->map(function ($a) {
            $a->jumlah_simpanan = $a->simpanan->count();
            $a->jumlah_pinjaman = $a->pinjaman->count();
            $a->total_plafon = $a->pinjaman->sum(fn ($p) => (float) ($p->plafon ?? 0));
            return $a;
        });

        return $this->streamPdf('pdf.laporan-cs.anggota.simpanan-pinjaman', [
            'anggota' => $anggotas,
            'filters' => $this->baseFilters($request),
        ], 'simpanan_pinjaman_anggota.pdf');
    }

    public function cetakSimpanPinjamDetail(Request $request)
    {
        $query = Anggota::with([
            'kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
            'simpanan:id,anggota_id,no_rekening,jenis_id,aktif,nominal_setor',
            'simpanan.jenis_simpanan:id,kode,nama,jenis',
            'pinjaman:id,anggota_id,no_pinjaman,plafon,bunga,jangka_waktu,angsuranke,aktif',
            'pinjaman.jenisPinjaman:id,nama',
        ])->where('status', 1);
        $this->applySearch($query, $request);

        return $this->streamPdf('pdf.laporan-cs.anggota.simpan-pinjam-detail', [
            'anggota' => $query->orderBy('no_anggota')->get(),
            'filters' => $this->baseFilters($request),
        ], 'simpan_pinjam_detail.pdf', 'A4', 'landscape');
    }

    public function cetakHutangKewajiban(Request $request)
    {
        $query = Anggota::with([
            'kelompok:id,kode,nama',
            'kantor:id,kode,nama_kantor',
            'pinjaman:id,anggota_id,no_pinjaman,plafon,nominal_angsuran,angsuranke,jangka_waktu,aktif',
            'pinjaman.jenisPinjaman:id,nama',
        ])->where('status', 1)
            ->whereHas('pinjaman', fn ($q) => $q->where('aktif', 1));
        $this->applySearch($query, $request);

        $anggotas = $query->orderBy('no_anggota')->get()->map(function ($a) {
            $a->pinjaman_aktif = $a->pinjaman->filter(fn ($p) => $p->aktif == 1);
            $a->total_hutang = $a->pinjaman_aktif->sum(fn ($p) => (float) ($p->plafon ?? 0));
            $a->total_angsuran_bulan = $a->pinjaman_aktif->sum(fn ($p) => (float) ($p->nominal_angsuran ?? 0));
            return $a;
        });

        return $this->streamPdf('pdf.laporan-cs.anggota.hutang-kewajiban', [
            'anggota' => $anggotas,
            'filters' => $this->baseFilters($request),
        ], 'hutang_kewajiban_anggota.pdf');
    }
}
