<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaporanKasHarianSeeder extends Seeder {
    public function run(): void {
        $u = DB::table("users")->pluck("id")->toArray();
        $k = DB::table("kantor")->pluck("id")->toArray();
        if (empty($u) || empty($k)) return;
        $s = ["draft","posted","batal"];
        for ($i = 1; $i <= 100; $i++) {
            $awal = rand(5, 50) * 1000000;
            $masuk = rand(10, 200) * 100000;
            $keluar = rand(5, 150) * 100000;
            DB::table("laporan_kas_harian")->insert([
                "no_laporan" => "LK-".date("Ymd", strtotime("-{$i} days")).str_pad($i,4,"0",STR_PAD_LEFT),
                "tgl_laporan" => now()->subDays(rand(1,365))->format("Y-m-d"),
                "saldo_awal" => $awal,
                "total_pemasukan" => $masuk,
                "total_pengeluaran" => $keluar,
                "saldo_akhir" => $awal + $masuk - $keluar,
                "keterangan" => "Laporan kas harian #".$i,
                "user_id" => $u[array_rand($u)],
                "kantor_id" => $k[array_rand($k)],
                "status" => $s[array_rand($s)],
                "created_at" => now(), "updated_at" => now(),
            ]);
        }
    }
}