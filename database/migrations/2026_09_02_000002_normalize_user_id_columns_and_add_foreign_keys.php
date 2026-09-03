<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Normalisasi seluruh kolom user_id menjadi BIGINT + FK ke tabel users:
 *
 * 1. Tabel yang masih menyimpan user_id sebagai VARCHAR (proposal,
 *    proposal_biaya, loan_cost_components, dan 5 tabel detail jadwal ulang)
 *    diubah menjadi BIGINT — PostgreSQL menolak perbandingan varchar = int.
 * 2. Seluruh tabel usang yang user_id-nya sudah BIGINT (dikonversi oleh
 *    migrasi 2026_08_21) tetapi tanpa FK constraint diberi FK ke users.
 *
 * FK sengaja TANPA CASCADE supaya histori "siapa yang mengubah data" tidak
 * ikut terhapus saat user dihapus.
 */
return new class extends Migration
{
    /** Tabel yang masih menyimpan user_id sebagai VARCHAR. */
    private const VARCHAR_USER_ID = [
        'proposal',
        'proposal_biaya',
        'loan_cost_components',
        'jadwal_ulang_biaya',
        'jadwal_ulang_jaminan',
        'jadwal_ulang_saksi',
        'jadwal_ulang_surat',
        'jadwal_ulang_penjamin',
    ];

    /** Tabel lama dengan user_id BIGINT yang belum memiliki FK constraint. */
    private const BIGINT_USER_ID = [
        'kelompok', 'kantor', 'anggota', 'acc_header', 'account',
        'parameter', 'simpanan_kode', 'simpanan_jenis', 'simpanan_jenis_kode',
        'simpanan_bunga', 'marketing', 'simpanan', 'simpanan_rencana',
        'simpanan_rencana_detail', 'deposito_jenis', 'deposito',
        'jaminan', 'jaminan_detail', 'pinjaman', 'pinjaman_biaya',
        'pinjaman_surat', 'pinjaman_jaminan', 'pinjaman_saksi',
        'pinjaman_penjamin', 'pinjaman_template',
        'pinj_jenis', 'pinj_jenis_komponen', 'pinj_jenis_kolektabilitas',
    ];

    private function columnType(string $table, string $column): ?string
    {
        return DB::scalar(
            "SELECT data_type FROM information_schema.columns
             WHERE table_name = ? AND column_name = ?",
            [$table, $column]
        );
    }

    public function up(): void
    {
        $adminId = DB::table('users')->where('role', 'superadmin')->min('id')
            ?? DB::table('users')->min('id');

        // 1) Konversi VARCHAR -> BIGINT (PostgreSQL tidak meng-cast implisit).
        foreach (self::VARCHAR_USER_ID as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'user_id')) {
                continue;
            }

            if ($this->columnType($table, 'user_id') !== 'character varying') {
                continue; // sudah BIGINT (mis. migrasi sebelumnya semi-berhasil)
            }

            if ($adminId !== null) {
                DB::statement(
                    "UPDATE \"{$table}\" SET user_id = ? WHERE CAST(user_id AS text) ~ '[^0-9]' OR CAST(user_id AS text) = '0'",
                    [$adminId]
                );
            }

            // Default lama ('0' pada loan_cost_components) harus dibuang dulu
            // karena PostgreSQL tidak bisa meng-cast-nya otomatis ke BIGINT.
            DB::statement("ALTER TABLE \"{$table}\" ALTER COLUMN \"user_id\" DROP DEFAULT");
            DB::statement(
                "ALTER TABLE \"{$table}\" ALTER COLUMN \"user_id\" TYPE BIGINT ".
                "USING NULLIF(\"user_id\", '')::BIGINT"
            );

            // Kembalikan default agar penyisipan tanpa user_id tetap valid.
            if ($table === 'loan_cost_components' && $adminId !== null) {
                DB::statement(
                    "ALTER TABLE \"{$table}\" ALTER COLUMN \"user_id\" SET DEFAULT ".
                    (int) $adminId
                );
            }
        }

        // 2) Tambahkan FK constraint di semua tabel yang memiliki user_id.
        foreach (array_merge(self::VARCHAR_USER_ID, self::BIGINT_USER_ID) as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'user_id')) {
                continue;
            }

            $hasFk = DB::scalar(
                "SELECT 1 FROM information_schema.table_constraints tc
                 WHERE tc.constraint_type = 'FOREIGN KEY'
                   AND tc.table_name = ?
                   AND EXISTS (
                       SELECT 1 FROM information_schema.key_column_usage kcu
                       WHERE kcu.constraint_name = tc.constraint_name
                         AND kcu.column_name = 'user_id'
                   )",
                [$table]
            );

            if ($hasFk) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users');
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan — BIGINT + FK adalah kondisi yang benar.
    }
};