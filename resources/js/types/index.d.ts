export interface DashboardTotals {
    totalAnggota: number;
    totalKelompok: number;
    totalKantor: number;
    totalUsers: number;
    totalAccgroup: number;
    totalAccheader: number;
    totalAccount: number;
}

export interface DashboardKpi {
    pinjamanAktif: number;
    totalPlafon: number;
    totalPokokTerbayar: number;
    totalSisaPokok: number;
    jumlahRekeningSimpanan: number;
    totalSimpanan: number;
    jumlahDeposito: number;
    totalDeposito: number;
    setoranBulanIni: number;
    tarikanBulanIni: number;
    angsuranBulanIni: number;
    anggotaBaruBulanIni: number;
}

export interface DashboardRecent {
    pinjaman: {
        id: number;
        no_pinjaman: string;
        tanggal: string;
        plafon: number;
        aktif: boolean;
        anggota: string | null;
    }[];
    setoran: {
        id: number;
        no_transaksi: string;
        tgl_transaksi: string;
        nominal: number;
        anggota: string | null;
    }[];
    angsuran: {
        id: number;
        no_transaksi: string;
        tgl_transaksi: string;
        total_angsuran: number;
        no_pinjaman: string | null;
        anggota: string | null;
    }[];
    anggotaBaru: {
        id: number;
        no_anggota: string;
        nama: string;
        tgl: string;
    }[];
}

export interface DashboardRecapItem {
    label: string;
    count: number;
    nominal?: number;
    money?: boolean;
}

export interface DashboardRecapSection {
    label: string;
    items: DashboardRecapItem[];
}

export interface DashboardCashMonth {
    bulan: string;
    setoran: number;
    tarikan: number;
    angsuran: number;
}

export interface DashboardAnggotaMonth {
    bulan: string;
    baru: number;
}

export interface DashboardDistItem {
    label: string;
    jumlah: number;
    nominal: number;
}

export interface DashboardCharts {
    kasBulanan: DashboardCashMonth[];
    anggotaBulanan: DashboardAnggotaMonth[];
    pinjamanPerProduk: DashboardDistItem[];
    simpananPerJenis: DashboardDistItem[];
}

export interface DashboardProps {
    totals: DashboardTotals;
    kpi: DashboardKpi;
    recent: DashboardRecent;
    recap: DashboardRecapSection[];
    charts: DashboardCharts;
}
