/* ============================================================
   Tipe entitas modul Master Data (C2)
   ============================================================ */

export interface PaginatedLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface Paginated<T> {
    data: T[];
    current_page: number;
    first_page_url: string | null;
    from: number | null;
    last_page: number;
    last_page_url: string | null;
    links: PaginatedLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}

export type Role = 'superadmin' | 'admin' | 'user';

export interface UserRow {
    id: number;
    nama: string;
    email: string;
    username: string;
    role: Role;
    avatar: string | null;
    created_at: string;
}

export interface KelompokRow {
    id: number;
    kode: string;
    nama: string;
    ketua_id: number | null;
    ketua?: { id: number; nama: string } | null;
}

export interface KantorRow {
    id: number;
    kode: string;
    nama_kantor: string;
    alamat_kantor: string;
    provinsi_id: string | null;
    kota_id: string | null;
    kecamatan_id: string | null;
    kelurahan_id: string | null;
    pejabat: string;
    jabatan: string;
    bendahara: string;
    provinsi?: { name: string } | null;
    kota?: { name: string } | null;
    kecamatan?: { name: string } | null;
    kelurahan?: { name: string } | null;
}

export interface MarketingRow {
    id: number;
    kode: string;
    nama: string;
    alamat: string;
    no_ktp: string;
    telepon: string | null;
    no_hp: string | null;
    aktif: boolean;
    kantor_id: number;
    kantor?: { id: number; nama_kantor: string } | null;
}

export interface AccGroupOption {
    id: number;
    nama: string;
}

export interface AccHeaderRow {
    id: number;
    no_header: string;
    nama: string;
    keterangan: string;
    jenis: string;
    group_id: number;
    group?: { id: number; nama: string } | null;
}

export interface AccountRow {
    id: number;
    no_account: string;
    nama: string;
    tipe: 'Debet' | 'Kredit';
    header_id: number;
    header?: { id: number; nama: string; no_header: string } | null;
}

export interface AnggotaRow {
    id: number;
    no_anggota: string;
    nama: string;
    pin: string | null;
    kelompok_id: number;
    kantor_id: number;
    alamat: string;
    provinsi_id: string | null;
    kota_id: string | null;
    kecamatan_id: string | null;
    kelurahan_id: string | null;
    email: string | null;
    tempat_lahir: string | null;
    tgl_lahir: string | null;
    jenis_kelamin: 'L' | 'P' | null;
    agama: string | null;
    pekerjaan: string | null;
    pendidikan: string | null;
    status_perkawinan: string | null;
    pasangan: string | null;
    telepon: string | null;
    no_hp: string | null;
    jenis_identitas: string | null;
    no_identitas: string | null;
    npwp: string | null;
    ibu: string | null;
    waris1: string | null;
    hubungan_waris1: string | null;
    waris2: string | null;
    hubungan_waris2: string | null;
    status: string;
    kelompok?: { id: number; nama: string } | null;
    kantor?: { id: number; nama_kantor: string } | null;
}

export type KodeFlag =
    | 'setoran'
    | 'tarikan'
    | 'transfer'
    | 'pokok'
    | 'wajib'
    | 'sukarela'
    | 'pinjaman'
    | 'saham'
    | 'pokok_pinjaman'
    | 'rencana';

export const KODE_FLAG_LABELS: Record<KodeFlag, string> = {
    setoran: 'Setoran',
    tarikan: 'Tarikan',
    transfer: 'Transfer',
    pokok: 'Simpanan Pokok',
    wajib: 'Simpanan Wajib',
    sukarela: 'Simpanan Sukarela',
    pinjaman: 'Pinjaman',
    saham: 'Saham',
    pokok_pinjaman: 'Pokok Pinjaman',
    rencana: 'Rencana',
};

export interface AccountMini {
    id: number;
    no_account: string;
    nama: string;
}

export type SimpananKodeRow = {
    id: number;
    kode: string;
    nama: string;
    account_debet: number;
    account_kredit: number;
    keterangan: string | null;
} & Record<KodeFlag, boolean> &
    Partial<{
        debetAccount: AccountMini | null;
        kreditAccount: AccountMini | null;
    }>;

export interface Wilayah {
    code: string;
    name: string;
}
