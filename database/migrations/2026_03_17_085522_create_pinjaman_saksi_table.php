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
        Schema::create('pinjaman_saksi', function (Blueprint $table) {
            $table->id();
            $table->string('pinjaman_id');
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->string('tgl_lahir');
            $table->string('no_ktp');
            $table->string('alamat');
            $table->string('pekerjaan_id');
            $table->string('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjaman_saksi');
    }
};
