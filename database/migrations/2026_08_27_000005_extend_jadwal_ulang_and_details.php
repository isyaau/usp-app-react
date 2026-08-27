<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Perluas jadwal_ulang agar sepenuhnya menampung data form 6-tab Pinjaman,
     * dan tambah tabel detail baru (biaya, jaminan, saksi, surat, penjamin)
     * yang meniru tabel pendukung pinjaman.
     */
    public function up(): void
    {
        Schema::table('jadwal_ulang', function (Blueprint $table) {
            $table->string('no_pinjaman_lama', 255)->nullable()->after('no_transaksi');
            $table->string('no_pinjaman', 255)->nullable()->after('no_pinjaman_lama');
            $table->date('tanggal')->nullable()->after('no_pinjaman');
            $table->unsignedBigInteger('anggota_id')->nullable()->after('pinjaman_id');
            $table->unsignedBigInteger('jenis_id')->nullable()->after('anggota_id');
            $table->unsignedBigInteger('jaminan_id')->default(0);
            $table->unsignedBigInteger('marketing_id')->default(0);
            $table->integer('sektor_id')->default(0);
            $table->string('jenis_angsuran', 50)->nullable();
            $table->string('bayar_pokok_per', 50)->nullable();
            $table->string('pembayaran', 50)->nullable()->default('manual');
            $table->date('jatuh_tempo')->nullable();
            $table->string('manual', 5)->nullable()->default('0');
            $table->unsignedBigInteger('tabungan_id')->default(0);
            $table->unsignedBigInteger('kode_id')->default(0);
            $table->string('kode_koreksi', 255)->nullable();
            $table->string('swp_id', 50)->nullable()->default('0');
            $table->string('spp_id', 50)->nullable()->default('0');
            $table->integer('periode')->default(0);
            $table->string('cair_simpanan', 5)->nullable()->default('');
            $table->string('sms', 5)->nullable()->default('');
            $table->string('rekening_koran', 5)->nullable()->default('');
            $table->string('aktif', 5)->nullable()->default('1');
            $table->index('anggota_id');
        });

        Schema::create('jadwal_ulang_biaya', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ulang_id')->constrained('jadwal_ulang')->cascadeOnDelete();
            $table->string('nama');
            $table->string('nominal');
            $table->string('persen');
            $table->string('account_id');
            $table->string('user_id');
            $table->timestamps();
        });

        Schema::create('jadwal_ulang_jaminan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ulang_id')->constrained('jadwal_ulang')->cascadeOnDelete();
            $table->string('nama');
            $table->string('keterangan');
            $table->string('nominal');
            $table->string('user_id');
            $table->timestamps();
        });

        Schema::create('jadwal_ulang_saksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ulang_id')->constrained('jadwal_ulang')->cascadeOnDelete();
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->string('tgl_lahir');
            $table->string('no_ktp');
            $table->string('alamat');
            $table->string('pekerjaan_id');
            $table->string('user_id');
            $table->timestamps();
        });

        Schema::create('jadwal_ulang_surat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ulang_id')->constrained('jadwal_ulang')->cascadeOnDelete();
            $table->string('surat_id');
            $table->string('keterangan');
            $table->string('surat');
            $table->string('user_id');
            $table->timestamps();
        });

        Schema::create('jadwal_ulang_penjamin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ulang_id')->constrained('jadwal_ulang')->cascadeOnDelete();
            $table->string('nama');
            $table->string('alamat');
            $table->string('no_ktp');
            $table->string('hubungan');
            $table->string('ibu');
            $table->string('telepon');
            $table->string('tampil');
            $table->string('user_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_ulang_penjamin');
        Schema::dropIfExists('jadwal_ulang_surat');
        Schema::dropIfExists('jadwal_ulang_saksi');
        Schema::dropIfExists('jadwal_ulang_jaminan');
        Schema::dropIfExists('jadwal_ulang_biaya');

        Schema::table('jadwal_ulang', function (Blueprint $table) {
            $table->dropColumn([
                'no_pinjaman_lama', 'no_pinjaman', 'tanggal', 'anggota_id', 'jenis_id',
                'jaminan_id', 'marketing_id', 'sektor_id', 'jenis_angsuran',
                'bayar_pokok_per', 'pembayaran', 'jatuh_tempo', 'manual', 'tabungan_id',
                'kode_id', 'kode_koreksi', 'swp_id', 'spp_id', 'periode',
                'cair_simpanan', 'sms', 'rekening_koran', 'aktif',
            ]);
        });
    }
};
