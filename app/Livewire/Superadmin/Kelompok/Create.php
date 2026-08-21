<?php

namespace App\Livewire\Superadmin\Kelompok;

use App\Models\Anggota;
use App\Models\Kelompok;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use SweetAlert2\Laravel\Traits\WithSweetAlert;

#[Title('Tambah Kelompok')]
class Create extends Component
{
    use WithSweetAlert;
    use WithFileUploads;

    #[Validate('required|string|unique:kelompok,kode')]
    public $kode;

    #[Validate('required|string||unique:kelompok,nama|max:255')]
    public $nama;

    #[Validate('nullable|integer|exists:users,id')]
    public $ketua_id;


    public $userLogin;


    public $query = '';
    public $users = [];
    public $showDropdown = false;
    public $selectedUser = null;

    // Ketika mengetik
    public function updatedQuery()
    {
        $this->showDropdown = true;

        $this->users = User::where('nama', 'like', '%' . $this->query . '%')
            ->limit(8)
            ->get();
    }

    // Ketika memilih user
    public function selectUser($id)
    {
        $user = User::find($id);

        $this->selectedUser = $user;
        $this->query = $user->nama;

        $this->ketua_id = $user->id;


        $this->showDropdown = false;   // Tutup dropdown
    }

    // Saat input kehilangan fokus → tutup dropdown
    public function hideDropdown()
    {
        $this->showDropdown = false;
    }

    public function updatedNama()
    {
        $this->validateOnly('nama');
    }



    public function mount()
    {
        $this->userLogin = auth()->guard('web')->user();
    }


    public function render()
    {
        return view('livewire.superadmin.kelompok.create', [
            'title' => 'Tambah Kelompok',
        ]);
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique' => 'Nama sudah digunakan.',
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode sudah digunakan.',
            'ketua_id.exists' => 'User tidak ditemukan.',
        ];
    }

    public function store()
    {

        $validated = $this->validate();
        // Pastikan ketua_id null jika kosong
        $validated['ketua_id'] = $validated['ketua_id'] ?: null;


        $user_id = $this->userLogin->id;

        Kelompok::create([
            'kode'     => $validated['kode'],
            'nama'    => $validated['nama'],
            'ketua_id'     => $validated['ketua_id'],
            'user_id'    => $user_id

        ]);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Kelompok berhasil dibuat!',
            'icon' => 'success',
            'confirmButtonText' => 'Oke'
        ]);

        return $this->redirect('/superadmin/kelompok', navigate: true);
    }
}
