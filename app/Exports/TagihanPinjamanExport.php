<?php

namespace App\Exports;

use App\Models\AngsuranPinjaman;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TagihanPinjamanExport implements FromArray, WithEvents
{
    protected $pinjaman;
    protected $filters;

    public function __construct($pinjaman, $filters)
    {
        $this->pinjaman = $pinjaman;
        $this->filters = $filters;
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

                $logoPath = public_path('img/logo-banner.jpg');
                if (file_exists($logoPath)) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Logo Perusahaan');
                    $drawing->setPath($logoPath);
                    $drawing->setHeight(50);
                    $drawing->setCoordinates('A2');
                    $drawing->setOffsetX(10);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->mergeCells('B2:M2');
                $sheet->setCellValue('B2', 'TAGIHAN PINJAMAN');
                $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('B2:M2')->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->mergeCells('B3:M3');
                $sheet->setCellValue('B3', $this->filterSummary());
                $sheet->getStyle('B3:M3')->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $headers = [
                    'A5' => 'No',
                    'B5' => 'No Pinjaman',
                    'C5' => 'Tgl Bayar',
                    'D5' => 'No Anggota',
                    'E5' => 'Nama',
                    'F5' => 'Produk',
                    'G5' => 'Plafon',
                    'H5' => 'Jangka Waktu',
                    'I5' => 'Satuan',
                    'J5' => 'Angsuran',
                    'K5' => 'Sisa Pokok',
                    'L5' => 'Tunggakan',
                    'M5' => 'Status',
                ];

                foreach ($headers as $cell => $text) {
                    $sheet->setCellValue($cell, $text);
                }

                $sheet->getStyle('A5:M5')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFC000'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $row = 6;
                $no = 1;

                foreach ($this->pinjaman as $item) {
                    $plafon = (float) $item->plafon;
                    $pokokTerbayar = (float) AngsuranPinjaman::where('pinjaman_id', $item->id)->sum('nominal_pokok');
                    $sisa = max(0, $plafon - $pokokTerbayar);
                    $tglBayar = $item->tgl_bayar ?? '';

                    $sheet->setCellValue("A{$row}", $no++);
                    $sheet->setCellValue("B{$row}", $item->no_pinjaman);
                    $sheet->setCellValue("C{$row}", is_string($tglBayar) ? $tglBayar : '');
                    $sheet->setCellValue("D{$row}", $item->anggota->no_anggota ?? '-');
                    $sheet->setCellValue("E{$row}", $item->anggota->nama ?? '-');
                    $sheet->setCellValue("F{$row}", $item->jenisPinjaman->nama ?? '-');
                    $sheet->setCellValue("G{$row}", $plafon);
                    $sheet->setCellValue("H{$row}", $item->jangka_waktu);
                    $sheet->setCellValue("I{$row}", $item->satuan);
                    $sheet->setCellValue("J{$row}", (float) $item->nominal_angsuran);
                    $sheet->setCellValue("K{$row}", $sisa);
                    $sheet->setCellValue("L{$row}", $sisa);
                    $sheet->setCellValue("M{$row}", $sisa <= 0 ? 'LUNAS' : 'BELUM LUNAS');

                    $row++;
                }

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A5:M{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                foreach (range('A', 'M') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

    private function filterSummary(): string
    {
        $parts = [];
        if (! empty($this->filters['status'])) {
            $parts[] = 'Status: '.($this->filters['status'] === 'lunas' ? 'Lunas' : ($this->filters['status'] === 'belum' ? 'Belum Lunas' : $this->filters['status']));
        }
        if (! empty($this->filters['search'])) {
            $parts[] = 'Pencarian: '.$this->filters['search'];
        }
        if (! empty($this->filters['mulai']) || ! empty($this->filters['sampai'])) {
            $parts[] = 'Jatuh Tempo: '.(! empty($this->filters['mulai']) ? $this->filters['mulai'] : '*').' s/d '.(! empty($this->filters['sampai']) ? $this->filters['sampai'] : '*');
        }

        return $parts ? implode('  |  ', $parts) : 'Semua Data';
    }
}
