<?php

namespace Database\Seeders;

use App\Models\KasHarian;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class KasHarianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@kopinka.com')->first()
            ?? User::where('role', 'superadmin')->first()
            ?? User::first();

        if (!$admin) {
            return;
        }

        $sampleData = [
            [
                'tanggal' => Carbon::today()->subDays(4),
                'kas_awal' => 5000000,
                'kas_masuk' => 2500000,
                'kas_keluar' => 1200000,
            ],
            [
                'tanggal' => Carbon::today()->subDays(3),
                'kas_awal' => 6300000,
                'kas_masuk' => 3100000,
                'kas_keluar' => 850000,
            ],
            [
                'tanggal' => Carbon::today()->subDays(2),
                'kas_awal' => 8550000,
                'kas_masuk' => 1800000,
                'kas_keluar' => 2100000,
            ],
            [
                'tanggal' => Carbon::today()->subDays(1),
                'kas_awal' => 8250000,
                'kas_masuk' => 4200000,
                'kas_keluar' => 1500000,
            ],
            [
                'tanggal' => Carbon::today(),
                'kas_awal' => 10950000,
                'kas_masuk' => 0,
                'kas_keluar' => 0,
            ],
        ];

        foreach ($sampleData as $data) {
            $data['kas_akhir'] = $data['kas_awal'] + $data['kas_masuk'] - $data['kas_keluar'];
            $data['user_id'] = $admin->id;

            KasHarian::updateOrCreate(
                ['tanggal' => $data['tanggal']],
                $data
            );
        }
    }
}
