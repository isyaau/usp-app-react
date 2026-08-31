<?php

namespace Database\Seeders;

use App\Models\Kantor;
use App\Models\Pinjaman;
use App\Models\SuratPeringatan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SuratPeringatanSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $kantors = Kantor::all();
        $pinjamanList = Pinjaman::with('anggota')->where('aktif', '1')->get();

        if ($users->isEmpty() || $kantors->isEmpty() || $pinjamanList->isEmpty()) {
            $this->command->warn('Tidak ada data user, kantor, atau pinjaman aktif.');
            return;
        }

        $kantorIds = $kantors->pluck('id')->toArray();
        $tahaps = ['SP-1', 'SP-2', 'SP-3'];

        $count = 0;
        $targetCount = 100;

        while ($count < $targetCount) {
            $pinjaman = $pinjamanList->random();
            $n = $count + 1;

            $noTransaksi = 'SP-' . str_pad($n, 4, '0', STR_PAD_LEFT) . '-' . date('Y');
            if (SuratPeringatan::where('no_transaksi', $noTransaksi)->exists()) {
                continue;
            }

            $tgl = Carbon::now()->subDays(rand(0, 365))->format('Y-m-d');
            $tahap = $tahaps[array_rand($tahaps)];

            $nama = $pinjaman->anggota->nama ?? 'Anggota';
            $noAnggota = $pinjaman->anggota->no_anggota ?? '-';
            $plafon = (float) $pinjaman->plafon;
            $noPinjaman = $pinjaman->no_pinjaman ?? '-';
            $jatuhTempo = $pinjaman->jatuh_tempo ?? $tgl;

            $isi = "Dengan hormat,\n"
                . "Sehubungan dengan keterlambatan pembayaran angsuran pinjaman dengan nomor {$noPinjaman} "
                . "atas nama {$nama} (No. Anggota: {$noAnggota}) dengan plafon sebesar Rp "
                . number_format($plafon, 0, ',', '.') . " yang jatuh tempo pada tanggal {$jatuhTempo}, "
                . "kami menyampaikan Surat Peringatan {$tahap} sebagai panggilan resmi dari Koperasi.\n"
                . "Kami mengharapkan Saudara dapat segera melunasi kewajiban angsuran yang tertunggak "
                . "dalam waktu yang telah ditentukan. Apabila pembayaran tidak segera dilakukan, "
                . "maka koperasi akan menempuh langkah-langkah selanjutnya sesuai ketentuan yang berlaku.\n"
                . "Demikian surat peringatan ini kami sampaikan, atas perhatian dan kerja samanya kami ucapkan terima kasih.";

            $statusRand = rand(1, 100);
            $status = $statusRand <= 20 ? 'draft' : ($statusRand <= 85 ? 'posted' : 'batal');

            SuratPeringatan::create([
                'no_transaksi' => $noTransaksi,
                'tgl_transaksi' => $tgl,
                'pinjaman_id' => $pinjaman->id,
                'tahap' => $tahap,
                'isi' => $isi,
                'user_id' => $users->random()->id,
                'kantor_id' => $kantorIds[array_rand($kantorIds)],
                'status' => $status,
                'created_at' => Carbon::parse($tgl)->subDays(rand(0, 3))->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($tgl)->format('Y-m-d H:i:s'),
            ]);

            $count++;
            if ($count % 20 === 0) {
                $this->command->info("Created {$count} surat peringatan records...");
            }
        }

        $this->command->info("Successfully created {$count} surat peringatan records.");
    }
}
