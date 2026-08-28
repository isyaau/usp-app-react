<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PinjamanTemplateExport implements FromArray, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return [
            'Tanggal',
            'No Pinjaman',
            'No Anggota',
            'Produk',
            'Plafon',
            'Bunga',
            'Jangka Waktu',
            'Satuan',
            'Nominal Angsuran',
            'Aktif',
        ];
    }

    public function array(): array
    {
        return [
            [
                '19-08-2026',
                '001.2608.0001',
                'AGT001',
                'Pinjaman Reguler',
                '5000000',
                '12',
                '12',
                'bulan',
                '',
                'Aktif',
            ]
        ];
    }
}