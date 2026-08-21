import { useState } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import {
    LayoutDashboard,
    ShieldCheck,
    Users,
    Receipt,
    HandCoins,
    Wallet,
    CalendarClock,
    Coins,
    ArrowLeftRight,
    ArrowDownUp,
    ArrowDownToLine,
    CircleDollarSign,
    Settings,
    ChevronRight,
    LogOut,
    Menu,
    X,
    Landmark,
} from 'lucide-react';

/* ============================================================
   Konfigurasi menu — replikasi penuh sidebar AdminLTE lama
   ============================================================ */
const MENU = [
    { label: 'Dashboard', icon: LayoutDashboard, route: 'superadmin.dashboard' },
    { header: 'Super Admin' },
    { label: 'User', icon: ShieldCheck, route: 'superadmin.user' },
    {
        label: 'Anggota',
        icon: Users,
        children: [
            { label: 'Kelompok', route: 'superadmin.kelompok' },
            { label: 'Anggota', route: 'superadmin.anggota' },
        ],
    },
    {
        label: 'Account',
        icon: Receipt,
        children: [
            { label: 'Header', route: 'superadmin.account-header' },
            { label: 'Account', route: 'superadmin.account' },
        ],
    },
    {
        label: 'Pinjaman',
        icon: HandCoins,
        children: [
            { label: 'Produk Pinjaman', route: 'superadmin.pinjaman.produk' },
            { label: 'Jaminan', route: 'superadmin.pinjaman.jaminan' },
            { label: 'Pinjaman', route: 'superadmin.pinjaman.pinjaman' },
            { label: 'Proposal', route: 'superadmin.pinjaman.proposal' },
            { label: 'Jadwal Ulang', route: 'superadmin.pinjaman.jadwal-ulang' },
            { label: 'Tagihan', route: 'superadmin.pinjaman.tagihan' },
            { label: 'Penghapusan', route: 'superadmin.pinjaman.penghapusan' },
            { label: 'Surat Peringatan', route: 'superadmin.pinjaman.surat-peringatan' },
            { label: 'Pengembalian Jaminan', route: 'superadmin.pinjaman.pengembalian-jaminan' },
        ],
    },
    {
        label: 'Simpanan',
        icon: Wallet,
        children: [
            { label: 'Kode Transaksi', route: 'superadmin.simpanan.kode-transaksi' },
            { label: 'Produk Simpanan', route: 'superadmin.simpanan.produk-simpanan' },
            { label: 'Simpanan', route: 'superadmin.simpanan' },
        ],
    },
    {
        label: 'Simpanan Berjangka',
        icon: CalendarClock,
        children: [
            { label: 'Produk Berjangka', route: 'superadmin.simpanan-berjangka.produk' },
            { label: 'Simpanan Berjangka', route: 'superadmin.simpanan-berjangka' },
        ],
    },
    { header: 'Front Office' },
    {
        label: 'Kas Harian',
        icon: Coins,
        children: [
            { label: 'Kas Awal', route: 'superadmin.kas-harian.kas-awal' },
            { label: 'Kas Keluar', route: 'superadmin.kas-harian.kas-keluar' },
            { label: 'Kas Masuk', route: 'superadmin.kas-harian.kas-masuk' },
            { label: 'Kas Akhir', route: 'superadmin.kas-harian.kas-akhir' },
        ],
    },
    {
        label: 'Transaksi Pinjaman',
        icon: ArrowLeftRight,
        children: [
            { label: 'Pencairan Pinjaman', route: 'superadmin.transaksi-pinjaman.pencairan-pinjaman' },
            { label: 'Penalti Pinjaman', route: 'superadmin.transaksi-pinjaman.penalti-pinjaman' },
            { label: 'Penalti Kolektif Tunai', route: 'superadmin.transaksi-pinjaman.penalti-pinjaman-kolektif-tunai' },
            { label: 'Angsuran Pinjaman', route: 'superadmin.transaksi-pinjaman.angsuran-pinjaman' },
            { label: 'Angsuran Kolektif Debet', route: 'superadmin.transaksi-pinjaman.angsuran-pinjaman-kolektif-debet' },
            { label: 'Angsuran Kolektif Tunai', route: 'superadmin.transaksi-pinjaman.angsuran-pinjaman-kolektif-tunai' },
            { label: 'Setoran Kolektif Bank', route: 'superadmin.transaksi-pinjaman.setoran-kolektif-bank' },
        ],
    },
    {
        label: 'Transaksi Simpanan',
        icon: ArrowDownUp,
        children: [
            { label: 'Setoran Simpanan', route: 'superadmin.transaksi-simpanan.setoran-simpanan' },
            { label: 'Setoran Kolektif', route: 'superadmin.transaksi-simpanan.setoran-simpanan-kolektif' },
            { label: 'Tarikan Simpanan', route: 'superadmin.transaksi-simpanan.tarikan-simpanan' },
            { label: 'Tarikan Kolektif', route: 'superadmin.transaksi-simpanan.tarikan-simpanan-kolektif' },
            { label: 'Pemindahbukuan', route: 'superadmin.transaksi-simpanan.pemindahbukuan-simpanan' },
            { label: 'Penutupan Simpanan', route: 'superadmin.transaksi-simpanan.penutupan-simpanan' },
        ],
    },
    {
        label: 'Transaksi Berjangka',
        icon: ArrowDownToLine,
        children: [
            { label: 'Setoran Berjangka', route: 'superadmin.transaksi-simpanan-berjangka.setoran-simpanan-berjangka' },
            { label: 'Penalti Berjangka', route: 'superadmin.transaksi-simpanan-berjangka.penalti-simpanan-berjangka' },
        ],
    },
    { label: 'Penarikan Dana Titipan', icon: CircleDollarSign, route: 'superadmin.penarikan-dana-titipan' },
    {
        label: 'Setting',
        icon: Settings,
        children: [
            { label: 'Kantor', route: 'superadmin.kantor' },
            { label: 'Marketing', route: 'superadmin.marketing' },
            { label: 'Options', route: 'superadmin.template' },
        ],
    },
];

/* ============================================================
   Item sidebar
   ============================================================ */
function NavLink({ item, active }) {
    return (
        <Link
            href={route(item.route)}
            className={`group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 ${
                active
                    ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30'
                    : 'text-slate-400 hover:bg-white/5 hover:text-white'
            }`}
        >
            <item.icon className={`size-4.5 shrink-0 transition ${active ? '' : 'text-slate-500 group-hover:text-brand-400'}`} />
            <span className="truncate">{item.label}</span>
        </Link>
    );
}

function NavGroup({ item, open, onToggle }) {
    const anyActive = item.children.some((c) => route().current(c.route) || route().current(c.route + '.*'));
    const [expanded, setExpanded] = useState(anyActive);

    return (
        <div>
            <button
                type="button"
                onClick={() => {
                    setExpanded((v) => !v);
                    onToggle?.();
                }}
                className={`group flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 ${
                    anyActive ? 'text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white'
                }`}
            >
                <item.icon className={`size-4.5 shrink-0 transition ${anyActive ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400'}`} />
                <span className="flex-1 truncate text-left">{item.label}</span>
                <ChevronRight className={`size-4 shrink-0 text-slate-500 transition-transform duration-300 ${expanded ? 'rotate-90' : ''}`} />
            </button>

            <div className={`grid transition-[grid-template-rows] duration-300 ease-in-out ${expanded ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'}`}>
                <div className="overflow-hidden">
                    <div className="ml-5 mt-1 space-y-0.5 border-l border-white/10 pl-3">
                        {item.children.map((child) => (
                            <NavLink key={child.route} item={child} active={route().current(child.route) || route().current(child.route + '.*')} />
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}

function SidebarContent({ onNavigate }) {
    return (
        <div className="flex h-full flex-col">
            {/* Brand */}
            <div className="flex h-16 shrink-0 items-center gap-3 border-b border-white/10 px-5">
                <span className="grid size-9 place-items-center rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-600/30">
                    <Landmark className="size-5" />
                </span>
                <div>
                    <p className="text-sm font-bold tracking-wide text-white">KSP KOPINKA</p>
                    <p className="text-[11px] text-slate-500">Simpan Pinjam</p>
                </div>
            </div>

            {/* Menu */}
            <nav className="flex-1 space-y-0.5 overflow-y-auto overflow-x-hidden px-3 py-4">
                {MENU.map((item, i) =>
                    item.header ? (
                        <p key={i} className="px-3 pt-4 pb-1.5 text-[11px] font-semibold uppercase tracking-widest text-slate-600">
                            {item.header}
                        </p>
                    ) : item.children ? (
                        <NavGroup key={item.label} item={item} />
                    ) : (
                        <NavLink key={item.route} item={item} active={route().current(item.route)} />
                    ),
                )}
            </nav>

            {/* Footer */}
            <div className="shrink-0 border-t border-white/10 px-5 py-3">
                <p className="text-[11px] text-slate-600">v2.0 · Laravel 13 · React</p>
            </div>
        </div>
    );
}

/* ============================================================
   Layout utama
   ============================================================ */
export default function AuthenticatedLayout({ children }) {
    const { auth, flash } = usePage().props;
    const [mobileOpen, setMobileOpen] = useState(false);
    const [userMenuOpen, setUserMenuOpen] = useState(false);

    const logout = (e) => {
        e.preventDefault();
        router.post(route('logout'));
    };

    return (
        <div className="min-h-screen bg-slate-100">
            {/* Sidebar desktop */}
            <aside className="fixed inset-y-0 left-0 z-40 hidden w-64 bg-night-800 lg:block">
                <SidebarContent />
            </aside>

            {/* Sidebar mobile */}
            {mobileOpen && (
                <div className="fixed inset-0 z-50 lg:hidden">
                    <div className="absolute inset-0 bg-night-900/60 backdrop-blur-sm" onClick={() => setMobileOpen(false)} />
                    <aside className="absolute inset-y-0 left-0 w-64 bg-night-800 shadow-2xl">
                        <button
                            type="button"
                            onClick={() => setMobileOpen(false)}
                            className="absolute top-4 right-3 rounded-lg p-1.5 text-slate-400 transition hover:bg-white/10 hover:text-white"
                            aria-label="Tutup menu"
                        >
                            <X className="size-5" />
                        </button>
                        <SidebarContent onNavigate={() => setMobileOpen(false)} />
                    </aside>
                </div>
            )}

            <div className="lg:pl-64">
                {/* Navbar */}
                <header className="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/80 px-4 backdrop-blur-md sm:px-6">
                    <div className="flex items-center gap-3">
                        <button
                            type="button"
                            onClick={() => setMobileOpen(true)}
                            className="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 lg:hidden"
                            aria-label="Buka menu"
                        >
                            <Menu className="size-5" />
                        </button>
                        <span className="hidden text-sm font-medium text-slate-500 sm:block">
                            {new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}
                        </span>
                    </div>

                    <div className="relative">
                        <button
                            type="button"
                            onClick={() => setUserMenuOpen((v) => !v)}
                            className="flex items-center gap-3 rounded-full py-1.5 pr-3 pl-1.5 transition hover:bg-slate-100"
                        >
                            <span className="grid size-9 place-items-center rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-sm font-bold text-white">
                                {(auth.user?.nama ?? 'A').charAt(0).toUpperCase()}
                            </span>
                            <span className="hidden text-left sm:block">
                                <span className="block text-sm font-semibold text-slate-800">{auth.user?.nama}</span>
                                <span className="block text-xs text-slate-500 capitalize">{auth.user?.role}</span>
                            </span>
                            <ChevronRight className="size-4 rotate-90 text-slate-400" />
                        </button>

                        {userMenuOpen && (
                            <>
                                <div className="fixed inset-0 z-10" onClick={() => setUserMenuOpen(false)} />
                                <div className="absolute right-0 z-20 mt-2 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                                    <div className="border-b border-slate-100 px-4 py-3">
                                        <p className="truncate text-sm font-semibold text-slate-800">{auth.user?.email}</p>
                                        <p className="text-xs capitalize text-slate-500">{auth.user?.role}</p>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={logout}
                                        className="flex w-full items-center gap-2.5 px-4 py-3 text-sm font-medium text-brand-600 transition hover:bg-brand-50"
                                    >
                                        <LogOut className="size-4" />
                                        Keluar
                                    </button>
                                </div>
                            </>
                        )}
                    </div>
                </header>

                {/* Flash toast */}
                {(flash?.status || flash?.error) && (
                    <div className="fixed top-20 right-6 z-50">
                        <div
                            className={`rounded-xl px-5 py-3.5 text-sm font-medium text-white shadow-2xl ${
                                flash.error ? 'bg-brand-600' : 'bg-emerald-600'
                            }`}
                        >
                            {flash.error || flash.status}
                        </div>
                    </div>
                )}

                {/* Konten */}
                <main className="p-4 sm:p-6 lg:p-8">{children}</main>
            </div>
        </div>
    );
}
