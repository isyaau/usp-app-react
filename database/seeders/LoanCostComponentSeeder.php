<?php

namespace Database\Seeders;

use App\Models\LoanCostComponent;
use Illuminate\Database\Seeder;

class LoanCostComponentSeeder extends Seeder
{
    use ResolvesAdminUser;

    /**
     * Komponen biaya default untuk tabel Biaya Proposal.
     * Idempoten via updateOrCreate pada kolom name.
     */
    public function run(): void
    {
        $defaults = [
            [
                'name' => 'Simpanan Wajib Pinjaman',
                'calculation_type' => 'flat',
                'amount' => '0',
                'percentage' => '0',
                'is_mandatory' => '1',
                'is_deducted_from_disbursement' => '1',
                'is_paid_separately' => '0',
                'active' => '1',
            ],
            [
                'name' => 'Administrasi',
                'calculation_type' => 'flat',
                'amount' => '12000',
                'percentage' => '0',
                'is_mandatory' => '1',
                'is_deducted_from_disbursement' => '1',
                'is_paid_separately' => '0',
                'active' => '1',
            ],
        ];

        foreach ($defaults as $row) {
            LoanCostComponent::updateOrCreate(
                ['name' => $row['name']],
                array_merge($row, ['user_id' => $this->adminUserId()]),
            );
        }
    }
}
