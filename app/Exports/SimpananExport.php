<?php

namespace App\Exports;

use App\Models\Simpanan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SimpananExport implements FromArray, WithEvents
{
    protected $tglMulai;
    protected $tglSampai;

    public function __construct($tglMulai = null, $tglSampai = null)
    {
        $this->tglMulai = $tglMulai;
        $this->tglSampai = $tglSampai;
    }

    public function array(): array
    {
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
                try {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Logo Perusahaan');
                    $drawing->setPath(public_path('img/logo-banner.jpg'));
                    $drawing->setHeight(50);
                    $drawing->setCoordinates('A2');
                    $drawing->setOffsetX(10);
                    $drawing->setWorksheet($sheet);
                } catch (\Throwable $e) {
                    // abaikan bila logo tidak tersedia
                }

                /*
                |--------------------------------------------------------------------------
                | JUDUL
                |--------------------------------------------------------------------------
                */
                $sheet->mergeCells('B2:Q2');
                $sheet->setCellValue('B2', 'LAPORAN DATA SIMPANAN');
                $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('B2:Q2')->getAlignment()
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

                $sheet->mergeCells('B3:Q3');
                $sheet->setCellValue('B3', $rangeText);
                $sheet->getStyle('B3:Q3')->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                /*
                |--------------------------------------------------------------------------
                | HEADER TABEL (BARIS 5)
                |--------------------------------------------------------------------------
                */
                $headers = [
                    'A5' => 'No',
                    'B5' => 'Tanggal',
                    'C5' => 'No. Rekening',
                    'D5' => 'Produk',
                    'E5' => 'Jenis',
                    'F5' => 'No. Anggota',
                    'G5' => 'Nama Anggota',
                    'H5' => 'Bagi Hasil',
                    'I5' => 'Marketing',
                    'J5' => 'Kantor',
                    'K5' => 'Nominal Setor',
                    'L5' => 'Status',
                    'M5' => 'SMS',
                    'N5' => 'Blokir',
                ];

                foreach ($headers as $cell => $text) {
                    $sheet->setCellValue($cell, $text);
                }

                $sheet->getStyle('A5:N5')->applyFromArray([
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
                $query = Simpanan::query()
                    ->with([
                        'anggota:id,no_anggota,nama',
                        'jenis_simpanan:id,kode,nama,jenis,bunga',
                        'marketing:id,nama',
                        'kantor:id,nama_kantor',
                    ]);

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

                $simpanan = $query->orderBy('created_at', 'DESC')->get();

                $jenisLabels = [
                    1 => 'Pokok',
                    2 => 'Wajib',
                    3 => 'Sukarela',
                    4 => 'Wajib Pinjaman',
                    5 => 'Saham',
                    6 => 'Pokok Pinjaman',
                    7 => 'Rencana',
                ];

                /*
                |--------------------------------------------------------------------------
                | ISI DATA (MULAI BARIS 6)
                |--------------------------------------------------------------------------
                */
                $row = 6;
                $no  = 1;

                foreach ($simpanan as $item) {
                    $sheet->setCellValue("A{$row}", $no++);
                    $sheet->setCellValue("B{$row}", $item->tanggal);
                    $sheet->setCellValue("C{$row}", $item->no_rekening);
                    $sheet->setCellValue("D{$row}", $item->jenis_simpanan?->nama ?? '');
                    $sheet->setCellValue(
                        "E{$row}",
                        $jenisLabels[$item->jenis_simpanan?->jenis ?? 0] ?? ''
                    );
                    $sheet->setCellValue("F{$row}", $item->anggota?->no_anggota ?? '');
                    $sheet->setCellValue("G{$row}", $item->anggota?->nama ?? '');
                    $sheet->setCellValue("H{$row}", $item->jenis_simpanan?->bunga ?? $item->bunga);
                    $sheet->setCellValue("I{$row}", $item->marketing?->nama ?? '');
                    $sheet->setCellValue("J{$row}", $item->kantor?->nama_kantor ?? '');
                    $sheet->setCellValue("K{$row}", $item->nominal_setor);
                    $sheet->setCellValue("L{$row}", $item->aktif === '1' ? 'Aktif' : 'Nonaktif');
                    $sheet->setCellValue("M{$row}", $item->sms === '1' ? 'Aktif' : 'Nonaktif');
                    $sheet->setCellValue("N{$row}", $item->blokir_simpanan === '1' ? 'Diblokir' : 'Tidak');

                    $row++;
                }

                /*
                |--------------------------------------------------------------------------
                | BORDER DATA
                |--------------------------------------------------------------------------
                */
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A5:N{$highestRow}")->applyFromArray([
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
                foreach (range('A', 'N') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
