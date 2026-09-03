<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tag pembuat/perubah data (user_id) untuk tabel yang belum memilikinya:
 * acc_group, angsuran_kolektif_detail, dan jadwal_ulang_detail.
 *
 * Kolom dibuat nullable dulu, diisi user admin, lalu diberi FK ke users.
 * Sengaja tidak CASCADE agar histori perubahan tidak ikut terhapus
 * ketika user dihapus.
 */
return new class extends Migration
{
    private const TABLES = ['acc_group', 'angsuran_kolektif_detail', 'jadwal_ulang_detail'];

    public function up(): void
    {
        $adminId = DB::table('users')->where('role', 'superadmin')->min('id')
            ?? DB::table('users')->min('id');

        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'user_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            });

            if ($adminId !== null && DB::table($table)->count() > 0) {
                DB::table($table)->update(['user_id' => $adminId]);
            }

            Schema::table($table, function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users');
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'user_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};