<?php

namespace Database\Seeders;

use App\Models\Kantor;
use App\Models\Simpanan;
use App\Models\SimpananRencana;
use Illuminate\Database\Seeder;

/**
 * Seeder Simpanan Rencana (simpanan_rencana + simpanan_rencana_detail).
 *
 * Membuat 100 rekaman Simpanan Rencana lengkap dengan beberapa detail
 * (rekening simpanan yang diblokir/direncanakan). Idempotent — dijalankan
 * berulang tidak menduplikasi data (updateOrCreate by no_bukti).
 */
class SimpananRencanaSeeder extends Seeder
{
    use ResolvesAdminUser;

    public function run(): void
    {
        $kantors = Kantor::orderBy('id')->pluck('id')->values();
        $simpanan = Simpanan::orderBy('id')->pluck('id')->values();

        if ($kantors->isEmpty() || $simpanan->isEmpty()) {
            return;
        }

        for ($i = 1; $i <= 100; $i++) {
            $noBukti = 'SMP-RCN-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            $jangka = rand(3, 60);
            $satuan = ['hari', 'bulan', 'tahun'][rand(0, 2)];
            $nominal = (string) (rand(5, 50) * 1000000);

            $mulai = now()->subDays(rand(1, 180))->format('Y-m-d');
            $jatuhtempo = \Carbon\Carbon::parse($mulai)->addMonths($jangka)->format('Y-m-d');

            $rencana = SimpananRencana::updateOrCreate(
                ['no_bukti' => $noBukti],
                [
                    'tanggal_mulai' => $mulai,
                    'tanggal_jatuhtempo' => $jatuhtempo,
                    'jangka_waktu' => (string) $jangka,
                    'satuan' => $satuan,
                    'nominal' => $nominal,
                    'bunga' => (string) rand(3, 12),
                    'keterangan' => 'Rencana simpanan ke-'.$i,
                    'kantor_id' => (string) $kantors[$i % $kantors->count()],
                    'user_id' => $this->adminUserId(),
                ]
            );

            // Detail: 1-3 rekening simpanan yang terlibat (mengelilingi data simpanan).
            $count = rand(1, 3);
            for ($j = 0; $j < $count; $j++) {
                $simpananId = $simpanan[($i + $j) % $simpanan->count()];
                \App\Models\SimpananRencanaDetail::updateOrCreate(
                    ['rencana_id' => $rencana->id, 'simpanan_id' => (string) $simpananId],
                    ['user_id' => '1']
                );
            }
        }
    }
}
