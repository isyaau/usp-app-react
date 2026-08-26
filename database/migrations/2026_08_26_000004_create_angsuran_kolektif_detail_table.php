<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angsuran_kolektif_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('angsuran_kolektif_id')->constrained('angsuran_kolektif')->cascadeOnDelete();
            $table->foreignId('pinjaman_id')->constrained('pinjaman')->cascadeOnDelete();
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnDelete();
            $table->integer('angsuran_ke');
            $table->decimal('nominal_pokok', 18, 2)->default(0);
            $table->decimal('nominal_bunga', 18, 2)->default(0);
            $table->decimal('total_angsuran', 18, 2)->default(0);
            $table->decimal('setoran_simpanan', 18, 2)->nullable();
            $table->decimal('denda', 18, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['angsuran_kolektif_id']);
            $table->index(['pinjaman_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('angsuran_kolektif_detail');
    }
};
