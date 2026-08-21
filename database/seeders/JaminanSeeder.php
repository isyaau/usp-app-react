<?php

namespace Database\Seeders;

use App\Models\Jaminan;
use Illuminate\Database\Seeder;

class JaminanSeeder extends Seeder
{
    public function run(): void
    {
        // Contoh kantor pertama
        Jaminan::create([
            'nama'          => 'Emas',
            'user_id'       => 1,
        ]);

        // Contoh kantor pertama
        Jaminan::create([
            'nama'          => 'Tanpa Agunan',
            'user_id'       => 1,
        ]);
    }
}
