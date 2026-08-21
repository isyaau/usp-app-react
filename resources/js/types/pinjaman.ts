/* ============================================================
   Tipe Produk Pinjaman (pinj_jenis)
   ============================================================ */

export interface PinjamanKomponenRow {
    id?: number;
    nama: string;
    nominal: number | null;
    persen: boolean;
    account_id: number | string;
    cair: boolean;
    angsuran: boolean;
    penalti: boolean;
    rumus_c?: string;
    rumus_a?: string;
    rumus_p?: string;
}

export interface PinjamanKolektabilitasRow {
    id?: number;
    kualitas_id: number;
    keterangan: string | null;
}

export interface PinjamanProdukRow {
    id: number;
    kode: string;
    nama: string;
    account_id: number;
    bunga: number | string;
    account_bunga: number;
    ditangguhkan: boolean;
    account_ditangguhkan: number | null;
    kas: number | string | null;
    account_bank: number | null;
    insentif: number | string;
    simpanan: boolean;
    swp_cair: boolean;
    swp_angsur: boolean;
    swp_persen: boolean;
    nominal_simpanan: number | string | null;
    simpanan_pokok: boolean;
    nominal_simpanan_pokok: number | string | null;
    toleransi: number;
    angsuran: string;
    account?: { id: number; no_account: string; nama: string } | null;
    kolektabilitas?: PinjamanKolektabilitasRow[];
    komponen?: PinjamanKomponenRow[];
}
