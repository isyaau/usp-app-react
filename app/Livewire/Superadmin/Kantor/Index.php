<?php

namespace App\Livewire\Superadmin\Kantor;

use Carbon\Carbon;
use App\Models\Kantor;
use Livewire\Component;
use App\Models\Kelompok;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Exports\KantorExport;
use App\Imports\KantorImport;

use Livewire\WithFileUploads;

use App\Imports\KelompokImport;
use Barryvdh\DomPDF\Facade\Pdf;
use Laravolt\Indonesia\Models\City;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KantorTemplateExport;
use Laravolt\Indonesia\Models\Village;
use App\Exports\KelompokTemplateExport;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;

class Index extends Component
{

    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';
    public $paginate = '10';
    public $search = '';
    public $userLogin;
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
            Excel::import(new KantorImport($this->userLogin->id), $this->file->getRealPath());

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
        return Excel::download(new KantorTemplateExport, 'template_kantor.xlsx');
    }



    public function domPdf($mulai, $sampai)
    {
        $query = Kantor::query();

        if ($mulai) {
            $mulaiCarbon = Carbon::createFromFormat('d-m-Y', $mulai)->startOfDay();
            $query->where('created_at', '>=', $mulaiCarbon);
        }

        if ($sampai) {
            $sampaiCarbon = Carbon::createFromFormat('d-m-Y', $sampai)->endOfDay();
            $query->where('created_at', '<=', $sampaiCarbon);
        }

        $kantor = $query->get();

        $pdf = Pdf::loadView('pdf.kantor', ['kantor' => $kantor]);

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
        }, 'data_kantor_' . ($mulai ?? 'all') . '-' . ($sampai ?? 'all') . '.pdf');


        $this->dispatch('exportSwal');
    }

    public function exportExcel()
    {
        $mulai = Carbon::createFromFormat('d-m-Y', $this->tglMulai)->startOfDay();
        $sampai = Carbon::createFromFormat('d-m-Y', $this->tglSampai)->endOfDay();
        return Excel::download(new KantorExport($this->tglMulai, $this->tglSampai), 'data_kelompok' . $mulai . '-' . $sampai . '.xlsx');
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
        return view('livewire.superadmin.kantor.index', [
            'title' => 'Data Kantor',
            'kantor' => Kantor::where('nama_kantor', 'LIKE', '%' . $this->search . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate($this->paginate),
        ]);
    }

    #[On('deleteKantor')]
    public function deleteKantor($id)
    {
        $kantor = Kantor::findOrFail($id);

        $kantor->delete();

        // Dispatch event ke browser
        $this->dispatch('deleteSwal');
    }
}
