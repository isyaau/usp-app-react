<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master komponen biaya pinjaman (loan_cost_component).
     * Mendorong tabel Biaya pada form/entri Proposal.
     *
     * - calculation_type: 'flat' | 'percentage'
     * - amount: nominal tetap (jika flat)
     * - percentage: persentase (jika percentage)
     * - account_id: referensi ke tabel account
     * - is_mandatory: apakah biaya wajib
     * - is_deducted_from_disbursement: apakah mengurangi Total Terima
     * - is_paid_separately: apakah dibayar terpisah
     * - active: status aktif
     */
    public function up(): void
    {
        Schema::create('loan_cost_components', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('calculation_type')->default('flat');
            $table->string('amount')->default('0');
            $table->string('percentage')->default('0');
            $table->string('account_id')->default('0');
            $table->string('is_mandatory')->default('0');
            $table->string('is_deducted_from_disbursement')->default('0');
            $table->string('is_paid_separately')->default('0');
            $table->string('active')->default('1');
            $table->string('user_id')->default('0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_cost_components');
    }
};
