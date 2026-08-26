<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("laporan_transaksi_simpanan", function (Blueprint $t) {
            $t->id();
            $t->string("no_laporan", 50)->unique();
            $t->date("tgl_laporan");
            $t->foreignId("anggota_id")->constrained("anggota")->cascadeOnDelete();
            $t->string("jenis_transaksi", 50);
            $t->decimal("nominal", 18, 2);
            $t->text("keterangan")->nullable();
            $t->foreignId("user_id")->constrained("users")->cascadeOnDelete();
            $t->foreignId("kantor_id")->constrained("kantor")->cascadeOnDelete();
            $t->enum("status", ["draft", "posted", "batal"])->default("draft");
            $t->timestamps();
            $t->index(["tgl_laporan", "kantor_id"]);
            $t->index(["anggota_id", "tgl_laporan"]);
            $t->index("status");
        });
    }
    public function down(): void { Schema::dropIfExists("laporan_transaksi_simpanan"); }
};