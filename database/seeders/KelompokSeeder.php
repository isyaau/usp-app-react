<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelompok;

class KelompokSeeder extends Seeder
{
    public function run(): void
    {
        Kelompok::create([
            'kode' => 'KL-001',
            'nama' => 'Kelompok A',
            'ketua_id' => null,
            'user_id' => 1,
        ]);

        Kelompok::create([
            'kode' => 'KL-002',
            'nama' => 'Kelompok B',
            'ketua_id' => null,
            'user_id' => 1,
        ]);

        Kelompok::create([
            'kode' => 'KL-003',
            'nama' => 'Kelompok C',
            'ketua_id' => null,
            'user_id' => 1,
        ]);
    }
}
