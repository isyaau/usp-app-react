<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('simpanan_jenis', function (Blueprint $table) {
            $table->string('harga_saham')->nullable()->after('nominal');
            $table->string('pajak_saldo')->nullable()->after('saldo_pajak');
            $table->string('update_bagi_hasil')->nullable()->after('saham');
        });
    }

    public function down(): void
    {
        Schema::table('simpanan_jenis', function (Blueprint $table) {
            $table->dropColumn(['harga_saham', 'pajak_saldo', 'update_bagi_hasil']);
        });
    }
};
