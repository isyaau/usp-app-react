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
        Schema::create('simpanan_rencana', function (Blueprint $table) {
            $table->id();
            $table->string('tanggal_mulai');
            $table->string('tanggal_jatuhtempo');
            $table->string('no_bukti');
            $table->string('jangka_waktu');
            $table->string('satuan');
            $table->string('nominal');
            $table->string('bunga');
            $table->string('keterangan');
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
        Schema::dropIfExists('simpanan_rencana');
    }
};
