<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("pencairan_pinjaman", function (Blueprint $table) {
            $table->id();
            $table->foreignId("pinjaman_id")->constrained("pinjaman")->onDelete("cascade");
            $table->date("tanggal_cair");
            $table->decimal("nominal_cair", 18, 2);
            $table->enum("metode_cair", ["transfer", "tunai", "cek", "giro"])->default("transfer");
            $table->string("no_rekening")->nullable();
            $table->string("nama_rekening")->nullable();
            $table->string("bank_id")->nullable();
            $table->decimal("biaya_admin", 18, 2)->default(0);
            $table->decimal("potongan_simpanan", 18, 2)->default(0);
            $table->text("keterangan")->nullable();
            $table->enum("status", ["pending", "disetujui", "ditolak", "dicairkan"])->default("pending");
            $table->foreignId("approved_by")->nullable()->constrained("users")->onDelete("set null");
            $table->timestamp("approved_at")->nullable();
            $table->foreignId("cair_oleh")->nullable()->constrained("users")->onDelete("set null");
            $table->timestamp("cair_at")->nullable();
            $table->foreignId("created_by")->nullable()->constrained("users")->onDelete("set null");
            $table->foreignId("kantor_id")->nullable()->constrained("kantor")->onDelete("set null");
            $table->timestamps();
            
            $table->index(["pinjaman_id", "status"]);
            $table->index(["tanggal_cair"]);
            $table->index(["status"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("pencairan_pinjaman");
    }
};
