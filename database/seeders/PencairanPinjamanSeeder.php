<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\PencairanPinjaman;
use App\Models\Pinjaman;
use App\Models\PinjamanProduk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PencairanPinjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $kantors = Kantor::all();

        if ($users->isEmpty()) {
            $this->command->warn('Tidak ada data user.');
            return;
        }

        // Kantor ID list (may be empty - kantor_id is nullable in pencairan_pinjaman)
        $kantorIds = $kantors->pluck('id')->toArray();
        $hasKantors = !empty($kantorIds);
        if (!$hasKantors) {
            $this->command->warn('Tidak ada data kantor, menggunakan kantor_id = null.');
        }

        // Get valid kantor IDs from existing pinjaman records
        $existingPinjaman = Pinjaman::first();
        $defaultKantorId = $existingPinjaman && $existingPinjaman->kantor_id ? (int) $existingPinjaman->kantor_id : null;
        if ($defaultKantorId && !in_array($defaultKantorId, $kantorIds)) {
            $defaultKantorId = null;
        }

        // Helper: get a valid kantor ID
        $getKantorId = function () use ($users, $hasKantors, $kantorIds, $defaultKantorId) {
            foreach ($users->random(5) as $user) {
                if ($user->kantor_id && in_array($user->kantor_id, $kantorIds)) {
                    return $user->kantor_id;
                }
            }
            if ($defaultKantorId) {
                return $defaultKantorId;
            }
            if ($hasKantors) {
                return $kantorIds[array_rand($kantorIds)];
            }
            return null;
        };

        // Ambil data pinjaman yang ada
        $pinjamanList = Pinjaman::with(['anggota', 'jenisPinjaman'])->get();

        // Jika tidak ada pinjaman, buat dummy pinjaman untuk testing
        if ($pinjamanList->isEmpty()) {
            $this->command->info('Membuat pinjaman dummy untuk testing...');

            $anggotaList = Anggota::all();
            $produkList = PinjamanProduk::all();

            if ($anggotaList->isEmpty()) {
                $this->command->warn('Tidak ada data anggota.');
                return;
            }

            // Buat 20 pinjaman dummy
            for ($i = 0; $i < 20; $i++) {
                $anggota = $anggotaList->random();
                $produk = $produkList->isNotEmpty() ? $produkList->random() : null;
                $plafon = mt_rand(5000000, 50000000);

                $kantorId = $getKantorId();

                $pinjaman = Pinjaman::create([
                    'tanggal' => Carbon::now()->subDays(mt_rand(1, 180))->format('Y-m-d'),
                    'no_pinjaman' => 'PJ-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT) . '-' . Carbon::now()->year,
                    'proposal_id' => '0',
                    'anggota_id' => (string) $anggota->id,
                    'jaminan_id' => '0',
                    'jenis_id' => $produk ? (string) $produk->id : '1',
                    'marketing_id' => '0',
                    'sektor_id' => '0',
                    'angsuran' => 'anuitas',
                    'plafon' => (string) $plafon,
                    'nominal_angsuran' => (string) ($plafon * 0.01),
                    'bunga' => '0.01',
                    'jangka_waktu' => '12',
                    'periode' => Carbon::now()->format('Y-m-d'),
                    'satuan' => 'bulan',
                    'pembayaran' => '1',
                    'manual' => '0',
                    'tabungan_id' => '0',
                    'kode_id' => '0',
                    'kode_koreksi' => '0',
                    'swp_id' => '0',
                    'spp_id' => '0',
                    'angsuranke' => '0',
                    'rekening_koran' => '0',
                    'cair_simpanan' => '1',
                    'sms' => '1',
                    'aktif' => '1',
                    'kantor_id' => $kantorId,
                    'user_id' => (string) $users->random()->id,
                ]);

                $pinjamanList->push($pinjaman);
            }
        }

        $metodeCair = ['transfer', 'tunai', 'cek', 'giro'];
        $banks = ['BCA', 'BRI', 'BNI', 'Mandiri', 'CIMB Niaga', 'Danamon', 'Permata', 'OCBC NISP', 'Maybank', 'BTN'];

        $count = 0;
        $targetCount = 100;

        while ($count < $targetCount) {
            $pinjaman = $pinjamanList->random();
            $plafon = (float) $pinjaman->plafon;

            $nominalCair = $plafon * (0.7 + mt_rand(0, 30) / 100);
            $nominalCair = round($nominalCair, -3); // Bulatkan ke ribuan

            $biayaAdmin = round($nominalCair * (0.005 + mt_rand(0, 15) / 1000), -2);
            $potonganSimpanan = round($nominalCair * (mt_rand(0, 5) / 100), -2);

            $tanggalCair = Carbon::now()->subDays(mt_rand(0, 30))->format('Y-m-d');

            $statusRand = mt_rand(1, 100);
            if ($statusRand <= 20) {
                $status = 'pending';
            } elseif ($statusRand <= 50) {
                $status = 'disetujui';
            } elseif ($statusRand <= 70) {
                $status = 'ditolak';
            } else {
                $status = 'dicairkan';
            }

            $metode = $metodeCair[array_rand($metodeCair)];
            $creator = $users->random();
            $approver = $users->random();
            $cairOleh = $users->random();

            $kantorId = $getKantorId();

            // Get anggota nama
            $anggotaNama = 'Anggota';
            $anggota = null;

            if ($pinjaman->anggota) {
                $anggota = $pinjaman->anggota;
            } elseif (isset($pinjaman->anggota_id)) {
                $anggota = Anggota::find($pinjaman->anggota_id);
            }

            if ($anggota && $anggota->nama) {
                $anggotaNama = $anggota->nama;
            }

            $data = [
                'pinjaman_id' => $pinjaman->id,
                'tanggal_cair' => $tanggalCair,
                'nominal_cair' => $nominalCair,
                'metode_cair' => $metode,
                'no_rekening' => $metode === 'transfer' ? Str::random(10) : null,
                'nama_rekening' => $metode === 'transfer' ? $anggotaNama : null,
                'bank_id' => $metode === 'transfer' ? $banks[array_rand($banks)] : null,
                'biaya_admin' => $biayaAdmin,
                'potongan_simpanan' => $potonganSimpanan,
                'keterangan' => $this->getRandomKeterangan($status),
                'status' => $status,
                'approved_by' => in_array($status, ['disetujui', 'ditolak', 'dicairkan']) ? $approver->id : null,
                'approved_at' => in_array($status, ['disetujui', 'ditolak', 'dicairkan'])
                    ? Carbon::parse($tanggalCair)->subDays(mt_rand(0, 3))->format('Y-m-d H:i:s')
                    : null,
                'cair_oleh' => $status === 'dicairkan' ? $cairOleh->id : null,
                'cair_at' => $status === 'dicairkan'
                    ? Carbon::parse($tanggalCair)->format('Y-m-d H:i:s')
                    : null,
                'created_by' => $creator->id,
                'kantor_id' => $kantorId,
                'created_at' => Carbon::parse($tanggalCair)->subDays(mt_rand(0, 5))->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($tanggalCair)->format('Y-m-d H:i:s'),
            ];

            PencairanPinjaman::create($data);
            $count++;

            if ($count % 20 === 0) {
                $this->command->info("Created {$count} pencairan pinjaman records...");
            }
        }

        $this->command->info("Successfully created {$count} pencairan pinjaman records.");
    }

    private function getRandomKeterangan(string $status): string
    {
        $keteranganByStatus = [
            'pending' => [
                'Menunggu persetujuan manajer',
                'Dokumen sedang diverifikasi',
                'Menunggu tanda tangan pemohon',
                'Proses verifikasi kelengkapan dokumen',
            ],
            'disetujui' => [
                'Disetujui oleh manajer cabang',
                'Persetujuan direksi telah diperoleh',
                'Dokumen lengkap dan valid, disetujui untuk dicairkan',
                'Setelah review tim kredit, disetujui',
            ],
            'ditolak' => [
                'Dokumen tidak lengkap (KTP, KK, slip gaji)',
                'Plafon melebihi batas kredit anggota',
                'Anggota memiliki tunggakan pinjaman lain',
                'Analisa kelayakan kredit tidak memenuhi syarat',
                'Jaminan tidak memenuhi standar',
            ],
            'dicairkan' => [
                'Dana telah ditransfer ke rekening anggota',
                'Pencairan tunai di kas kantor',
                'Cek pencairan telah diserahkan ke anggota',
                'Dana cair via giro, bukti transfer terlampir',
            ],
        ];

        $options = $keteranganByStatus[$status] ?? ['Proses pencairan pinjaman'];
        return $options[array_rand($options)];
    }
}
