<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\ImageManager;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\Marketing;
use App\Models\Simpanan;
use App\Models\SimpananJenis;

/**
 * Seeder Rekening Simpanan (simpanan).
 *
 * Semua field terisi (tidak ada yang kosong):
 *  - id relasi (anggota, jenis/produk, marketing, kantor) di-resolve saat runtime.
 *  - idempotent (updateOrCreate by no_rekening).
 *  - ttd (tanda tangan) dibuat sebagai file PNG placeholder yang valid.
 */
class SimpananSeeder extends Seeder
{
    use ResolvesAdminUser;

    public function run(): void
    {
        $kantors = Kantor::orderBy('id')->pluck('id')->values();          // mis. [215,216,217]
        $marketing = Marketing::orderBy('id')->pluck('id')->first();       // id marketing pertama yang valid
        $anggota = Anggota::orderBy('id')->pluck('id')->take(3)->values(); // 3 anggota pertama

        if (!$marketing || $kantors->isEmpty() || $anggota->isEmpty()) {
            return;
        }

        $produk = SimpananJenis::orderBy('id')->get()->keyBy('kode');

        // Semua field terisi dengan variasi status (blokir, nonaktif, sms, dll).
        $specs = [
            'SP'     => ['nominal' => '1000000', 'bunga' => '0',   'blokir' => '0', 'blokirNom' => '0', 'blokirTgl' => '0', 'sms' => '1', 'aktif' => '1', 'tglBlokir' => null],
            'SW'     => ['nominal' => '500000',  'bunga' => '3',   'blokir' => '0', 'blokirNom' => '0', 'blokirTgl' => '0', 'sms' => '1', 'aktif' => '1', 'tglBlokir' => null],
            'SS'     => ['nominal' => '2000000', 'bunga' => '6',   'blokir' => '0', 'blokirNom' => '0', 'blokirTgl' => '0', 'sms' => '0', 'aktif' => '1', 'tglBlokir' => null],
            'SB-001' => ['nominal' => '5000000', 'bunga' => '5',   'blokir' => '0', 'blokirNom' => '0', 'blokirTgl' => '0', 'sms' => '1', 'aktif' => '1', 'tglBlokir' => null],
            'SB-002' => ['nominal' => '10000000', 'bunga' => '4',  'blokir' => '1', 'blokirNom' => '1', 'blokirTgl' => '1', 'sms' => '1', 'aktif' => '1', 'tglBlokir' => '2026-09-30'],
            'SB-003' => ['nominal' => '7500000',  'bunga' => '4.5','blokir' => '0', 'blokirNom' => '0', 'blokirTgl' => '0', 'sms' => '0', 'aktif' => '0', 'tglBlokir' => null],
        ];

        $i = 0;
        foreach ($specs as $kode => $s) {
            $jenis = $produk->get($kode);
            if (!$jenis) {
                continue;
            }
            $kantorId = $kantors[$i % $kantors->count()];
            $anggotaId = $anggota[$i % $anggota->count()];
            $i++;
            $noRekening = 'REK-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT);

            $ttdBaru = $this->signature();

            $ini = Simpanan::updateOrCreate(
                ['no_rekening' => $noRekening],
                [
                    'tanggal'          => '2026-08-01',
                    'anggota_id'       => $anggotaId,
                    'jenis_id'         => $jenis->id,
                    'marketing_id'     => $marketing,
                    'qq'               => "Rekening {$jenis->kode}",
                    'bunga'            => $s['bunga'],
                    'baris'            => '1',
                    'ttd'              => $ttdBaru,
                    'blokir_simpanan'  => $s['blokir'],
                    'blokir_nominal'   => $s['blokirNom'],
                    'nominal_blokir'   => $s['blokirNom'] === '1' ? '500000' : '0',
                    'blokir_tgl'       => $s['blokirTgl'],
                    'tgl_blokir'       => $s['tglBlokir'],
                    'nominal_setor'    => $s['nominal'],
                    'sms'              => $s['sms'],
                    'aktif'            => $s['aktif'],
                    'kantor_id'        => $kantorId,
                    'user_id'          => $this->adminUserId(),
                ]
            );

            // Bersihkan file TTD lama milik rekaman ini agar tidak menumpuk.
            $ttdLama = $ini->getOriginal('ttd');
            if ($ttdLama && $ttdLama !== $ttdBaru && Storage::disk('public')->exists($ttdLama)
                && !Simpanan::where('ttd', $ttdLama)->where('id', '!=', $ini->id)->exists()) {
                Storage::disk('public')->delete($ttdLama);
            }
        }
    }

    /**
     * Buat satu file TTD placeholder baru yang valid untuk mengisi kolom ttd.
     * Berisi coretan tanda tangan (scribble) agar terlihat seperti TTD asli,
     * bukan kotak putih kosong. Selalu membuat file baru (unik) per rekaman
     * agar referensi tidak pernah menunjuk ke file bersama yang bisa terhapus
     * oleh proses lain (mis. test).
     */
    protected function signature(): string
    {
        $image = ImageManager::withDriver(Driver::class)
            ->create(240, 90)
            ->fill('#ffffff');

        $ink = '#1e293b';
        // Tiga jalur coretan (stroke) yang saling menyambung membentuk tanda tangan.
        $strokes = [
            // Awal: lingkaran kecil + garis miring ke atas.
            $this->curve(18, 62, 30, 40, 44, 60, 34),
            $this->curve(44, 60, 64, 74, 66, 38, 30),
            // Bagian tengah: gelombang (menggambarkan nama terurai).
            $this->curve(66, 38, 88, 46, 110, 34, 26),
            $this->curve(110, 34, 132, 52, 158, 36, 28),
            $this->curve(158, 36, 182, 56, 204, 44, 26),
            // Akhir: garis turun + titik.
            $this->curve(204, 44, 218, 30, 226, 66, 24),
        ];

        foreach ($strokes as $pts) {
            for ($i = 0; $i < count($pts) - 1; $i++) {
                $image->drawLine(function ($line) use ($pts, $i, $ink) {
                    $line->from($pts[$i][0], $pts[$i][1])
                        ->to($pts[$i + 1][0], $pts[$i + 1][1])
                        ->color($ink)
                        ->width(2);
                });
            }
        }

        $image->drawCircle(231, 68, function ($c) use ($ink) {
            $c->radius(3)->border($ink, 2);
        });

        $path = 'ttd/ttd_' . Str::uuid() . '.png';
        Storage::disk('public')->put($path, (string) $image->encode(new PngEncoder()));

        return $path;
    }

    /**
     * Sampel titik sepanjang segmen garis dari (x1,y1) ke (x2,y2).
     *
     * @return array<int, array{0:int,1:int}>
     */
    protected function curve(int $x1, int $y1, int $x2, int $y2, int $x3, int $y3, int $segments): array
    {
        $pts = [];
        for ($t = 0; $t <= 1; $t += 1 / max(1, $segments)) {
            $mt = 1 - $t;
            $x = (int) round($mt * $mt * $x1 + 2 * $mt * $t * $x2 + $t * $t * $x3);
            $y = (int) round($mt * $mt * $y1 + 2 * $mt * $t * $y2 + $t * $t * $y3);
            $pts[] = [$x, $y];
        }

        return $pts;
    }
}
