<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pinj_jenis', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nama');
            $table->string('account_id');
            $table->string('bunga');
            $table->string('account_bunga');
            $table->string('ditangguhkan');
            $table->string('account_ditangguhkan');
            $table->string('kas');
            $table->string('account_bank')->nullable();
            $table->string('insentif');
            $table->string('simpanan');
            $table->string('swp_cair');
            $table->string('swp_angsur');
            $table->string('swp_persen');
            $table->string('nominal_simpanan');
            $table->string('simpanan_pokok');
            $table->string('nominal_simpanan_pokok');
            $table->string('toleransi');
            $table->string('angsuran');
            $table->string('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinj_jenis');
    }
};
