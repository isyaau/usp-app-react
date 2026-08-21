import { Head } from '@inertiajs/react';
import {
    Users,
    UserCheck,
    Building2,
    UsersRound,
    FolderTree,
    Bookmark,
    BookOpen,
    ArrowUpRight,
    CheckCircle2,
    LoaderCircle,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Separator } from '@/Components/ui/separator';
import type { DashboardProps } from '@/types';

interface StatCard {
    key: keyof DashboardProps['totals'];
    label: string;
    icon: LucideIcon;
    gradient: string;
    shadow: string;
}

const STATS: StatCard[] = [
    { key: 'totalAnggota', label: 'Total Anggota', icon: UsersRound, gradient: 'from-rose-500 to-red-600', shadow: 'shadow-rose-500/25' },
    { key: 'totalKelompok', label: 'Total Kelompok', icon: Users, gradient: 'from-sky-500 to-blue-600', shadow: 'shadow-sky-500/25' },
    { key: 'totalKantor', label: 'Total Kantor', icon: Building2, gradient: 'from-violet-500 to-purple-600', shadow: 'shadow-violet-500/25' },
    { key: 'totalUsers', label: 'Total Pengguna', icon: UserCheck, gradient: 'from-emerald-500 to-teal-600', shadow: 'shadow-emerald-500/25' },
    { key: 'totalAccgroup', label: 'Grup Akun', icon: FolderTree, gradient: 'from-amber-500 to-orange-600', shadow: 'shadow-amber-500/25' },
    { key: 'totalAccheader', label: 'Header Akun', icon: Bookmark, gradient: 'from-cyan-500 to-sky-600', shadow: 'shadow-cyan-500/25' },
    { key: 'totalAccount', label: 'Akun', icon: BookOpen, gradient: 'from-fuchsia-500 to-pink-600', shadow: 'shadow-fuchsia-500/25' },
];

export default function Dashboard({ totals }: DashboardProps) {
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
                            Ringkasan data master dan aktivitas sistem Anda hari ini.
                        </p>
                    </div>
                    <Badge className="bg-white/15 text-white backdrop-blur hover:bg-white/15">
                        PostgreSQL · Laravel 13 · React + TS
                    </Badge>
                </div>
            </div>

            {/* Kartu statistik */}
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
                {STATS.map((stat) => (
                    <Card
                        key={stat.key}
                        className="group relative gap-3 overflow-hidden py-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                    >
                        <div className={`absolute -top-10 -right-10 size-24 rounded-full bg-gradient-to-br ${stat.gradient} opacity-10 blur-xl transition group-hover:opacity-25`} />
                        <CardContent className="px-5">
                            <span className={`mb-3 grid size-11 place-items-center rounded-xl bg-gradient-to-br ${stat.gradient} text-white shadow-lg ${stat.shadow}`}>
                                <stat.icon className="size-5" />
                            </span>
                            <p className="text-2xl font-extrabold">{(totals?.[stat.key] ?? 0).toLocaleString('id-ID')}</p>
                            <p className="mt-0.5 flex items-center gap-1 text-xs font-medium text-muted-foreground">
                                {stat.label}
                                <ArrowUpRight className="size-3 opacity-0 transition group-hover:opacity-100" />
                            </p>
                        </CardContent>
                    </Card>
                ))}
            </div>

            {/* Panel info */}
            <div className="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <Card className="lg:col-span-2">
                    <CardHeader>
                        <CardTitle>Aktivitas Sistem</CardTitle>
                        <CardDescription>Riwayat transformasi aplikasi KSP KOPINKA.</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-3">
                        {[
                            ['Migrasi database ke PostgreSQL', 'Selesai'],
                            ['Upgrade framework ke Laravel 13', 'Selesai'],
                            ['Transformasi frontend ke React + TypeScript', 'Berjalan'],
                        ].map(([label, status]) => (
                            <div
                                key={label}
                                className="flex items-center justify-between rounded-xl border bg-muted/40 px-4 py-3 transition hover:bg-muted/70"
                            >
                                <span className="text-sm font-medium">{label}</span>
                                <Badge variant={status === 'Selesai' ? 'success' : 'warning'}>
                                    {status === 'Selesai' ? <CheckCircle2 /> : <LoaderCircle />}
                                    {status}
                                </Badge>
                            </div>
                        ))}
                    </CardContent>
                </Card>

                <Card className="border-night-700 bg-night-800 text-slate-100">
                    <CardHeader>
                        <CardTitle className="text-white">Kredensial Demo</CardTitle>
                        <CardDescription className="text-slate-400">Gunakan akun berikut untuk masuk:</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-2 rounded-xl bg-white/5 p-4 font-mono text-sm">
                            <p><span className="text-slate-400">email:</span> admin@admin.com</p>
                            <p><span className="text-slate-400">password:</span> password</p>
                        </div>
                        <Separator className="my-4 bg-white/10" />
                        <p className="text-xs text-slate-500">
                            Ganti password default setelah instalasi produksi.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}
