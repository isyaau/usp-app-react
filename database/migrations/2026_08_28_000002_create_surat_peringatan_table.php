<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_peringatan', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi', 50)->unique();
            $table->date('tgl_transaksi');
            $table->foreignId('pinjaman_id')->constrained('pinjaman')->cascadeOnDelete();
            $table->enum('tahap', ['SP-1', 'SP-2', 'SP-3'])->default('SP-1');
            $table->text('isi')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kantor_id')->constrained('kantor')->cascadeOnDelete();
            $table->enum('status', ['draft', 'posted', 'batal'])->default('draft');
            $table->timestamps();

            $table->index(['tgl_transaksi', 'kantor_id']);
            $table->index(['pinjaman_id', 'tgl_transaksi']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_peringatan');
    }
};