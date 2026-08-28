<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\LoanCostComponent;
use App\Models\Marketing;
use App\Models\PinjamanProduk;
use App\Models\Proposal;
use App\Models\ProposalBiaya;
use App\Models\User;
use App\Services\LoanCalculationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Seeder 100 data Proposal Pinjaman untuk menu "Proposal".
 *
 * Membuat data pendukung (user, kantor, anggota, produk, marketing, akun,
 * komponen biaya) bila belum ada, lalu membuat 100 proposal dengan prefix
 * no_bukti `PROP-SEED-` beserta detail biayanya. Berjalan idempoten:
 * record lama ber-prefix `PROP-SEED-` dihapus sebelum membuat yang baru.
 *
 * Menjalankan:
 *   php artisan db:seed --class=ProposalSeeder
 */
class ProposalSeeder extends Seeder
{
    private const METODE = ['Anuitas', 'Flat', 'Flat Efektif', 'Pokok Tetap', 'Bagi Hasil Menurun'];
    private const PLAFON = [5000000, 10000000, 15000000, 20000000, 25000000, 30000000, 50000000];
    private const JANGKA = [6, 12, 18, 24, 36, 48, 60];
    private const PENGGUNAAN = ['Modal Usaha', 'Modal Kerja', 'Pendidikan', 'Konsumsi', 'Renovasi Rumah'];
    private const JAMINAN = ['BPKB Kendaraan', 'SHM Rumah', 'SK Pensiun', 'Deposito', 'Sertifikat Tanah'];

    private LoanCalculationService $loanCalc;

    public function __construct()
    {
        $this->loanCalc = app(LoanCalculationService::class);
    }

    public function run(): void
    {
        $m = $this->ensureMasterData();
        $this->bersihkanDataLama();

        $this->command->info('Membuat 100 data proposal pinjaman...');

        for ($i = 1; $i <= 100; $i++) {
            $anggota = $m['anggotaPool'][($i - 1) % count($m['anggotaPool'])];
            $produk = $m['produkPool'][($i - 1) % count($m['produkPool'])];
            $marketing = $m['marketingPool'][($i - 1) % count($m['marketingPool'])];

            $plafon = self::PLAFON[array_rand(self::PLAFON)];
            $bunga = rand(8, 15);
            $jangka = self::JANGKA[array_rand(self::JANGKA)];
            $satuan = 'bulan';
            $metode = $produk->angsuran ?: self::METODE[array_rand(self::METODE)];

            $hasil = $this->loanCalc->calculate([
                'plafon' => (float) $plafon,
                'bunga' => (float) $bunga,
                'jangka_waktu' => (int) $jangka,
                'satuan' => $satuan,
                'metode' => $metode,
            ]);

            $noBukti = 'PROP-SEED-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            $kantorId = $anggota->kantor_id ?: $m['kantor']->id;

            $proposal = Proposal::create([
                'tanggal' => Carbon::now()->subDays(rand(1, 365))->format('Y-m-d'),
                'no_bukti' => $noBukti,
                'anggota_id' => (string) $anggota->id,
                'jenis_id' => (string) $produk->id,
                'marketing_id' => (string) $marketing->id,
                'plafon' => (string) $plafon,
                'bunga' => (string) $bunga,
                'jangka_waktu' => (string) $jangka,
                'satuan' => $satuan,
                'bayar_pokok_per' => '',
                'pembayaran' => 'per-jangka',
                'setiap_saat' => '0',
                'jenis_angsuran' => $metode,
                'nominal_angsuran' => (string) $hasil['nominal_angsuran'],
                'penggunaan_kredit' => self::PENGGUNAAN[array_rand(self::PENGGUNAAN)],
                'jaminan' => self::JAMINAN[array_rand(self::JAMINAN)],
                'total_biaya' => '0',
                'total_terima' => (string) $plafon,
                'status' => '1',
                'kantor_id' => (string) $kantorId,
                'user_id' => (string) $m['user']->id,
            ]);

            $this->syncBiaya($proposal, $m, $plafon);
        }

        $this->command->info('Seeder Proposal selesai. 100 data dibuat (prefix PROP-SEED-).');
        $this->command->info('Login user : admin@admin.com / password');
    }

    /* ------------------------------------------------------------------ */

    /** Pastikan data master tersedia (dropdown form maupun relasi). */
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

        $anggotaPool = Anggota::whereNotNull('kantor_id')->limit(10)->get();
        if ($anggotaPool->isEmpty()) {
            $anggota = Anggota::create([
                'no_anggota' => 'TEST-KP-0001',
                'nama' => 'Anggota Uji Proposal',
                'alamat' => 'Jl. Tes Koperasi No. 1',
                'kelompok_id' => 1,
                'pin' => '123456',
                'provinsi_id' => 34,
                'kota_id' => 3404,
                'kecamatan_id' => 340402,
                'kelurahan_id' => 3404022007,
                'email' => 'ujiproposal@example.com',
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
            $anggotaPool = collect([$anggota]);
        }

        $produkPool = PinjamanProduk::limit(4)->get();
        if ($produkPool->isEmpty()) {
            foreach (['Anuitas', 'Flat Efektif'] as $i => $metode) {
                $produkPool->push(PinjamanProduk::create([
                    'kode' => 'TEST-PJ-'.($i + 1),
                    'nama' => 'Produk Pinjaman Test '.($i + 1),
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
                    'angsuran' => $metode,
                    'user_id' => $user->id,
                ]));
            }
        }

        $account = Account::first();

        $costComponents = LoanCostComponent::where('active', '1')->limit(4)->get();
        if ($costComponents->isEmpty()) {
            foreach ([
                ['name' => 'Administrasi', 'calculation_type' => 'nominal', 'percentage' => '0'],
                ['name' => 'Provisi', 'calculation_type' => 'percentage', 'percentage' => '1'],
                ['name' => 'Premi Asuransi', 'calculation_type' => 'percentage', 'percentage' => '1'],
                ['name' => 'Materai', 'calculation_type' => 'nominal', 'percentage' => '0'],
            ] as $komponen) {
                $costComponents->push(LoanCostComponent::create([
                    'name' => $komponen['name'],
                    'calculation_type' => $komponen['calculation_type'],
                    'amount' => '0',
                    'percentage' => $komponen['percentage'],
                    'account_id' => $account?->id ?? 0,
                    'is_mandatory' => '1',
                    'is_deducted_from_disbursement' => '0',
                    'is_paid_separately' => '0',
                    'active' => '1',
                    'user_id' => $user->id,
                ]));
            }
        }

        return [
            'user' => $user,
            'kantor' => $kantor,
            'marketing' => $marketing,
            'anggotaPool' => $anggotaPool,
            'produkPool' => $produkPool,
            'marketingPool' => collect([$marketing]),
            'account' => $account,
            'costComponents' => $costComponents,
        ];
    }

    /** Hapus proposal lama ber-prefix seed beserta biayanya (idempoten). */
    private function bersihkanDataLama(): void
    {
        $lama = Proposal::where('no_bukti', 'LIKE', 'PROP-SEED-%')->pluck('id');
        if ($lama->isNotEmpty()) {
            ProposalBiaya::whereIn('proposal_id', $lama)->delete();
            Proposal::whereIn('id', $lama)->delete();
        }
    }

    /** Isi detail biaya + hitung Total Biaya & Total Terima seperti controller. */
    private function syncBiaya(Proposal $proposal, array $m, int $plafon): void
    {
        $rows = [
            ['component_id' => null, 'nama' => 'Biaya Administrasi', 'nominal' => 25000, 'persen' => false, 'deduct' => false],
            ['component_id' => null, 'nama' => 'Premi Asuransi', 'nominal' => 1, 'persen' => true, 'deduct' => true],
        ];

        foreach ($m['costComponents'] as $i => $component) {
            if ($i >= count($rows)) {
                continue;
            }
            $rows[$i]['component_id'] = $component->id;
        }

        $totalBiaya = 0;
        $totalPotongan = 0;

        foreach ($rows as $row) {
            $nominal = (float) $row['nominal'];
            if ($row['persen']) {
                $nominal = $plafon * ($nominal / 100);
            }
            $nominal = round($nominal, 0);

            $totalBiaya += $nominal;
            if ($row['deduct']) {
                $totalPotongan += $nominal;
            }

            ProposalBiaya::create([
                'proposal_id' => $proposal->id,
                'component_id' => (string) ($row['component_id'] ?: 0),
                'nama' => $row['nama'],
                'nominal' => (string) $nominal,
                'persen' => $row['persen'] ? '1' : '0',
                'account_id' => (string) ($m['account']?->id ?: 0),
                'is_deducted_from_disbursement' => $row['deduct'] ? '1' : '0',
                'user_id' => (string) $m['user']->id,
            ]);
        }

        $proposal->update([
            'total_biaya' => (string) round($totalBiaya, 0),
            'total_terima' => (string) round(max(0, $plafon - $totalPotongan), 0),
        ]);
    }
}