<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\AccHeader;

class AccountSeeder extends Seeder
{
    use ResolvesAdminUser;

    public function run(): void
    {
        $accounts = [

            // 100
            ['100-01', 'KAS BESAR', 'Debet'],
            ['100-02', 'KAS KECIL', 'Debet'],
            ['100-03', 'BON SEMENTARA', 'Debet'],

            // 110
            ['110-01', 'BANK BCA', 'Debet'],
            ['110-02', 'BANK LIPPO', 'Debet'],

            // 150
            ['150-01', 'PINJAMAN ANUITAS', 'Debet'],
            ['150-02', 'PINJAMAN POKOK TETAP', 'Debet'],

            // 151
            ['151-01', 'SEWA DIBAYAR DIMUKA', 'Debet'],

            // 153
            ['153-01', 'PIUTANG BUNGA', 'Debet'],
            ['153-02', 'PIUTANG DENDA', 'Debet'],

            // 155
            ['155-01', 'PIUTANG KARYAWAN', 'Debet'],
            ['155-02', 'PIUTANG LAIN', 'Debet'],

            // 160
            ['160-01', 'CADANGAN PENGHAPUSAN ASET PRODUKTIF', 'Debet'],

            // 170
            ['170-01', 'SUPLIES KANTOR', 'Debet'],
            ['170-02', 'PERSEDIAAN BEA MATERAI', 'Debet'],

            // 200
            ['200-01', 'INVENTARIS KOMPUTER', 'Debet'],
            ['200-02', 'INVENTARIS KENDARAAN', 'Debet'],
            ['200-03', 'INVENTARIS KANTOR', 'Debet'],
            ['200-04', 'FURNITURE', 'Debet'],
            ['200-05', 'BANGUNAN', 'Debet'],
            ['200-06', 'TANAH', 'Debet'],

            // 201
            ['201-01', 'AKUMULASI PENYUSUTAN INVENTARIS KOMPUTER', 'Debet'],
            ['201-02', 'AKUMULASI PENYUSUTAN KENDARAAN', 'Debet'],
            ['201-03', 'AKUMULASI PENYUSUTAN KANTOR', 'Debet'],
            ['201-04', 'AKUMULASI PENYUSUTAN FURNITURE', 'Debet'],
            ['201-05', 'AKUMULASI PENYUSUTAN BANGUNAN', 'Debet'],

            // 400
            ['400-01', 'PENDAPATAN YANG AKAN DITERIMA', 'Debet'],
            ['400-02', 'PAJAK PBB DIBAYAR DIMUKA', 'Debet'],
            ['400-03', 'BEBAN DITANGGUHKAN', 'Debet'],
            ['400-04', 'SEWA DIBAYAR DIMUKA', 'Debet'],
            ['400-05', 'AGUNAN YANG DIAMBIL ALIH', 'Debet'],
            ['400-06', 'PENDIRIAN KOPERASI', 'Debet'],
            ['400-07', 'SOFTWARE KOMPUTER', 'Debet'],

            // 500
            ['500-01', 'BEBAN PAJAK BUNGA SIMPANAN', 'Kredit'],
            ['500-02', 'BEBAN PAJAK SIMPANAN BERJANGKA', 'Kredit'],
            ['500-03', 'BEBAN BIAYA NOTARIS', 'Kredit'],
            ['500-04', 'BEBAN TITIPAN BUNGA BERJANGKA', 'Kredit'],
            ['500-05', 'BEBAN TITIPAN ANGSURAN', 'Kredit'],
            ['500-06', 'BAGIAN SHU ANGGOTA PENYIMPAN', 'Kredit'],
            ['500-07', 'BAGIAN SHU ANGGOTA BERJASA', 'Kredit'],
            ['500-08', 'BAGIAN SHU PENGURUS', 'Kredit'],
            ['500-09', 'DANA KESEJAHTERAAN KARYAWAN', 'Kredit'],
            ['500-10', 'DANA SOSIAL', 'Kredit'],
            ['500-11', 'DANA PENDIDIKAN', 'Kredit'],
            ['500-12', 'DANA TITIPAN ANGGOTA', 'Kredit'],

            // 510
            ['510-01', 'SIMPANAN ANGGOTA', 'Kredit'],

            // 520
            ['520-01', 'SIMPANAN BERJANGKA', 'Kredit'],

            // 560
            ['560-01', 'HUTANG LAIN-LAIN', 'Kredit'],

            // 561
            ['561-01', 'HUTANG BANK', 'Kredit'],

            // 700
            ['700-01', 'BUNGA YANG MASIH HARUS DIBAYAR', 'Kredit'],
            ['700-02', 'PAJAK PENGHASILAN', 'Kredit'],
            ['700-03', 'PENDAPATAN DITERIMA DIMUKA', 'Kredit'],

            // 800
            ['800-01', 'SISA HASIL USAHA', 'Kredit'],
            ['800-02', 'SIMPANAN POKOK', 'Kredit'],
            ['800-03', 'SIMPANAN WAJIB', 'Kredit'],
            ['800-04', 'MODAL PENYERTAAN PARTISIPASI ANGGOTA', 'Kredit'],
            ['800-05', 'MODAL CADANGAN', 'Kredit'],
            ['800-06', 'MODAL DONASI', 'Kredit'],

            // 900
            ['900-01', 'HASIL BUNGA PINJAMAN', 'Kredit'],
            ['900-02', 'RETUR HASIL BUNGA PINJAMAN', 'Debet'],

            // 901
            ['901-01', 'HASIL BUNGA TABUNGAN', 'Kredit'],

            // 902
            ['902-01', 'HASIL BUNGA BANK', 'Kredit'],

            // 903
            ['903-01', 'HASIL BUNGA SIMPANAN BERJANGKA', 'Kredit'],

            // 910
            ['910-01', 'PROVISI PINJAMAN', 'Kredit'],

            // 931
            ['931-01', 'PENDAPATAN ADMINISTRASI SIMPANAN', 'Kredit'],
            ['931-02', 'PENDAPATAN DENDA PINJAMAN', 'Kredit'],
            ['931-03', 'PENDAPATAN ASURANSI PINJAMAN', 'Kredit'],
            ['931-04', 'PENDAPATAN ADMINISTRASI PINALTI SIMPANAN BERJANGKA', 'Kredit'],
            ['931-05', 'PENDAPATAN ADMINISTRASI PINJAMAN', 'Kredit'],
            ['931-06', 'PENDAPATAN PINALTI PINJAMAN', 'Kredit'],
            ['931-07', 'PENDAPATAN BIAYA METERAI', 'Kredit'],
            ['931-08', 'PENDAPATAN BIAYA NOTARIS', 'Kredit'],
            ['931-09', 'PENDAPATAN PREMI RESIKO KREDIT', 'Kredit'],
            ['931-10', 'PAJAK SIMPANAN', 'Kredit'],

            // 940
            ['940-01', 'LABA PENJUALAN AKTIVA TETAP', 'Kredit'],
            ['940-02', 'PENDAPATAN NONOPERASIONAL LAINNYA', 'Kredit'],

            // 950
            ['950-01', 'AMORTISASI PENDAPATAN BUNGA DIMUKA', 'Debet'],
            ['950-02', 'AMORTISASI PAJAK PBB DIBAYAR DIMUKA', 'Debet'],
            ['950-03', 'AMORTISASI BEBAN DITANGGUHKAN', 'Debet'],
            ['950-04', 'AMORTISASI SEWA DIBAYAR DIMUKA', 'Debet'],
            ['950-05', 'AMORTISASI PENDIRIAN GEDUNG', 'Debet'],
            ['950-06', 'AMORTISASI PROGRAM KOMPUTER', 'Debet'],

            // 960
            ['960-01', 'BIAYA BUNGA SIMPANAN ANGGOTA', 'Debet'],
            ['960-02', 'BIAYA BUNGA SIMPANAN BERJANGKA', 'Debet'],
            ['960-03', 'BIAYA BUNGA SIMPANAN YANG DITERIMA', 'Debet'],
            ['960-04', 'BIAYA BUNGA REKENING KORAN', 'Debet'],

            // 961
            ['961-01', 'BIAYA PAJAK BUNGA REKENING KORAN', 'Debet'],
            ['961-02', 'BIAYA PAJAK DEPOSITO', 'Debet'],
            ['961-03', 'BIAYA PAJAK TABUNGAN', 'Debet'],

            // 962
            ['962-01', 'POTONGAN BUNGA', 'Debet'],
            ['962-02', 'POTONGAN DENDA', 'Debet'],

            // 970
            ['970-01', 'BIAYA GAJI DAN HONORARIUM', 'Debet'],
            ['970-02', 'BIAYA UANG LEMBUR', 'Debet'],
            ['970-03', 'BIAYA PENDIDIKAN', 'Debet'],
            ['970-04', 'BIAYA UANG MAKAN KARYAWAN', 'Debet'],
            ['970-05', 'BIAYA TENAGA KERJA LAINNYA', 'Debet'],
            ['970-06', 'BIAYA BONUS KARYAWAN', 'Debet'],

            // 980
            ['980-01', 'BIAYA SEWA INVENTARIS', 'Debet'],
            ['980-02', 'BIAYA SEWA GEDUNG', 'Debet'],
            ['980-03', 'BIAYA SEWA KENDARAAN', 'Debet'],
            ['980-04', 'BIAYA SEWA LAINNYA', 'Debet'],

            // 981
            ['981-01', 'BIAYA ALAT TULIS KANTOR', 'Debet'],
            ['981-02', 'BIAYA TELEPON', 'Debet'],
            ['981-03', 'BIAYA FOTOKOPI', 'Debet'],
            ['981-04', 'BIAYA PERJALANAN DINAS', 'Debet'],
            ['981-05', 'BIAYA IKLAN DAN KONSULTAN', 'Debet'],
            ['981-06', 'BIAYA SURAT KABAR', 'Debet'],
            ['981-07', 'BIAYA PERJAMUAN', 'Debet'],
            ['981-08', 'BIAYA LISTRIK DAN AIR', 'Debet'],
            ['981-09', 'BIAYA METERAI LAINNYA', 'Debet'],
            ['981-10', 'BIAYA AKTA NOTARIS', 'Debet'],
            ['981-11', 'BIAYA JASA ADMINISTRASI LAINNYA', 'Debet'],
            ['981-12', 'BIAYA BENSIN DAN TRANSPORTASI', 'Debet'],
            ['981-13', 'BIAYA RUMAH TANGGA KANTOR', 'Debet'],
            ['981-14', 'BIAYA BARANG CETAKAN', 'Debet'],
            ['981-15', 'BIAYA FILM DAN CETAK FOTO', 'Debet'],
            ['981-16', 'BIAYA KEAMANAN', 'Debet'],
            ['981-17', 'BIAYA KOMISI', 'Debet'],
            ['981-18', 'BIAYA PARKIR', 'Debet'],
            ['981-19', 'BIAYA BUNGA PINJAMAN BANK', 'Debet'],

            // 982
            ['982-01', 'BIAYA PEMELIHARAAN INVENTARIS', 'Debet'],
            ['982-02', 'BIAYA PEMELIHARAAN GEDUNG', 'Debet'],
            ['982-03', 'BIAYA PEMELIHARAAN KENDARAAN', 'Debet'],
            ['982-04', 'BIAYA PEMELIHARAAN LAINNYA', 'Debet'],
            ['982-05', 'BIAYA PEMELIHARAAN KOMPUTER', 'Debet'],

            // 983
            ['983-01', 'BIAYA PAJAK PBB', 'Debet'],
            ['983-02', 'BIAYA PAJAK INVENTARIS GEDUNG', 'Debet'],
            ['983-03', 'BEA METERAI KERTAS SEGEL', 'Debet'],
            ['983-04', 'BIAYA PAJAK REKLAME', 'Debet'],
            ['983-05', 'BIAYA PAJAK LAINNYA', 'Debet'],

            // 984
            ['984-01', 'BIAYA PENYUSUTAN REKENING PINJAMAN', 'Debet'],
            ['984-02', 'BIAYA PENYUSUTAN ASET PRODUKTIF LAINNYA', 'Debet'],

            // 985
            ['985-01', 'BIAYA ASURANSI INVENTARIS', 'Debet'],
            ['985-02', 'BIAYA ASURANSI KENDARAAN', 'Debet'],
            ['985-03', 'BIAYA ASURANSI KARYAWAN', 'Debet'],
            ['985-04', 'BIAYA OPERASIONAL LAINNYA', 'Debet'],

            // 986
            ['986-01', 'BIAYA PENYUSUTAN INVENTARIS KOMPUTER', 'Debet'],
            ['986-02', 'BIAYA PENYUSUTAN KENDARAAN', 'Debet'],
            ['986-03', 'BIAYA PENYUSUTAN GEDUNG', 'Debet'],
            ['986-04', 'BIAYA PENYUSUTAN INVENTARIS LAINNYA', 'Debet'],

            // 990
            ['990-01', 'PENJUALAN RUGI AKTIVA TETAP', 'Debet'],
            ['990-02', 'BIAYA SUMBANGAN', 'Debet'],
            ['990-03', 'BIAYA DENDA', 'Debet'],
            ['990-04', 'BIAYA NONOPERASIONAL LAINNYA', 'Debet'],
            ['990-05', 'BIAYA KERUGIAN', 'Debet'],
        ];

        foreach ($accounts as [$noAccount, $nama, $tipe]) {

            $noHeader = explode('-', $noAccount)[0];
            $header = AccHeader::where('no_header', $noHeader)->first();

            if (!$header) {
                continue;
            }

            Account::firstOrCreate(
                ['no_account' => $noAccount],
                [
                    'nama'      => $nama,
                    'header_id' => $header->id,
                    'tipe'      => $tipe,
                    'user_id'   => $this->adminUserId(),
                ]
            );
        }
    }
}
