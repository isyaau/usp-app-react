<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SimpananJenis;

class SimpananJenisSeeder extends Seeder
{
    public function run(): void
    {
        SimpananJenis::create([
            'kode' => 'SB-001',
            'nama' => 'Deposito Tiap Bulan',
            'account_id' => 1,
            'minimum' => '1000000',
            'mengendap' => '50000',
            'bunga_id' => 1,
            'jenis_bunga' => 'Tetap',
            'bunga' => '5',
            'account_bunga' => 1,
            'rumus_bunga' => 1,
            'bulan' => '12',
            'biaya_id' => 1,
            'biaya' => '10000',
            'account_biaya' => 1,
            'pajak_id' => 1,
            'pajak' => '10',
            'account_pajak' => 1,
            'saldo_pajak' => '0',
            'android' => '0',
            'nominal_android' => '0',
            'account_android' => 1,
            'nominal' => '1000000',
            'jenis' => 'Reguler',
            'setor_id' => 1,
            'tarik_id' => 1,
            'insentif' => '0',
            'saham' => '0',
            'user_id' => 1,
        ]);

        SimpananJenis::create([
            'kode' => 'SB-002',
            'nama' => 'Deposito Diawal',
            'account_id' => 2,
            'minimum' => '500000',
            'mengendap' => '25000',
            'bunga_id' => 2,
            'jenis_bunga' => 'Tetap',
            'bunga' => '4',
            'account_bunga' => 2,
            'rumus_bunga' => 1,
            'bulan' => '6',
            'biaya_id' => 2,
            'biaya' => '5000',
            'account_biaya' => 2,
            'pajak_id' => 2,
            'pajak' => '10',
            'account_pajak' => 2,
            'saldo_pajak' => '0',
            'android' => '0',
            'nominal_android' => '0',
            'account_android' => 2,
            'nominal' => '500000',
            'jenis' => 'Reguler',
            'setor_id' => 2,
            'tarik_id' => 2,
            'insentif' => '0',
            'saham' => '0',
            'user_id' => 1,
        ]);

        SimpananJenis::create([
            'kode' => 'SB-003',
            'nama' => 'Deposito Diakhir',
            'account_id' => 3,
            'minimum' => '750000',
            'mengendap' => '30000',
            'bunga_id' => 3,
            'jenis_bunga' => 'Tetap',
            'bunga' => '4.5',
            'account_bunga' => 3,
            'rumus_bunga' => 1,
            'bulan' => '9',
            'biaya_id' => 3,
            'biaya' => '7000',
            'account_biaya' => 3,
            'pajak_id' => 3,
            'pajak' => '10',
            'account_pajak' => 3,
            'saldo_pajak' => '0',
            'android' => '0',
            'nominal_android' => '0',
            'account_android' => 3,
            'nominal' => '750000',
            'jenis' => 'Reguler',
            'setor_id' => 3,
            'tarik_id' => 3,
            'insentif' => '0',
            'saham' => '0',
            'user_id' => 1,
        ]);
    }
}
