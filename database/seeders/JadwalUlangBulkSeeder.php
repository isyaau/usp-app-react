<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Anggota;
use App\Models\AngsuranPinjaman;
use App\Models\JadwalUlang;
use App\Models\JadwalUlangBiaya;
use App\Models\JadwalUlangDetail;
use App\Models\JadwalUlangJaminan;
use App\Models\JadwalUlangPenjamin;
use App\Models\JadwalUlangSaksi;
use App\Models\JadwalUlangSurat;
use App\Models\Jaminan;
use App\Models\Kantor;
use App\Models\Marketing;
use App\Models\Pinjaman;
use App\Models\PinjamanProduk;
use App\Models\Simpanan;
use App\Models\SimpananKode;
use App\Models\User;
use App\Services\LoanCalculationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Seeder massal (100 record) data Jadwal Ulang Pinjaman beserta relasinya.
 *
 * Setiap record jadwal ulang di-relasikan ke:
 *   - pinjaman      (pinjaman asal via pinjaman_id, FK -> pinjaman)
 *   - anggota       (anggota_id)
 *   - produk        (jenis_id)
 *   - kantor        (kantor_id)
 *   - user          (user_id)
 *   - jadwal_ulang_detail  (deret angsuran hasil LoanCalculationService)
 *   - jadwal_ulang_biaya / jaminan / saksi / surat / penjamin
 *
 * Skema data mengikuti JadwalUlangSeeder yang sudah ada, namun dibuat berulang
 * (loop 100x) dengan variasi acak agar halaman Index/Show/Edit terisi penuh.
 *
 * Menjalankan:
 *   php artisan db:seed --class=JadwalUlangBulkSeeder
 */
class JadwalUlangBulkSeeder extends Seeder
{
    private LoanCalculationService $loanCalc;

    private const METODES = ['Anuitas', 'Flat', 'Flat Efektif', 'Pokok Tetap', 'Bagi Hasil Menurun'];

    private const HUBUNGAN = ['Saudara', 'Orang Tua', 'Suami/Istri', 'Anak', 'Rekan Kerja'];

    public function __construct()
    {
        $this->loanCalc = app(LoanCalculationService::class);
    }

    public function run(): void
    {
        $m = $this->masterData();

        $pinjamanPool = Pinjaman::where('aktif', '1')->get();
        if ($pinjamanPool->isEmpty()) {
            $pinjamanPool = Pinjaman::all();
        }
        if ($pinjamanPool->isEmpty()) {
            $this->command->warn('Tidak ada pinjaman asal. Jalankan PencairanPinjamanSeeder terlebih dahulu.');
            return;
        }

        $anggotaPool = Anggota::all();
        if ($anggotaPool->isEmpty()) {
            $this->command->warn('Tidak ada anggota. Jalankan AnggotaSeeder terlebih dahulu.');
            return;
        }

        $targetCount = 100;
        $count = 0;

        while ($count < $targetCount) {
            $pinjaman = $pinjamanPool->random();
            $anggota = $anggotaPool->random();

            $sisaPokok = max(0, (float) $pinjaman->plafon - (float) AngsuranPinjaman::where('pinjaman_id', $pinjaman->id)->sum('nominal_pokok'));

            $metodeBaru = $pinjaman->angsuran ?: self::METODES[array_rand(self::METODES)];
            $jangkaBaru = rand(12, 60);
            $bungaBaru = rand(6, 18);

            $hasil = $this->loanCalc->calculate([
                'plafon' => $sisaPokok,
                'bunga' => $bungaBaru,
                'jangka_waktu' => $jangkaBaru,
                'satuan' => 'bulan',
                'metode' => $metodeBaru,
            ]);

            $tanggal = Carbon::now()->subDays(rand(0, 365));
            $statusRand = rand(1, 100);
            $status = $statusRand <= 25 ? 'draft' : ($statusRand <= 85 ? 'posted' : 'batal');

            $noTransaksi = 'JU-'.now()->format('YmdHis').'-'.str_pad((string) ($count + 1), 3, '0', STR_PAD_LEFT);

            $jadwalUlang = JadwalUlang::create([
                'no_transaksi' => $noTransaksi,
                'no_pinjaman_lama' => $pinjaman->no_pinjaman,
                'no_pinjaman' => '',
                'tanggal' => $tanggal->format('Y-m-d'),
                'tgl_transaksi' => $tanggal->format('Y-m-d'),
                'pinjaman_id' => $pinjaman->id,
                'anggota_id' => $anggota->id,
                'jenis_id' => (int) $pinjaman->jenis_id,
                'jaminan_id' => (int) $pinjaman->jaminan_id,
                'marketing_id' => (int) $pinjaman->marketing_id,
                'sektor_id' => max(1, (int) $pinjaman->sektor_id),
                'plafon' => $sisaPokok,
                'sisa_pokok' => $sisaPokok,
                'bunga' => $bungaBaru,
                'jangka_waktu' => $jangkaBaru,
                'satuan' => 'bulan',
                'metode' => $metodeBaru,
                'jenis_angsuran' => $metodeBaru,
                'bayar_pokok_per' => '0',
                'pembayaran' => 'manual',
                'jatuh_tempo' => $tanggal->copy()->addMonths($jangkaBaru)->format('Y-m-d'),
                'manual' => '0',
                'tabungan_id' => (int) $pinjaman->tabungan_id,
                'kode_id' => (int) $pinjaman->kode_id,
                'kode_koreksi' => '',
                'swp_id' => $pinjaman->swp_id ?: '0',
                'spp_id' => $pinjaman->spp_id ?: '0',
                'periode' => $hasil['jumlah_periode'],
                'nominal_angsuran' => $hasil['nominal_angsuran'],
                'total_bunga' => $hasil['total_bunga'],
                'cair_simpanan' => '1',
                'sms' => (string) rand(0, 1),
                'rekening_koran' => '0',
                'aktif' => '1',
                'keterangan' => 'Jadwal ulang ke-'.$pinjaman->angsuranke.' dari pinjaman '.$pinjaman->no_pinjaman.' (seed massal)',
                'user_id' => $m['user']->id,
                'kantor_id' => $m['kantor']->id,
                'status' => $status,
                'created_at' => $tanggal->format('Y-m-d H:i:s'),
                'updated_at' => $tanggal->format('Y-m-d H:i:s'),
            ]);

            $this->saveScheduleDetails($jadwalUlang, $hasil['jadwal']);
            $this->saveRelated($jadwalUlang, $m, $sisaPokok);

            $count++;
            if ($count % 20 === 0) {
                $this->command->info("Created {$count} jadwal ulang records...");
            }
        }

        $this->command->info("Successfully created {$count} jadwal ulang records beserta relasinya.");
    }

    /* ------------------------------------------------------------------ */

    /** Siapkan data master (user, kantor, produk, marketing, jaminan, dll). */
    private function masterData(): array
    {
        $user = User::where('role', 'superadmin')->first()
            ?? User::where('email', 'admin@admin.com')->first()
            ?? User::first();

        if (! $user) {
            $user = User::factory()->create([
                'nama' => 'Admin',
                'email' => 'admin@admin.com',
                'role' => 'superadmin',
                'password' => bcrypt('password'),
            ]);
        }

        $kantor = Kantor::first() ?? Kantor::create([
            'kode' => 'TEST-KTR',
            'nama_kantor' => 'Kantor Pusat Test',
            'alamat_kantor' => 'Jln. Test No. 1',
            'pejabat' => 'Manager Test',
            'jabatan' => 'Pimpinan',
            'bendahara' => 'Bendahara Test',
            'user_id' => $user->id,
        ]);

        $produk = PinjamanProduk::first() ?? PinjamanProduk::create([
            'kode' => 'TEST-ANUITAS',
            'nama' => 'Pinjaman Anuitas',
            'account_id' => '0',
            'bunga' => '12',
            'account_bunga' => '0',
            'ditangguhkan' => '0',
            'account_ditangguhkan' => '0',
            'kas' => '0',
            'insentif' => '0',
            'simpanan' => '1',
            'swp_cair' => '1',
            'swp_angsur' => '0',
            'swp_persen' => '0',
            'nominal_simpanan' => '100000',
            'simpanan_pokok' => '0',
            'nominal_simpanan_pokok' => '0',
            'toleransi' => '0',
            'angsuran' => 'Anuitas',
            'user_id' => $user->id,
        ]);

        $marketing = Marketing::first() ?? Marketing::create([
            'kode' => 'TEST-MKT',
            'nama' => 'Marketing Test',
            'alamat' => 'Marketing Test',
            'no_ktp' => '3501010000000000',
            'telepon' => '081000000000',
            'no_hp' => '081000000000',
            'aktif' => '1',
            'kantor_id' => $kantor->id,
            'user_id' => $user->id,
        ]);

        $jaminan = Jaminan::first() ?? Jaminan::create([
            'nama' => 'Kendaraan',
            'user_id' => $user->id,
        ]);

        $accountBiaya = Account::first() ?? Account::create([
            'no_account' => '931-08',
            'nama' => 'Biaya Administrasi',
            'user_id' => $user->id,
        ]);

        $kodeTarikan = SimpananKode::where('tarikan', '1')->first() ?? SimpananKode::first();

        return [
            'user' => $user,
            'kantor' => $kantor,
            'produk' => $produk,
            'marketing' => $marketing,
            'jaminan' => $jaminan,
            'accountBiaya' => $accountBiaya,
            'kodeTarikan' => $kodeTarikan,
        ];
    }

    /** Simpan deret angsuran ke jadwal_ulang_detail. */
    private function saveScheduleDetails(JadwalUlang $ju, array $jadwal): void
    {
        foreach ($jadwal as $row) {
            JadwalUlangDetail::create([
                'jadwal_ulang_id' => $ju->id,
                'angsuran_ke' => $row['ke'],
                'nominal_pokok' => $row['pokok'],
                'nominal_bunga' => $row['bunga'],
                'total_angsuran' => $row['angsuran'],
                'sisa_pokok' => $row['sisa'],
            ]);
        }
    }

    /** Simpan 5 detail pendukung (biaya, jaminan, saksi, surat, penjamin). */
    private function saveRelated(JadwalUlang $ju, array $m, float $sisaPokok): void
    {
        $userId = $m['user']->id;

        JadwalUlangBiaya::create([
            'jadwal_ulang_id' => $ju->id,
            'nama' => 'Biaya Administrasi',
            'nominal' => '25000',
            'persen' => '0',
            'account_id' => (string) $m['accountBiaya']->id,
            'user_id' => (string) $userId,
        ]);
        JadwalUlangBiaya::create([
            'jadwal_ulang_id' => $ju->id,
            'nama' => 'Premi Asuransi',
            'nominal' => '1',
            'persen' => '1',
            'account_id' => (string) $m['accountBiaya']->id,
            'user_id' => (string) $userId,
        ]);

        JadwalUlangJaminan::create([
            'jadwal_ulang_id' => $ju->id,
            'nama' => 'BPKB Kendaraan',
            'keterangan' => 'Asli - disimpan koperasi',
            'nominal' => (string) max(5000000, $sisaPokok),
            'user_id' => (string) $userId,
        ]);
        JadwalUlangJaminan::create([
            'jadwal_ulang_id' => $ju->id,
            'nama' => 'STNK Kendaraan',
            'keterangan' => 'Fotokopi terlegalisir',
            'nominal' => (string) max(5000000, $sisaPokok),
            'user_id' => (string) $userId,
        ]);

        $nomor = random_int(0, 999999);
        JadwalUlangSaksi::create([
            'jadwal_ulang_id' => $ju->id,
            'nama' => 'Saksi '.Str::title(Str::random(6)),
            'tempat_lahir' => 'Madiun',
            'tgl_lahir' => '1992-02-02',
            'no_ktp' => '350101'.str_pad((string) $nomor, 10, '0', STR_PAD_LEFT),
            'alamat' => 'Jl. Saksi No. '.($nomor % 100),
            'pekerjaan_id' => '1',
            'user_id' => (string) $userId,
        ]);

        JadwalUlangSurat::create([
            'jadwal_ulang_id' => $ju->id,
            'surat_id' => '1',
            'keterangan' => 'Ditandatangani di atas materai',
            'surat' => 'Surat Perjanjian Pinjaman',
            'user_id' => (string) $userId,
        ]);

        $namaPenjamin = 'Penjamin '.Str::title(Str::random(5));
        JadwalUlangPenjamin::create([
            'jadwal_ulang_id' => $ju->id,
            'nama' => $namaPenjamin,
            'alamat' => 'Jl. Penjamin No. '.($nomor % 100),
            'no_ktp' => '350101'.str_pad((string) ($nomor + 1), 10, '0', STR_PAD_LEFT),
            'hubungan' => self::HUBUNGAN[array_rand(self::HUBUNGAN)],
            'ibu' => 'Ibu '.$namaPenjamin,
            'telepon' => '081'.str_pad((string) $nomor, 8, '0', STR_PAD_LEFT),
            'tampil' => '1',
            'user_id' => (string) $userId,
        ]);
    }
}
