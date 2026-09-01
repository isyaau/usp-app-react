<?php

namespace App\Http\Controllers;

use App\Models\AccGroup;
use App\Models\AccHeader;
use App\Models\Account;
use App\Models\Anggota;
use App\Models\AngsuranKolektif;
use App\Models\AngsuranPinjaman;
use App\Models\Deposito;
use App\Models\DepositoJenis;
use App\Models\Jaminan;
use App\Models\JadwalUlang;
use App\Models\Kantor;
use App\Models\KasHarian;
use App\Models\Kelompok;
use App\Models\Marketing;
use App\Models\PencairanPinjaman;
use App\Models\PenaltiPinjaman;
use App\Models\PenghapusanPinjaman;
use App\Models\PenutupanSimpanan;
use App\Models\Pinjaman;
use App\Models\PinjamanProduk;
use App\Models\Proposal;
use App\Models\SetoranSimpanan;
use App\Models\SetoranSimpananBerjangka;
use App\Models\Simpanan;
use App\Models\SimpananJenis;
use App\Models\SimpananKode;
use App\Models\SimpananRencana;
use App\Models\SuratPeringatan;
use App\Models\TarikanSimpanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard dengan ringkasan statistik master,
     * indikator keuangan, rekapitulasi seluruh modul, dan aktivitas terbaru.
     */
    public function index(): Response
    {
        $startOfMonth = Carbon::today()->startOfMonth();

        $totalPlafon = (float) Pinjaman::where('aktif', '1')->sum(DB::raw('CAST(plafon AS NUMERIC)'));
        $pokokTerbayar = (float) AngsuranPinjaman::query()
            ->whereRaw('EXISTS (SELECT 1 FROM pinjaman pp WHERE pp.id = angsuran_pinjaman.pinjaman_id AND pp.aktif = 1::text)')
            ->sum('nominal_pokok');

        return Inertia::render('Dashboard', [
            'totals' => [
                'totalKelompok' => Kelompok::count(),
                'totalUsers' => User::count(),
                'totalKantor' => Kantor::count(),
                'totalAnggota' => Anggota::count(),
                'totalAccgroup' => AccGroup::count(),
                'totalAccheader' => AccHeader::count(),
                'totalAccount' => Account::count(),
            ],
            'kpi' => [
                'pinjamanAktif' => (int) Pinjaman::where('aktif', '1')->count(),
                'totalPlafon' => $totalPlafon,
                'totalPokokTerbayar' => $pokokTerbayar,
                'totalSisaPokok' => max(0, $totalPlafon - $pokokTerbayar),
                'jumlahRekeningSimpanan' => (int) Simpanan::where('aktif', '1')->count(),
                'totalSimpanan' => (float) Simpanan::where('aktif', '1')->sum(DB::raw('CAST(nominal_setor AS NUMERIC)')),
                'jumlahDeposito' => (int) Deposito::count(),
                'totalDeposito' => (float) Deposito::sum(DB::raw('CAST(nominal AS NUMERIC)')),
                'setoranBulanIni' => (float) SetoranSimpanan::where('status', 'posted')
                    ->whereDate('tgl_transaksi', '>=', $startOfMonth)
                    ->sum('nominal'),
                'tarikanBulanIni' => (float) TarikanSimpanan::where('status', 'posted')
                    ->whereDate('tgl_transaksi', '>=', $startOfMonth)
                    ->sum('nominal'),
                'angsuranBulanIni' => (float) AngsuranPinjaman::where('status', 'posted')
                    ->whereDate('tgl_transaksi', '>=', $startOfMonth)
                    ->sum('total_angsuran'),
                'anggotaBaruBulanIni' => (int) Anggota::whereDate('created_at', '>=', $startOfMonth)->count(),
            ],
            'recap' => $this->recap(),
            'charts' => $this->charts(),
            'recent' => [
                'pinjaman' => Pinjaman::query()
                    ->with('anggota:id,no_anggota,nama')
                    ->orderBy('created_at', 'DESC')
                    ->limit(5)
                    ->get()
                    ->map(fn (Pinjaman $p) => [
                        'id' => $p->id,
                        'no_pinjaman' => $p->no_pinjaman,
                        'tanggal' => $p->tanggal,
                        'plafon' => (float) $p->plafon,
                        'aktif' => $p->aktif === '1',
                        'anggota' => $p->anggota ? $p->anggota->nama : null,
                    ]),
                'setoran' => SetoranSimpanan::query()
                    ->with('anggota:id,nama')
                    ->where('status', 'posted')
                    ->orderBy('tgl_transaksi', 'DESC')
                    ->limit(5)
                    ->get()
                    ->map(fn (SetoranSimpanan $s) => [
                        'id' => $s->id,
                        'no_transaksi' => $s->no_transaksi,
                        'tgl_transaksi' => $s->tgl_transaksi,
                        'nominal' => (float) $s->nominal,
                        'anggota' => $s->anggota ? $s->anggota->nama : null,
                    ]),
                'angsuran' => AngsuranPinjaman::query()
                    ->with('pinjaman:id,no_pinjaman', 'pinjaman.anggota:id,nama')
                    ->where('status', 'posted')
                    ->orderBy('tgl_transaksi', 'DESC')
                    ->limit(5)
                    ->get()
                    ->map(fn (AngsuranPinjaman $a) => [
                        'id' => $a->id,
                        'no_transaksi' => $a->no_transaksi,
                        'tgl_transaksi' => $a->tgl_transaksi,
                        'total_angsuran' => (float) $a->total_angsuran,
                        'no_pinjaman' => $a->pinjaman?->no_pinjaman,
                        'anggota' => $a->pinjaman?->anggota?->nama,
                    ]),
                'anggotaBaru' => Anggota::query()
                    ->orderBy('created_at', 'DESC')
                    ->limit(5)
                    ->get()
                    ->map(fn (Anggota $a) => [
                        'id' => $a->id,
                        'no_anggota' => $a->no_anggota,
                        'nama' => $a->nama,
                        'tgl' => $a->created_at?->format('d/m/Y'),
                    ]),
            ],
        ]);
    }

    /**
     * Rekapitulasi seluruh modul aplikasi.
     * Setiap modul = label + daftar item { label, count, nominal?, money? }.
     */
    private function recap(): array
    {
        // [table, column] -> config; setiap entry dipakai untuk menghitung
        // jumlah record dan (opsional) total nominal.
        $modules = [
            'Master Data' => [
                ['Grup Akun', 'acc_group', null],
                ['Header Akun', 'acc_header', null],
                ['Akun', 'account', null],
                ['Kantor', 'kantor', null],
                ['Marketing', 'marketing', null],
                ['Kelompok', 'kelompok', null],
                ['Anggota', 'anggota', null],
                ['Pengguna', 'users', null],
            ],
            'Simpanan' => [
                ['Kode Transaksi', 'simpanan_kode', null],
                ['Produk Simpanan', 'simpanan_jenis', null],
                ['Rekening Simpanan', 'simpanan', 'nominal_setor'],
                ['Simpanan Rencana', 'simpanan_rencana', 'nominal'],
                ['Setoran Simpanan', 'setoran_simpanan', 'nominal'],
                ['Tarikan Simpanan', 'tarikan_simpanan', 'nominal'],
                ['Penutupan Simpanan', 'penutupan_simpanan', 'nominal'],
            ],
            'Simpanan Berjangka' => [
                ['Produk Berjangka', 'deposito_jenis', null],
                ['Simpanan Berjangka', 'deposito', 'nominal'],
                ['Setoran Berjangka', 'setoran_simpanan_berjangka', 'nominal'],
                ['Pencairan Berjangka', 'pencairan_simpanan_berjangka', 'nominal_diterima'],
            ],
            'Pinjaman' => [
                ['Produk Pinjaman', 'pinj_jenis', null],
                ['Jaminan', 'jaminan', null],
                ['Pinjaman Aktif', 'pinjaman_active', 'plafon'],
                ['Proposal', 'proposal', 'plafon'],
                ['Jadwal Ulang', 'jadwal_ulang', 'plafon'],
                ['Pencairan Pinjaman', 'pencairan_pinjaman', 'nominal_cair'],
                ['Angsuran Pinjaman', 'angsuran_pinjaman', 'total_angsuran'],
                ['Penalti Pinjaman', 'penalti_pinjaman', 'nominal_penalti'],
                ['Penghapusan Pinjaman', 'penghapusan_pinjaman', null],
                ['Surat Peringatan', 'surat_peringatan', null],
            ],
            'Kas' => [
                ['Kas Harian', 'kas_harian', 'kas_akhir'],
            ],
            'Angsuran Kolektif' => [
                ['Angsuran Kolektif', 'angsuran_kolektif', null],
            ],
        ];

        $recap = [];
        foreach ($modules as $section => $items) {
            $rows = [];
            foreach ($items as [$label, $table, $column]) {
                $row = ['label' => $label, 'count' => $this->countTable($table)];
                if ($column !== null) {
                    $row['nominal'] = $this->sumColumn($table, $column);
                    $row['money'] = true;
                }
                $rows[] = $row;
            }
            $recap[] = ['label' => $section, 'items' => $rows];
        }

        return $recap;
    }

    private function countTable(string $table): int
    {
        if ($table === 'pinjaman_active') {
            return Pinjaman::where('aktif', '1')->count();
        }

        return (int) DB::table($table)->count();
    }

    private function sumColumn(string $table, string $column): float
    {
        if ($table === 'pinjaman_active') {
            return (float) Pinjaman::where('aktif', '1')->sum(DB::raw('CAST(plafon AS NUMERIC)'));
        }

        return (float) DB::table($table)->sum(DB::raw("CAST(\"{$column}\" AS NUMERIC)"));
    }

    /**
     * Data untuk grafik dashboard: arus kas bulanan, pertumbuhan anggota,
     * serta distribusi pinjaman & simpanan per produk.
     */
    private function charts(): array
    {
        // 6 bulan terakhir (termasuk bulan berjalan).
        $labels = [];
        $cursor = Carbon::today()->startOfMonth();
        for ($i = 5; $i >= 0; $i--) {
            $labels[] = $cursor->copy()->subMonths($i)->format('Y-m');
        }
        $start = $labels[0] . '-01';

        $monthlySum = function (string $table, string $column, string $status = 'posted') use ($start): array {
            return DB::table($table)
                ->where('status', $status)
                ->where('tgl_transaksi', '>=', $start)
                ->selectRaw("TO_CHAR(tgl_transaksi, 'YYYY-MM') AS bulan")
                ->selectRaw("SUM(CAST({$column} AS NUMERIC)) AS total")
                ->groupBy('bulan')
                ->pluck('total', 'bulan')
                ->map(fn ($v) => (float) $v)
                ->all();
        };

        $setoran = $monthlySum('setoran_simpanan', 'nominal');
        $tarikan = $monthlySum('tarikan_simpanan', 'nominal');
        $angsuran = $monthlySum('angsuran_pinjaman', 'total_angsuran');

        $kasBulanan = array_map(fn ($bulan) => [
            'bulan' => $bulan,
            'setoran' => (float) ($setoran[$bulan] ?? 0),
            'tarikan' => (float) ($tarikan[$bulan] ?? 0),
            'angsuran' => (float) ($angsuran[$bulan] ?? 0),
        ], $labels);

        $anggotaBaru = DB::table('anggota')
            ->where('created_at', '>=', $start)
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM') AS bulan")
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->map(fn ($v) => (int) $v)
            ->all();

        $anggotaBulanan = array_map(fn ($bulan) => [
            'bulan' => $bulan,
            'baru' => (int) ($anggotaBaru[$bulan] ?? 0),
        ], $labels);

        $pinjamanPerProduk = DB::table('pinjaman')
            ->join('pinj_jenis', 'pinjaman.jenis_id', '=', 'pinj_jenis.id')
            ->where('pinjaman.aktif', '1')
            ->selectRaw('COALESCE(pinj_jenis.nama, \'Tanpa Produk\') AS nama')
            ->selectRaw('COUNT(*) AS jumlah')
            ->selectRaw('SUM(CAST(pinjaman.plafon AS NUMERIC)) AS nominal')
            ->groupBy('pinj_jenis.nama')
            ->orderByDesc('jumlah')
            ->limit(8)
            ->get()
            ->map(fn ($r) => [
                'label' => $r->nama,
                'jumlah' => (int) $r->jumlah,
                'nominal' => (float) $r->nominal,
            ]);

        $simpananPerJenis = DB::table('simpanan')
            ->join('simpanan_jenis', 'simpanan.jenis_id', '=', 'simpanan_jenis.id')
            ->where('simpanan.aktif', '1')
            ->selectRaw('COALESCE(simpanan_jenis.nama, \'Tanpa Jenis\') AS nama')
            ->selectRaw('COUNT(*) AS jumlah')
            ->selectRaw('SUM(CAST(simpanan.nominal_setor AS NUMERIC)) AS nominal')
            ->groupBy('simpanan_jenis.nama')
            ->orderByDesc('jumlah')
            ->limit(8)
            ->get()
            ->map(fn ($r) => [
                'label' => $r->nama,
                'jumlah' => (int) $r->jumlah,
                'nominal' => (float) $r->nominal,
            ]);

        return [
            'kasBulanan' => $kasBulanan,
            'anggotaBulanan' => $anggotaBulanan,
            'pinjamanPerProduk' => $pinjamanPerProduk,
            'simpananPerJenis' => $simpananPerJenis,
        ];
    }
}
