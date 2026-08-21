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
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->id();
            $table->string('tanggal');
            $table->string('no_pinjaman');
            $table->string('proposal_id');
            $table->string('anggota_id');
            $table->string('jaminan_id');
            $table->string('jenis_id');
            $table->string('marketing_id');
            $table->string('sektor_id');
            $table->string('angsuran');
            $table->string('plafon');
            $table->string('nominal_angsuran');
            $table->string('bunga');
            $table->string('jangka_waktu');
            $table->string('periode');
            $table->string('satuan');
            $table->string('pembayaran');
            $table->string('manual');
            $table->string('tabungan_id');
            $table->string('kode_id');
            $table->string('kode_koreksi');
            $table->string('swp_id');
            $table->string('spp_id');
            $table->string('angsuranke');
            $table->string('rekening_koran');
            $table->string('cair_simpanan');
            $table->string('sms');
            $table->string('aktif');
            $table->string('kantor_id');
            $table->string('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjaman');
    }
};
