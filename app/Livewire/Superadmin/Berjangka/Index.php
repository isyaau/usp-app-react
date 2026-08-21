<?php

namespace App\Livewire\Superadmin\Berjangka;

use Carbon\Carbon;
use App\Models\Account;
use Livewire\Component;
use App\Models\Kelompok;
use App\Models\AccHeader;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\SimpananJenis;
use Livewire\WithFileUploads;
use App\Exports\KelompokExport;
use App\Imports\KelompokImport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Imports\AccheaderImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KelompokTemplateExport;
use App\Exports\AccheaderTemplateExport;
use App\Models\Deposito;

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
        $berjangka = Deposito::with([
            'anggota',
            'produk',
            'marketing',
            'kantor'
        ])
            ->when($this->search, function ($query) {
                $query->where('no_deposito', 'LIKE', '%' . $this->search . '%');
            })
            ->orderBy('tanggal', 'asc')
            ->paginate($this->paginate);

        return view('livewire.superadmin.berjangka.index', [
            'title' => 'Data Simpanan Berjangka',
            'berjangka' => $berjangka
        ]);
    }



    #[On('deleteProduk')]
    public function deleteProduk($id)
    {
        DB::transaction(function () use ($id) {

            $produk = SimpananJenis::findOrFail($id);

            // 1. Hapus bunga (hasMany)
            $produk->tingkat()->delete();

            // 2. Lepas relasi kode transaksi (pivot)
            $produk->simpananKodes()->detach();

            // 3. Hapus produk simpanan
            $produk->delete();
        });

        // 🔔 SweetAlert
        $this->dispatch('deleteSwal', [
            'title' => 'Berhasil!',
            'text'  => 'Produk simpanan berhasil dihapus',
            'icon'  => 'success',
        ]);

        // 🔁 Redirect SPA (opsional)
        // $this->redirectRoute('superadmin.simpanan.produk-simpanan', navigate: true);
    }
}
