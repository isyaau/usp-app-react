<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'Tanggal Lahir',
            'Nomor Telepon',
        ];
    }

    public function array(): array
    {
        return []; // kosong, hanya header
    }
}
