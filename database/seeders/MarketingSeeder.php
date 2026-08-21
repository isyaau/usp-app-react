<?php

namespace Database\Seeders;

use App\Models\Marketing;
use Illuminate\Database\Seeder;

class MarketingSeeder extends Seeder
{
    public function run(): void
    {
        // Contoh marketing tunggal
        Marketing::create([
            'kode'       => 'MK-001',
            'nama'       => 'Rina Saputra',
            'alamat'     => 'Jl. Sudirman No. 12, Madiun',
            'no_ktp'     => '3501011234567890',
            'telepon'    => '0351-123456',
            'no_hp'      => '081234567890',
            'aktif'      => '1', // 1 = aktif, 0 = nonaktif
            'kantor_id'  => '1', // contoh kantor
            'user_id'    => '1', // user pembuat
        ]);

        // Contoh beberapa marketing tambahan
        Marketing::create([
            'kode'       => 'MK-002',
            'nama'       => 'Budi Santoso',
            'alamat'     => 'Jl. Diponegoro No. 5, Madiun',
            'no_ktp'     => '3501029876543210',
            'telepon'    => '0351-654321',
            'no_hp'      => '081298765432',
            'aktif'      => '1',
            'kantor_id'  => '1',
            'user_id'    => '1',
        ]);

        Marketing::create([
            'kode'       => 'MK-003',
            'nama'       => 'Siti Aminah',
            'alamat'     => 'Jl. Pahlawan No. 20, Madiun',
            'no_ktp'     => '3501031231231234',
            'telepon'    => '0351-789012',
            'no_hp'      => '081345678901',
            'aktif'      => '1',
            'kantor_id'  => '2',
            'user_id'    => '1',
        ]);
    }
}
