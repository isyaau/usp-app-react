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
        Schema::create('simpanan_bunga', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_id')->nullable();
            $table->string('minimal')->nullable();
            $table->string('maksimal')->nullable();
            $table->string('bunga')->nullable();
            $table->string('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simpanan_bunga');
    }
};
