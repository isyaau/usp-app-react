<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AnggotaTemplateExport implements FromArray, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function headings(): array
    {
        return [
            'Kelompok',
            'Kantor',
            'Nomor Anggota',
            'PIN',
            'Nama',
            'Email',
            'Alamat',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Agama',
            'Telepon',
            'Nomor HP',
            'Pendidikan',
            'Pekerjaan',
            'Status Perkawinan',
            'Nama Pasangan',
            'Nama Ibu',
            'Jenis Identitas',
            'Nomor Identitas',
            'NPWP',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Kelompok A',
                'Kantor Pusat',
                'AGT001',
                '123456',
                'Budi Santoso',
                'budi@example.com',
                'Jl. Merdeka No. 10, Jakarta',
                'Jakarta',
                '19-08-1995',
                'Laki-laki',
                'Islam',
                '0211234567',
                '081234567890',
                'S1',
                'Karyawan Swasta',
                'Menikah',
                'Siti Aminah',
                'Sri Wahyuni',
                'KTP',
                '317xxxxxxxxxxxxx',
                '12.345.678.9-012.000',
            ]
        ];
    }
}
