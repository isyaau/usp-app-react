<?php

namespace App\Livewire\Superadmin\Kantor;

use Livewire\Component;
use App\Models\Kantor;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;
use Livewire\Attributes\Validate;

class Create extends Component
{
    // Semua field form
    #[Validate('required|string|unique:kantor,kode')]
    public $kode;

    #[Validate('required|string|max:255')]
    public $nama_kantor;

    #[Validate('required|string|max:500')]
    public $alamat_kantor;

    #[Validate('required|string')]
    public $provinsi_id;

    #[Validate('required|string')]
    public $kota_id;

    #[Validate('required|string')]
    public $kecamatan_id;

    #[Validate('required|string')]
    public $kelurahan_id;

    #[Validate('required|string|max:255')]
    public $pejabat;

    #[Validate('required|string|max:255')]
    public $jabatan;

    #[Validate('required|string|max:255')]
    public $bendahara;

    public $userLogin;

    // Data dropdown
    public $cities = [];
    public $districts = [];
    public $villages = [];





    public $nama;

    #[Validate('nullable|integer|exists:users,id')]
    public $ketua_id;

    public function mount()
    {
        $this->userLogin = auth()->guard('web')->user();
    }

    // ====================== Dropdown Berantai ===========================
    public function updatedProvinsiId()
    {
        $this->cities = City::where('province_code', $this->provinsi_id)->orderBy('name', 'asc')->get(); // Urutkan kota berdasarkan nama
        $this->kota_id = null;
        $this->districts = [];
        $this->villages = [];
        $this->kecamatan_id = null;
        $this->kelurahan_id = null;
    }

    public function updatedKotaId()
    {
        $this->districts = District::where('city_code', $this->kota_id)->orderBy('name', 'asc')->get(); // Urutkan kecamatan berdasarkan nama
        $this->kecamatan_id = null;
        $this->villages = [];
        $this->kelurahan_id = null;
    }

    public function updatedKecamatanId()
    {
        $this->villages = Village::where('district_code', $this->kecamatan_id)->orderBy('name', 'asc')->get(); // Urutkan kelurahan berdasarkan nama
        $this->kelurahan_id = null;
    }


    // ====================== Simpan Data Kantor ==========================

    public function messages()
    {
        return [
            'kode.required' => 'Kode Kantor wajib diisi.',
            'kode.unique' => 'Kode Kantor sudah digunakan.',
            'nama_kantor.required' => 'Nama Kantor wajib diisi.',
            'alamat_kantor.required' => 'Alamat Kantor wajib diisi.',
            'provinsi_id.required' => 'Provinsi wajib dipilih.',
            'kota_id.required' => 'Kota/Kabupaten wajib dipilih.',
            'kecamatan_id.required' => 'Kecamatan wajib dipilih.',
            'kelurahan_id.required' => 'Kelurahan wajib dipilih.',
            'pejabat.required' => 'Nama Pejabat wajib diisi.',
            'jabatan.required' => 'Jabatan Pejabat wajib diisi.',
            'bendahara.required' => 'Nama Bendahara wajib diisi.',
        ];
    }
    public function store()
    {
        $validated = $this->validate();



        $user_id = $this->userLogin->id;

        Kantor::create([
            'kode'          => $validated['kode'],
            'nama_kantor'   => $validated['nama_kantor'],
            'alamat_kantor' => $validated['alamat_kantor'],
            'provinsi_id'   => $validated['provinsi_id'],
            'kota_id'       => $validated['kota_id'],
            'kecamatan_id'  => $validated['kecamatan_id'],
            'kelurahan_id'  => $validated['kelurahan_id'],
            'pejabat'       => $validated['pejabat'],
            'jabatan'       => $validated['jabatan'],
            'bendahara'     => $validated['bendahara'],
            'user_id'       => $user_id

        ]);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Kantor berhasil dibuat!',
            'icon' => 'success',
            'confirmButtonText' => 'Oke'
        ]);

        return $this->redirect('/superadmin/kantor', navigate: true);
    }

    // ====================== Render ==========================
    public function render()
    {
        return view('livewire.superadmin.kantor.create', [
            'title'     => 'Tambah Kantor',
            'provinces' => Province::orderBy('name', 'asc')->get(),
        ]);
    }
}
