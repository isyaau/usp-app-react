<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KelompokTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Kode',
            'Nama',
        ];
    }

    public function array(): array
    {
        return []; // kosong, hanya header
    }
}
