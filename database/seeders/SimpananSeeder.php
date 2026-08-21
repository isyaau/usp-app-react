<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Simpanan;

class SimpananSeeder extends Seeder
{
    public function run(): void
    {
        Simpanan::create([
            'tanggal' => '2026-03-10',
            'no_rekening' => '1001001',
            'anggota_id' => '1',
            'jenis_id' => '1',
            'marketing_id' => '1',
            'qq' => null,
            'bunga' => '5',
            'baris' => '1',
            'ttd' => null,
            'blokir_simpanan' => '0',
            'blokir_nominal' => '0',
            'nominal_blokir' => '0',
            'blokir_tgl' => null,
            'tgl_blokir' => null,
            'nominal_setor' => '1000000',
            'sms' => '0',
            'aktif' => '1',
            'kantor_id' => '1',
            'user_id' => '1',
        ]);

        Simpanan::create([
            'tanggal' => '2026-03-11',
            'no_rekening' => '1001002',
            'anggota_id' => '1',
            'jenis_id' => '2',
            'marketing_id' => '2',
            'qq' => null,
            'bunga' => '4',
            'baris' => '1',
            'ttd' => null,
            'blokir_simpanan' => '0',
            'blokir_nominal' => '0',
            'nominal_blokir' => '0',
            'blokir_tgl' => null,
            'tgl_blokir' => null,
            'nominal_setor' => '500000',
            'sms' => '0',
            'aktif' => '1',
            'kantor_id' => '1',
            'user_id' => '1',
        ]);

        Simpanan::create([
            'tanggal' => '2026-03-12',
            'no_rekening' => '1001003',
            'anggota_id' => '1',
            'jenis_id' => '1',
            'marketing_id' => '1',
            'qq' => null,
            'bunga' => '4.5',
            'baris' => '1',
            'ttd' => null,
            'blokir_simpanan' => '0',
            'blokir_nominal' => '0',
            'nominal_blokir' => '0',
            'blokir_tgl' => null,
            'tgl_blokir' => null,
            'nominal_setor' => '750000',
            'sms' => '0',
            'aktif' => '1',
            'kantor_id' => '1',
            'user_id' => '1',
        ]);
    }
}
