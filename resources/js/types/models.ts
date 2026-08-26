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
    kelompok_id: number | null;
    kantor_id: number | null;
    alamat: string | null;
    email: string | null;
    telepon: string | null;
    no_hp: string | null;
    status: number;
    foto: string | null;
    kelompok?: { id: number; nama: string } | null;
    kantor?: { id: number; nama_kantor: string } | null;
}

/** Data lengkap untuk halaman Show/Edit. */
export interface AnggotaDetail extends AnggotaRow {
    provinsi_id: string | null;
    kota_id: string | null;
    kecamatan_id: string | null;
    kelurahan_id: string | null;
    tempat_lahir: string | null;
    tgl_lahir: string | null;
    jenis_kelamin: string | null;
    agama: string | null;
    pekerjaan: string | null;
    pendidikan: string | null;
    status_perkawinan: string | null;
    pasangan: string | null;
    jenis_identitas: string | null;
    no_identitas: string | null;
    npwp: string | null;
    ibu: string | null;
    pengurus: number;
    pengurus_jabatan: string | null;
    tgl_pengurus_diangkat: string | null;
    tgl_pengurus_berhenti: string | null;
    pengurus_berhenti: string | null;
    pengawas: number;
    pengawas_jabatan: string | null;
    tgl_pengawas_diangkat: string | null;
    tgl_pengawas_berhenti: string | null;
    pengawas_berhenti: string | null;
    waris1: string | null;
    hubungan_waris1: string | null;
    waris2: string | null;
    hubungan_waris2: string | null;
    tgl_anggota_berhenti: string | null;
    anggota_berhenti: string | null;
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

/* ============================================================
   Tipe entitas modul Transaksi Simpanan (C4)
   ============================================================ */

/** Rekening simpanan ringkas untuk dropdown form transaksi. */
export interface SimpananMini {
    id: number;
    no_rekening: string;
    jenis?: string | null;
}

/** Baris daftar transaksi (setoran/tarikan/penutupan). */
export interface TransaksiSimpananRow {
    id: number;
    no_transaksi: string;
    tgl_transaksi: string;
    anggota_id: number;
    simpanan_id: number;
    kode_transaksi_id: number;
    nominal: string | number;
    keterangan: string | null;
    status: 'draft' | 'posted' | 'batal';
    anggota?: { id: number; nama: string; no_anggota: string } | null;
    simpanan?: { id: number; no_rekening: string } | null;
    kodeTransaksi?: { id: number; kode: string; nama: string } | null;
    kantor?: { id: number; nama_kantor: string } | null;
}

/** Detail penutupan memuat nominal bunga. */
export interface PenutupanSimpananRow extends TransaksiSimpananRow {
    nominal_bunga: string | number;
}

export interface DepositoJenisRow {
    id: number;
    kode: string;
    nama: string;
    account_id: number | null;
    jangka_waktu: string | null;
    bunga: string | null;
    rumus_bunga: string | null;
    penalti: string | null;
    pajak: string | null;
    saldo_pajak: string | null;
    insentif: string | null;
}

export interface DepositoJenisDetail extends DepositoJenisRow {
    account_bunga: number | null;
    account_penalti: number | null;
    account_pajak: number | null;
    account?: AccountMini | null;
    bungaAccount?: AccountMini | null;
    penaltiAccount?: AccountMini | null;
    pajakAccount?: AccountMini | null;
}

/** Rekening simpanan berjangka (tabel deposito). */
export interface DepositoRow {
    id: number;
    tanggal: string;
    no_deposito: string;
    anggota_id: number;
    jenis_id: number;
    jangka_waktu: string;
    bunga: string;
    nominal: string;
    otomatis: string;
    blokir: string;
    anggota?: { id: number; no_anggota: string; nama: string } | null;
    produk?:
        | { id: number; kode: string; nama: string; jangka_waktu: string | null; bunga: string | null }
        | null;
    marketing?: { id: number; nama: string } | null;
    kantor?: { id: number; nama_kantor: string } | null;
}

export interface DepositoDetail extends DepositoRow {
    qq: string | null;
    marketing_id: number | null;
    bayar_bunga: string;
    diawal: string;
    bunga_accrual: string;
    account_bungaaccrual: string | null;
    tabunganbunga_id: number | null;
    tabungantempo_id: number | null;
    bayar_jatuhtempo: string;
    kantor_id: number;
    tabunganBunga?: { id: number; no_rekening: string } | null;
    tabunganTempo?: { id: number; no_rekening: string } | null;
    produk?: DepositoJenisRow | null;
}

/** Baris pemindahbukuan: dua rekening (asal & tujuan). */
export interface PemindahbukuanSimpananRow extends Omit<TransaksiSimpananRow, 'simpanan'> {
    simpanan_dari_id: number;
    simpanan_ke_id: number;
    simpananDari?: { id: number; no_rekening: string } | null;
    simpananKe?: { id: number; no_rekening: string } | null;
}

/* ===================== Data Simpanan (rekening simpanan) ===================== */

/** Opsi produk simpanan untuk dropdown form rekening. */
export interface SimpananJenisOption {
    id: number;
    kode: string | null;
    nama: string | null;
    bunga: string | null;
}

/** Baris daftar rekening simpanan. */
export interface SimpananRow {
    id: number;
    tanggal: string | null;
    no_rekening: string;
    anggota?: { id: number; no_anggota: string; nama: string } | null;
    jenis_simpanan?: { id: number; kode: string | null; nama: string | null } | null;
    marketing?: { id: number; nama: string } | null;
    kantor?: { id: number; nama_kantor: string } | null;
    nominal_setor: string | null;
    bunga: string | null;
    aktif: string | null;
    sms: string | null;
}

/** Detail rekening simpanan untuk halaman show/edit. */
export interface SimpananDetail extends SimpananRow {
    qq: string | null;
    baris: string | null;
    ttd: string | null;
    blokir_simpanan: string | null;
    blokir_nominal: string | null;
    nominal_blokir: string | null;
    blokir_tgl: string | null;
    tgl_blokir: string | null;
    kantor_id: number | null;
    anggota_id: number;
    jenis_id: number;
    marketing_id: number;
}

/** Nilai form rekening simpanan. */
export interface SimpananFormValues {
    tanggal: string;
    no_rekening: string;
    anggota_id: string;
    jenis_id: string;
    marketing_id: string;
    qq: string;
    bunga: string;
    nominal_setor: string;
    aktif: boolean;
    sms: boolean;
    blokir_simpanan: boolean;
    blokir_nominal: boolean;
    nominal_blokir: string;
    blokir_tgl: boolean;
    tgl_blokir: string;
    kantor_id: string;
}

/* ===================== Simpanan Rencana ===================== */

/** Baris daftar rencana simpanan. */
export interface RencanaRow {
    id: number;
    tanggal_mulai: string;
    tanggal_jatuhtempo: string;
    no_bukti: string;
    jangka_waktu: string;
    satuan: string;
    nominal: string;
    bunga: string;
    keterangan: string | null;
    kantor?: { id: number; nama_kantor: string } | null;
    details_count?: number;
}

/** Rekening yang terlibat dalam sebuah rencana. */
export interface RencanaDetailItem {
    id: number;
    simpanan_id: number;
    no_rekening: string;
    anggota_nama: string | null;
}

/** Opsi rekening simpanan untuk pemilih di form rencana. */
export interface RekeningOption {
    id: number;
    no_rekening: string;
    jenis_nama?: string | null;
    anggota_nama?: string | null;
}

/** Nilai form simpanan rencana. */
export interface RencanaFormValues {
    tanggal_mulai: string;
    tanggal_jatuhtempo: string;
    no_bukti: string;
    jangka_waktu: string;
    satuan: string;
    nominal: string;
    bunga: string;
    keterangan: string;
    kantor_id: string;
    simpanan_ids: number[];
}

/* ===================== Data Pinjaman ===================== */

/** Opsi anggota ringkas untuk dropdown pinjaman. */
export interface PinjamanAnggotaOption {
    id: number;
    no_anggota: string;
    nama: string;
}

/** Opsi produk pinjaman ringkas untuk dropdown. */
export interface PinjamanJenisOptionLite {
    id: number;
    nama: string;
}

/** Baris daftar pinjaman. */
export interface PinjamanRow {
    id: number;
    tanggal: string;
    no_pinjaman: string;
    plafon: string;
    bunga: string;
    jangka_waktu: string;
    satuan: string;
    angsuranke: string;
    aktif: string;
    jenisPinjaman?: { id: number; nama: string } | null;
    anggota?: { id: number; no_anggota: string; nama: string } | null;
    kantor?: { id: number; nama_kantor: string } | null;
}

/** Nilai form pinjaman. */
export interface PinjamanFormValues {
    tanggal: string;
    no_pinjaman: string;
    anggota_id: string;
    jenis_id: string;
    plafon: string;
    bunga: string;
    jangka_waktu: string;
    satuan: string;
}

/* ===================== Pencairan Pinjaman ===================== */

/** Opsi pinjaman ringkas untuk dropdown pencairan. */
export interface PinjamanOptionLite {
    id: number;
    no_pinjaman: string;
    plafon: string;
    anggota?: { id: number; no_anggota: string; nama: string } | null;
    jenisPinjaman?: { id: number; nama: string } | null;
}

/** Baris daftar pencairan pinjaman. */
export interface PencairanPinjamanRow {
    id: number;
    pinjaman_id: number;
    tanggal_cair: string;
    nominal_cair: string;
    metode_cair: 'transfer' | 'tunai' | 'cek' | 'giro';
    no_rekening: string | null;
    nama_rekening: string | null;
    bank_id: string | null;
    biaya_admin: string;
    potongan_simpanan: string;
    keterangan: string | null;
    status: 'pending' | 'disetujui' | 'ditolak' | 'dicairkan';
    approved_by: number | null;
    approved_at: string | null;
    cair_oleh: number | null;
    cair_at: string | null;
    created_by: number | null;
    kantor_id: number | null;
    created_at: string;
    updated_at: string;
    pinjaman?: {
        id: number;
        no_pinjaman: string;
        plafon: string;
        anggota?: { id: number; no_anggota: string; nama: string } | null;
        jenisPinjaman?: { id: number; nama: string } | null;
    } | null;
    approvedBy?: { id: number; name: string } | null;
    cairOleh?: { id: number; name: string } | null;
    createdBy?: { id: number; name: string } | null;
    kantor?: { id: number; nama_kantor: string } | null;
}

/** Nilai form pencairan pinjaman. */
export interface PencairanPinjamanFormValues {
    pinjaman_id: string;
    tanggal_cair: string;
    nominal_cair: string;
    metode_cair: 'transfer' | 'tunai' | 'cek' | 'giro';
    no_rekening: string;
    nama_rekening: string;
    bank_id: string;
    biaya_admin: string;
    potongan_simpanan: string;
    keterangan: string;
    status: 'pending' | 'disetujui' | 'ditolak' | 'dicairkan';
}
