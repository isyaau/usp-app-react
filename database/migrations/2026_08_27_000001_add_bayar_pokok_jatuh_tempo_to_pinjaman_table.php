<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom pendukung form transaksi pinjaman (6 tab).
     */
    public function up(): void
    {
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->string('bayar_pokok_per')->nullable()->after('periode');
            $table->string('jatuh_tempo')->nullable()->after('bayar_pokok_per');
        });
    }

    public function down(): void
    {
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->dropColumn(['bayar_pokok_per', 'jatuh_tempo']);
        });
    }
};
