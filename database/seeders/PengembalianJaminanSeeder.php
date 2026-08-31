<?php

namespace Database\Seeders;

use App\Models\AngsuranPinjaman;
use App\Models\Kantor;
use App\Models\PengembalianJaminan;
use App\Models\Pinjaman;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PengembalianJaminanSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $kantors = Kantor::all();
        $pinjamanList = Pinjaman::with('anggota')->where('aktif', '1')->get();

        if ($users->isEmpty() || $kantors->isEmpty() || $pinjamanList->isEmpty()) {
            $this->command->warn('Tidak ada data user, kantor, atau pinjaman aktif.');
            return;
        }

        $kantorIds = $kantors->pluck('id')->toArray();

        $count = 0;
        $targetCount = 100;

        while ($count < $targetCount) {
            $pinjaman = $pinjamanList->random();
            $n = $count + 1;

            $noTransaksi = 'PJ-' . str_pad($n, 4, '0', STR_PAD_LEFT) . '-' . date('Y');
            if (PengembalianJaminan::where('no_transaksi', $noTransaksi)->exists()) {
                continue;
            }

            $plafon = (float) $pinjaman->plafon;
            $pokokTerbayar = (float) AngsuranPinjaman::where('pinjaman_id', $pinjaman->id)->sum('nominal_pokok');
            $sisaPokok = max(0, $plafon - $pokokTerbayar);

            $tgl = Carbon::now()->subDays(rand(0, 365))->format('Y-m-d');

            $nama = $pinjaman->anggota->nama ?? 'Anggota';
            $noPinjaman = $pinjaman->no_pinjaman ?? '-';

            $keterangan = "Pengembalian jaminan atas pinjaman {$noPinjaman} atas nama {$nama} "
                . "dengan sisa pokok sebesar Rp " . number_format($sisaPokok, 0, ',', '.')
                . ". Jaminan telah dikembalikan kepada anggota setelah kewajiban terpenuhi.";

            $statusRand = rand(1, 100);
            $status = $statusRand <= 20 ? 'draft' : ($statusRand <= 85 ? 'posted' : 'batal');

            PengembalianJaminan::create([
                'no_transaksi' => $noTransaksi,
                'tgl_transaksi' => $tgl,
                'pinjaman_id' => $pinjaman->id,
                'sisa_pokok' => $sisaPokok,
                'keterangan' => $keterangan,
                'user_id' => $users->random()->id,
                'kantor_id' => $kantorIds[array_rand($kantorIds)],
                'status' => $status,
                'created_at' => Carbon::parse($tgl)->subDays(rand(0, 3))->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($tgl)->format('Y-m-d H:i:s'),
            ]);

            $count++;
            if ($count % 20 === 0) {
                $this->command->info("Created {$count} pengembalian jaminan records...");
            }
        }

        $this->command->info("Successfully created {$count} pengembalian jaminan records.");
    }
}
