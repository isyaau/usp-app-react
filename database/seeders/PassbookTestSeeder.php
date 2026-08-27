<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\Kelompok;
use App\Models\Marketing;
use App\Models\SetoranSimpanan;
use App\Models\Simpanan;
use App\Models\SimpananJenis;
use App\Models\SimpananKode;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PassbookTestSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::first()?->id ?? 1;
        $kantorId = Kantor::first()?->id ?? 1;
        $marketingId = Marketing::first()?->id ?? 1;
        $kelompokId = Kelompok::first()?->id ?? 1;

        $jenisPokok = $this->ensureJenis('SP', 'Simpanan Pokok', 1, 5, $userId);
        $jenisWajib = $this->ensureJenis('SW', 'Simpanan Wajib', 2, 3, $userId);
        $jenisSukarela = $this->ensureJenis('SS', 'Simpanan Sukarela', 3, 6, $userId);

        $accountKas = Account::where('no_account', '100-01')->first();
        $accountSukarela = Account::where('no_account', '510-01')->first();
        $accountBunga = Account::where('no_account', '960-01')->first();

        if (!$accountKas || !$accountSukarela || !$accountBunga) {
            $this->command->error('Account tidak lengkap. Pastikan AccountSeeder sudah dijalankan.');
            return;
        }

        // Kode transaksi: 01=setoran (kredit), 02=tarikan (debet), 10=bunga (kredit)
        $kodeSetoran = $this->ensureKode('01', 'Setoran Tunai', $accountKas->id, $accountSukarela->id, 1, 0, $userId);
        $kodeTarikan = $this->ensureKode('02', 'Tarikan Tunai', $accountSukarela->id, $accountKas->id, 0, 1, $userId);
        $kodeBunga = $this->ensureKode('10', 'Bunga Simpanan', $accountBunga->id, $accountSukarela->id, 1, 0, $userId);

        // Bersihkan data test lama (agar tidak ada duplikat) sebelum membuat ulang
        $rkbIds = Simpanan::where('no_rekening', 'like', 'RKB-%')->pluck('id');
        if ($rkbIds->isNotEmpty()) {
            $this->command->info('Menghapus data test lama...');
            SetoranSimpanan::whereIn('simpanan_id', $rkbIds)->delete();
            $tbAnggotaIds = Anggota::where('no_anggota', 'like', 'TB-%')->pluck('id');
            Simpanan::whereIn('id', $rkbIds)->delete();
            Anggota::whereIn('id', $tbAnggotaIds)->delete();
        }

        $this->command->info('Membuat 5 anggota test untuk cetak buku tabungan...');

        $anggotaData = [
            ['nama' => 'Ahmad Fauzi', 'jk' => 'L', 'tl' => 'Madiun', 'tldr' => '1985-03-15', 'alamat' => 'Jl. Merdeka No. 10, Madiun'],
            ['nama' => 'Siti Rahmawati', 'jk' => 'P', 'tl' => 'Ngawi', 'tldr' => '1990-07-22', 'alamat' => 'Jl. Sudirman No. 25, Ngawi'],
            ['nama' => 'Budi Santoso', 'jk' => 'L', 'tl' => 'Ponorogo', 'tldr' => '1988-11-08', 'alamat' => 'Jl. Ahmad Yani No. 5, Ponorogo'],
            ['nama' => 'Dewi Lestari', 'jk' => 'P', 'tl' => 'Madiun', 'tldr' => '1992-05-30', 'alamat' => 'Jl. Pemuda No. 18, Madiun'],
            ['nama' => 'Eko Prasetyo', 'jk' => 'L', 'tl' => 'Ngawi', 'tldr' => '1987-01-12', 'alamat' => 'Jl. Kartini No. 7, Ngawi'],
        ];

        $jenisList = [$jenisPokok, $jenisWajib, $jenisSukarela];

        $simpananList = [];

        foreach ($anggotaData as $i => $data) {
            $anggota = Anggota::create([
                'no_anggota' => 'TB-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'nama' => $data['nama'],
                'pin' => '123456',
                'kelompok_id' => $kelompokId,
                'kantor_id' => $kantorId,
                'alamat' => $data['alamat'],
                'provinsi_id' => 28,
                'kota_id' => 185,
                'kecamatan_id' => 9837,
                'kelurahan_id' => 449283,
                'email' => 'tb' . ($i + 1) . '@example.com',
                'tempat_lahir' => $data['tl'],
                'tgl_lahir' => $data['tldr'],
                'jenis_kelamin' => $data['jk'],
                'agama' => 'Islam',
                'pekerjaan' => 'Wiraswasta',
                'pendidikan' => 'SMA',
                'jenis_identitas' => 'KTP',
                'no_identitas' => '357901' . str_pad(100000 + $i, 6, '0', STR_PAD_LEFT),
                'ibu' => 'Ibu Anggota ' . ($i + 1),
                'foto' => '-',
                'status' => 1,
                'user_id' => $userId,
            ]);

            $jenisSimpanan = $jenisList[$i % 3];

            $rekening = Simpanan::create([
                'no_rekening' => 'RKB-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'anggota_id' => $anggota->id,
                'jenis_id' => $jenisSimpanan->id,
                'marketing_id' => $marketingId,
                'bunga' => $jenisSimpanan->bunga ?? 5,
                'nominal_setor' => 0,
                'aktif' => 1,
                'kantor_id' => $kantorId,
                'user_id' => $userId,
            ]);

            $simpananList[] = $rekening;

            $this->command->info("  {$anggota->no_anggota} - {$anggota->nama} -> {$rekening->no_rekening} ({$jenisSimpanan->nama})");
        }

        $this->command->info('Membuat transaksi simpanan (setoran + tarikan)...');

        $txCount = 0;

        foreach ($simpananList as $si => $rekening) {
            $anggota = $rekening->anggota;
            $balance = 0;
            $numTransactions = rand(40, 60);
            $startDate = now()->subMonths(6);

            // Bangun daftar transaksi secara kronologis (urut tanggal) agar
            // saldo berjalan di buku tabungan tidak pernah negatif.
            $dates = collect();
            while ($dates->count() < $numTransactions) {
                $d = $startDate->copy()->addDays(rand(0, 180));
                if (!$dates->contains(fn ($x) => $x->format('Y-m-d') === $d->format('Y-m-d'))) {
                    $dates->push($d);
                }
            }
            $dates = $dates->sort()->values();

            foreach ($dates as $t => $date) {
                // Transaksi pertama harus setoran agar saldo berjalan tidak negatif di awal
                $isSetoran = ($t === 0) ? true : (($t < $dates->count() - 3) ? rand(1, 100) > 30 : true);

                if ($isSetoran) {
                    $nominal = $this->randomNominal($si);
                    $kodeId = $kodeSetoran->id;
                    $keterangan = $this->randomSetoranDesc();
                    $balance += $nominal;
                } else {
                    // Tarikan hanya jika saldo cukup
                    if ($balance <= 100000) {
                        $nominal = $this->randomNominal($si);
                        $kodeId = $kodeSetoran->id;
                        $keterangan = $this->randomSetoranDesc();
                        $balance += $nominal;
                    } else {
                        $maxPenarikan = (int) max(100000, $balance * 0.6);
                        $nominal = $this->randomNominal($si, $maxPenarikan);
                        if ($nominal > $balance) {
                            $nominal = max(50000, (int) ($balance * 0.3));
                        }
                        $kodeId = $kodeTarikan->id;
                        $keterangan = $this->randomTarikanDesc();
                        $balance -= $nominal;
                    }
                }

                if ($balance < 0) {
                    $balance = 0;
                }

                SetoranSimpanan::create([
                    'no_transaksi' => 'TB-' . $date->format('Ymd') . '-' . str_pad($si + 1, 2, '0', STR_PAD_LEFT) . '-' . str_pad($t + 1, 4, '0', STR_PAD_LEFT),
                    'tgl_transaksi' => $date->format('Y-m-d'),
                    'anggota_id' => $anggota->id,
                    'simpanan_id' => $rekening->id,
                    'kode_transaksi_id' => $kodeId,
                    'nominal' => $nominal,
                    'keterangan' => $keterangan,
                    'user_id' => $userId,
                    'kantor_id' => $kantorId,
                    'status' => 'posted',
                ]);

                $txCount++;
            }

            $lastMonth = now()->subMonth();
            for ($m = 0; $m < 3; $m++) {
                $bungaNominal = (int) ($balance * 0.004);
                if ($bungaNominal >= 1000) {
                    $date = $lastMonth->copy()->subMonths($m)->endOfMonth();
                    SetoranSimpanan::create([
                        'no_transaksi' => 'TB-' . $date->format('Ymd') . '-' . str_pad($si + 1, 2, '0', STR_PAD_LEFT) . '-BG' . str_pad($m + 1, 2, '0', STR_PAD_LEFT),
                        'tgl_transaksi' => $date->format('Y-m-d'),
                        'anggota_id' => $anggota->id,
                        'simpanan_id' => $rekening->id,
                        'kode_transaksi_id' => $kodeBunga->id,
                        'nominal' => $bungaNominal,
                        'keterangan' => 'Bunga simpanan bulan ' . $date->format('M Y'),
                        'user_id' => $userId,
                        'kantor_id' => $kantorId,
                        'status' => 'posted',
                    ]);
                    $txCount++;
                    $balance += $bungaNominal;
                }
            }
        }

        $this->command->info("Selesai! {$txCount} transaksi dibuat untuk 5 rekening unik.");
        $this->command->info('Buka halaman Transaksi Simpanan -> cari no. rekening -> "Cetak Buku Tabungan".');
    }

    private function ensureJenis(string $kode, string $nama, int $jenis, float $bunga, int $userId): SimpananJenis
    {
        return SimpananJenis::firstOrCreate(
            ['kode' => $kode],
            [
                'nama' => $nama,
                'jenis' => $jenis,
                'bunga' => $bunga,
                'minimum' => 100000,
                'mengendap' => 50000,
                'user_id' => $userId,
            ]
        );
    }

    private function ensureKode(string $kode, string $nama, int $debetId, int $kreditId, bool $setoran, bool $tarikan, int $userId): SimpananKode
    {
        return SimpananKode::updateOrCreate(
            ['kode' => $kode],
            [
                'nama' => $nama,
                'account_debet' => $debetId,
                'account_kredit' => $kreditId,
                'setoran' => $setoran ? '1' : '0',
                'tarikan' => $tarikan ? '1' : '0',
                'transfer' => '0',
                'pokok' => '0',
                'wajib' => '0',
                'sukarela' => '1',
                'pinjaman' => '0',
                'saham' => '0',
                'pokok_pinjaman' => '0',
                'rencana' => '0',
                'keterangan' => '',
                'user_id' => $userId,
            ]
        );
    }

    private function randomNominal(int $index, int $max = 5000000): int
    {
        $bases = [500000, 1000000, 2000000, 2500000, 5000000];
        $base = $bases[$index % count($bases)];
        $variations = [0.5, 0.75, 1.0, 1.25, 1.5, 2.0];
        $nominal = (int) ($base * $variations[array_rand($variations)]);
        $nominal = max(50000, min($nominal, $max));
        return (int) (round($nominal / 10000) * 10000);
    }

    private function randomSetoranDesc(): string
    {
        return ['Setoran tunai', 'Setoran awal', 'Setoran bulanan', 'Setoran mingguan', 'Setoran via transfer', 'Setoran tambahan'][array_rand([5])];
    }

    private function randomTarikanDesc(): string
    {
        return ['Penarikan tunai', 'Penarikan via ATM', 'Penarikan sebagian'][array_rand([3])];
    }
}
