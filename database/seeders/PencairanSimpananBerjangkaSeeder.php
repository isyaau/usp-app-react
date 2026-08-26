<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PencairanSimpananBerjangkaSeeder extends Seeder {
    public function run(): void {
        $a = DB::table("anggota")->pluck("id")->toArray();
        $d = DB::table("deposito")->pluck("id")->toArray();
        $u = DB::table("users")->pluck("id")->toArray();
        $k = DB::table("kantor")->pluck("id")->toArray();
        if (empty($a) || empty($d)) return;
        $s = ["draft","posted","batal"];
        for ($i = 1; $i <= 100; $i++) {
            $pokok = rand(10,200)*100000;
            $bunga = (int)($pokok*rand(3,8)/100);
            $pajak = (int)($bunga*20/100);
            DB::table("pencairan_simpanan_berjangka")->insert([
                "no_transaksi" => "PC-".date("Ymd", strtotime("-{$i} days")).str_pad($i,4,"0",STR_PAD_LEFT),
                "tgl_transaksi" => now()->subDays(rand(1,365))->format("Y-m-d"),
                "anggota_id" => $a[array_rand($a)],
                "deposito_id" => $d[array_rand($d)],
                "nominal_pokok" => $pokok,
                "nominal_bunga" => $bunga,
                "nominal_pajak" => $pajak,
                "nominal_penalti" => 0,
                "nominal_diterima" => $pokok+$bunga-$pajak,
                "keterangan" => "Pencairan berjangka #".$i,
                "user_id" => $u[array_rand($u)],
                "kantor_id" => $k[array_rand($k)],
                "status" => $s[array_rand($s)],
                "created_at" => now(), "updated_at" => now(),
            ]);
        }
    }
}