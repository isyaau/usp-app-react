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
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->string('no_anggota');
            $table->string('nama');
            $table->string('alamat');
            $table->string('kelompok_id');
            $table->string('pin');

            // Alamat Indonesia
            $table->unsignedBigInteger('provinsi_id');
            $table->unsignedBigInteger('kota_id');
            $table->unsignedBigInteger('kecamatan_id');
            $table->unsignedBigInteger('kelurahan_id');

            $table->string('email');
            $table->string('tempat_lahir');
            $table->string('tgl_lahir');
            $table->string('jenis_kelamin');
            // Biodata Lainnya
            $table->string('agama');
            $table->string('pekerjaan');
            $table->string('pendidikan');
            $table->string('status_perkawinan')->nullable();
            $table->string('pasangan')->nullable();
            $table->string('telepon')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('jenis_identitas');
            $table->string('no_identitas');
            $table->string('npwp')->nullable();
            $table->string('ibu');
            // Data Keanggotaan
            $table->string('hutang')->nullable();
            $table->string('harga_id')->nullable();
            $table->string('foto');
            // Pengurus
            $table->integer('pengurus')->nullable()->default(0);
            $table->string('pengurus_jabatan')->nullable();
            $table->string('tgl_pengurus_diangkat')->nullable();
            $table->string('pengurus_berhenti')->nullable();
            $table->string('tgl_pengurus_berhenti')->nullable();
            // Pengawas
            $table->integer('pengawas')->nullable()->default(0);
            $table->string('pengawas_jabatan')->nullable();
            $table->string('tgl_pengawas_diangkat')->nullable();
            $table->string('pengawas_berhenti')->nullable();
            $table->string('tgl_pengawas_berhenti')->nullable();

            // Waris
            $table->string('waris1')->nullable();
            $table->string('hubungan_waris1')->nullable();
            $table->string('waris2')->nullable();
            $table->string('hubungan_waris2')->nullable();

            // Keuangan
            $table->string('blokir_pinjaman')->nullable();
            $table->string('bagian_id')->nullable();
            $table->string('nomor_rekening')->nullable();

            // Status Anggota
            $table->integer('status')->default(1);
            $table->string('tgl_anggota_berhenti')->nullable();
            $table->string('anggota_berhenti')->nullable();
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
        Schema::dropIfExists('anggota');
    }
};
