<?php

namespace App\Livewire\Superadmin\Marketing;

use App\Models\Kantor;

use Livewire\Component;
use App\Models\Marketing;

class Create extends Component
{
    public $userLogin, $kode, $nama, $alamat, $no_ktp, $telepon, $no_hp, $kantor_id, $kantors;
    public int $aktif = 1;




    /* =======================
     * MOUNT
     * ======================= */
    public function mount()
    {
        $this->userLogin = auth()->user();
        $this->kantors = Kantor::orderBy('nama_kantor')->get();
    }


    /* =======================
     * VALIDATION MESSAGE
     * ======================= */
    protected function messages()
    {
        return [
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode sudah digunakan.',
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique' => 'Nama sudah digunakan.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_ktp.required' => 'No KTP wajib diisi.',
            'no_hp.required' => 'No HP wajib diisi.',
            'kantor_id.required' => 'Kantor wajib dipilih.',
            'kantor_id.exists' => 'Kantor tidak valid.',
        ];
    }

    /* =======================
     * STORE
     * ======================= */
    public function store()
    {
        $validated = $this->validate([
            'kode' => ['required', 'string', 'max:255', 'unique:marketing,kode'],
            'nama' => ['required', 'string', 'max:255', 'unique:marketing,nama'],
            'alamat' => ['required', 'string', 'max:255'],
            'no_ktp' => ['required', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:255'],
            'aktif' => ['required', 'in:0,1'],
            'kantor_id' => ['required', 'exists:kantor,id'],
        ]);


        Marketing::create([
            'kode' => $validated['kode'],
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'no_ktp' => $validated['no_ktp'],
            'telepon' => $validated['telepon'],
            'no_hp' => $validated['no_hp'],
            'kantor_id' => $validated['kantor_id'],
            // boolean → 0 / 1
            'aktif' => $validated['aktif'] ? 1 : 0,
            'user_id' => $this->userLogin->id,
        ]);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Marketing berhasil dibuat!',
            'icon' => 'success',
        ]);

        return redirect()->route('superadmin.marketing');
    }

    /* =======================
     * RENDER
     * ======================= */
    public function render()
    {
        return view('livewire.superadmin.marketing.create', [
            'title' => 'Tambah Marketing',
        ]);
    }
}
