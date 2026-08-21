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
        Schema::create('simpanan_jenis', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->nullable();
            $table->string('nama')->nullable();
            $table->string('account_id')->nullable();
            $table->string('minimum')->nullable();
            $table->string('mengendap')->nullable();
            $table->string('bunga_id')->nullable();
            $table->string('jenis_bunga')->nullable();
            $table->string('bunga')->nullable();
            $table->string('account_bunga')->nullable();
            $table->string('rumus_bunga')->nullable();
            $table->string('bulan')->nullable();
            $table->string('biaya_id')->nullable();
            $table->string('biaya')->nullable();
            $table->string('account_biaya')->nullable();
            $table->string('pajak_id')->nullable();
            $table->string('pajak')->nullable();
            $table->string('account_pajak')->nullable();
            $table->string('saldo_pajak')->nullable();
            $table->string('android')->nullable();
            $table->string('nominal_android')->nullable();
            $table->string('account_android')->nullable();
            $table->string('nominal')->nullable();
            $table->string('jenis')->nullable();
            $table->string('setor_id')->nullable();
            $table->string('tarik_id')->nullable();
            $table->string('insentif')->nullable();
            $table->string('saham')->nullable();
            $table->string('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simpanan_jenis');
    }
};
