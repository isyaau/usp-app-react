export interface DashboardTotals {
    totalAnggota: number;
    totalKelompok: number;
    totalKantor: number;
    totalUsers: number;
    totalAccgroup: number;
    totalAccheader: number;
    totalAccount: number;
}

export interface DashboardProps {
    totals: DashboardTotals;
}
