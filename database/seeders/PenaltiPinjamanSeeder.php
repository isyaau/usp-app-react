<?php

namespace Database\Seeders;

use App\Models\Kantor;
use App\Models\PenaltiPinjaman;
use App\Models\Pinjaman;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PenaltiPinjamanSeeder extends Seeder
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
            $nominalPenalti = rand(100000, 5000000);
            $denda = rand(0, 10) > 7 ? rand(50000, 500000) : 0;
            $tgl = Carbon::now()->subDays(rand(0, 365))->format('Y-m-d');

            $statusRand = rand(1, 100);
            $status = $statusRand <= 20 ? 'draft' : ($statusRand <= 80 ? 'posted' : 'batal');

            PenaltiPinjaman::create([
                'no_transaksi' => 'PP-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT) . '-' . date('Y'),
                'tgl_transaksi' => $tgl,
                'pinjaman_id' => $pinjaman->id,
                'nominal_penalti' => $nominalPenalti,
                'denda' => $denda,
                'keterangan' => 'Penalti keterlambatan angsuran',
                'user_id' => $users->random()->id,
                'kantor_id' => !empty($kantorIds) ? $kantorIds[array_rand($kantorIds)] : 1,
                'status' => $status,
                'created_at' => Carbon::parse($tgl)->subDays(rand(0, 3))->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($tgl)->format('Y-m-d H:i:s'),
            ]);

            $count++;
            if ($count % 20 === 0) {
                $this->command->info("Created {$count} penalti pinjaman records...");
            }
        }

        $this->command->info("Successfully created {$count} penalti pinjaman records.");
    }
}
