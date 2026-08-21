<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SimpananKode;
use App\Models\Account;

class KodeTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['11', 'Setoran Tunai', '100-01', '800-02', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', '', 'admin'],
            ['110', 'Bunga', '960-01', '800-02', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', '', 'admin'],
            ['21', 'Setoran Tunai', '100-01', '800-03', '1', '0', '0', '0', '1', '0', '0', '0', '0', '0', '', 'admin'],
            ['22', 'Tarikan Tunai', '800-03', '100-01', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', '', 'admin'],
            ['23', 'Pemindahan Simpanan', '800-03', '800-03', '0', '0', '1', '0', '1', '0', '0', '0', '0', '0', 'Pemindahan dana antar sesama simpanan anggota', 'admin'],
            ['24', 'Koreksi Debet', '100-01', '800-03', '0', '1', '1', '0', '1', '0', '0', '0', '0', '0', 'Koreksi debet pada simpanan dengan di kredit sesuai nominal yang salah', 'admin'],
            ['25', 'Koreksi Kredit', '800-03', '100-01', '1', '0', '1', '0', '1', '0', '0', '0', '0', '0', 'Koreksi kredit pada simpanan dengan di debet sesuai nominal yang salah', 'admin'],
            ['26', 'Transaksi Debet', '800-03', '100-01', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', 'Tarikan simpanan bisa dengan tujuan apapun dan dalam pemakaian tidak tergantung pada account debet atau kredit yang telah di set sebelumnya.', 'admin'],
            ['27', 'Transaksi Kredit', '100-01', '800-03', '1', '0', '0', '0', '1', '0', '0', '0', '0', '0', 'Setoran simpanan bisa dengan tujuan apapun dan dalam pemakaian tidak tergantung pada account debet atau kredit yang telah di set sebelumnya.', 'admin'],
            ['28', 'Biaya Administrasi', '800-03', '931-01', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', 'Biaya Administrasi', 'admin'],
            ['29', 'Pajak', '800-03', '931-10', '0', '1', '0', '0', '1', '0', '0', '0', '0', '0', 'Pajak simpanan', 'admin'],

            ['12', 'Tarikan Tunai', '800-02', '100-01', '0', '1', '0', '1', '0', '0', '0', '0', '0', '0', '', 'admin'],
            ['210', 'Bunga', '960-01', '800-03', '1', '0', '0', '0', '1', '0', '0', '0', '0', '0', '', 'admin'],
            ['13', 'Pemindahan Simpanan', '800-02', '800-02', '0', '0', '1', '1', '0', '0', '0', '0', '0', '0', 'Pemindahan dana antar sesama simpanan anggota', 'admin'],
            ['14', 'Koreksi Debet', '100-01', '800-02', '0', '1', '1', '1', '0', '0', '0', '0', '0', '0', 'Koreksi debet pada simpanan dengan di kredit sesuai nominal yang salah', 'admin'],
            ['15', 'Koreksi Kredit', '800-02', '100-01', '1', '0', '1', '1', '0', '0', '0', '0', '0', '0', 'Koreksi kredit pada simpanan dengan di debet sesuai nominal yang salah', 'admin'],
            ['16', 'Transaksi Debet', '800-02', '100-01', '0', '1', '0', '1', '0', '0', '0', '0', '0', '0', 'Tarikan simpanan bisa dengan tujuan apapun dan dalam pemakaian tidak tergantung pada account debet atau kredit yang telah di set sebelumnya.', 'admin'],
            ['17', 'Transaksi Kredit', '100-01', '800-02', '1', '0', '0', '1', '0', '0', '0', '0', '0', '0', 'Setoran simpanan bisa dengan tujuan apapun dan dalam pemakaian tidak tergantung pada account debet atau kredit yang telah di set sebelumnya.', 'admin'],
            ['18', 'Biaya Administrasi', '800-02', '931-01', '0', '1', '0', '1', '0', '0', '0', '0', '0', '0', 'Biaya Administrasi', 'admin'],
            ['19', 'Pajak', '800-02', '931-10', '0', '1', '0', '1', '0', '0', '0', '0', '0', '0', 'Pajak simpanan', 'admin'],

            ['01', 'Setoran Tunai', '100-01', '510-01', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '', 'admin'],
            ['02', 'Tarikan Tunai', '510-01', '100-01', '0', '1', '0', '0', '0', '1', '0', '0', '0', '0', '', 'admin'],
            ['03', 'Pemindahan Simpanan', '510-01', '510-01', '0', '0', '1', '0', '0', '1', '0', '0', '0', '0', 'Pemindahan dana antar sesama simpanan anggota', 'admin'],
            ['04', 'Koreksi Debet', '100-01', '510-01', '0', '1', '1', '0', '0', '1', '0', '0', '0', '0', 'Koreksi debet pada simpanan dengan di kredit sesuai nominal yang salah', 'admin'],
            ['05', 'Koreksi Kredit', '510-01', '100-01', '1', '0', '1', '0', '0', '1', '0', '0', '0', '0', 'Koreksi kredit pada simpanan dengan di debet sesuai nominal yang salah', 'admin'],
            ['06', 'Transaksi Debet', '510-01', '100-01', '0', '1', '0', '0', '0', '1', '0', '0', '0', '0', 'Tarikan simpanan bisa dengan tujuan apapun dan dalam pemakaian tidak tergantung pada account debet atau kredit yang telah di set sebelumnya.', 'admin'],
            ['07', 'Transaksi Kredit', '100-01', '510-01', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', 'Setoran simpanan bisa dengan tujuan apapun dan dalam pemakaian tidak tergantung pada account debet atau kredit yang telah di set sebelumnya.', 'admin'],
            ['08', 'Biaya Administrasi', '510-01', '931-01', '0', '1', '0', '0', '0', '1', '0', '0', '0', '0', 'Biaya Administrasi', 'admin'],
            ['09', 'Pajak', '510-01', '931-10', '0', '1', '0', '0', '0', '1', '0', '0', '0', '0', 'Pajak simpanan', 'admin'],
            ['10', 'Bunga', '960-01', '510-01', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '', 'admin'],
        ];

        foreach ($data as $item) {
            [
                $kode,
                $nama,
                $debetNo,
                $kreditNo,
                $setoran,
                $tarikan,
                $transfer,
                $pokok,
                $wajib,
                $sukarela,
                $pinjaman,
                $saham,
                $pokok_pinjaman,
                $rencana,
                $keterangan,
                $user_id
            ] = $item;

            $debet = Account::where('no_account', $debetNo)->first();
            $kredit = Account::where('no_account', $kreditNo)->first();

            if (!$debet || !$kredit) {
                continue;
            }

            SimpananKode::firstOrCreate(
                ['kode' => $kode],
                [
                    'nama'            => $nama,
                    'account_debet'   => $debet->id,
                    'account_kredit'  => $kredit->id,
                    'setoran'         => $setoran,
                    'tarikan'         => $tarikan,
                    'transfer'        => $transfer,
                    'pokok'           => $pokok,
                    'wajib'           => $wajib,
                    'sukarela'        => $sukarela,
                    'pinjaman'        => $pinjaman,
                    'saham'           => $saham,
                    'pokok_pinjaman'  => $pokok_pinjaman,
                    'rencana'         => $rencana,
                    'keterangan'      => $keterangan,
                    'user_id'         => $user_id,
                ]
            );
        }
    }
}
