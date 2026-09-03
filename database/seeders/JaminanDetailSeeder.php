<?php

namespace Database\Seeders;

use App\Models\JaminanDetail;
use Illuminate\Database\Seeder;

class JaminanDetailSeeder extends Seeder
{
    use ResolvesAdminUser;

    public function run(): void
    {
        // Contoh kantor pertama
        JaminanDetail::create([
            'jaminan_id'          => '1',
            'detail'          => 'Berat',
            'user_id'       => $this->adminUserId(),
        ]);

        JaminanDetail::create([
            'jaminan_id'          => '1',
            'detail'          => 'Kadar',
            'user_id'       => $this->adminUserId(),
        ]);
    }
}
