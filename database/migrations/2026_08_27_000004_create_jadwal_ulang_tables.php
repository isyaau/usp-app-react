<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadwal Ulang Pinjaman.
     *
     * Merekam perhitungan ulang jadwal angsuran sebuah pinjaman (reschedule).
     * Header menyimpan parameter jadwal baru + plafon/sisa pokok yang dipakai;
     * jadwal_ulang_detail menyimpan deret angsuran hasil LoanCalculationService.
     */
    public function up(): void
    {
        Schema::create('jadwal_ulang', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi', 50)->unique();
            $table->date('tgl_transaksi');
            $table->foreignId('pinjaman_id')->constrained('pinjaman')->cascadeOnDelete();
            $table->decimal('plafon', 18, 2)->default(0);
            $table->decimal('sisa_pokok', 18, 2)->default(0);
            $table->decimal('bunga', 8, 2)->default(0);
            $table->decimal('jangka_waktu', 12, 0)->default(0);
            $table->string('satuan', 20);
            $table->string('metode', 50);
            $table->decimal('nominal_angsuran', 18, 2)->default(0);
            $table->decimal('total_bunga', 18, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kantor_id')->constrained('kantor')->cascadeOnDelete();
            $table->enum('status', ['draft', 'posted', 'batal'])->default('draft');
            $table->timestamps();

            $table->index(['tgl_transaksi', 'kantor_id']);
            $table->index(['pinjaman_id', 'status']);
            $table->index('status');
        });

        Schema::create('jadwal_ulang_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ulang_id')->constrained('jadwal_ulang')->cascadeOnDelete();
            $table->integer('angsuran_ke');
            $table->decimal('nominal_pokok', 18, 2)->default(0);
            $table->decimal('nominal_bunga', 18, 2)->default(0);
            $table->decimal('total_angsuran', 18, 2)->default(0);
            $table->decimal('sisa_pokok', 18, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_ulang_detail');
        Schema::dropIfExists('jadwal_ulang');
    }
};
