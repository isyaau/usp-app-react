<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ubah tipe proposal_biaya.proposal_id menjadi bigint.
 *
 * Kolom dibuat varchar (mengikuti pola lama), padahal selalu diisi id
 * numeric dari tabel `proposal`. Akibatnya eager load / delete berbasis
 * relasi (`ProposalBiaya::where('proposal_id', $proposal->id)`) gagal di
 * PostgreSQL karena membandingkan bigint dengan character varying.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE proposal_biaya ALTER COLUMN proposal_id TYPE bigint USING proposal_id::bigint');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE proposal_biaya ALTER COLUMN proposal_id TYPE varchar USING proposal_id::text');
    }
};