<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("laporan_kas_harian", function (Blueprint $t) {
            $t->id();
            $t->string("no_laporan", 50)->unique();
            $t->date("tgl_laporan");
            $t->decimal("saldo_awal", 18, 2)->default(0);
            $t->decimal("total_pemasukan", 18, 2)->default(0);
            $t->decimal("total_pengeluaran", 18, 2)->default(0);
            $t->decimal("saldo_akhir", 18, 2)->default(0);
            $t->text("keterangan")->nullable();
            $t->foreignId("user_id")->constrained("users")->cascadeOnDelete();
            $t->foreignId("kantor_id")->constrained("kantor")->cascadeOnDelete();
            $t->enum("status", ["draft", "posted", "batal"])->default("draft");
            $t->timestamps();
            $t->index(["tgl_laporan", "kantor_id"]);
            $t->index("status");
        });
    }
    public function down(): void { Schema::dropIfExists("laporan_kas_harian"); }
};