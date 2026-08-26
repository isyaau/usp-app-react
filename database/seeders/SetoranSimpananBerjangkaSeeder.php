<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SetoranSimpananBerjangkaSeeder extends Seeder {
    public function run(): void {
        $a = DB::table("anggota")->pluck("id")->toArray();
        $d = DB::table("deposito")->pluck("id")->toArray();
        $u = DB::table("users")->pluck("id")->toArray();
        $k = DB::table("kantor")->pluck("id")->toArray();
        if (empty($a) || empty($d)) return;
        $s = ["draft","posted","batal"];
        for ($i = 1; $i <= 100; $i++) {
            DB::table("setoran_simpanan_berjangka")->insert([
                "no_transaksi" => "SB-".date("Ymd", strtotime("-{$i} days")).str_pad($i,4,"0",STR_PAD_LEFT),
                "tgl_transaksi" => now()->subDays(rand(1,365))->format("Y-m-d"),
                "anggota_id" => $a[array_rand($a)],
                "deposito_id" => $d[array_rand($d)],
                "nominal" => rand(5,100)*100000,
                "keterangan" => "Setoran berjangka #".$i,
                "user_id" => $u[array_rand($u)],
                "kantor_id" => $k[array_rand($k)],
                "status" => $s[array_rand($s)],
                "created_at" => now(), "updated_at" => now(),
            ]);
        }
    }
}