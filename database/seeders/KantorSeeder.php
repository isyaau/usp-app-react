<?php

namespace Database\Seeders;

use App\Models\Kantor;
use Illuminate\Database\Seeder;

class KantorSeeder extends Seeder
{
    use ResolvesAdminUser;

    public function run(): void
    {
        // Contoh kantor pertama
        Kantor::create([
            'kode'          => 'KT-001',
            'nama_kantor'   => 'Kantor Pusat Madiun',
            'alamat_kantor' => 'Jl. Pahlawan No. 1, Madiun',
            'provinsi_id'   => 34,
            'kota_id'       => 3404,
            'kecamatan_id'  => 340402,
            'kelurahan_id'  => 3404022007,
            'pejabat'       => 'Ahmad Santoso',
            'jabatan'       => 'Kepala Kantor',
            'bendahara'     => 'Rina Saputra',
            'user_id'       => $this->adminUserId(),
        ]);

        // Contoh kantor kedua
        Kantor::create([
            'kode'          => 'KT-002',
            'nama_kantor'   => 'Kantor Cabang Ngawi',
            'alamat_kantor' => 'Jl. Merdeka No. 10, Ngawi',
            'provinsi_id'   => 34,
            'kota_id'       => 3404,
            'kecamatan_id'  => 340402,
            'kelurahan_id'  => 3404022007,
            'pejabat'       => 'Budi Santoso',
            'jabatan'       => 'Kepala Cabang',
            'bendahara'     => 'Siti Aminah',
            'user_id'       => $this->adminUserId(),
        ]);
    }
}
