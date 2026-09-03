<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DepositoJenis;

class DepositoJenisSeeder extends Seeder
{
    use ResolvesAdminUser;

    public function run(): void
    {
        DepositoJenis::create([
            'kode' => 'DJ-001',
            'nama' => 'Deposito Reguler',
            'account_id' => 1,
            'jangka_waktu' => '12', // bulan
            'bunga' => '5', // persen
            'account_bunga' => 1,
            'rumus_bunga' => 1,
            'penalti' => '2', // persen
            'account_penalti' => 1,
            'pajak' => '10', // persen
            'account_pajak' => 1,
            'saldo_pajak' => null,
            'insentif' => null,
            'user_id' => $this->adminUserId(),
        ]);

        DepositoJenis::create([
            'kode' => 'DJ-002',
            'nama' => 'Deposito Premium',
            'account_id' => 1,
            'jangka_waktu' => '24',
            'bunga' => '6',
            'account_bunga' => 1,
            'rumus_bunga' => 1,
            'penalti' => '1.5',
            'account_penalti' => 1,
            'pajak' => '10',
            'account_pajak' => 1,
            'saldo_pajak' => null,
            'insentif' => null,
            'user_id' => $this->adminUserId(),
        ]);

        DepositoJenis::create([
            'kode' => 'DJ-003',
            'nama' => 'Deposito Silver',
            'account_id' => 1,
            'jangka_waktu' => '6',
            'bunga' => '4',
            'account_bunga' => 1,
            'rumus_bunga' => 1,
            'penalti' => '2',
            'account_penalti' => 1,
            'pajak' => '10',
            'account_pajak' => 1,
            'saldo_pajak' => null,
            'insentif' => null,
            'user_id' => $this->adminUserId(),
        ]);
    }
}
