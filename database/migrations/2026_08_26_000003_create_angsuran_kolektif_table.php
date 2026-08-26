<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angsuran_kolektif', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi', 50)->unique();
            $table->date('tgl_transaksi');
            $table->foreignId('kelompok_id')->constrained('kelompok')->cascadeOnDelete();
            $table->enum('jenis', ['angsuran', 'penalti', 'angsuran_dan_setoran']);
            $table->enum('metode_pembayaran', ['tunai', 'debet_simpanan', 'bank', 'custom']);
            $table->decimal('nominal_total', 18, 2)->default(0);
            $table->integer('jumlah_anggota')->default(0);
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kantor_id')->constrained('kantor')->cascadeOnDelete();
            $table->enum('status', ['draft', 'posted', 'batal'])->default('draft');
            $table->timestamps();

            $table->index(['tgl_transaksi', 'kantor_id']);
            $table->index(['kelompok_id', 'tgl_transaksi']);
            $table->index('status');
            $table->index('jenis');
            $table->index('metode_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('angsuran_kolektif');
    }
};
