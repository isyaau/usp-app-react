/**
 * Loan Calculation — TypeScript mirror.
 *
 * Formula identik dengan App\Services\LoanCalculationService (PHP).
 * Dipakai SEMATA-MATA untuk live preview di form; hasil final & validasi
 * tetap dihitung ulang secara authoritative oleh service PHP saat simpan.
 */

export type LoanMethod =
    | 'Anuitas'
    | 'Flat'
    | 'Flat Efektif'
    | 'Pokok Tetap'
    | 'Bagi Hasil Menurun';

export type LoanSatuan = 'hari' | 'minggu' | 'bulan' | 'tahun';

export interface LoanScheduleRow {
    ke: number;
    pokok: number;
    bunga: number;
    angsuran: number;
    sisa: number;
}

export interface LoanResult {
    nominal_angsuran: number;
    total_bunga: number;
    jumlah_periode: number;
    metode: LoanMethod;
    jadwal: LoanScheduleRow[];
}

const PERIODE_PER_TAHUN: Record<LoanSatuan, number> = {
    hari: 360,
    minggu: 48,
    bulan: 12,
    tahun: 1,
};

export function normalizeMethod(metode?: string | null): LoanMethod {
    const m = (metode ?? '').toLowerCase().trim();
    if (m.includes('anuit')) return 'Anuitas';
    if (m.includes('efektif') || m.includes('effektif')) return 'Flat Efektif';
    if (m.includes('pokok tetap')) return 'Pokok Tetap';
    if (m.includes('bagi hasil')) return 'Bagi Hasil Menurun';
    return 'Flat';
}

export function calculateLoan(input: {
    plafon: number;
    bunga: number;
    jangka_waktu: number;
    satuan: LoanSatuan;
    metode?: string | null;
}): LoanResult {
    const plafon = Number(input.plafon) || 0;
    const bungaTahunan = Number(input.bunga) || 0;
    const jangka = Math.max(1, Number(input.jangka_waktu) || 0);
    const satuan: LoanSatuan = input.satuan || 'bulan';
    const metode = normalizeMethod(input.metode);

    const periodePerTahun = PERIODE_PER_TAHUN[satuan] ?? 12;
    const jumlahPeriode = Math.max(1, jangka);

    const rate = bungaTahunan / 100 / periodePerTahun;

    const jadwal = buildJadwal(plafon, rate, jumlahPeriode, metode);

    const nominalAngsuran = jadwal.length ? round2(jadwal[0].angsuran) : 0;
    const totalBunga = jadwal.reduce((s, r) => s + r.bunga, 0);

    return {
        nominal_angsuran: round2(nominalAngsuran),
        total_bunga: round2(totalBunga),
        jumlah_periode: jumlahPeriode,
        metode,
        jadwal,
    };
}

function round2(n: number): number {
    return Math.round((n + Number.EPSILON) * 100) / 100;
}

function buildJadwal(
    plafon: number,
    rate: number,
    periode: number,
    metode: LoanMethod,
): LoanScheduleRow[] {
    if (plafon <= 0) return [];
    switch (metode) {
        case 'Anuitas':
            return jadwalAnuitas(plafon, rate, periode);
        case 'Flat':
            return jadwalFlat(plafon, rate, periode);
        case 'Flat Efektif':
        case 'Pokok Tetap':
        case 'Bagi Hasil Menurun':
            return jadwalBagiHasilMenurun(plafon, rate, periode);
        default:
            return jadwalFlat(plafon, rate, periode);
    }
}

function jadwalAnuitas(plafon: number, rate: number, periode: number): LoanScheduleRow[] {
    let angsuran =
        rate > 0
            ? (plafon * rate * Math.pow(1 + rate, periode)) /
              (Math.pow(1 + rate, periode) - 1)
            : plafon / periode;
    let sisa = plafon;
    const hasil: LoanScheduleRow[] = [];
    for (let i = 1; i <= periode; i++) {
        const bunga = sisa * rate;
        let pokok = angsuran - bunga;
        if (i === periode) {
            pokok = sisa;
            angsuran = sisa + bunga;
        }
        sisa -= pokok;
        hasil.push({
            ke: i,
            pokok: round2(Math.max(0, pokok)),
            bunga: round2(Math.max(0, bunga)),
            angsuran: round2(Math.max(0, angsuran)),
            sisa: round2(Math.max(0, sisa)),
        });
    }
    return hasil;
}

function jadwalFlat(plafon: number, rate: number, periode: number): LoanScheduleRow[] {
    const pokok = plafon / periode;
    const bunga = plafon * rate;
    const angsuran = pokok + bunga;
    let sisa = plafon;
    const hasil: LoanScheduleRow[] = [];
    for (let i = 1; i <= periode; i++) {
        sisa -= pokok;
        hasil.push({
            ke: i,
            pokok: round2(pokok),
            bunga: round2(bunga),
            angsuran: round2(angsuran),
            sisa: round2(Math.max(0, sisa)),
        });
    }
    return hasil;
}

function jadwalBagiHasilMenurun(plafon: number, rate: number, periode: number): LoanScheduleRow[] {
    const pokok = plafon / periode;
    let sisa = plafon;
    const hasil: LoanScheduleRow[] = [];
    for (let i = 1; i <= periode; i++) {
        const bunga = sisa * rate;
        const angsuran = pokok + bunga;
        sisa -= pokok;
        hasil.push({
            ke: i,
            pokok: round2(pokok),
            bunga: round2(bunga),
            angsuran: round2(angsuran),
            sisa: round2(Math.max(0, sisa)),
        });
    }
    return hasil;
}
