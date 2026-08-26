<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\AngsuranKolektif;
use App\Models\AngsuranKolektifDetail;
use App\Models\Kantor;
use App\Models\Kelompok;
use App\Models\Pinjaman;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AngsuranKolektifSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $kantors = Kantor::all();
        $kelompoks = Kelompok::all();

        if ($users->isEmpty() || $kelompoks->isEmpty()) {
            $this->command->warn('Tidak ada data user atau kelompok.');
            return;
        }

        $kantorIds = $kantors->pluck('id')->toArray();

        $jenisList = ['angsuran', 'penalti', 'angsuran_dan_setoran'];
        $metodeList = ['tunai', 'debet_simpanan', 'bank', 'custom'];

        $count = 0;
        $targetCount = 100;

        while ($count < $targetCount) {
            $kelompok = $kelompoks->random();
            $anggotas = Anggota::where('kelompok_id', $kelompok->id)->get();

            if ($anggotas->isEmpty()) {
                continue;
            }

            $pinjamanList = Pinjaman::whereIn('anggota_id', $anggotas->pluck('id'))->get();
            if ($pinjamanList->isEmpty()) {
                continue;
            }

            $jenis = $jenisList[array_rand($jenisList)];
            $metode = $metodeList[array_rand($metodeList)];
            $tgl = Carbon::now()->subDays(rand(0, 180))->format('Y-m-d');

            $nominalTotal = 0;
            $details = [];

            foreach ($pinjamanList->random(min(5, $pinjamanList->count())) as $pinjaman) {
                $nominalPokok = rand(200000, 3000000);
                $nominalBunga = round($nominalPokok * 0.02);
                $total = $nominalPokok + $nominalBunga;
                $nominalTotal += $total;

                $details[] = [
                    'pinjaman_id' => $pinjaman->id,
                    'anggota_id' => $pinjaman->anggota_id,
                    'angsuran_ke' => rand(1, 12),
                    'nominal_pokok' => $nominalPokok,
                    'nominal_bunga' => $nominalBunga,
                    'total_angsuran' => $total,
                    'setoran_simpanan' => $jenis === 'angsuran_dan_setoran' ? rand(50000, 500000) : null,
                    'denda' => 0,
                    'keterangan' => '',
                ];
            }

            $statusRand = rand(1, 100);
            $status = $statusRand <= 20 ? 'draft' : ($statusRand <= 80 ? 'posted' : 'batal');

            $kolektif = AngsuranKolektif::create([
                'no_transaksi' => 'AK-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT) . '-' . date('Y'),
                'tgl_transaksi' => $tgl,
                'kelompok_id' => $kelompok->id,
                'jenis' => $jenis,
                'metode_pembayaran' => $metode,
                'nominal_total' => $nominalTotal,
                'jumlah_anggota' => count($details),
                'keterangan' => 'Kolektif ' . str_replace('_', ' ', $jenis) . ' - ' . $kelompok->nama_kelompok,
                'user_id' => $users->random()->id,
                'kantor_id' => !empty($kantorIds) ? $kantorIds[array_rand($kantorIds)] : 1,
                'status' => $status,
                'created_at' => Carbon::parse($tgl)->subDays(rand(0, 3))->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($tgl)->format('Y-m-d H:i:s'),
            ]);

            foreach ($details as $detail) {
                $detail['angsuran_kolektif_id'] = $kolektif->id;
                AngsuranKolektifDetail::create($detail);
            }

            $count++;
            if ($count % 20 === 0) {
                $this->command->info("Created {$count} angsuran kolektif records...");
            }
        }

        $this->command->info("Successfully created {$count} angsuran kolektif records.");
    }
}
