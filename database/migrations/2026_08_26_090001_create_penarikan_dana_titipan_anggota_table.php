<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penarikan_dana_titipan_anggota', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi', 50)->unique();
            $table->date('tgl_transaksi');
            $table->foreignId('anggota_id')->constrained('anggota')->cascadeOnDelete();
            $table->decimal('nominal_penarikan', 18, 2);
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

    public function down(): void
    {
        Schema::dropIfExists('penarikan_dana_titipan_anggota');
    }
};