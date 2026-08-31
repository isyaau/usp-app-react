<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanTunggakanPinjamanExport implements FromArray, WithEvents
{
    protected $pinjaman;
    protected $sektors;
    protected $filters;

    public function __construct($pinjaman, $sektors, $filters)
    {
        $this->pinjaman = $pinjaman;
        $this->sektors = $sektors;
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

                $sheet->mergeCells('B2:N2');
                $sheet->setCellValue('B2', 'LAPORAN TUNGGAKAN PINJAMAN');
                $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('B2:N2')->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->mergeCells('B3:N3');
                $sheet->setCellValue('B3', $this->filterSummary());
                $sheet->getStyle('B3:N3')->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $headers = [
                    'A5' => 'No',
                    'B5' => 'No Pinjaman',
                    'C5' => 'No Anggota',
                    'D5' => 'Nama',
                    'E5' => 'Kelompok',
                    'F5' => 'Jenis',
                    'G5' => 'Sektor',
                    'H5' => 'Plafon',
                    'I5' => 'Angsuran ke',
                    'J5' => 'Jangka Waktu',
                    'K5' => 'Jatuh Tempo',
                    'L5' => 'Sisa Hari',
                    'M5' => 'Tunggakan',
                    'N5' => 'Kantor',
                ];

                foreach ($headers as $cell => $text) {
                    $sheet->setCellValue($cell, $text);
                }

                $sheet->getStyle('A5:N5')->applyFromArray([
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
                    $pokokTerbayar = (float) \App\Models\AngsuranPinjaman::where('pinjaman_id', $item->id)->sum('nominal_pokok');
                    $sisa = max(0, $plafon - $pokokTerbayar);

                    $jatuhTempo = $item->jatuh_tempo ? \Carbon\Carbon::parse($item->jatuh_tempo) : null;
                    $sisaHari = $jatuhTempo ? max(0, $jatuhTempo->startOfDay()->diffInDays(now()->startOfDay(), false)) : '';

                    $sektor = $this->sektors->get((int) $item->sektor_id) ?? '-';

                    $sheet->setCellValue("A{$row}", $no++);
                    $sheet->setCellValue("B{$row}", $item->no_pinjaman);
                    $sheet->setCellValue("C{$row}", $item->anggota->no_anggota ?? '-');
                    $sheet->setCellValue("D{$row}", $item->anggota->nama ?? '-');
                    $sheet->setCellValue("E{$row}", $item->anggota->kelompok->nama ?? '-');
                    $sheet->setCellValue("F{$row}", $item->jenisPinjaman->nama ?? '-');
                    $sheet->setCellValue("G{$row}", $sektor);
                    $sheet->setCellValue("H{$row}", $plafon);
                    $sheet->setCellValue("I{$row}", $item->angsuranke);
                    $sheet->setCellValue("J{$row}", $item->jangka_waktu.' '.($item->satuan ?? 'bulan'));
                    $sheet->setCellValue("K{$row}", $item->jatuh_tempo ?? '-');
                    $sheet->setCellValue("L{$row}", $sisaHari);
                    $sheet->setCellValue("M{$row}", $sisa);
                    $sheet->setCellValue("N{$row}", $item->kantor->nama_kantor ?? '-');

                    $row++;
                }

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A5:N{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                foreach (range('A', 'N') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

    private function filterSummary(): string
    {
        $parts = [];
        if (! empty($this->filters['kantor_id'])) {
            $kantor = \App\Models\Kantor::find($this->filters['kantor_id']);
            $parts[] = 'Kantor: '.($kantor->nama_kantor ?? $this->filters['kantor_id']);
        }
        if (! empty($this->filters['jenis_id'])) {
            $jenis = \App\Models\PinjamanProduk::find($this->filters['jenis_id']);
            $parts[] = 'Produk: '.($jenis->nama ?? $this->filters['jenis_id']);
        }
        if (! empty($this->filters['sektor_id'])) {
            $parts[] = 'Sektor: '.($this->sektors->get((int) $this->filters['sektor_id']) ?? $this->filters['sektor_id']);
        }
        if (! empty($this->filters['mulai']) || ! empty($this->filters['sampai'])) {
            $parts[] = 'Jatuh Tempo: '.(! empty($this->filters['mulai']) ? $this->filters['mulai'] : '*').' s/d '.(! empty($this->filters['sampai']) ? $this->filters['sampai'] : '*');
        }
        if (! empty($this->filters['hari_lagi'])) {
            $parts[] = 'Jatuh Tempo dalam '.$this->filters['hari_lagi'].' hari';
        }

        return $parts ? implode('  |  ', $parts) : 'Semua Data';
    }
}
