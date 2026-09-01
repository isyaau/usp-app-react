<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SimpananTemplateExport implements FromArray, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return [
            'Tanggal',
            'No Rekening',
            'No Anggota',
            'Produk',
            'Marketing',
            'QQ',
            'Bagi Hasil',
            'Nominal Setor',
            'Aktif',
            'SMS',
            'Blokir Simpanan',
            'Blokir Nominal',
            'Nominal Blokir',
            'Blokir s/d Tanggal',
            'Kantor',
        ];
    }

    public function array(): array
    {
        return [
            [
                '19-08-2026',
                '001.2608.0001',
                'AGT001',
                'Simpanan Wajib',
                'Marketing A',
                'KOPINKA',
                '12',
                '100000',
                'Aktif',
                'Ya',
                'Tidak',
                'Tidak',
                '',
                '',
                'Kantor Pusat',
            ]
        ];
    }
}
