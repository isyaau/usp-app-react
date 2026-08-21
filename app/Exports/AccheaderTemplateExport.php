<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccheaderTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'No Header',
            'Nama',
            'Group',
        ];
    }

    public function array(): array
    {
        return []; // kosong, hanya header
    }
}
