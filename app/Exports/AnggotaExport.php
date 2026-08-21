<?php

namespace App\Exports;


use App\Models\Anggota;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AnggotaExport implements FromArray, WithEvents
{
    protected $tglMulai;
    protected $tglSampai;

    public function __construct($tglMulai, $tglSampai)
    {
        $this->tglMulai = $tglMulai;
        $this->tglSampai = $tglSampai;
    }

    public function array(): array
    {
        // Return kosong, data akan ditulis manual di AfterSheet
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                /*
            |--------------------------------------------------------------------------
            | LOGO
            |--------------------------------------------------------------------------
            */
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo Perusahaan');
                $drawing->setPath(public_path('img/logo-banner.jpg'));
                $drawing->setHeight(50);
                $drawing->setCoordinates('A2');
                $drawing->setOffsetX(10);
                $drawing->setWorksheet($sheet);

                /*
            |--------------------------------------------------------------------------
            | JUDUL
            |--------------------------------------------------------------------------
            */
                $sheet->mergeCells('B2:V2');
                $sheet->setCellValue('B2', 'LAPORAN DATA ANGGOTA');
                $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('B2:V2')->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                /*
            |--------------------------------------------------------------------------
            | RANGE TANGGAL
            |--------------------------------------------------------------------------
            */
                $rangeText = 'Semua Data';
                if ($this->tglMulai && $this->tglSampai) {
                    $rangeText = "Dari {$this->tglMulai} Sampai {$this->tglSampai}";
                } elseif ($this->tglMulai) {
                    $rangeText = "Mulai {$this->tglMulai}";
                } elseif ($this->tglSampai) {
                    $rangeText = "Sampai {$this->tglSampai}";
                }

                $sheet->mergeCells('B3:V3');
                $sheet->setCellValue('B3', $rangeText);
                $sheet->getStyle('B3:V3')->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                /*
            |--------------------------------------------------------------------------
            | HEADER TABEL (BARIS 5)
            |--------------------------------------------------------------------------
            */
                $headers = [
                    'A5' => 'No',
                    'B5' => 'Kelompok',
                    'C5' => 'Kantor',
                    'D5' => 'Nomor Anggota',
                    'E5' => 'PIN',
                    'F5' => 'Nama',
                    'G5' => 'Email',
                    'H5' => 'Alamat',
                    'I5' => 'Tempat Lahir',
                    'J5' => 'Tanggal Lahir',
                    'K5' => 'Jenis Kelamin',
                    'L5' => 'Agama',
                    'M5' => 'Telepon',
                    'N5' => 'Nomor HP',
                    'O5' => 'Pendidikan',
                    'P5' => 'Pekerjaan',
                    'Q5' => 'Status Perkawinan',
                    'R5' => 'Nama Pasangan',
                    'S5' => 'Nama Ibu',
                    'T5' => 'Jenis Identitas',
                    'U5' => 'Nomor Identitas',
                    'V5' => 'NPWP',
                ];

                foreach ($headers as $cell => $text) {
                    $sheet->setCellValue($cell, $text);
                }

                $sheet->getStyle('A5:V5')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFC000'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);

                /*
            |--------------------------------------------------------------------------
            | QUERY DATA
            |--------------------------------------------------------------------------
            */
                $query = Anggota::query();

                if ($this->tglMulai) {
                    $query->whereDate(
                        'created_at',
                        '>=',
                        \Carbon\Carbon::createFromFormat('d-m-Y', $this->tglMulai)
                    );
                }

                if ($this->tglSampai) {
                    $query->whereDate(
                        'created_at',
                        '<=',
                        \Carbon\Carbon::createFromFormat('d-m-Y', $this->tglSampai)
                    );
                }

                $anggota = $query->get([
                    'kelompok_id',
                    'kantor_id',
                    'no_anggota',
                    'pin',
                    'nama',
                    'email',
                    'alamat',
                    'tempat_lahir',
                    'tgl_lahir',
                    'jenis_kelamin',
                    'agama',
                    'telepon',
                    'no_hp',
                    'pendidikan',
                    'pekerjaan',
                    'status_perkawinan',
                    'pasangan',
                    'ibu',
                    'jenis_identitas',
                    'no_identitas',
                    'npwp',
                ]);

                /*
            |--------------------------------------------------------------------------
            | ISI DATA (MULAI BARIS 6)
            |--------------------------------------------------------------------------
            */
                $row = 6;
                $no  = 1;

                foreach ($anggota as $item) {
                    $sheet->setCellValue("A{$row}", $no++);
                    $sheet->setCellValue("B{$row}", $item->kelompok?->nama ?? '');
                    $sheet->setCellValue("C{$row}", $item->kantor?->nama_kantor ?? '');
                    $sheet->setCellValue("D{$row}", $item->no_anggota);
                    $sheet->setCellValue("E{$row}", $item->pin);
                    $sheet->setCellValue("F{$row}", $item->nama);
                    $sheet->setCellValue("G{$row}", $item->email);
                    $sheet->setCellValue("H{$row}", $item->alamat);
                    $sheet->setCellValue("I{$row}", $item->tempat_lahir);
                    $sheet->setCellValue("J{$row}", $item->tgl_lahir);
                    $sheet->setCellValue("K{$row}", $item->jenis_kelamin);
                    $sheet->setCellValue("L{$row}", $item->agama);
                    $sheet->setCellValue("M{$row}", $item->telepon);
                    $sheet->setCellValue("N{$row}", $item->no_hp);
                    $sheet->setCellValue("O{$row}", $item->pendidikan);
                    $sheet->setCellValue("P{$row}", $item->pekerjaan);
                    $sheet->setCellValue("Q{$row}", $item->status_perkawinan);
                    $sheet->setCellValue("R{$row}", $item->pasangan);
                    $sheet->setCellValue("S{$row}", $item->ibu);
                    $sheet->setCellValue("T{$row}", $item->jenis_identitas);
                    $sheet->setCellValue("U{$row}", $item->no_identitas);
                    $sheet->setCellValue("V{$row}", $item->npwp);

                    $row++;
                }

                /*
            |--------------------------------------------------------------------------
            | BORDER DATA
            |--------------------------------------------------------------------------
            */
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A5:V{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);

                /*
            |--------------------------------------------------------------------------
            | AUTO WIDTH
            |--------------------------------------------------------------------------
            */
                foreach (range('A', 'V') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
