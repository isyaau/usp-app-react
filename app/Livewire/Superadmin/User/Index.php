<?php

namespace App\Livewire\Superadmin\User;

use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Exports\UserTemplateExport;
use App\Imports\UsersImport;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF as DomPDF;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';
    public $paginate = '10';
    public $search = '';

    public $userLogin;

    public $file;

    public $users;

    public $tglMulai;
    public $tglSampai;

    public function mount()
    {
        $this->userLogin = auth()->guard('web')->user();
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        Excel::import(new UsersImport, $this->file->getRealPath());

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text'  => 'User berhasil diimport!',
            'icon'  => 'success',
            'confirmButtonText' => 'Oke'
        ]);

        return $this->redirect('/superadmin/user', navigate: true);
    }

    public function downloadTemplate()
    {
        return Excel::download(new UserTemplateExport, 'template_user.xlsx');
    }

    public function exportExcel()
    {
        $mulai = Carbon::createFromFormat('d-m-Y', $this->tglMulai)->startOfDay();
        $sampai = Carbon::createFromFormat('d-m-Y', $this->tglSampai)->endOfDay();
        return Excel::download(new UsersExport($this->tglMulai, $this->tglSampai), 'data_pengguna' . $mulai . '-' . $sampai . '.xlsx');
    }

    public function domPdf()
    {
        $query = User::query();

        if ($this->tglMulai) {
            $mulai = Carbon::createFromFormat('d-m-Y', $this->tglMulai)->startOfDay();
            $query->where('created_at', '>=', $mulai);
        }

        if ($this->tglSampai) {
            $sampai = Carbon::createFromFormat('d-m-Y', $this->tglSampai)->endOfDay();
            $query->where('created_at', '<=', $sampai);
        }

        $users = $query->get();

        $data = ['users' => $users];
        $pdf = FacadePdf::loadView('pdf.users', $data);

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(250, 820, "Halaman {PAGE_NUM} / {PAGE_COUNT}", null, 10, [0, 0, 0]);

        try {
            // Generate PDF dan tampilkan atau download
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'data_pengguna_' . $mulai . '-' . $sampai . '.pdf');
        } catch (\Exception $e) {
            // Tangani error jika terjadi masalah
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function exportPdf()
    {
        // Panggil method yang generate PDF
        $response = $this->domPdf();

        // Reset tanggal setelah PDF berhasil dibuat
        $this->reset(['tglMulai', 'tglSampai']);
        $this->dispatch('export-complete');

        return $response;
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



    public function snappyPdf()
    {
        // Ambil data sesuai range tanggal jika ada
        $mulai = $this->tglMulai ? Carbon::createFromFormat('d-m-Y', $this->tglMulai)->startOfDay() : null;
        $sampai = $this->tglSampai ? Carbon::createFromFormat('d-m-Y', $this->tglSampai)->endOfDay() : null;

        $query = User::query();
        if ($mulai && $sampai) {
            $query->whereBetween('created_at', [$mulai, $sampai]);
        }
        $users = $query->get();

        // Format tanggal di setiap user
        $users->transform(function ($user) {
            $user->created_at_formatted = Carbon::parse($user->created_at)->format('d-m-Y');
            return $user;
        });

        // Simpan header/footer dari Blade ke file HTML
        file_put_contents(public_path('pdf/header.html'), view('pdf.partials.header')->render());
        file_put_contents(public_path('pdf/footer.html'), view('pdf.partials.footer')->render());

        $pdf = PDF::loadView('pdf.users', compact('users'))
            ->setOption('enable-local-file-access', true)
            ->setOption('footer-center', 'Halaman: {PAGE_NUM} / {PAGE_COUNT}')
            ->setOption('header-html', public_path('pdf/header.html'))
            ->setOption('footer-html', public_path('pdf/footer.html'))
            ->setOption('margin-top', 20)
            ->setOption('margin-bottom', 30)
            ->setOption('enable-smart-shrinking', true);

        $pdfContent = $pdf->output();
        Log::info('PDF Output: ' . $pdfContent);
        // Stream download tetap bisa dipakai
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'data_pengguna.pdf');
    }


    #[On('deleteUser')]
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Hapus avatar lama jika bukan default
        if ($user->avatar && $user->avatar !== 'avatar/avatar-default.jpg') {
            Storage::disk('public')->delete($user->avatar);
        }

        // Hapus user
        $user->delete();

        // Flash SweetAlert
        // Dispatch event ke browser
        $this->dispatch('deleteSwal');
    }





    public function render()
    {
        return view('livewire.superadmin.user.index', [
            'title' => 'Data User',
            'user' => User::where('nama', 'LIKE', '%' . $this->search . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate($this->paginate),
        ]);
    }
}
