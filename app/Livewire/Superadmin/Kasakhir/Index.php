<?php

namespace App\Livewire\Superadmin\Kasakhir;

use App\Exports\AccheaderTemplateExport;
use App\Imports\AccheaderImport;
use App\Models\Jaminan;
use App\Models\JaminanDetail;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

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
        return view('livewire.superadmin.kasakhir.index', [
            'title' => 'Data Kas Akhir',
            'jaminan' => Jaminan::with('details')->where('nama', 'LIKE', '%' . $this->search . '%')
                ->orderBy('nama', 'ASC')
                ->paginate($this->paginate),
        ]);
    }

    #[On('deleteJaminan')]
    public function deleteJaminan($id)
    {
        // 1. Cari data parent (Jaminan)
        $jaminan = Jaminan::findOrFail($id);

        // 2. Hapus semua data child (JaminanDetail) yang terkait dengan jaminan_id ini
        // (Sangat penting dilakukan lebih dulu jika database Anda tidak menggunakan onDelete('cascade'))
        JaminanDetail::where('jaminan_id', $id)->delete();

        // 3. Hapus data parent (Jaminan)
        $jaminan->delete();

        // 4. Dispatch event ke browser untuk memicu SweetAlert
        $this->dispatch('deleteSwal');
    }
}
