<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel proposal pinjaman (modul tersendiri, 2 kolom) + detail biayanya.
     *
     * Proposal menyimpan draft/entri pinjaman berbasis biaya master
     * (loan_cost_components) dan perhitungan Total Terima di sisi backend.
     */
    public function up(): void
    {
        Schema::create('proposal', function (Blueprint $table) {
            $table->id();
            $table->string('tanggal');
            $table->string('no_bukti');
            $table->string('anggota_id');
            $table->string('jenis_id');
            $table->string('marketing_id')->default('0');
            $table->string('plafon');
            $table->string('bunga');
            $table->string('jangka_waktu');
            $table->string('satuan');
            $table->string('bayar_pokok_per')->default('');
            $table->string('pembayaran')->default('per-jangka');
            $table->string('setiap_saat')->default('0');
            $table->string('jenis_angsuran')->default('Flat');
            $table->string('nominal_angsuran')->default('0');
            $table->string('penggunaan_kredit')->nullable();
            $table->string('jaminan')->nullable();
            $table->string('total_biaya')->default('0');
            $table->string('total_terima')->default('0');
            $table->string('status')->default('1');
            $table->string('kantor_id')->nullable();
            $table->string('user_id');
            $table->timestamps();

            $table->unique('no_bukti');
        });

        Schema::create('proposal_biaya', function (Blueprint $table) {
            $table->id();
            $table->string('proposal_id');
            $table->string('component_id')->default('0');
            $table->string('nama');
            $table->string('nominal');
            $table->string('persen');
            $table->string('account_id');
            $table->string('is_deducted_from_disbursement')->default('0');
            $table->string('user_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_biaya');
        Schema::dropIfExists('proposal');
    }
};
