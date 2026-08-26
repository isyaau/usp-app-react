<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("pencairan_simpanan_berjangka", function (Blueprint $t) {
            $t->id();
            $t->string("no_transaksi", 50)->unique();
            $t->date("tgl_transaksi");
            $t->foreignId("anggota_id")->constrained("anggota")->cascadeOnDelete();
            $t->foreignId("deposito_id")->constrained("deposito")->cascadeOnDelete();
            $t->decimal("nominal_pokok", 18, 2);
            $t->decimal("nominal_bunga", 18, 2)->default(0);
            $t->decimal("nominal_pajak", 18, 2)->default(0);
            $t->decimal("nominal_penalti", 18, 2)->default(0);
            $t->decimal("nominal_diterima", 18, 2);
            $t->text("keterangan")->nullable();
            $t->foreignId("user_id")->constrained("users")->cascadeOnDelete();
            $t->foreignId("kantor_id")->constrained("kantor")->cascadeOnDelete();
            $t->enum("status", ["draft", "posted", "batal"])->default("draft");
            $t->timestamps();
            $t->index(["tgl_transaksi", "kantor_id"]);
            $t->index(["anggota_id", "tgl_transaksi"]);
            $t->index("status");
        });
    }
    public function down(): void { Schema::dropIfExists("pencairan_simpanan_berjangka"); }
};