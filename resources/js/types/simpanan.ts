/* ============================================================
   Tipe Produk Simpanan (simpanan_jenis)
   ============================================================ */

export interface SimpananKodeOption {
    id: number;
    kode: string;
    nama: string;
    account_debet?: number | string | null;
    account_kredit?: number | string | null;
}

export interface SimpananJenisKodeOption {
    id: number;
    kode: string;
    nama: string;
    account_debet?: number | string | null;
    account_kredit?: number | string | null;
    debet_account?: { id: number; no_account: string; nama: string } | null;
    kredit_account?: { id: number; no_account: string; nama: string } | null;
}

export interface SimpananKodeRef {
    id?: number | null;
    kode: string;
    nama: string;
}

export interface SimpananBungaRow {
    id?: number;
    minimal: number | string | null;
    maksimal: number | string | null;
    bunga: number | string;
}

export interface SimpananProdukRow {
    id: number;
    kode: string;
    nama: string;
    account_id: number;
    minimum: number | string | null;
    mengendap: number | string | null;
    bunga_id: number | null;
    jenis_bunga: number; // 1 = Flat, 2 = Bertingkat
    bunga: number | string | null;
    account_bunga: number | null;
    rumus_bunga: number | null; // 1..3
    bulan: boolean;
    biaya_id: number | null;
    biaya: number | string | null;
    account_biaya: number | null;
    pajak_id: number | null;
    pajak: number | string | null;
    account_pajak: number | null;
    saldo_pajak: boolean;
    pajak_saldo: number | string | null;
    android: number | null;
    nominal_android: number | string | null;
    account_android: number | null;
    nominal: number | string | null;
    harga_saham: number | string | null;
    jenis: number; // 1..7 (lihat JENIS_LABELS)
    setor_id: number | null;
    tarik_id: number | null;
    insentif: number | string | null;
    saham: boolean;
    update_bagi_hasil: boolean;
    id_account?: { id: number; no_account: string; nama: string } | null;
    tingkat?: SimpananBungaRow[];
    simpanan_kodes?: SimpananJenisKodeOption[];
    bunga_kode?: SimpananKodeRef | null;
    biaya_kode?: SimpananKodeRef | null;
    pajak_kode?: SimpananKodeRef | null;
    android_kode?: SimpananKodeRef | null;
    setor_kode?: SimpananKodeRef | null;
    tarik_kode?: SimpananKodeRef | null;
}

export const JENIS_SIMPANAN_LABELS: Record<number, string> = {
    1: 'Pokok',
    2: 'Wajib',
    3: 'Sukarela',
    4: 'Wajib Pinjaman',
    5: 'Saham',
    6: 'Pokok Pinjaman',
    7: 'Rencana',
};

export const RUMUS_BUNGA_OPTIONS: Array<{ value: number; label: string }> = [
    { value: 1, label: 'Saldo Terendah' },
    { value: 2, label: 'Saldo Harian' },
    { value: 3, label: 'Saldo Rata-rata' },
];
