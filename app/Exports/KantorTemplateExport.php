<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KantorTemplateExport implements FromArray, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return [
            'Kode',
            'Nama Kantor',
            'Alamat Kantor',
            'Pejabat',
            'Jabatan',
            'Bendahara',
        ];
    }

    public function array(): array
    {
        return []; // kosong, hanya header
    }
}
