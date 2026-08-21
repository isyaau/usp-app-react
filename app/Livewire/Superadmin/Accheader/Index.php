<?php

namespace App\Livewire\Superadmin\Accheader;

use App\Exports\AccheaderTemplateExport;
use App\Exports\KelompokExport;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Kelompok;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KelompokTemplateExport;
use App\Imports\AccheaderImport;
use App\Imports\KelompokImport;
use App\Models\AccHeader;
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
            Excel::import(new AccheaderImport($this->userLogin->id), $this->file->getRealPath());

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
        return Excel::download(new AccheaderTemplateExport, 'template_acc_header.xlsx');
    }


    public function render()
    {
        return view('livewire.superadmin.accheader.index', [
            'title' => 'Data Header',
            'accheader' => AccHeader::where('nama', 'LIKE', '%' . $this->search . '%')
                ->orderBy('group_id', 'ASC')
                ->paginate($this->paginate),
        ]);
    }

    #[On('deleteAccheader')]
    public function deleteAccheader($id)
    {
        $acc_header = AccHeader::findOrFail($id);

        $acc_header->delete();

        // Dispatch event ke browser
        $this->dispatch('deleteSwal');

        // Opsional: redirect SPA setelah SweetAlert ditutup
        // $this->redirect('/superadmin/kelompok', navigate: true);
    }
}
