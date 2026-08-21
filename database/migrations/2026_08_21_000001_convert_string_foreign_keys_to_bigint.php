<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Konversi seluruh kolom foreign key yang awalnya dibuat sebagai VARCHAR
 * menjadi BIGINT agar kompatibel dengan PostgreSQL (operator varchar = int
 * tidak dikenal di PG).
 *
 * Kolom-kolom ini dulunya aman di MySQL karena konversi tipe implisit,
 * tetapi PostgreSQL menolak perbandingan antar tipe secara diam-diam.
 */
return new class extends Migration
{
    /**
     * Mapping tabel => [kolom => apakah NOT NULL].
     */
    public function up(): void
    {
        // Perbaikan data: simpanan_kode.user_id berisi teks ('admin') dari
        // implementasi lama — petakan ke user superadmin pertama.
        if (Schema::hasTable('simpanan_kode') && Schema::hasColumn('simpanan_kode', 'user_id')) {
            $adminId = DB::table('users')->where('role', 'superadmin')->min('id')
                ?? DB::table('users')->min('id');

            if ($adminId !== null) {
                DB::statement(
                    "UPDATE simpanan_kode SET user_id = ? ".
                    "WHERE user_id ~ '[^0-9]'",
                    [$adminId]
                );
            }
        }

        $mapping = [
            'kelompok' => ['ketua_id' => false, 'user_id' => true],
            'kantor' => ['user_id' => true],
            'anggota' => ['kelompok_id' => true, 'harga_id' => false, 'bagian_id' => false, 'kantor_id' => true, 'user_id' => true],
            'acc_header' => ['group_id' => true, 'user_id' => true],
            'account' => ['header_id' => false, 'user_id' => true],
            'pinj_jenis' => ['account_id' => true, 'user_id' => true],
            'pinj_jenis_komponen' => ['pinj_jenis_id' => true, 'account_id' => true, 'user_id' => true],
            'pinj_jenis_kolektabilitas' => ['pinj_jenis_id' => true, 'kualitas_id' => true, 'user_id' => true],
            'parameter' => ['user_id' => true],
            'simpanan_kode' => ['user_id' => true],
            'simpanan_jenis' => [
                'account_id' => false, 'bunga_id' => false, 'biaya_id' => false,
                'pajak_id' => false, 'setor_id' => false, 'tarik_id' => false, 'user_id' => false,
            ],
            'simpanan_jenis_kode' => ['kode_id' => false, 'jenis_id' => false, 'user_id' => false],
            'simpanan_bunga' => ['jenis_id' => false, 'user_id' => false],
            'marketing' => ['kantor_id' => false, 'user_id' => false],
            'simpanan' => ['anggota_id' => true, 'jenis_id' => true, 'marketing_id' => true, 'kantor_id' => false, 'user_id' => false],
            'simpanan_rencana' => ['kantor_id' => true, 'user_id' => true],
            'simpanan_rencana_detail' => ['rencana_id' => true, 'simpanan_id' => true, 'user_id' => true],
            'deposito_jenis' => ['account_id' => false, 'user_id' => false],
            'deposito' => [
                'anggota_id' => true, 'jenis_id' => true, 'marketing_id' => false,
                'tabunganbunga_id' => false, 'tabungantempo_id' => false,
                'kantor_id' => true, 'user_id' => true,
            ],
            'jaminan' => ['user_id' => true],
            'jaminan_detail' => ['jaminan_id' => true, 'user_id' => true],
            'pinjaman' => [
                'proposal_id' => true, 'anggota_id' => true, 'jaminan_id' => true,
                'jenis_id' => true, 'marketing_id' => true, 'sektor_id' => true,
                'tabungan_id' => true, 'kode_id' => true, 'swp_id' => true,
                'spp_id' => true, 'kantor_id' => true, 'user_id' => true,
            ],
            'pinjaman_biaya' => ['pinjaman_id' => true, 'account_id' => true, 'user_id' => true],
            'pinjaman_surat' => ['pinjaman_id' => true, 'surat_id' => true, 'user_id' => true],
            'pinjaman_jaminan' => ['pinjaman_id' => true, 'user_id' => true],
            'pinjaman_saksi' => ['pinjaman_id' => true, 'pekerjaan_id' => true, 'user_id' => true],
            'pinjaman_penjamin' => ['pinjaman_id' => true, 'user_id' => true],
            'pinjaman_template' => ['pinjaman_id' => true, 'user_id' => true],
        ];

        foreach ($mapping as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column => $notNull) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                // Ubah tipe ke BIGINT; kosong string ('') menjadi NULL.
                DB::statement(
                    "ALTER TABLE \"{$table}\" ALTER COLUMN \"{$column}\" TYPE BIGINT ".
                    "USING NULLIF(\"{$column}\", '')::BIGINT"
                );

                if ($notNull) {
                    DB::statement("ALTER TABLE \"{$table}\" ALTER COLUMN \"{$column}\" SET NOT NULL");
                }
            }
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan — tipe BIGINT adalah kondisi yang benar.
    }
};
