<?php

namespace Database\Seeders;

use App\Models\AngsuranPinjaman;
use App\Models\Kantor;
use App\Models\Pinjaman;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AngsuranPinjamanSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $kantors = Kantor::all();
        $pinjamanList = Pinjaman::all();

        if ($users->isEmpty() || $pinjamanList->isEmpty()) {
            $this->command->warn('Tidak ada data user atau pinjaman.');
            return;
        }

        $kantorIds = $kantors->pluck('id')->toArray();

        $count = 0;
        $targetCount = 100;

        while ($count < $targetCount) {
            $pinjaman = $pinjamanList->random();
            $angsuranKe = rand(1, 12);
            $nominalPokok = rand(500000, 5000000);
            $nominalBunga = round($nominalPokok * 0.02);
            $total = $nominalPokok + $nominalBunga;
            $denda = rand(0, 10) > 8 ? rand(10000, 200000) : 0;
            $tgl = Carbon::now()->subDays(rand(0, 365))->format('Y-m-d');

            $statusRand = rand(1, 100);
            $status = $statusRand <= 20 ? 'draft' : ($statusRand <= 80 ? 'posted' : 'batal');

            AngsuranPinjaman::create([
                'no_transaksi' => 'AP-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT) . '-' . date('Y'),
                'tgl_transaksi' => $tgl,
                'pinjaman_id' => $pinjaman->id,
                'angsuran_ke' => $angsuranKe,
                'nominal_pokok' => $nominalPokok,
                'nominal_bunga' => $nominalBunga,
                'total_angsuran' => $total,
                'denda' => $denda,
                'keterangan' => 'Angsuran ke-' . $angsuranKe,
                'user_id' => $users->random()->id,
                'kantor_id' => !empty($kantorIds) ? $kantorIds[array_rand($kantorIds)] : 1,
                'status' => $status,
                'created_at' => Carbon::parse($tgl)->subDays(rand(0, 3))->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($tgl)->format('Y-m-d H:i:s'),
            ]);

            $count++;
            if ($count % 20 === 0) {
                $this->command->info("Created {$count} angsuran pinjaman records...");
            }
        }

        $this->command->info("Successfully created {$count} angsuran pinjaman records.");
    }
}
