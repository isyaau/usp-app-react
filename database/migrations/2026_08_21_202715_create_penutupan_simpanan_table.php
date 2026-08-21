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
        Schema::create('penutupan_simpanan', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi', 50)->unique();
            $table->date('tgl_transaksi');
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnDelete();
            $table->foreignId('simpanan_id')->constrained('simpanan')->cascadeOnDelete();
            $table->foreignId('kode_transaksi_id')->constrained('simpanan_kode')->cascadeOnDelete();
            $table->decimal('nominal', 18, 2);
            $table->decimal('nominal_bunga', 18, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kantor_id')->constrained('kantor')->cascadeOnDelete();
            $table->enum('status', ['draft', 'posted', 'batal'])->default('draft');
            $table->timestamps();
            
            $table->index(['tgl_transaksi', 'kantor_id']);
            $table->index(['anggota_id', 'tgl_transaksi']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penutupan_simpanan');
    }
};
