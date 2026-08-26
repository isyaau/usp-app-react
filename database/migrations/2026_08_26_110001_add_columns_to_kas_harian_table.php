<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table("kas_harian", function (Blueprint $t) {
            $t->date("tanggal")->nullable();
            $t->decimal("kas_awal", 18, 2)->default(0);
            $t->decimal("kas_masuk", 18, 2)->default(0);
            $t->decimal("kas_keluar", 18, 2)->default(0);
            $t->decimal("kas_akhir", 18, 2)->default(0);
            $t->foreignId("user_id")->nullable()->constrained("users")->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::table("kas_harian", function (Blueprint $t) {
            $t->dropForeign(["user_id"]);
            $t->dropColumn(["tanggal", "kas_awal", "kas_masuk", "kas_keluar", "kas_akhir", "user_id"]);
        });
    }
};
