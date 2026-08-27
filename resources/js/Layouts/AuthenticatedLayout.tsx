import { useState } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import {
    LayoutDashboard,
    ShieldCheck,
    Users,
    Receipt,
    HandCoins,
    Wallet,
    CalendarClock,
    ArrowDownUp,
    Settings,
    Banknote,
    ChevronRight,
    LogOut,
    Menu,
    X,
    Landmark,
    DollarSign,
    FileText,
    CreditCard,
} from 'lucide-react';

import { Button } from '@/Components/ui/button';
import { Avatar, AvatarFallback } from '@/Components/ui/avatar';
import { Separator } from '@/Components/ui/separator';
import { Badge } from '@/Components/ui/badge';
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/Components/ui/dropdown-menu';

/* ============================================================
   Konfigurasi menu — replikasi penuh sidebar AdminLTE lama
   ============================================================ */
interface MenuChild {
    label: string;
    route: string;
}

interface MenuHeader {
    header: string;
}

interface MenuLink {
    label: string;
    icon: LucideIcon;
    route: string;
}

interface MenuParent {
    label: string;
    icon: LucideIcon;
    children: MenuChild[];
}

type MenuItem = MenuHeader | MenuLink | MenuParent;

const MENU: MenuItem[] = [
    { label: 'Dashboard', icon: LayoutDashboard, route: 'superadmin.dashboard' },
    { header: 'Super Admin' },
    { label: 'User', icon: ShieldCheck, route: 'superadmin.user' },
    {
        label: 'Account',
        icon: Receipt,
        children: [
            { label: 'Header', route: 'superadmin.account-header' },
            { label: 'Account', route: 'superadmin.account' },
        ],
    },
    {
        label: 'Kas Harian',
        icon: DollarSign,
        children: [
            { label: 'Daftar Kas Harian', route: 'superadmin.kas-harian' },
            { label: 'Tambah Kas Harian', route: 'superadmin.kas-harian.create' },
        ],
    },
    { header: 'Customer Service' },
    {
        label: 'Anggota',
        icon: Users,
        children: [
            { label: 'Kelompok', route: 'superadmin.kelompok' },
            { label: 'Anggota', route: 'superadmin.anggota' },
        ],
    },
    {
        label: 'Pinjaman',
        icon: HandCoins,
        children: [
            { label: 'Produk Pinjaman', route: 'superadmin.pinjaman.produk' },
            { label: 'Jaminan', route: 'superadmin.pinjaman.jaminan' },
            { label: 'Pinjaman', route: 'superadmin.pinjaman.pinjaman' },
            { label: 'Pencairan Pinjaman', route: 'superadmin.pencairan-pinjaman' },
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
    {
        label: 'Laporan CS',
        icon: FileText,
        children: [
            { label: 'Daftar Anggota', route: 'superadmin.laporan-cs.anggota.daftar-anggota' },
            { label: 'Non Anggota', route: 'superadmin.laporan-cs.anggota.non-anggota' },
            { label: 'Pengurus', route: 'superadmin.laporan-cs.anggota.pengurus' },
            { label: 'Pengawas', route: 'superadmin.laporan-cs.anggota.pengawas' },
            { label: 'Per Kelompok', route: 'superadmin.laporan-cs.anggota.per-kelompok' },
            { label: 'Kartu Anggota', route: 'superadmin.laporan-cs.anggota.kartu' },
            { label: 'Laporan Anggota', route: 'superadmin.laporan-cs.anggota.laporan' },
            { label: 'Penarikan Anggota', route: 'superadmin.laporan-cs.anggota.penarikan' },
            { label: 'Sisa Penarikan Dana', route: 'superadmin.laporan-cs.anggota.sisa-penarikan' },
            { label: 'Simpanan & Pinjaman', route: 'superadmin.laporan-cs.anggota.simpanan-pinjaman' },
            { label: 'Simpan Pinjam Detail', route: 'superadmin.laporan-cs.anggota.simpan-pinjam-detail' },
            { label: 'Hutang & Kewajiban', route: 'superadmin.laporan-cs.anggota.hutang-kewajiban' },
        ],
    },
    {
        label: 'Laporan Pinjaman',
        icon: CreditCard,
        children: [
            { label: 'Daftar Pinjaman', route: 'superadmin.laporan-cs.pinjaman.daftar-pinjaman' },
            { label: 'Daftar Nama Peminjam', route: 'superadmin.laporan-cs.pinjaman.daftar-nama-peminjam' },
            { label: 'Kartu Pinjaman', route: 'superadmin.laporan-cs.pinjaman.kartu' },
            { label: 'Kartu Pinjaman (Data)', route: 'superadmin.laporan-cs.pinjaman.kartu-data' },
            { label: 'Laporan Angsuran', route: 'superadmin.laporan-cs.pinjaman.angsuran' },
            { label: 'Laporan Angsuran Detail', route: 'superadmin.laporan-cs.pinjaman.angsuran-detail' },
            { label: 'Kolektibilitas', route: 'superadmin.laporan-cs.pinjaman.kolektibilitas' },
            { label: 'Mutasi Pinjaman', route: 'superadmin.laporan-cs.pinjaman.mutasi' },
            { label: 'Nominatif (Sisa)', route: 'superadmin.laporan-cs.pinjaman.nominatif-sisa' },
            { label: 'Nominatif (Angsuran)', route: 'superadmin.laporan-cs.pinjaman.nominatif-angsuran' },
            { label: 'Nominatif (Jaminan)', route: 'superadmin.laporan-cs.pinjaman.nominatif-jaminan' },
            { label: 'Nominatif (Denda)', route: 'superadmin.laporan-cs.pinjaman.nominatif-denda' },
            { label: 'Pinjaman Custom', route: 'superadmin.laporan-cs.pinjaman.custom' },
            { label: 'Pinjaman Jatuh Tempo', route: 'superadmin.laporan-cs.pinjaman.jatuh-tempo' },
            { label: 'Pinjaman Lunas', route: 'superadmin.laporan-cs.pinjaman.lunas' },
            { label: 'Pendapatan Bagi Hasil', route: 'superadmin.laporan-cs.pinjaman.bagi-hasil' },
            { label: 'Pengembalian Jaminan', route: 'superadmin.laporan-cs.pinjaman.pengembalian-jaminan' },
            { label: 'Proposal Pinjaman', route: 'superadmin.laporan-cs.pinjaman.proposal' },
            { label: 'Proposal Penalti', route: 'superadmin.laporan-cs.pinjaman.proposal-penalti' },
            { label: 'Proposal Mobile', route: 'superadmin.laporan-cs.pinjaman.proposal-mobile' },
            { label: 'Tabel Angsuran', route: 'superadmin.laporan-cs.pinjaman.tabel-angsuran' },
            { label: 'Tabel Angsuran (Kosong)', route: 'superadmin.laporan-cs.pinjaman.tabel-angsuran-kosong' },
            { label: 'Transaksi Pinjaman', route: 'superadmin.laporan-cs.pinjaman.transaksi' },
            { label: 'Tunggakan Pinjaman', route: 'superadmin.laporan-cs.pinjaman.tunggakan' },
            { label: 'Tunggakan Per Kota', route: 'superadmin.laporan-cs.pinjaman.tunggakan-per-kota' },
            { label: 'Pencairan Pinjaman', route: 'superadmin.laporan-cs.pinjaman.pencairan' },
            { label: 'Transaksi Harian', route: 'superadmin.laporan-cs.pinjaman.harian' },
            { label: 'Kontrol Angsuran', route: 'superadmin.laporan-cs.pinjaman.kontrol-angsuran' },
            { label: 'Rekapitulasi', route: 'superadmin.laporan-cs.pinjaman.rekapitulasi' },
            { label: 'Rekapitulasi Sektor', route: 'superadmin.laporan-cs.pinjaman.rekapitulasi-sektor' },
            { label: 'Rekap Bagi Hasil', route: 'superadmin.laporan-cs.pinjaman.rekap-bagi-hasil' },
            { label: 'Rekap Nominatif (Produk)', route: 'superadmin.laporan-cs.pinjaman.rekap-produk' },
            { label: 'Rekap Nominatif (Marketing)', route: 'superadmin.laporan-cs.pinjaman.rekap-marketing' },
            { label: 'Pasal Surat Perjanjian', route: 'superadmin.laporan-cs.pinjaman.pasal-perjanjian' },
            { label: 'Surat Perjanjian', route: 'superadmin.laporan-cs.pinjaman.surat-perjanjian' },
            { label: 'Surat Kuasa', route: 'superadmin.laporan-cs.pinjaman.surat-kuasa' },
            { label: 'Surat Pernyataan', route: 'superadmin.laporan-cs.pinjaman.surat-pernyataan' },
            { label: 'Tanda Terima Jaminan', route: 'superadmin.laporan-cs.pinjaman.tanda-terima-jaminan' },
            { label: 'Simulasi Tagihan', route: 'superadmin.laporan-cs.pinjaman.simulasi-tagihan' },
            { label: 'Angsuran Per Anggota', route: 'superadmin.laporan-cs.pinjaman.angsuran-per-anggota' },
            { label: 'Rekapan Pemasukan', route: 'superadmin.laporan-cs.pinjaman.rekapan-pemasukan' },
            { label: 'Jatuh Tempo Angsuran', route: 'superadmin.laporan-cs.pinjaman.jatuh-tempo-angsuran' },
            { label: 'Penilaian Terlambat', route: 'superadmin.laporan-cs.pinjaman.penilaian-terlambat' },
        ],
    },
    {
        label: 'Laporan Simpanan',
        icon: Wallet,
        children: [
            { label: 'Kartu Simpanan (Depan)', route: 'superadmin.laporan-cs.simpanan.kartu-depan' },
            { label: 'Kartu Simpanan (Belakang)', route: 'superadmin.laporan-cs.simpanan.kartu-belakang' },
            { label: 'Kartu Simpanan (Belakang Data)', route: 'superadmin.laporan-cs.simpanan.kartu-belakang-data' },
            { label: 'Pasal Kartu Simpanan', route: 'superadmin.laporan-cs.simpanan.pasal-kartu' },
            { label: 'Rekening Koran', route: 'superadmin.laporan-cs.simpanan.rekening-koran' },
            { label: 'Rekening Koran Kolektif', route: 'superadmin.laporan-cs.simpanan.rekening-koran-kolektif' },
            { label: 'Transaksi Simpanan', route: 'superadmin.laporan-cs.simpanan.transaksi-simpanan' },
            { label: 'Daftar Simpanan', route: 'superadmin.laporan-cs.simpanan.daftar-simpanan' },
            { label: 'Mutasi Simpanan', route: 'superadmin.laporan-cs.simpanan.mutasi-simpanan' },
            { label: 'Mutasi Harian Simpanan', route: 'superadmin.laporan-cs.simpanan.mutasi-harian-simpanan' },
            { label: 'Bagi Hasil Simpanan', route: 'superadmin.laporan-cs.simpanan.bagi-hasil-simpanan' },
            { label: 'Bagi Hasil Simpanan 2', route: 'superadmin.laporan-cs.simpanan.bagi-hasil-simpanan-2' },
            { label: 'Nominatif Simpanan', route: 'superadmin.laporan-cs.simpanan.nominatif-simpanan' },
            { label: 'Nominatif Simpanan Detail', route: 'superadmin.laporan-cs.simpanan.nominatif-simpanan-detail' },
            { label: 'Saldo Simpanan', route: 'superadmin.laporan-cs.simpanan.saldo-simpanan' },
            { label: 'Simpanan Baru', route: 'superadmin.laporan-cs.simpanan.simpanan-baru' },
            { label: 'Penutupan Simpanan', route: 'superadmin.laporan-cs.simpanan.penutupan-simpanan' },
            { label: 'Tunggakan Setoran Wajib', route: 'superadmin.laporan-cs.simpanan.tunggakan-setoran-wajib' },
            { label: 'Simpanan Tidak Aktif', route: 'superadmin.laporan-cs.simpanan.tidak-aktif' },
            { label: 'Simpanan Jatuh Tempo', route: 'superadmin.laporan-cs.simpanan.jatuh-tempo-simpanan' },
            { label: 'Rekapitulasi Produk', route: 'superadmin.laporan-cs.simpanan.rekapitulasi-produk' },
            { label: 'Rekapitulasi Grafik', route: 'superadmin.laporan-cs.simpanan.rekapitulasi-grafik' },
            { label: 'Rekapitulasi Bagi Hasil', route: 'superadmin.laporan-cs.simpanan.rekapitulasi-bagi-hasil' },
        ],
    },
    {
        label: 'Laporan Simpanan Berjangka',
        icon: Landmark,
        children: [
            { label: 'Daftar Simpanan Berjangka', route: 'superadmin.laporan-cs.simpanan-berjangka.daftar-berjangka' },
            { label: 'Bilyet Simpanan Berjangka', route: 'superadmin.laporan-cs.simpanan-berjangka.bilyet-berjangka' },
            { label: 'Kartu Simpanan Berjangka', route: 'superadmin.laporan-cs.simpanan-berjangka.kartu-berjangka' },
            { label: 'Konfirmasi Perubahan Bagi Hasil', route: 'superadmin.laporan-cs.simpanan-berjangka.konfirmasi-perubahan-bagi-hasil' },
            { label: 'Simpanan Berjangka Baru', route: 'superadmin.laporan-cs.simpanan-berjangka.simpanan-berjangka-baru' },
            { label: 'Pencairan Simpanan Berjangka', route: 'superadmin.laporan-cs.simpanan-berjangka.pencairan-berjangka' },
            { label: 'Bagi Hasil Simpanan Berjangka', route: 'superadmin.laporan-cs.simpanan-berjangka.bagi-hasil-berjangka' },
            { label: 'Bagi Hasil Simpanan Berjangka 2', route: 'superadmin.laporan-cs.simpanan-berjangka.bagi-hasil-berjangka-2' },
            { label: 'Posting Bagi Hasil', route: 'superadmin.laporan-cs.simpanan-berjangka.posting-bagi-hasil' },
            { label: 'Nominatif Simpanan Berjangka', route: 'superadmin.laporan-cs.simpanan-berjangka.nominatif-berjangka' },
            { label: 'Simpanan Berjangka Jatuh Tempo', route: 'superadmin.laporan-cs.simpanan-berjangka.jatuh-tempo-berjangka' },
            { label: 'Rekapitulasi Bagi Hasil Berjangka', route: 'superadmin.laporan-cs.simpanan-berjangka.rekapitulasi-bagi-hasil' },
        ],
    },
    { header: 'Front Office' },
    {
        label: 'Transaksi Simpanan',
        icon: ArrowDownUp,
        children: [
            { label: 'Setoran Simpanan', route: 'superadmin.transaksi-simpanan.setoran-simpanan' },
            { label: 'Tarikan Simpanan', route: 'superadmin.transaksi-simpanan.tarikan-simpanan' },
            { label: 'Pemindahbukuan', route: 'superadmin.transaksi-simpanan.pemindahbukuan-simpanan' },
        ],
    },
    {
        label: 'Penarikan Dana Titipan',
        route: 'superadmin.transaksi-titipan.penarikan-dana-titipan',
        icon: Banknote,
    },
    {
        label: 'Laporan',
        icon: Receipt,
        children: [
            { label: 'Laporan Kas Harian', route: 'superadmin.laporan.laporan-kas-harian' },
            { label: 'Laporan Transaksi Simpanan', route: 'superadmin.laporan.laporan-transaksi-simpanan' },
            { label: 'Laporan Transaksi Pinjaman', route: 'superadmin.laporan.laporan-transaksi-pinjaman' },
        ],
    },
    {
        label: 'Transaksi Simpanan Berjangka',
        icon: CalendarClock,
        children: [
            { label: 'Setoran Berjangka', route: 'superadmin.transaksi-simpanan-berjangka.setoran-simpanan-berjangka' },
            { label: 'Pencairan Berjangka', route: 'superadmin.transaksi-simpanan-berjangka.pencairan-simpanan-berjangka' },
            { label: 'Penalti Berjangka', route: 'superadmin.transaksi-simpanan-berjangka.penalti-simpanan-berjangka' },
        ],
    },
    {
        label: 'Transaksi Pinjaman',
        icon: HandCoins,
        children: [
            { label: 'Pencairan Pinjaman', route: 'superadmin.pencairan-pinjaman' },
            { label: 'Penalti Pinjaman', route: 'superadmin.transaksi-pinjaman.penalti-pinjaman' },
            { label: 'Penalti Kolektif Tunai', route: 'superadmin.transaksi-pinjaman.penalti-kolektif-tunai' },
            { label: 'Angsuran Pinjaman', route: 'superadmin.transaksi-pinjaman.angsuran-pinjaman' },
            { label: 'Angsuran Kolektif Debet Simpanan', route: 'superadmin.transaksi-pinjaman.angsuran-kolektif-debet-simpanan' },
            { label: 'Angsuran Kolektif Tunai', route: 'superadmin.transaksi-pinjaman.angsuran-kolektif-tunai' },
            { label: 'Setoran & Angsuran Bank', route: 'superadmin.transaksi-pinjaman.setoran-angsuran-bank' },
            { label: 'Setoran & Angsuran Custom', route: 'superadmin.transaksi-pinjaman.setoran-angsuran-custom' },
        ],
    },
    {
        label: 'Setting',
        icon: Settings,
        children: [
            { label: 'Kantor', route: 'superadmin.kantor' },
            { label: 'Marketing', route: 'superadmin.marketing' },
        ],
    },
];

/* ============================================================
   Item sidebar
   ============================================================ */
function NavLink({ item, active }: { item: MenuChild; active: boolean }) {
    return (
        <Link
            href={route(item.route)}
            className={`group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 ${
                active
                    ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30'
                    : 'text-sidebar-foreground hover:bg-white/5 hover:text-white'
            }`}
        >
            <span className={`size-4 shrink-0 transition ${active ? '' : 'text-slate-500 group-hover:text-brand-400'}`}>
                <span className="block size-1.5 rounded-full bg-current" />
            </span>
            <span className="truncate">{item.label}</span>
        </Link>
    );
}

function NavGroup({ item }: { item: MenuParent }) {
    const anyActive = item.children.some((c) => route().current(c.route) || route().current(c.route + '.*'));
    const [expanded, setExpanded] = useState(anyActive);
    const Icon = item.icon;

    return (
        <div>
            <button
                type="button"
                onClick={() => setExpanded((v) => !v)}
                className={`group flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 ${
                    anyActive ? 'text-white' : 'text-sidebar-foreground hover:bg-white/5 hover:text-white'
                }`}
            >
                <Icon className={`size-4 shrink-0 transition ${anyActive ? 'text-brand-400' : 'text-slate-500 group-hover:text-brand-400'}`} />
                <span className="flex-1 truncate text-left">{item.label}</span>
                <ChevronRight className={`size-3.5 shrink-0 text-slate-500 transition-transform duration-300 ${expanded ? 'rotate-90' : ''}`} />
            </button>

            <div className={`grid transition-[grid-template-rows] duration-300 ease-in-out ${expanded ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'}`}>
                <div className="overflow-hidden">
                    <div className="ml-5 mt-1 space-y-0.5 border-l border-white/10 pl-3">
                        {item.children.map((child) => (
                            <NavLink
                                key={child.route}
                                item={child}
                                active={
                                    ['superadmin.simpanan', 'superadmin.simpanan-berjangka'].includes(child.route)
                                        ? route().current(child.route) && !route().current(child.route + '.*')
                                        : route().current(child.route) || route().current(child.route + '.*')
                                }
                            />
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}

function SidebarContent() {
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
            <nav className="flex-1 space-y-0.5 overflow-y-auto overflow-x-hidden px-3 py-4 sidebar-scroll">
                {MENU.map((item, i) => {
                    if ('header' in item) {
                        return (
                            <p key={i} className="px-3 pt-4 pb-1.5 text-[11px] font-semibold uppercase tracking-widest text-slate-600">
                                {item.header}
                            </p>
                        );
                    }
                    if ('children' in item) {
                        return <NavGroup key={item.label} item={item} />;
                    }
                    return (
                        <NavLink
                            key={item.route}
                            item={{ label: item.label, route: item.route }}
                            active={route().current(item.route)}
                        />
                    );
                })}
            </nav>

            {/* Footer */}
            <Separator className="bg-white/10" />
            <div className="shrink-0 px-5 py-3">
                <p className="text-[11px] text-slate-600">v2.0 · Laravel 13 · React · TypeScript</p>
            </div>
        </div>
    );
}

/* ============================================================
   Layout utama
   ============================================================ */
export default function AuthenticatedLayout({ children }: { children: React.ReactNode }) {
    const { auth, flash } = usePage().props;
    const [mobileOpen, setMobileOpen] = useState(false);

    const logout = (e: React.MouseEvent) => {
        e.preventDefault();
        router.post(route('logout'));
    };

    return (
        <div className="min-h-screen bg-background">
            {/* Sidebar desktop */}
            <aside className="fixed inset-y-0 left-0 z-40 hidden w-64 bg-night-800 lg:block">
                <SidebarContent />
            </aside>

            {/* Sidebar mobile */}
            {mobileOpen && (
                <div className="fixed inset-0 z-50 lg:hidden">
                    <div className="absolute inset-0 bg-night-900/60 backdrop-blur-sm" onClick={() => setMobileOpen(false)} />
                    <aside className="absolute inset-y-0 left-0 w-64 bg-night-800 shadow-2xl">
                        <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => setMobileOpen(false)}
                            className="absolute top-4 right-3 text-slate-400 hover:bg-white/10 hover:text-white"
                            aria-label="Tutup menu"
                        >
                            <X className="size-5" />
                        </Button>
                        <SidebarContent />
                    </aside>
                </div>
            )}

            <div className="lg:pl-64">
                {/* Navbar */}
                <header className="sticky top-0 z-30 flex h-16 items-center justify-between border-b bg-card/80 px-4 backdrop-blur-md sm:px-6">
                    <div className="flex items-center gap-3">
                        <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => setMobileOpen(true)}
                            className="lg:hidden"
                            aria-label="Buka menu"
                        >
                            <Menu className="size-5" />
                        </Button>
                        <Badge variant="secondary" className="hidden sm:inline-flex">
                            {new Date().toLocaleDateString('id-ID', {
                                weekday: 'long',
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric',
                            })}
                        </Badge>
                    </div>

                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" className="gap-3 rounded-full py-1.5 pl-1.5">
                                <Avatar className="size-9">
                                    <AvatarFallback className="bg-gradient-to-br from-brand-500 to-brand-700 text-sm font-bold text-white">
                                        {(auth.user?.nama ?? 'A').charAt(0).toUpperCase()}
                                    </AvatarFallback>
                                </Avatar>
                                <span className="hidden text-left sm:block">
                                    <span className="block text-sm font-semibold">{auth.user?.nama}</span>
                                    <span className="block text-xs capitalize text-muted-foreground">{auth.user?.role}</span>
                                </span>
                                <ChevronRight className="size-4 rotate-90 text-muted-foreground" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" className="w-56">
                            <DropdownMenuLabel>
                                <p className="truncate">{auth.user?.email}</p>
                                <p className="text-xs font-normal capitalize text-muted-foreground">{auth.user?.role}</p>
                            </DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem variant="destructive" onClick={logout}>
                                <LogOut />
                                Keluar
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </header>

                {/* Flash toast */}
                {(flash?.status || flash?.error) && (
                    <div className="animate-in fade-in slide-in-from-top-2 fixed top-20 right-6 z-50">
                        <div
                            className={`rounded-xl px-5 py-3.5 text-sm font-medium text-white shadow-2xl ${
                                flash.error ? 'bg-destructive' : 'bg-emerald-600'
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
