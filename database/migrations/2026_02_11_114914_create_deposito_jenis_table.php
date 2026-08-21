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
        Schema::create('deposito_jenis', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nama');
            $table->string('account_id')->nullable();
            $table->string('jangka_waktu')->nullable();
            $table->string('bunga')->nullable();
            $table->string('account_bunga')->nullable();
            $table->string('rumus_bunga')->nullable();
            $table->string('penalti')->nullable();
            $table->string('account_penalti')->nullable();
            $table->string('pajak')->nullable();
            $table->string('account_pajak')->nullable();
            $table->string('saldo_pajak')->nullable();
            $table->string('insentif')->nullable();
            $table->string('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposito_jenis');
    }
};
