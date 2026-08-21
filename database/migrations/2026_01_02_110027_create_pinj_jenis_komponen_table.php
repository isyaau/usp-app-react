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
        Schema::create('pinj_jenis_komponen', function (Blueprint $table) {
            $table->id();
            $table->string('pinj_jenis_id');
            $table->string('nama');
            $table->string('nominal');
            $table->string('persen');
            $table->string('account_id');
            $table->string('cair');
            $table->string('tunggakan');
            $table->string('denda_t');
            $table->string('denda_h');
            $table->string('angsuran');
            $table->string('penalti');
            $table->string('rumus_c');
            $table->string('rumus_a');
            $table->string('rumus_p');
            $table->string('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinj_jenis_detail');
    }
};
