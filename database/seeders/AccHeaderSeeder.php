<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccHeader;

class AccHeaderSeeder extends Seeder
{
    public function run(): void
    {
        $headers = [
            ['no_header' => 100, 'nama' => 'AKTIVA', 'group_id' => 1],
            ['no_header' => 110, 'nama' => 'BANK', 'group_id' => 1],
            ['no_header' => 150, 'nama' => 'REKENING PINJAMAN YANG DIBERIKAN', 'group_id' => 1],
            ['no_header' => 151, 'nama' => 'REKENING SEWA DIBAYAR DIMUKA', 'group_id' => 1],
            ['no_header' => 153, 'nama' => 'PIUTANG ANGSURAN', 'group_id' => 1],
            ['no_header' => 155, 'nama' => 'PIUTANG KARYAWAN', 'group_id' => 1],
            ['no_header' => 160, 'nama' => 'PENYISIHAN PENGHAPUSAN AKTIVA PRODUKTIF', 'group_id' => 1],
            ['no_header' => 170, 'nama' => 'SUPLIES KANTOR', 'group_id' => 1],
            ['no_header' => 200, 'nama' => 'INVENTARIS', 'group_id' => 2],
            ['no_header' => 201, 'nama' => 'INVENTARIS', 'group_id' => 2],
            ['no_header' => 400, 'nama' => 'BIAYA DIBAYAR DIMUKA', 'group_id' => 1],
            ['no_header' => 500, 'nama' => 'KEWAJIBAN SEGERA DIBAYAR', 'group_id' => 4],
            ['no_header' => 510, 'nama' => 'KEWAJIBAN REKENING SIMPANAN ANGGOTA', 'group_id' => 4],
            ['no_header' => 520, 'nama' => 'KEWAJIBAN REKENING SIMPANAN BERJANGKA', 'group_id' => 4],
            ['no_header' => 560, 'nama' => 'HUTANG LAIN-LAIN', 'group_id' => 4],
            ['no_header' => 561, 'nama' => 'HUTANG BANK', 'group_id' => 4],
            ['no_header' => 700, 'nama' => 'RUPA-RUPA PASIVA', 'group_id' => 5],
            ['no_header' => 800, 'nama' => 'MODAL', 'group_id' => 6],
            ['no_header' => 900, 'nama' => 'PENDAPATAN HASIL BUNGA PINJAMAN', 'group_id' => 7],
            ['no_header' => 901, 'nama' => 'PENDAPATAN HASIL BUNGA TABUNGAN', 'group_id' => 7],
            ['no_header' => 902, 'nama' => 'PENDAPATAN HASIL BUNGA GIRO', 'group_id' => 7],
            ['no_header' => 903, 'nama' => 'PENDAPATAN HASIL BUNGA SIMPANAN BERJANGKA', 'group_id' => 7],
            ['no_header' => 910, 'nama' => 'PENDAPATAN PROVISI PINJAMAN', 'group_id' => 7],
            ['no_header' => 931, 'nama' => 'PENDAPATAN', 'group_id' => 7],
            ['no_header' => 940, 'nama' => 'PENDAPATAN NONOPERASIONAL LAIN', 'group_id' => 7],
            ['no_header' => 950, 'nama' => 'RUPA-RUPA AKTIVA', 'group_id' => 8],
            ['no_header' => 960, 'nama' => 'BIAYA BUNGA SIMPANAN', 'group_id' => 8],
            ['no_header' => 961, 'nama' => 'BIAYA PAJAK BUNGA', 'group_id' => 8],
            ['no_header' => 962, 'nama' => 'PIUTANG ANGSURAN', 'group_id' => 8],
            ['no_header' => 970, 'nama' => 'BIAYA TENAGA KERJA', 'group_id' => 8],
            ['no_header' => 980, 'nama' => 'BIAYA SEWA', 'group_id' => 8],
            ['no_header' => 981, 'nama' => 'BIAYA', 'group_id' => 8],
            ['no_header' => 982, 'nama' => 'BIAYA PEMELIHARAAN', 'group_id' => 8],
            ['no_header' => 983, 'nama' => 'BIAYA PAJAK', 'group_id' => 8],
            ['no_header' => 984, 'nama' => 'BIAYA PENYUSUTAN AKTIVA PRODUKTIF', 'group_id' => 8],
            ['no_header' => 985, 'nama' => 'BIAYA ASURANSI', 'group_id' => 8],
            ['no_header' => 986, 'nama' => 'BIAYA PENYUSUTAN AKTIVA TETAP', 'group_id' => 8],
            ['no_header' => 990, 'nama' => 'BIAYA NONOPERASIONAL LAINNYA', 'group_id' => 8],
        ];

        foreach ($headers as $item) {
            AccHeader::firstOrCreate(
                ['no_header' => $item['no_header']], // cek duplikat berdasarkan no_header
                [
                    'nama' => $item['nama'],
                    'group_id' => $item['group_id'],
                    'user_id' => 1,
                ]
            );
        }
    }
}
