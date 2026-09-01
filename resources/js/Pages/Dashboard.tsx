import { Head } from '@inertiajs/react';
import {
    Users,
    Building2,
    UsersRound,
    Wallet,
    HandCoins,
    Landmark,
    TrendingUp,
    TrendingDown,
    FileText,
    ArrowRight,
    CircleDollarSign,
    PiggyBank,
    BadgePercent,
    UserPlus,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { GroupedBarChart, SingleBarChart, HorizontalBar } from '@/Components/DashboardCharts';
import type { DashboardProps } from '@/types';

function formatRupiah(value: number): string {
    return 'Rp ' + (value ?? 0).toLocaleString('id-ID');
}

function formatNumber(value: number): string {
    return (value ?? 0).toLocaleString('id-ID');
}

interface FinancialCard {
    key: keyof DashboardProps['kpi'];
    label: string;
    icon: LucideIcon;
    gradient: string;
    shadow: string;
    format: (v: number) => string;
}

const FINANCIAL: FinancialCard[] = [
    { key: 'totalSisaPokok', label: 'Piutang Outstanding', icon: HandCoins, gradient: 'from-rose-500 to-red-600', shadow: 'shadow-rose-500/25', format: formatRupiah },
    { key: 'totalPlafon', label: 'Total Plafon Pinjaman', icon: Landmark, gradient: 'from-sky-500 to-blue-600', shadow: 'shadow-sky-500/25', format: formatRupiah },
    { key: 'totalSimpanan', label: 'Total Simpanan', icon: PiggyBank, gradient: 'from-emerald-500 to-teal-600', shadow: 'shadow-emerald-500/25', format: formatRupiah },
    { key: 'totalDeposito', label: 'Total Deposito', icon: Wallet, gradient: 'from-violet-500 to-purple-600', shadow: 'shadow-violet-500/25', format: formatRupiah },
    { key: 'setoranBulanIni', label: 'Setoran Bulan Ini', icon: TrendingUp, gradient: 'from-amber-500 to-orange-600', shadow: 'shadow-amber-500/25', format: formatRupiah },
    { key: 'tarikanBulanIni', label: 'Tarikan Bulan Ini', icon: TrendingDown, gradient: 'from-cyan-500 to-sky-600', shadow: 'shadow-cyan-500/25', format: formatRupiah },
    { key: 'angsuranBulanIni', label: 'Angsuran Diterima Bulan Ini', icon: CircleDollarSign, gradient: 'from-fuchsia-500 to-pink-600', shadow: 'shadow-fuchsia-500/25', format: formatRupiah },
    { key: 'anggotaBaruBulanIni', label: 'Anggota Baru Bulan Ini', icon: UserPlus, gradient: 'from-indigo-500 to-blue-700', shadow: 'shadow-indigo-500/25', format: formatNumber },
];

interface MasterStat {
    key: keyof DashboardProps['totals'];
    label: string;
    icon: LucideIcon;
    color: string;
}

const MASTER: MasterStat[] = [
    { key: 'totalAnggota', label: 'Anggota', icon: UsersRound, color: 'text-rose-600 bg-rose-50' },
    { key: 'totalKelompok', label: 'Kelompok', icon: Users, color: 'text-sky-600 bg-sky-50' },
    { key: 'totalKantor', label: 'Kantor', icon: Building2, color: 'text-violet-600 bg-violet-50' },
    { key: 'totalUsers', label: 'Pengguna', icon: BadgePercent, color: 'text-emerald-600 bg-emerald-50' },
];

const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

export default function Dashboard({ totals, kpi, recent, recap, charts }: DashboardProps) {
    const now = new Date();
    const bulanIni = `${monthNames[now.getMonth()]} ${now.getFullYear()}`;

    const shortMonth = (ym: string): string => {
        const [, m] = ym.split('-');
        return monthNames[(Number(m) || 1) - 1].slice(0, 3);
    };

    const kasData = charts.kasBulanan.map((d) => ({
        label: shortMonth(d.bulan),
        values: [d.setoran, d.tarikan, d.angsuran],
    }));

    const kasSeries = [
        { key: 'setoran', label: 'Setoran', color: '#10b981' },
        { key: 'tarikan', label: 'Tarikan', color: '#f43f5e' },
        { key: 'angsuran', label: 'Angsuran', color: '#0ea5e9' },
    ];

    const anggotaData = charts.anggotaBulanan.map((d) => ({
        label: shortMonth(d.bulan),
        value: d.baru,
    }));

    return (
        <AuthenticatedLayout>
            <Head title="Dashboard" />

            {/* Hero */}
            <div className="relative mb-6 overflow-hidden rounded-2xl bg-gradient-to-r from-brand-700 via-brand-600 to-brand-800 p-8 text-white shadow-xl shadow-brand-900/20">
                <div
                    className="absolute inset-0 opacity-15"
                    style={{
                        backgroundImage:
                            'radial-gradient(circle at 80% 20%, rgba(255,255,255,.5) 0, transparent 40%), radial-gradient(circle at 20% 80%, rgba(255,255,255,.3) 0, transparent 35%)',
                    }}
                />
                <div className="relative flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p className="text-sm font-medium tracking-wide text-white/70">Selamat datang kembali</p>
                        <h1 className="mt-1 text-3xl font-extrabold">Dashboard KSP KOPINKA</h1>
                        <p className="mt-2 max-w-xl text-sm text-white/75">
                            Ringkasan kinerja keuangan dan aktivitas koperasi. Periode berjalan: <span className="font-semibold text-white">{bulanIni}</span>.
                        </p>
                    </div>
                    <Badge className="bg-white/15 text-white backdrop-blur hover:bg-white/15">
                        <FileText className="size-3.5" />
                        Periode aktif
                    </Badge>
                </div>
            </div>

            {/* Indikator keuangan */}
            <p className="mb-3 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Indikator Keuangan</p>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {FINANCIAL.map((stat) => (
                    <Card
                        key={stat.key}
                        className="group relative overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                    >
                        <div className={`absolute -top-10 -right-10 size-24 rounded-full bg-gradient-to-br ${stat.gradient} opacity-10 blur-xl transition group-hover:opacity-25`} />
                        <CardContent className="px-5 py-5">
                            <div className="flex items-start justify-between">
                                <div>
                                    <p className="text-[11px] font-medium uppercase tracking-wide text-muted-foreground">{stat.label}</p>
                                    <p className="mt-2 text-xl font-extrabold tracking-tight">
                                        {stat.format(kpi?.[stat.key] ?? 0)}
                                    </p>
                                </div>
                                <span className={`grid size-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br ${stat.gradient} text-white shadow-lg ${stat.shadow}`}>
                                    <stat.icon className="size-5" />
                                </span>
                            </div>
                        </CardContent>
                    </Card>
                ))}
            </div>

            {/* Grafik */}
            <p className="mb-3 mt-8 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Grafik</p>
            <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Arus Kas 6 Bulan Terakhir</CardTitle>
                        <CardDescription>Perbandingan setoran, tarikan, dan angsuran per bulan.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <GroupedBarChart data={kasData} series={kasSeries} />
                        <div className="mt-2 flex flex-wrap items-center gap-4">
                            {kasSeries.map((s) => (
                                <span key={s.key} className="flex items-center gap-1.5 text-xs text-muted-foreground">
                                    <span className="inline-block size-2.5 rounded-sm" style={{ backgroundColor: s.color }} />
                                    {s.label}
                                </span>
                            ))}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Pertumbuhan Anggota</CardTitle>
                        <CardDescription>Jumlah anggota baru per bulan (6 bulan terakhir).</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <SingleBarChart data={anggotaData} color="#8b5cf6" />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Pinjaman per Produk</CardTitle>
                        <CardDescription>Jumlah & plafon pinjaman aktif berdasarkan produk.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <HorizontalBar data={charts.pinjamanPerProduk} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Simpanan per Jenis</CardTitle>
                        <CardDescription>Jumlah rekening & nominal simpanan aktif per jenis.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <HorizontalBar data={charts.simpananPerJenis} />
                    </CardContent>
                </Card>
            </div>

            {/* Basis data master */}
            <p className="mb-3 mt-8 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Data Master</p>
            <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
                {MASTER.map((stat) => (
                    <Card key={stat.key} className="transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                        <CardContent className="flex items-center gap-3 px-5 py-4">
                            <span className={`grid size-10 shrink-0 place-items-center rounded-lg ${stat.color}`}>
                                <stat.icon className="size-5" />
                            </span>
                            <div>
                                <p className="text-lg font-extrabold leading-tight">{formatNumber(totals?.[stat.key] ?? 0)}</p>
                                <p className="text-xs font-medium text-muted-foreground">{stat.label}</p>
                            </div>
                        </CardContent>
                    </Card>
                ))}
            </div>

            {/* Rekapitulasi seluruh modul */}
            <p className="mb-3 mt-8 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Rekapitulasi Semua Modul</p>
            {recap.map((section) => (
                <Card key={section.label} className="mb-4">
                    <CardHeader className="pb-3">
                        <CardTitle className="text-base">{section.label}</CardTitle>
                    </CardHeader>
                    <CardContent className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                        {section.items.map((item) => (
                            <div
                                key={item.label}
                                className="flex flex-col justify-between rounded-xl border bg-muted/40 px-4 py-3 transition hover:bg-muted/70"
                            >
                                <span className="truncate text-xs font-medium text-muted-foreground">{item.label}</span>
                                <span className="mt-1 text-lg font-extrabold leading-tight">{formatNumber(item.count)}</span>
                                {item.money && (
                                    <span className="mt-1 truncate text-xs font-semibold text-brand-700 dark:text-brand-300">
                                        {formatRupiah(item.nominal ?? 0)}
                                    </span>
                                )}
                            </div>
                        ))}
                    </CardContent>
                </Card>
            ))}

            {/* Aktivitas terbaru */}
            <div className="mt-8 grid grid-cols-1 gap-4 lg:grid-cols-2">
                {/* Pinjaman terbaru */}
                <Card>
                    <CardHeader className="flex-row items-center justify-between space-y-0">
                        <div>
                            <CardTitle>Pinjaman Terbaru</CardTitle>
                            <CardDescription>{recent.pinjaman.length} pinjaman terakhir yang dicatat.</CardDescription>
                        </div>
                        <HandCoins className="size-5 text-muted-foreground" />
                    </CardHeader>
                    <CardContent className="space-y-2">
                        {recent.pinjaman.length === 0 && (
                            <p className="py-6 text-center text-sm text-muted-foreground">Belum ada data pinjaman.</p>
                        )}
                        {recent.pinjaman.map((p) => (
                            <div key={p.id} className="flex items-center justify-between rounded-xl border bg-muted/40 px-4 py-3">
                                <div className="min-w-0">
                                    <p className="truncate text-sm font-semibold">{p.no_pinjaman}</p>
                                    <p className="truncate text-xs text-muted-foreground">{p.anggota ?? '—'}</p>
                                </div>
                                <div className="flex shrink-0 items-center gap-3">
                                    <span className="text-sm font-bold">{formatRupiah(p.plafon)}</span>
                                    <Badge variant={p.aktif ? 'success' : 'destructive'}>{p.aktif ? 'Aktif' : 'Nonaktif'}</Badge>
                                </div>
                            </div>
                        ))}
                    </CardContent>
                </Card>

                {/* Setoran terbaru */}
                <Card>
                    <CardHeader className="flex-row items-center justify-between space-y-0">
                        <div>
                            <CardTitle>Setoran Terbaru</CardTitle>
                            <CardDescription>Transaksi setoran simpanan terakhir.</CardDescription>
                        </div>
                        <TrendingUp className="size-5 text-muted-foreground" />
                    </CardHeader>
                    <CardContent className="space-y-2">
                        {recent.setoran.length === 0 && (
                            <p className="py-6 text-center text-sm text-muted-foreground">Belum ada transaksi setoran.</p>
                        )}
                        {recent.setoran.map((s) => (
                            <div key={s.id} className="flex items-center justify-between rounded-xl border bg-muted/40 px-4 py-3">
                                <div className="min-w-0">
                                    <p className="truncate text-sm font-semibold">{s.no_transaksi}</p>
                                    <p className="truncate text-xs text-muted-foreground">{s.anggota ?? '—'}</p>
                                </div>
                                <span className="shrink-0 text-sm font-bold text-emerald-600">+{formatRupiah(s.nominal)}</span>
                            </div>
                        ))}
                    </CardContent>
                </Card>

                {/* Angsuran terbaru */}
                <Card>
                    <CardHeader className="flex-row items-center justify-between space-y-0">
                        <div>
                            <CardTitle>Angsuran Diterima</CardTitle>
                            <CardDescription>Pembayaran angsuran pinjaman terakhir.</CardDescription>
                        </div>
                        <CircleDollarSign className="size-5 text-muted-foreground" />
                    </CardHeader>
                    <CardContent className="space-y-2">
                        {recent.angsuran.length === 0 && (
                            <p className="py-6 text-center text-sm text-muted-foreground">Belum ada pembayaran angsuran.</p>
                        )}
                        {recent.angsuran.map((a) => (
                            <div key={a.id} className="flex items-center justify-between rounded-xl border bg-muted/40 px-4 py-3">
                                <div className="min-w-0">
                                    <p className="truncate text-sm font-semibold">{a.no_pinjaman ?? a.no_transaksi}</p>
                                    <p className="truncate text-xs text-muted-foreground">{a.anggota ?? '—'}</p>
                                </div>
                                <span className="shrink-0 text-sm font-bold">{formatRupiah(a.total_angsuran)}</span>
                            </div>
                        ))}
                    </CardContent>
                </Card>

                {/* Anggota terbaru */}
                <Card>
                    <CardHeader className="flex-row items-center justify-between space-y-0">
                        <div>
                            <CardTitle>Anggota Terdaftar</CardTitle>
                            <CardDescription>Anggota terakhir yang bergabung.</CardDescription>
                        </div>
                        <ArrowRight className="size-5 text-muted-foreground" />
                    </CardHeader>
                    <CardContent className="space-y-2">
                        {recent.anggotaBaru.length === 0 && (
                            <p className="py-6 text-center text-sm text-muted-foreground">Belum ada anggota terdaftar.</p>
                        )}
                        {recent.anggotaBaru.map((a) => (
                            <div key={a.id} className="flex items-center justify-between rounded-xl border bg-muted/40 px-4 py-3">
                                <div className="min-w-0">
                                    <p className="truncate text-sm font-semibold">{a.nama}</p>
                                    <p className="text-xs text-muted-foreground">{a.no_anggota}</p>
                                </div>
                                <span className="shrink-0 text-xs font-medium text-muted-foreground">{a.tgl}</span>
                            </div>
                        ))}
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}
