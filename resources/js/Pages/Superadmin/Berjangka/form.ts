import type { AccountMini } from '@/types/models';

/** Nilai form rekening simpanan berjangka. */
export interface DepositoFormValues {
    tanggal: string;
    anggota_id: string;
    jenis_id: string;
    marketing_id: string;
    qq: string;
    jangka_waktu: string;
    bunga: string;
    nominal: string;
    otomatis: boolean;
    bayar_bunga: string;
    diawal: string;
    bunga_accrual: boolean;
    account_bungaaccrual: string;
    tabunganbunga_id: string;
    tabungantempo_id: string;
    bayar_jatuhtempo: string;
    blokir: boolean;
    kantor_id: string;
}

export function emptyDepositoForm(tanggal = ''): DepositoFormValues {
    return {
        tanggal,
        anggota_id: '',
        jenis_id: '',
        marketing_id: '',
        qq: '',
        jangka_waktu: '',
        bunga: '',
        nominal: '',
        otomatis: false,
        bayar_bunga: '1',
        diawal: '1',
        bunga_accrual: false,
        account_bungaaccrual: '',
        tabunganbunga_id: '',
        tabungantempo_id: '',
        bayar_jatuhtempo: '1',
        blokir: false,
        kantor_id: '',
    };
}

/** Opsi pendukung form deposito yang dikirim controller. */
export interface DepositoFormOptions {
    anggotaOptions: { id: number; no_anggota: string; nama: string }[];
    produkOptions: {
        id: number;
        kode: string;
        nama: string;
        jangka_waktu: string | null;
        bunga: string | null;
    }[];
    marketingOptions: { id: number; nama: string }[];
    kantorOptions: { id: number; nama_kantor: string }[];
    accountOptions: AccountMini[];
    simpananOptions: {
        id: number;
        no_rekening: string;
        anggota?: { nama: string } | null;
    }[];
}

/** Label pilihan cara pembayaran (kolom `diawal`). */
export const LIST_PEMBAYARAN: Record<string, string> = {
    '1': 'Tiap Bulan',
    '2': 'Diawal',
    '3': 'Diakhir',
};
