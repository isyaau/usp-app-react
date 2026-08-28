<?php

namespace Database\Seeders;

use App\Models\JaminanDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    protected static ?string $password;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AccGroupSeeder::class,
            AccHeaderSeeder::class,
            AccountSeeder::class,
            ParameterSeeder::class,
            KodeTransaksiSeeder::class,
            KelompokSeeder::class,
            AnggotaSeeder::class,
            MarketingSeeder::class,
            KantorSeeder::class,
            DepositoJenisSeeder::class,
            SimpananJenisSeeder::class,
            SimpananSeeder::class,
            JaminanSeeder::class,
            JaminanDetailSeeder::class,
            KasHarianSeeder::class,
            PencairanPinjamanSeeder::class,
            LoanCostComponentSeeder::class,
            JadwalUlangSeeder::class,
            ProposalSeeder::class,
        ]);
    }
}
