<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\SimpananJenis;
use App\Models\SimpananJenisKode;
use App\Models\SimpananKode;

/**
 * Seeder Produk Simpanan (simpanan_jenis).
 *
 * Menyesuaikan struktur form terbaru:
 *  - jenis        : integer 1..7 (Pokok, Wajib, Sukarela, Wajib Pinjaman,
 *                   Saham, Pokok Pinjaman, Rencana)
 *  - jenis_bunga  : integer 1 = Tidak Bertingkat, 2 = Bertingkat
 *  - rumus_bunga  : integer 1 = Saldo Terendah, 2 = Saldo Harian, 3 = Saldo Rata-rata
 *  - bulan        : boolean (opsi "1 Bulan" hanya utk Saldo Terendah)
 *  - mengendap    : satuan bulan
 *  - kolom baru   : harga_saham, pajak_saldo, update_bagi_hasil
 *  - transaksi    : baris simpanan_jenis_kode (kode transaksi terkait)
 */
class SimpananJenisSeeder extends Seeder
{
    use ResolvesAdminUser;

    public function run(): void
    {
        $user = $this->adminUserId();

        $products = [
            [
                'kode' => 'SP',
                'nama' => 'Simpanan Pokok',
                'jenis' => 1,
                'minimal' => '100000',
                'mengendap' => '1',
                'bunga' => '0',
                'rumus' => 1, // Saldo Terendah
                'bulan' => true,
                'account' => '800-02',
                'kodeSetor' => '11',
                'kodeTarik' => '12',
                'kodeBunga' => '110',
                'kodeBiaya' => '18',
                'kodePajak' => '19',
                'transaksi' => ['11', '12', '110', '18', '19'],
            ],
            [
                'kode' => 'SW',
                'nama' => 'Simpanan Wajib',
                'jenis' => 2,
                'minimal' => '50000',
                'mengendap' => '1',
                'bunga' => '3',
                'rumus' => 1, // Saldo Terendah
                'bulan' => true,
                'account' => '800-03',
                'kodeSetor' => '21',
                'kodeTarik' => '22',
                'kodeBunga' => '210',
                'kodeBiaya' => '28',
                'kodePajak' => '29',
                'transaksi' => ['21', '22', '210', '28', '29'],
            ],
            [
                'kode' => 'SS',
                'nama' => 'Simpanan Sukarela',
                'jenis' => 3,
                'minimal' => '1000000',
                'mengendap' => '0',
                'bunga' => '6',
                'rumus' => 2, // Saldo Harian (default)
                'bulan' => false,
                'account' => '510-01',
                'kodeSetor' => '01',
                'kodeTarik' => '02',
                'kodeBunga' => '10',
                'kodeBiaya' => '08',
                'kodePajak' => '09',
                'transaksi' => ['01', '02', '10', '08', '09'],
            ],
            [
                'kode' => 'SB-001',
                'nama' => 'Deposito Tiap Bulan',
                'jenis' => 7, // Rencana (simpanan berjangka)
                'minimal' => '1000000',
                'mengendap' => '12',
                'bunga' => '5',
                'rumus' => 2, // Saldo Harian
                'bulan' => false,
                'account' => '520-01',
                'kodeSetor' => '01',
                'kodeTarik' => '02',
                'kodeBunga' => '10',
                'kodeBiaya' => '08',
                'kodePajak' => '09',
                'transaksi' => ['01', '02', '10', '08', '09'],
            ],
            [
                'kode' => 'SB-002',
                'nama' => 'Deposito Diawal',
                'jenis' => 7,
                'minimal' => '500000',
                'mengendap' => '6',
                'bunga' => '4',
                'rumus' => 2,
                'bulan' => false,
                'account' => '520-01',
                'kodeSetor' => '01',
                'kodeTarik' => '02',
                'kodeBunga' => '10',
                'kodeBiaya' => '08',
                'kodePajak' => '09',
                'transaksi' => ['01', '02', '10', '08', '09'],
            ],
            [
                'kode' => 'SB-003',
                'nama' => 'Deposito Diakhir',
                'jenis' => 7,
                'minimal' => '750000',
                'mengendap' => '9',
                'bunga' => '4.5',
                'rumus' => 2,
                'bulan' => false,
                'account' => '520-01',
                'kodeSetor' => '01',
                'kodeTarik' => '02',
                'kodeBunga' => '10',
                'kodeBiaya' => '08',
                'kodePajak' => '09',
                'transaksi' => ['01', '02', '10', '08', '09'],
            ],
        ];

        $accountBiaya = Account::where('no_account', '931-01')->first();
        $accountPajak = Account::where('no_account', '931-10')->first();
        $accountBunga = Account::where('no_account', '960-01')->first();
        $accountAndroid = Account::where('no_account', '100-01')->first();

        // Spesifikasi kode transaksi yang dibutuhkan produk (sumber: KodeTransaksiSeeder).
        // Memastikan kode transaksi tersedia sebelum ditautkan, agar tabel transaksi
        // produk selalu terisi sesuai jenis simpanannya.
        $specs = $this->kodeSpecs();

        foreach ($products as $p) {
            // Pastikan semua kode transaksi produk tersedia.
            foreach ($p['transaksi'] as $kodeStr) {
                $this->ensureKode($kodeStr, $specs);
            }
            $this->ensureKode($p['kodeSetor'], $specs);
            $this->ensureKode($p['kodeTarik'], $specs);

            $account = Account::where('no_account', $p['account'])->first();
            $kodeSetor = SimpananKode::where('kode', $p['kodeSetor'])->first();
            $kodeTarik = SimpananKode::where('kode', $p['kodeTarik'])->first();
            $kodeBunga = SimpananKode::where('kode', $p['kodeBunga'])->first();
            $kodeBiaya = SimpananKode::where('kode', $p['kodeBiaya'])->first();
            $kodePajak = SimpananKode::where('kode', $p['kodePajak'])->first();

            $jenis = SimpananJenis::updateOrCreate(
                ['kode' => $p['kode']],
                [
                    'nama' => $p['nama'],
                    'account_id' => $account->id ?? 1,
                    'minimum' => $p['minimal'],
                    'mengendap' => $p['mengendap'],
                    'jenis' => $p['jenis'],
                    // Bagi Hasil
                    'jenis_bunga' => 1, // Tidak Bertingkat
                    'bunga' => $p['bunga'],
                    'bunga_id' => $kodeBunga->id ?? null,
                    'account_bunga' => $accountBunga->id ?? null,
                    'rumus_bunga' => $p['rumus'],
                    'bulan' => $p['bulan'],
                    // Biaya Administrasi
                    'biaya' => '10000',
                    'biaya_id' => $kodeBiaya->id ?? null,
                    'account_biaya' => $accountBiaya->id ?? null,
                    // Pajak
                    'pajak' => '10',
                    'pajak_id' => $kodePajak->id ?? null,
                    'account_pajak' => $accountPajak->id ?? null,
                    'pajak_saldo' => $p['minimal'],
                    // Biaya Android
                    'nominal_android' => '2500',
                    'android' => $kodeSetor->id ?? null,
                    'account_android' => $accountAndroid->id ?? null,
                    // Jenis / Setoran / Tarikan
                    'nominal' => $p['minimal'],
                    'harga_saham' => $p['jenis'] === 5 ? '25000' : null,
                    'setor_id' => $kodeSetor->id ?? null,
                    'tarik_id' => $kodeTarik->id ?? null,
                    'insentif' => '0',
                    'saham' => $p['jenis'] === 5 ? 1 : 0,
                    'update_bagi_hasil' => 0,
                    'user_id' => $user,
                ]
            );

            // Transaksi terkait (simpanan_jenis_kode)
            SimpananJenisKode::where('jenis_id', $jenis->id)->delete();
            foreach ($p['transaksi'] as $kodeStr) {
                $kode = SimpananKode::where('kode', $kodeStr)->first();
                if (!$kode) {
                    continue;
                }
                SimpananJenisKode::create([
                    'jenis_id' => $jenis->id,
                    'kode_id' => $kode->id,
                    'user_id' => $user,
                ]);
            }
        }
    }

    /**
     * Spesifikasi kode transaksi (kode, nama, account debet, account kredit)
     * yang dibutuhkan oleh produk simpanan. Konsisten dengan KodeTransaksiSeeder.
     */
    protected function kodeSpecs(): array
    {
        return [
            '11' => ['Setoran Tunai', '100-01', '800-02'],
            '110' => ['Bunga', '960-01', '800-02'],
            '12' => ['Tarikan Tunai', '800-02', '100-01'],
            '18' => ['Biaya Administrasi', '800-02', '931-01'],
            '19' => ['Pajak', '800-02', '931-10'],
            '21' => ['Setoran Tunai', '100-01', '800-03'],
            '210' => ['Bunga', '960-01', '800-03'],
            '22' => ['Tarikan Tunai', '800-03', '100-01'],
            '28' => ['Biaya Administrasi', '800-03', '931-01'],
            '29' => ['Pajak', '800-03', '931-10'],
            '01' => ['Setoran Tunai', '100-01', '510-01'],
            '02' => ['Tarikan Tunai', '510-01', '100-01'],
            '10' => ['Bunga', '960-01', '510-01'],
            '08' => ['Biaya Administrasi', '510-01', '931-01'],
            '09' => ['Pajak', '510-01', '931-10'],
        ];
    }

    /**
     * Pastikan kode transaksi tersedia (firstOrCreate) dengan account yang benar.
     */
    protected function ensureKode(string $kode, array $specs): void
    {
        if (SimpananKode::where('kode', $kode)->exists()) {
            return;
        }
        $spec = $specs[$kode] ?? null;
        if (!$spec) {
            return;
        }
        [$nama, $debetNo, $kreditNo] = $spec;
        $debet = Account::where('no_account', $debetNo)->first();
        $kredit = Account::where('no_account', $kreditNo)->first();
        if (!$debet || !$kredit) {
            return;
        }
        SimpananKode::create([
            'kode' => $kode,
            'nama' => $nama,
            'account_debet' => $debet->id,
            'account_kredit' => $kredit->id,
            'user_id' => $this->adminUserId(),
        ]);
    }
}
