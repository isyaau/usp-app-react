<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaporanTransaksiPinjamanSeeder extends Seeder {
    public function run(): void {
        $a = DB::table("anggota")->pluck("id")->toArray();
        $u = DB::table("users")->pluck("id")->toArray();
        $k = DB::table("kantor")->pluck("id")->toArray();
        if (empty($a)) return;
        $j = ["pencairan","angsuran","penalti","angsuran_kolektif"];
        $s = ["draft","posted","batal"];
        for ($i = 1; $i <= 100; $i++) {
            DB::table("laporan_transaksi_pinjaman")->insert([
                "no_laporan" => "LP-".date("Ymd", strtotime("-{$i} days")).str_pad($i,4,"0",STR_PAD_LEFT),
                "tgl_laporan" => now()->subDays(rand(1,365))->format("Y-m-d"),
                "anggota_id" => $a[array_rand($a)],
                "jenis_transaksi" => $j[array_rand($j)],
                "nominal" => rand(5,500)*100000,
                "keterangan" => "Laporan transaksi pinjaman #".$i,
                "user_id" => $u[array_rand($u)],
                "kantor_id" => $k[array_rand($k)],
                "status" => $s[array_rand($s)],
                "created_at" => now(), "updated_at" => now(),
            ]);
        }
    }
}