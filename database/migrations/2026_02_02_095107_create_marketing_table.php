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
        Schema::create('marketing', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama')->nullable();
            $table->string('alamat')->nullable();
            $table->string('no_ktp')->nullable();
            $table->string('telepon')->nullable();
            $table->string('no_hp')->nullable();
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
        Schema::dropIfExists('marketing');
    }
};
