<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angsuran_pinjaman', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi', 50)->unique();
            $table->date('tgl_transaksi');
            $table->foreignId('pinjaman_id')->constrained('pinjaman')->cascadeOnDelete();
            $table->integer('angsuran_ke');
            $table->decimal('nominal_pokok', 18, 2)->default(0);
            $table->decimal('nominal_bunga', 18, 2)->default(0);
            $table->decimal('total_angsuran', 18, 2)->default(0);
            $table->decimal('denda', 18, 2)->default(0);
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('angsuran_pinjaman');
    }
};
