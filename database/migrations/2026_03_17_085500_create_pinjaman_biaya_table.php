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
        Schema::create('pinjaman_biaya', function (Blueprint $table) {
            $table->id();
            $table->string('pinjaman_id');
            $table->string('nama');
            $table->string('nominal');
            $table->string('persen');
            $table->string('account_id');
            $table->string('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjaman_biaya');
    }
};
