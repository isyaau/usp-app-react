<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenarikanDanaTitipanSeeder extends Seeder
{
    public function run(): void
    {
        $a = DB::table("anggota")->pluck("id")->toArray();
        $u = DB::table("users")->pluck("id")->toArray();
        $k = DB::table("kantor")->pluck("id")->toArray();
        if (empty($a)) return;
        $s = ["draft", "posted", "batal"];
        for ($i = 1; $i <= 100; $i++) {
            DB::table("penarikan_dana_titipan_anggota")->insert([
                "no_transaksi" => "DT-" . date("Ymd", strtotime("-{$i} days")) . str_pad($i, 4, "0", STR_PAD_LEFT),
                "tgl_transaksi" => now()->subDays(rand(1, 365))->format("Y-m-d"),
                "anggota_id" => $a[array_rand($a)],
                "nominal_penarikan" => rand(10, 500) * 10000,
                "keterangan" => "Penarikan dana titipan #" . $i,
                "user_id" => $u[array_rand($u)],
                "kantor_id" => $k[array_rand($k)],
                "status" => $s[array_rand($s)],
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }
    }
}
