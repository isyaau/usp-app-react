<?php

namespace App\Livewire\Superadmin\Kelompok;

use App\Exports\KelompokExport;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Kelompok;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KelompokTemplateExport;
use App\Imports\KelompokImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\WithFileUploads;

class Index extends Component
{

    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';
    public $paginate = '10';
    public $search = '';
    public $userLogin;
    public $kelompoks;
    public $file;
    public $tglMulai; // <-- tambahkan ini
    public $tglSampai; // <-- tambahkan ini


    public function mount()
    {
        $this->userLogin = auth()->guard('web')->user();
    }

    public function import()
    {
        // Mulai spinner
        $this->dispatch('processing-start', type: 'import');

        try {
            // Validasi file
            $this->validate([
                'file' => 'required|mimes:xlsx,csv',
            ]);

            // Proses import
            Excel::import(new KelompokImport($this->userLogin->id), $this->file->getRealPath());

            // Reset input file
            $this->reset(['file']);

            // Stop spinner
            $this->dispatch('processing-finish', type: 'import');

            // Swal sukses
            $this->dispatch('importSwal');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $this->dispatch('processing-finish', type: 'import');

            $failures = $e->failures();
            $msg = $failures[0]->errors()[0] ?? 'Data tidak valid atau duplikat.';
            $this->dispatch('importErrorSwal', message: $msg);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('processing-finish', type: 'import');
            $this->dispatch('importErrorSwal', message: 'File tidak valid. Pastikan format xlsx atau csv.');
        } catch (\Throwable $e) {
            $this->dispatch('processing-finish', type: 'import');
            $this->dispatch('importErrorSwal', message: 'Import gagal. Periksa format dan isi file Anda.');
        }
    }




    public function downloadTemplate()
    {
        return Excel::download(new KelompokTemplateExport, 'template_kelompok.xlsx');
    }



    public function domPdf($mulai, $sampai)
    {
        $query = Kelompok::query();

        if ($mulai) {
            $mulaiCarbon = Carbon::createFromFormat('d-m-Y', $mulai)->startOfDay();
            $query->where('created_at', '>=', $mulaiCarbon);
        }

        if ($sampai) {
            $sampaiCarbon = Carbon::createFromFormat('d-m-Y', $sampai)->endOfDay();
            $query->where('created_at', '<=', $sampaiCarbon);
        }

        $kelompok = $query->get();

        $pdf = Pdf::loadView('pdf.kelompok', ['kelompok' => $kelompok]);

        // Tambahkan page number
        $dompdf = $pdf->getDomPDF();
        $dompdf->render();
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(250, 820, "Halaman {PAGE_NUM} / {PAGE_COUNT}", null, 10, [0, 0, 0]);

        return $pdf;
    }

    public function exportPdf()
    {
        // Start spinner
        $this->dispatch('processing-start', type: 'export');

        // Simpan tanggal sebelum reset
        $mulai = $this->tglMulai;
        $sampai = $this->tglSampai;

        // Generate PDF
        $pdf = $this->domPdf($mulai, $sampai);

        // Reset tanggal setelah PDF dibuat
        $this->reset(['tglMulai', 'tglSampai']);

        // Stop spinner
        $this->dispatch('processing-finish', type: 'export');


        // Return PDF stream untuk download
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'data_kelompok_' . ($mulai ?? 'all') . '-' . ($sampai ?? 'all') . '.pdf');


        $this->dispatch('exportSwal');
    }

    public function exportExcel()
    {
        $mulai = Carbon::createFromFormat('d-m-Y', $this->tglMulai)->startOfDay();
        $sampai = Carbon::createFromFormat('d-m-Y', $this->tglSampai)->endOfDay();
        return Excel::download(new KelompokExport($this->tglMulai, $this->tglSampai), 'data_kelompok' . $mulai . '-' . $sampai . '.xlsx');
    }

    public function exportXls()
    {
        // Panggil method yang generate PDF
        $response = $this->exportExcel();

        // Reset tanggal setelah PDF berhasil dibuat
        $this->reset(['tglMulai', 'tglSampai']);
        $this->dispatch('export-complete');

        return $response;
    }


    public function render()
    {
        return view('livewire.superadmin.kelompok.index', [
            'title' => 'Data Kelompok',
            'kelompok' => Kelompok::where('nama', 'LIKE', '%' . $this->search . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate($this->paginate),
        ]);
    }

    #[On('deleteKelompok')]
    public function deleteKelompok($id)
    {
        $kelompok = Kelompok::findOrFail($id);

        $kelompok->delete();

        // Dispatch event ke browser
        $this->dispatch('deleteSwal');

        // Opsional: redirect SPA setelah SweetAlert ditutup
        // $this->redirect('/superadmin/kelompok', navigate: true);
    }
}
