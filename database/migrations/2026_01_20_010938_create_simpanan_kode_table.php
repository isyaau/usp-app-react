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
        Schema::create('simpanan_kode', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nama');
            $table->string('account_debet');
            $table->string('account_kredit');
            $table->string('setoran')->nullable();
            $table->string('tarikan')->nullable();
            $table->string('transfer')->nullable();
            $table->string('pokok')->nullable();
            $table->string('wajib')->nullable();
            $table->string('sukarela')->nullable();
            $table->string('pinjaman')->nullable();
            $table->string('saham')->nullable();
            $table->string('pokok_pinjaman')->nullable();
            $table->string('rencana')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simpanan_kode');
    }
};
