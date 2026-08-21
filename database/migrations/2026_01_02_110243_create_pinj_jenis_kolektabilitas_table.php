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
        Schema::create('pinj_jenis_kolektabilitas', function (Blueprint $table) {
            $table->id();
            $table->string('pinj_jenis_id');
            $table->string('kualitas_id');
            $table->string('keterangan');
            $table->string('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinj_jenis_detail_2');
    }
};
