<?php

namespace App\Exports;


use App\Models\Kantor;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class KantorExport implements FromArray, WithEvents
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
                $sheet->mergeCells('B2:G2');
                $sheet->setCellValue('B2', 'Laporan Data Kantor');
                $sheet->getStyle('B2:G2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('B2:G2')->getAlignment()
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
                $sheet->mergeCells('B3:G3');
                $sheet->setCellValue('B3', $rangeText);
                $sheet->getStyle('B3:G3')->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // --- Header tabel (baris 5) ---
                $sheet->setCellValue('A5', 'No');
                $sheet->setCellValue('B5', 'Kode');
                $sheet->setCellValue('C5', 'Nama Kantor');
                $sheet->setCellValue('D5', 'Alamat Kantor');
                $sheet->setCellValue('E5', 'Pejabat');
                $sheet->setCellValue('F5', 'Jabatan');
                $sheet->setCellValue('G5', 'Bendahara');

                // Styling header: bold, warna background, border, rata tengah
                $sheet->getStyle('A5:G5')->applyFromArray([
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
                $query = Kantor::query();
                if ($this->tglMulai) {
                    $mulai = Carbon::createFromFormat('d-m-Y', $this->tglMulai)->startOfDay();
                    $query->where('created_at', '>=', $mulai);
                }
                if ($this->tglSampai) {
                    $sampai = Carbon::createFromFormat('d-m-Y', $this->tglSampai)->endOfDay();
                    $query->where('created_at', '<=', $sampai);
                }
                $kantor = $query->get(['id', 'kode', 'nama_kantor', 'alamat_kantor', 'pejabat', 'jabatan', 'bendahara']);

                // Mulai tulis data dari baris 6
                $row = 6;
                foreach ($kantor as $item) {
                    $sheet->setCellValue("A{$row}", $row - 5);
                    $sheet->setCellValue("B{$row}", $item->kode);
                    $sheet->setCellValue("C{$row}", $item->nama_kantor);
                    $sheet->setCellValue("D{$row}", $item->alamat_kantor);
                    $sheet->setCellValue("E{$row}", $item->pejabat);
                    $sheet->setCellValue("F{$row}", $item->jabatan);
                    $sheet->setCellValue("G{$row}", $item->bendahara);
                    $row++;
                }
                // --- Border untuk semua data ---
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A6:G{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        ]
                    ]
                ]);

                // --- Auto width kolom ---
                foreach (range('A', 'G') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
