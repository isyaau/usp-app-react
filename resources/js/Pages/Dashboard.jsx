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
} from 'lucide-react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const STATS = [
    { key: 'totalAnggota', label: 'Total Anggota', icon: UsersRound, gradient: 'from-rose-500 to-red-600', shadow: 'shadow-rose-500/25' },
    { key: 'totalKelompok', label: 'Total Kelompok', icon: Users, gradient: 'from-sky-500 to-blue-600', shadow: 'shadow-sky-500/25' },
    { key: 'totalKantor', label: 'Total Kantor', icon: Building2, gradient: 'from-violet-500 to-purple-600', shadow: 'shadow-violet-500/25' },
    { key: 'totalUsers', label: 'Total Pengguna', icon: UserCheck, gradient: 'from-emerald-500 to-teal-600', shadow: 'shadow-emerald-500/25' },
    { key: 'totalAccgroup', label: 'Grup Akun', icon: FolderTree, gradient: 'from-amber-500 to-orange-600', shadow: 'shadow-amber-500/25' },
    { key: 'totalAccheader', label: 'Header Akun', icon: Bookmark, gradient: 'from-cyan-500 to-sky-600', shadow: 'shadow-cyan-500/25' },
    { key: 'totalAccount', label: 'Akun', icon: BookOpen, gradient: 'from-fuchsia-500 to-pink-600', shadow: 'shadow-fuchsia-500/25' },
];

export default function Dashboard({ totals }) {
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
                <div className="relative">
                    <p className="text-sm font-medium tracking-wide text-white/70">Selamat datang kembali</p>
                    <h1 className="mt-1 text-3xl font-extrabold">Dashboard KSP KOPINKA</h1>
                    <p className="mt-2 max-w-xl text-sm text-white/75">
                        Ringkasan data master dan aktivitas sistem Anda hari ini.
                    </p>
                </div>
            </div>

            {/* Kartu statistik */}
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
                {STATS.map((stat) => (
                    <div
                        key={stat.key}
                        className="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 transition-all duration-300 hover:-translate-y-1 hover:border-transparent hover:shadow-xl"
                    >
                        <div className={`absolute -top-10 -right-10 size-24 rounded-full bg-gradient-to-br ${stat.gradient} opacity-10 blur-xl transition group-hover:opacity-25`} />
                        <span className={`mb-4 grid size-11 place-items-center rounded-xl bg-gradient-to-br ${stat.gradient} text-white shadow-lg ${stat.shadow}`}>
                            <stat.icon className="size-5" />
                        </span>
                        <p className="text-2xl font-extrabold text-slate-800">{(totals?.[stat.key] ?? 0).toLocaleString('id-ID')}</p>
                        <p className="mt-0.5 flex items-center gap-1 text-xs font-medium text-slate-500">
                            {stat.label}
                            <ArrowUpRight className="size-3 opacity-0 transition group-hover:opacity-100" />
                        </p>
                    </div>
                ))}
            </div>

            {/* Panel info */}
            <div className="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div className="rounded-2xl border border-slate-200 bg-white p-6 lg:col-span-2">
                    <h2 className="text-base font-bold text-slate-800">Aktivitas Sistem</h2>
                    <p className="mt-1 text-sm text-slate-500">
                        Database PostgreSQL aktif · Laravel 13 · Frontend React + Inertia.
                    </p>
                    <div className="mt-4 space-y-3">
                        {[
                            ['Migrasi database ke PostgreSQL', 'Selesai'],
                            ['Upgrade framework ke Laravel 13', 'Selesai'],
                            ['Transformasi frontend ke React', 'Berjalan'],
                        ].map(([label, status]) => (
                            <div key={label} className="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                                <span className="text-sm font-medium text-slate-700">{label}</span>
                                <span
                                    className={`rounded-full px-2.5 py-1 text-[11px] font-semibold ${
                                        status === 'Selesai' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'
                                    }`}
                                >
                                    {status}
                                </span>
                            </div>
                        ))}
                    </div>
                </div>

                <div className="rounded-2xl border border-slate-200 bg-night-800 p-6 text-white">
                    <h2 className="text-base font-bold">Kredensial Demo</h2>
                    <p className="mt-1 text-sm text-slate-400">Gunakan akun berikut untuk masuk:</p>
                    <div className="mt-4 space-y-2 rounded-xl bg-white/5 p-4 font-mono text-sm">
                        <p><span className="text-slate-400">email:</span> admin@admin.com</p>
                        <p><span className="text-slate-400">password:</span> password</p>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
