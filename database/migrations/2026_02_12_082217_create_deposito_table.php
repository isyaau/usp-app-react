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
        Schema::create('deposito', function (Blueprint $table) {
            $table->id();
            $table->string('tanggal');
            $table->string('no_deposito');
            $table->string('anggota_id');
            $table->string('jenis_id');
            $table->string('marketing_id')->nullable();
            $table->string('qq');
            $table->string('jangka_waktu');
            $table->string('bunga');
            $table->string('nominal');
            $table->string('otomatis');
            $table->string('bayar_bunga');
            $table->string('diawal');
            $table->string('bunga_accrual');
            $table->string('account_bungaaccrual')->nullable();
            $table->string('tabunganbunga_id')->nullable();
            $table->string('tabungantempo_id')->nullable();
            $table->string('bayar_jatuhtempo');
            $table->string('blokir');
            $table->string('kantor_id');
            $table->string('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposito');
    }
};
