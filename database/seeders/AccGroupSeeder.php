<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parameter;

class AccGroupSeeder extends Seeder
{
    use ResolvesAdminUser;

    public function run(): void
    {
        $data = [
            ['nama' => 'Ahli Waris Anggota Kelompok(n)', 'jenis' => '1'],
            ['nama' => 'Alamat', 'jenis' => '1'],
            ['nama' => 'Alamat Anggota Kelompok(n)', 'jenis' => '1'],
            ['nama' => 'Anggota', 'jenis' => '1'],
            ['nama' => 'Angsuran', 'jenis' => '1'],
            ['nama' => 'Angsuran Ke', 'jenis' => '1'],
            ['nama' => 'Angsuran Terbilang', 'jenis' => '1'],
            ['nama' => 'AngsuranBagiHasil', 'jenis' => '2'],
            ['nama' => 'AngsuranPokok', 'jenis' => '2'],
            ['nama' => 'Bagi Hasil', 'jenis' => '1'],
            ['nama' => 'Bagi Hasil Per Bulan', 'jenis' => '1'],
            ['nama' => 'Bagi Hasil Per Bulan Terbilang', 'jenis' => '1'],
            ['nama' => 'Bagi Hasil Terbilang', 'jenis' => '1'],
            ['nama' => 'BagiHasilNominal', 'jenis' => '2'],
            ['nama' => 'Nominal', 'jenis' => '2'],
            ['nama' => 'DendaTunggakan', 'jenis' => '2'],
            ['nama' => 'SisaBagiHasil', 'jenis' => '2'],
            ['nama' => 'SisaPinjaman', 'jenis' => '2'],
            ['nama' => 'SisaPokok', 'jenis' => '2'],
            ['nama' => 'Terlambat', 'jenis' => '2'],
            ['nama' => 'Tunggakan', 'jenis' => '2'],
        ];

        foreach ($data as $item) {
            Parameter::firstOrCreate(
                ['nama' => $item['nama']],
                [
                    'jenis'   => $item['jenis'],
                    'user_id' => $this->adminUserId(),
                ]
            );
        }
    }
}
