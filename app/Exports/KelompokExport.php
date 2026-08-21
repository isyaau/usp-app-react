<?php

namespace App\Exports;

use App\Models\Kelompok;
use App\Models\User;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class KelompokExport implements FromArray, WithEvents
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

                // --- Logo ---
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo Perusahaan');
                $drawing->setPath(public_path('img/logo-banner.jpg')); // ganti sesuai path logo
                $drawing->setHeight(50);
                $drawing->setCoordinates('A2'); // posisi logo kiri atas
                $drawing->setOffsetX(50);
                $drawing->setWorksheet($sheet);

                // --- Judul (baris 2-3) ---
                $sheet->mergeCells('B2:D2');
                $sheet->setCellValue('B2', 'Laporan Data Kelompok');
                $sheet->getStyle('B2:D2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('B2:D2')->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                // --- Range tanggal (baris 3) ---
                $rangeText = 'Semua Data';
                if ($this->tglMulai && $this->tglSampai) {
                    $rangeText = "Dari {$this->tglMulai} Sampai {$this->tglSampai}";
                } elseif ($this->tglMulai) {
                    $rangeText = "Mulai {$this->tglMulai}";
                } elseif ($this->tglSampai) {
                    $rangeText = "Sampai {$this->tglSampai}";
                }
                $sheet->mergeCells('B3:D3');
                $sheet->setCellValue('B3', $rangeText);
                $sheet->getStyle('B3:D3')->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // --- Header tabel (baris 5) ---
                $sheet->setCellValue('A5', 'No');
                $sheet->setCellValue('B5', 'Kode');
                $sheet->setCellValue('C5', 'Nama');
                $sheet->setCellValue('D5', 'Ketua');

                // Styling header: bold, warna background, border, rata tengah
                $sheet->getStyle('A5:D5')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFC000'], // warna header kuning
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);

                // --- Ambil data users sesuai tanggal ---
                $query = Kelompok::query();
                if ($this->tglMulai) {
                    $mulai = Carbon::createFromFormat('d-m-Y', $this->tglMulai)->startOfDay();
                    $query->where('created_at', '>=', $mulai);
                }
                if ($this->tglSampai) {
                    $sampai = Carbon::createFromFormat('d-m-Y', $this->tglSampai)->endOfDay();
                    $query->where('created_at', '<=', $sampai);
                }
                $kelompok = $query->with('ketua')->get(['id', 'kode', 'nama', 'ketua_id']);

                // Mulai tulis data dari baris 6
                $row = 6;
                foreach ($kelompok as $item) {
                    $sheet->setCellValue("A{$row}", $row - 5);
                    $sheet->setCellValue("B{$row}", $item->kode);
                    $sheet->setCellValue("C{$row}", $item->nama);

                    if ($item->ketua) {
                        $sheet->setCellValue("D{$row}", "1 - " . $item->ketua->nama);
                    } else {
                        $sheet->setCellValue("D{$row}", "-");
                    }

                    $row++;
                }
                // --- Border untuk semua data ---
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A6:D{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);

                // --- Auto width kolom ---
                foreach (range('A', 'D') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
