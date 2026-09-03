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
use App\Models\JaminanDetail;
use App\Models\Kantor;
use App\Models\Marketing;
use App\Models\Pinjaman;
use App\Models\PinjamanBiaya;
use App\Models\PinjamanJaminan;
use App\Models\PinjamanPenjamin;
use App\Models\PinjamanProduk;
use App\Models\PinjamanSaksi;
use App\Models\PinjamanSurat;
use App\Models\Simpanan;
use App\Models\SimpananJenis;
use App\Models\SimpananKode;
use App\Models\User;
use App\Services\LoanCalculationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Seeder data uji untuk menu Jadwal Ulang Pinjaman.
 *
 * Membuat data pendukung form (produk, jaminan, marketing, simpanan, kode
 * tarikan, anggota) + pinjaman asal aktif dengan seluruh detail 6-tab
 * (biaya, jaminan, saksi, surat, penjamin) + cicilan terbayar agar
 * sisa_pokok terisi, dan 1 contoh jadwal ulang untuk halaman Index/Show.
 *
 * Menjalankan:
 *   php artisan db:seed --class=JadwalUlangSeeder
 */
class JadwalUlangSeeder extends Seeder
{
    private LoanCalculationService $loanCalc;

    public function __construct()
    {
        $this->loanCalc = app(LoanCalculationService::class);
    }

    public function run(): void
    {
        $master = $this->ensureMasterData();

        $this->command->info('Membuat pinjaman asal untuk jadwal ulang...');
        $pinjamanAnuitas = $this->makePinjamanAsal($master, [
            'no_pinjaman' => 'TEST-PJL-0001',
            'produk' => $master['produkAnuitas'],
            'tanggal' => Carbon::now()->subMonths(6)->format('Y-m-d'),
            'plafon' => 20000000,
            'bunga' => 12,
            'jangka_waktu' => 24,
            'metode' => 'Anuitas',
            'angsuranTerbayar' => 6,
        ]);

        $this->makePinjamanAsal($master, [
            'no_pinjaman' => 'TEST-PJL-0002',
            'produk' => $master['produkFlat'],
            'tanggal' => Carbon::now()->subMonths(3)->format('Y-m-d'),
            'plafon' => 5000000,
            'bunga' => 10,
            'jangka_waktu' => 12,
            'metode' => 'Flat Efektif',
            'angsuranTerbayar' => 2,
        ]);

        $this->makeJadwalUlangContoh($master, $pinjamanAnuitas);

        $this->command->info('Seeder Jadwal Ulang selesai.');
        $this->command->info('Anggota tes      : '.$master['anggota']->no_anggota.' — '.$master['anggota']->nama);
        $this->command->info('Pinjaman asal    : TEST-PJL-0001 & TEST-PJL-0002 (aktif, bisa dipilih di form Jadwal Ulang)');
        $this->command->info('Login user       : admin@admin.com / password');
    }

    /* ------------------------------------------------------------------ */

    /** Pastikan data master (dropdown form) tersedia. */
    private function ensureMasterData(): array
    {
        $this->command->info('Memastikan data master tersedia...');

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

        $produkAnuitas = PinjamanProduk::firstOrCreate(
            ['kode' => 'TEST-ANUITAS'],
            [
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
            ]
        );

        $produkFlat = PinjamanProduk::firstOrCreate(
            ['kode' => 'TEST-FLAT'],
            [
                'nama' => 'Pinjaman Flat Efektif',
                'account_id' => '0',
                'bunga' => '10',
                'account_bunga' => '0',
                'ditangguhkan' => '0',
                'account_ditangguhkan' => '0',
                'kas' => '0',
                'insentif' => '0',
                'simpanan' => '1',
                'swp_cair' => '1',
                'swp_angsur' => '0',
                'swp_persen' => '0',
                'nominal_simpanan' => '50000',
                'simpanan_pokok' => '0',
                'nominal_simpanan_pokok' => '0',
                'toleransi' => '0',
                'angsuran' => 'Flat Efektif',
                'user_id' => $user->id,
            ]
        );

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

        $jaminanType = Jaminan::first() ?? Jaminan::create([
            'nama' => 'Kendaraan',
            'user_id' => $user->id,
        ]);

        if ($jaminanType->details()->count() === 0) {
            JaminanDetail::firstOrCreate(
                ['jaminan_id' => $jaminanType->id, 'detail' => 'BPKB Kendaraan'],
                ['user_id' => $user->id]
            );
            JaminanDetail::firstOrCreate(
                ['jaminan_id' => $jaminanType->id, 'detail' => 'STNK Kendaraan'],
                ['user_id' => $user->id]
            );
        }

        $accountBiaya = Account::where('no_account', '931-08')->first() ?? Account::first();

        $kodeTarikan = SimpananKode::where('tarikan', '1')->first() ?? SimpananKode::first();

        $jenisSimpanan = SimpananJenis::first() ?? SimpananJenis::create([
            'kode' => 'TEST-SJ',
            'nama' => 'Simpanan Test',
            'account_id' => 1,
            'minimum' => '0',
            'mengendap' => '0',
            'bunga_id' => 1,
            'jenis_bunga' => 'Tetap',
            'bunga' => '0',
            'account_bunga' => 1,
            'rumus_bunga' => 1,
            'bulan' => '12',
            'biaya_id' => 1,
            'biaya' => '0',
            'account_biaya' => 1,
            'pajak_id' => 1,
            'pajak' => '0',
            'account_pajak' => 1,
            'saldo_pajak' => '0',
            'android' => '0',
            'nominal_android' => '0',
            'account_android' => 1,
            'nominal' => '0',
            'jenis' => 'Reguler',
            'setor_id' => 1,
            'tarik_id' => 1,
            'insentif' => '0',
            'saham' => '0',
            'user_id' => $user->id,
        ]);

        $anggota = Anggota::where('no_anggota', 'TEST-KP-0001')->first() ?? Anggota::create([
            'no_anggota' => 'TEST-KP-0001',
            'nama' => 'Anggota Uji Jadwal Ulang',
            'alamat' => 'Jl. Tes Koperasi No. 1',
            'kelompok_id' => '1',
            'pin' => '123456',
            'provinsi_id' => 34,
            'kota_id' => 3404,
            'kecamatan_id' => 340402,
            'kelurahan_id' => 3404022007,
            'email' => 'ujijadwaluang@example.com',
            'tempat_lahir' => 'Madiun',
            'tgl_lahir' => '1990-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'ISLAM',
            'pekerjaan' => 'PEGAWAI SWASTA',
            'pendidikan' => 'S1',
            'status_perkawinan' => 'Kawin',
            'pasangan' => 'Pasangan Uji',
            'telepon' => '0351-000000',
            'no_hp' => '081000000001',
            'jenis_identitas' => 'KTP',
            'no_identitas' => '3501010000000001',
            'ibu' => 'Ibu Uji',
            'foto' => 'anggota/default.png',
            'pengurus' => 0,
            'pengawas' => 0,
            'status' => 1,
            'kantor_id' => $kantor->id,
            'user_id' => $user->id,
        ]);

        $simpanan = Simpanan::where('no_rekening', 'TEST-REK-001')->first() ?? Simpanan::create([
            'tanggal' => Carbon::now()->format('Y-m-d'),
            'no_rekening' => 'TEST-REK-001',
            'anggota_id' => $anggota->id,
            'jenis_id' => $jenisSimpanan->id,
            'marketing_id' => $marketing->id,
            'qq' => null,
            'bunga' => '0',
            'baris' => '1',
            'ttd' => null,
            'blokir_simpanan' => '0',
            'blokir_nominal' => '0',
            'nominal_blokir' => '0',
            'blokir_tgl' => null,
            'tgl_blokir' => null,
            'nominal_setor' => '1000000',
            'sms' => '0',
            'aktif' => '1',
            'kantor_id' => $kantor->id,
            'user_id' => $user->id,
        ]);

        return [
            'user' => $user,
            'kantor' => $kantor,
            'produkAnuitas' => $produkAnuitas,
            'produkFlat' => $produkFlat,
            'marketing' => $marketing,
            'jaminanType' => $jaminanType,
            'accountBiaya' => $accountBiaya,
            'kodeTarikan' => $kodeTarikan,
            'anggota' => $anggota,
            'simpanan' => $simpanan,
        ];
    }

    /** Buat pinjaman asal aktif lengkap + cicilan terbayar. */
    private function makePinjamanAsal(array $m, array $spec): Pinjaman
    {
        $produk = $spec['produk'];
        $plafon = (float) $spec['plafon'];
        $jangka = (int) $spec['jangka_waktu'];
        $metode = $spec['metode'];

        $hasil = $this->loanCalc->calculate([
            'plafon' => $plafon,
            'bunga' => (float) $spec['bunga'],
            'jangka_waktu' => $jangka,
            'satuan' => 'bulan',
            'metode' => $metode,
        ]);

        $pinjaman = Pinjaman::where('no_pinjaman', $spec['no_pinjaman'])->first();

        $data = [
            'tanggal' => $spec['tanggal'],
            'no_pinjaman' => $spec['no_pinjaman'],
            'proposal_id' => '0',
            'anggota_id' => (string) $m['anggota']->id,
            'jaminan_id' => (string) $m['jaminanType']->id,
            'jenis_id' => (string) $produk->id,
            'marketing_id' => (string) $m['marketing']->id,
            'sektor_id' => '1',
            'angsuran' => $metode,
            'plafon' => (string) $plafon,
            'nominal_angsuran' => (string) $hasil['nominal_angsuran'],
            'bunga' => (string) $spec['bunga'],
            'jangka_waktu' => (string) $jangka,
            'periode' => (string) $hasil['jumlah_periode'],
            'bayar_pokok_per' => '0',
            'jatuh_tempo' => Carbon::parse($spec['tanggal'])->addMonths($jangka)->format('Y-m-d'),
            'satuan' => 'bulan',
            'pembayaran' => 'manual',
            'manual' => '0',
            'tabungan_id' => (string) $m['simpanan']->id,
            'kode_id' => (string) $m['kodeTarikan']->id,
            'kode_koreksi' => '0',
            'swp_id' => $produk->nominal_simpanan,
            'spp_id' => $produk->nominal_simpanan_pokok,
            'angsuranke' => (string) $spec['angsuranTerbayar'],
            'rekening_koran' => '0',
            'cair_simpanan' => '1',
            'sms' => '1',
            'aktif' => '1',
            'kantor_id' => (string) $m['kantor']->id,
            'user_id' => (string) $m['user']->id,
        ];

        if ($pinjaman) {
            $pinjaman->update($data);
        } else {
            $pinjaman = Pinjaman::create($data);
        }

        $this->syncPinjamanDetails($pinjaman, $m);
        $this->buatCicilanTerbayar($pinjaman, $m, $spec['angsuranTerbayar'], $hasil['jadwal']);

        return $pinjaman;
    }

    /** Isi detail 6-tab (biaya, jaminan, saksi, surat, penjamin). */
    private function syncPinjamanDetails(Pinjaman $pinjaman, array $m): void
    {
        $userId = $m['user']->id;

        PinjamanBiaya::where('pinjaman_id', $pinjaman->id)->delete();
        PinjamanBiaya::create([
            'pinjaman_id' => $pinjaman->id,
            'nama' => 'Biaya Administrasi',
            'nominal' => '25000',
            'persen' => '0',
            'account_id' => (string) $m['accountBiaya']->id,
            'user_id' => (string) $userId,
        ]);
        PinjamanBiaya::create([
            'pinjaman_id' => $pinjaman->id,
            'nama' => 'Premi Asuransi',
            'nominal' => '1',
            'persen' => '1',
            'account_id' => (string) $m['accountBiaya']->id,
            'user_id' => (string) $userId,
        ]);

        PinjamanJaminan::where('pinjaman_id', $pinjaman->id)->delete();
        foreach ($m['jaminanType']->details as $detail) {
            PinjamanJaminan::create([
                'pinjaman_id' => $pinjaman->id,
                'nama' => $detail->detail,
                'keterangan' => 'Asli - disimpan koperasi',
                'nominal' => (string) max(5000000, $pinjaman->plafon),
                'user_id' => (string) $userId,
            ]);
        }

        PinjamanSaksi::where('pinjaman_id', $pinjaman->id)->delete();
        PinjamanSaksi::create([
            'pinjaman_id' => $pinjaman->id,
            'nama' => 'Saksi Uji Satu',
            'tempat_lahir' => 'Madiun',
            'tgl_lahir' => '1992-02-02',
            'no_ktp' => '3501010000000002',
            'alamat' => 'Jl. Saksi No. 2',
            'pekerjaan_id' => '1',
            'user_id' => (string) $userId,
        ]);

        PinjamanSurat::where('pinjaman_id', $pinjaman->id)->delete();
        PinjamanSurat::create([
            'pinjaman_id' => $pinjaman->id,
            'surat_id' => '1',
            'keterangan' => 'Ditandatangani di atas materai',
            'surat' => 'Surat Perjanjian Pinjaman',
            'user_id' => (string) $userId,
        ]);

        PinjamanPenjamin::where('pinjaman_id', $pinjaman->id)->delete();
        PinjamanPenjamin::create([
            'pinjaman_id' => $pinjaman->id,
            'nama' => 'Penjamin Uji',
            'alamat' => 'Jl. Penjamin No. 3',
            'no_ktp' => '3501010000000003',
            'hubungan' => 'Saudara',
            'ibu' => 'Ibu Penjamin',
            'telepon' => '081000000003',
            'tampil' => '1',
            'user_id' => (string) $userId,
        ]);
    }

    /** Buat cicilan terbayar agar sisa_pokok < plafon. */
    private function buatCicilanTerbayar(Pinjaman $pinjaman, array $m, int $jumlahTerbayar, array $jadwal): void
    {
        $realTerbayar = min($jumlahTerbayar, count($jadwal));
        $tglMulai = Carbon::parse($pinjaman->tanggal)->addMonth();

        for ($i = 0; $i < $realTerbayar; $i++) {
            $row = $jadwal[$i];
            AngsuranPinjaman::firstOrCreate(
                ['no_transaksi' => 'AP-TEST-'.$pinjaman->id.'-'.($i + 1)],
                [
                    'pinjaman_id' => $pinjaman->id,
                    'tgl_transaksi' => $tglMulai->copy()->addMonths($i)->format('Y-m-d'),
                    'angsuran_ke' => $i + 1,
                    'nominal_pokok' => $row['pokok'],
                    'nominal_bunga' => $row['bunga'],
                    'total_angsuran' => $row['angsuran'],
                    'denda' => 0,
                    'keterangan' => 'Cicilan terbayar (seed)',
                    'user_id' => $m['user']->id,
                    'kantor_id' => $m['kantor']->id,
                    'status' => 'posted',
                ]
            );
        }
    }

    /** Buat 1 contoh jadwal ulang agar halaman Index/Show/Edit berisi. */
    private function makeJadwalUlangContoh(array $m, Pinjaman $pinjaman): void
    {
        $sisaPokok = max(0, (float) $pinjaman->plafon - (float) AngsuranPinjaman::where('pinjaman_id', $pinjaman->id)->sum('nominal_pokok'));
        $metodeBaru = $pinjaman->angsuran;
        $jangkaBaru = 36;
        $bungaBaru = 12;

        $hasil = $this->loanCalc->calculate([
            'plafon' => $sisaPokok,
            'bunga' => $bungaBaru,
            'jangka_waktu' => $jangkaBaru,
            'satuan' => 'bulan',
            'metode' => $metodeBaru,
        ]);

        $tanggal = Carbon::now();

        $jadwalUlang = JadwalUlang::firstOrCreate(
            ['no_transaksi' => 'JU-TEST-'.$pinjaman->id],
            [
                'no_pinjaman_lama' => $pinjaman->no_pinjaman,
                'no_pinjaman' => '',
                'tanggal' => $tanggal->format('Y-m-d'),
                'tgl_transaksi' => $tanggal->format('Y-m-d'),
                'pinjaman_id' => $pinjaman->id,
                'anggota_id' => $m['anggota']->id,
                'jenis_id' => (int) $pinjaman->jenis_id,
                'jaminan_id' => (int) $pinjaman->jaminan_id,
                'marketing_id' => (int) $pinjaman->marketing_id,
                'sektor_id' => (int) $pinjaman->sektor_id,
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
                'tabungan_id' => $m['simpanan']->id,
                'kode_id' => $m['kodeTarikan']->id,
                'kode_koreksi' => '',
                'swp_id' => $m['produkAnuitas']->nominal_simpanan,
                'spp_id' => $m['produkAnuitas']->nominal_simpanan_pokok,
                'periode' => $hasil['jumlah_periode'],
                'nominal_angsuran' => $hasil['nominal_angsuran'],
                'total_bunga' => $hasil['total_bunga'],
                'cair_simpanan' => '1',
                'sms' => '0',
                'rekening_koran' => '0',
                'aktif' => '1',
                'keterangan' => 'Jadwal ulang dari pinjaman '.$pinjaman->no_pinjaman.' (seed)',
                'user_id' => $m['user']->id,
                'kantor_id' => $m['kantor']->id,
                'status' => 'posted',
            ]
        );

        JadwalUlangDetail::where('jadwal_ulang_id', $jadwalUlang->id)->delete();
        foreach ($hasil['jadwal'] as $row) {
            JadwalUlangDetail::create([
                'jadwal_ulang_id' => $jadwalUlang->id,
                'angsuran_ke' => $row['ke'],
                'nominal_pokok' => $row['pokok'],
                'nominal_bunga' => $row['bunga'],
                'total_angsuran' => $row['angsuran'],
                'sisa_pokok' => $row['sisa'],
                'user_id' => $m['user']->id,
            ]);
        }

        JadwalUlangBiaya::where('jadwal_ulang_id', $jadwalUlang->id)->delete();
        JadwalUlangBiaya::create([
            'jadwal_ulang_id' => $jadwalUlang->id,
            'nama' => 'Biaya Administrasi',
            'nominal' => '25000',
            'persen' => '0',
            'account_id' => (string) $m['accountBiaya']->id,
            'user_id' => (string) $m['user']->id,
        ]);

        JadwalUlangJaminan::where('jadwal_ulang_id', $jadwalUlang->id)->delete();
        JadwalUlangJaminan::create([
            'jadwal_ulang_id' => $jadwalUlang->id,
            'nama' => 'BPKB Kendaraan',
            'keterangan' => 'Asli - disimpan koperasi',
            'nominal' => (string) max(5000000, $sisaPokok),
            'user_id' => (string) $m['user']->id,
        ]);

        JadwalUlangSaksi::where('jadwal_ulang_id', $jadwalUlang->id)->delete();
        JadwalUlangSaksi::create([
            'jadwal_ulang_id' => $jadwalUlang->id,
            'nama' => 'Saksi Uji Satu',
            'tempat_lahir' => 'Madiun',
            'tgl_lahir' => '1992-02-02',
            'no_ktp' => '3501010000000002',
            'alamat' => 'Jl. Saksi No. 2',
            'pekerjaan_id' => '1',
            'user_id' => (string) $m['user']->id,
        ]);

        JadwalUlangSurat::where('jadwal_ulang_id', $jadwalUlang->id)->delete();
        JadwalUlangSurat::create([
            'jadwal_ulang_id' => $jadwalUlang->id,
            'surat_id' => '1',
            'keterangan' => 'Ditandatangani di atas materai',
            'surat' => 'Surat Perjanjian Pinjaman',
            'user_id' => (string) $m['user']->id,
        ]);

        JadwalUlangPenjamin::where('jadwal_ulang_id', $jadwalUlang->id)->delete();
        JadwalUlangPenjamin::create([
            'jadwal_ulang_id' => $jadwalUlang->id,
            'nama' => 'Penjamin Uji',
            'alamat' => 'Jl. Penjamin No. 3',
            'no_ktp' => '3501010000000003',
            'hubungan' => 'Saudara',
            'ibu' => 'Ibu Penjamin',
            'telepon' => '081000000003',
            'tampil' => '1',
            'user_id' => (string) $m['user']->id,
        ]);
    }
}