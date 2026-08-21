<?php

namespace App\Livewire\Superadmin\Kelompok;

use App\Models\Kelompok;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use SweetAlert2\Laravel\Traits\WithSweetAlert;

class Edit extends Component
{
    use WithSweetAlert;
    use WithFileUploads;

    #[Title('Edit User')]

    public $kelompokId;

    #[Validate('required|string|max:255')]
    public $kode;

    #[Validate('required|string|max:255')]
    public $nama;

    #[Validate('required|string')]
    public $ketua_id;


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




    public function mount($id)
    {
        $kelompok = Kelompok::findOrFail($id);
        $this->kelompokId = $id;

        $this->kode = $kelompok->kode;
        $this->nama = $kelompok->nama;
        $this->ketua_id = $kelompok->ketua_id;

        if ($kelompok->ketua) {
            $this->selectedUser = $kelompok->ketua;
            $this->query = $kelompok->ketua->nama;
        }
    }


    public function messages()
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode sudah digunakan.',
            'ketua_id.required' => 'Ketua wajib dipilih.',
        ];
    }

    public function update()
    {
        // Validasi dinamis untuk email unique: except ID saat ini
        $this->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|unique:kelompok,kode,' . $this->kelompokId,
            'ketua_id' => 'required|integer|exists:users,id',
        ]);

        $kelompok = Kelompok::findOrFail($this->kelompokId);



        $kelompok->update([
            'nama' => $this->nama,
            'kode' => $this->kode,
            'ketua_id' => $this->ketua_id,
        ]);

        // Kirim event ke UserIndex
        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Kelompok berhasil diupdate!',
            'icon' => 'success',
            'confirmButtonText' => 'Oke'
        ]);

        return $this->redirect('/superadmin/kelompok', navigate: true);
    }

    public function render()
    {
        return view('livewire.superadmin.kelompok.edit', [
            'title' => 'Edit Kelompok',
        ]);
    }
}
