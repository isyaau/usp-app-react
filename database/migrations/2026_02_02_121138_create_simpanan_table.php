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
        Schema::create('simpanan', function (Blueprint $table) {
            $table->id();
            $table->string('tanggal')->nullable();
            $table->string('no_rekening');
            $table->string('anggota_id');
            $table->string('jenis_id');
            $table->string('marketing_id');
            $table->string('qq')->nullable();
            $table->string('bunga')->nullable();
            $table->string('baris')->nullable();
            $table->string('ttd')->nullable();
            $table->string('blokir_simpanan')->nullable();
            $table->string('blokir_nominal')->nullable();
            $table->string('nominal_blokir')->nullable();
            $table->string('blokir_tgl')->nullable();
            $table->string('tgl_blokir')->nullable();
            $table->string('nominal_setor')->nullable();
            $table->string('sms')->nullable();
            $table->string('aktif')->nullable();
            $table->string('kantor_id')->nullable();
            $table->string('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simpanan');
    }
};
