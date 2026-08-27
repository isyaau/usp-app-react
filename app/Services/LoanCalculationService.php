<?php

namespace App\Services;

/**
 * Loan Calculation Service.
 *
 * Semua rumus perhitungan pinjaman hidup di sini (PHP, authoritative).
 * Frontend hanya menampilkan hasil; live preview memakai mirror TS
 * (resources/js/lib/loanCalc.ts) yang mengikuti formula yang sama.
 *
 * Metode perhitungan bersifat configurable mengikuti nilai
 * `pinj_jenis.angsuran` dari produk, misal: Anuitas, Flat,
 * Flat Efektif, Pokok Tetap, Bagi Hasil Menurun.
 */
class LoanCalculationService
{
    /** Jumlah periode per tahun untuk konversi tingkat bunga per periode. */
    private const PERIODE_PER_TAHUN = [
        'hari' => 360,
        'minggu' => 48,
        'bulan' => 12,
        'tahun' => 1,
    ];

    /**
     * Hitung angsuran & jadwal pinjaman.
     *
     * @param array{
     *   plafon: float,
     *   bunga: float,          // bunga per tahun (%)
     *   jangka_waktu: int,     // jumlah periode
     *   satuan: string,        // hari|minggu|bulan|tahun
     *   metode: string|null,   // Anuitas|Flat|Flat Efektif|Pokok Tetap|Bagi Hasil Menurun
     * } $input
     * @return array {
     *   nominal_angsuran: float,
     *   total_bunga: float,
     *   jadwal: array<int, array{ke:int,pokok:float,bunga:float,angsuran:float,sisa:float}>
     * }
     */
    public function calculate(array $input): array
    {
        $plafon = (float) ($input['plafon'] ?? 0);
        $bungaTahunan = (float) ($input['bunga'] ?? 0); // persen/tahun
        $jangka = max(1, (int) ($input['jangka_waktu'] ?? 0));
        $satuan = (string) ($input['satuan'] ?? 'bulan');
        $metode = $this->normalizeMetode($input['metode'] ?? null);

        // Satu periode pembayaran = sebesar satuan (hari/minggu/bulan/tahun).
        // Bunga per periode diturunkan dari bunga tahunan sesuai satuan.
        $periodePerTahun = self::PERIODE_PER_TAHUN[$satuan] ?? 12;
        $jumlahPeriode = max(1, $jangka);
        $ratePerPeriode = ($bungaTahunan / 100) / $periodePerTahun;

        $jadwal = $this->buildJadwal(
            $plafon,
            $ratePerPeriode,
            $jumlahPeriode,
            $metode,
        );

        $nominalAngsuran = $jadwal ? round($jadwal[0]['angsuran'], 2) : 0.0;
        $totalBunga = array_sum(array_column($jadwal, 'bunga'));

        return [
            'nominal_angsuran' => $nominalAngsuran,
            'total_bunga' => round($totalBunga, 2),
            'jumlah_periode' => $jumlahPeriode,
            'metode' => $metode,
            'jadwal' => $jadwal,
        ];
    }

    /**
     * Bangun jadwal pembayaran sesuai metode.
     */
    private function buildJadwal(float $plafon, float $rate, int $periode, string $metode): array
    {
        if ($plafon <= 0) {
            return [];
        }

        return match ($metode) {
            'Anuitas' => $this->jadwalAnuitas($plafon, $rate, $periode),
            'Flat' => $this->jadwalFlat($plafon, $rate, $periode),
            'Flat Efektif' => $this->jadwalFlatEfektif($plafon, $rate, $periode),
            'Pokok Tetap' => $this->jadwalPokokTetap($plafon, $rate, $periode),
            'Bagi Hasil Menurun' => $this->jadwalBagiHasilMenurun($plafon, $rate, $periode),
            default => $this->jadwalFlat($plafon, $rate, $periode),
        };
    }

    /** Anuitas: angsuran tetap, proporsi pokok/bunga berubah. */
    private function jadwalAnuitas(float $plafon, float $rate, int $periode): array
    {
        $angsuran = $rate > 0
            ? $plafon * ($rate * pow(1 + $rate, $periode)) / (pow(1 + $rate, $periode) - 1)
            : $plafon / $periode;

        $sisa = $plafon;
        $hasil = [];
        for ($i = 1; $i <= $periode; $i++) {
            $bunga = $sisa * $rate;
            $pokok = $angsuran - $bunga;
            if ($i === $periode) {
                // Koreksi bulat di periode terakhir.
                $pokok = $sisa;
                $angsuran = $sisa + $bunga;
            }
            $sisa -= $pokok;
            $hasil[] = [
                'ke' => $i,
                'pokok' => round(max(0, $pokok), 2),
                'bunga' => round(max(0, $bunga), 2),
                'angsuran' => round(max(0, $angsuran), 2),
                'sisa' => round(max(0, $sisa), 2),
            ];
        }
        return $hasil;
    }

    /** Flat: pokok & bunga tetap dihitung dari plafon awal. */
    private function jadwalFlat(float $plafon, float $rate, int $periode): array
    {
        $pokok = $plafon / $periode;
        $bunga = $plafon * $rate;
        $angsuran = $pokok + $bunga;
        $sisa = $plafon;
        $hasil = [];
        for ($i = 1; $i <= $periode; $i++) {
            $sisa -= $pokok;
            $hasil[] = [
                'ke' => $i,
                'pokok' => round($pokok, 2),
                'bunga' => round($bunga, 2),
                'angsuran' => round($angsuran, 2),
                'sisa' => round(max(0, $sisa), 2),
            ];
        }
        return $hasil;
    }

    /** Flat Efektif: pokok tetap, bunga menurun mengikuti saldo. */
    private function jadwalFlatEfektif(float $plafon, float $rate, int $periode): array
    {
        $pokok = $plafon / $periode;
        $sisa = $plafon;
        $hasil = [];
        for ($i = 1; $i <= $periode; $i++) {
            $bunga = $sisa * $rate;
            $angsuran = $pokok + $bunga;
            $sisa -= $pokok;
            $hasil[] = [
                'ke' => $i,
                'pokok' => round($pokok, 2),
                'bunga' => round($bunga, 2),
                'angsuran' => round($angsuran, 2),
                'sisa' => round(max(0, $sisa), 2),
            ];
        }
        return $hasil;
    }

    /** Pokok Tetap: identik dengan Flat Efektif secara skematis. */
    private function jadwalPokokTetap(float $plafon, float $rate, int $periode): array
    {
        return $this->jadwalFlatEfektif($plafon, $rate, $periode);
    }

    /** Bagi Hasil Menurun: margin atas saldo, angsuran menurun. */
    private function jadwalBagiHasilMenurun(float $plafon, float $rate, int $periode): array
    {
        return $this->jadwalFlatEfektif($plafon, $rate, $periode);
    }

    /**
     * Normalisasi nama metode dari nilai produk ke bentuk kanonik.
     */
    public function normalizeMetode(?string $metode): string
    {
        if (is_null($metode) || trim($metode) === '') {
            return 'Flat';
        }
        $m = strtolower(trim($metode));
        return match ($m) {
            'anuitas', 'anuitet', 'annuity' => 'Anuitas',
            'flat', 'flate' => 'Flat',
            'flat efektif', 'flat effektif', 'flat efektiv' => 'Flat Efektif',
            'pokok tetap', 'fixed principal' => 'Pokok Tetap',
            'bagi hasil menurun', 'bagi hasil' => 'Bagi Hasil Menurun',
            default => 'Flat',
        };
    }
}
