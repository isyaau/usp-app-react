<?php

namespace App\Livewire\Superadmin\Kantor;

use App\Models\Kantor;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Village;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;

class Edit extends Component
{
    // Semua field form
    #[Validate('required|string|unique:kantor,kode')]
    public $kode;

    #[Validate('required|string|max:255')]
    public $nama_kantor;

    #[Validate('required|string|max:500')]
    public $alamat_kantor;

    #[Validate('required')]
    public $provinsi_id;

    #[Validate('required')]
    public $kota_id;

    #[Validate('required')]
    public $kecamatan_id;

    #[Validate('required')]
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
    public $provinces = [];
    public $districts = [];
    public $villages = [];
    public $kantorId;
    public $query = '';
    public $users = [];
    public $showDropdown = false;
    public $selectedUser = null;

    public $activeTab = 'keanggotaan';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function mount($id)
    {
        $kantor = Kantor::findOrFail($id);

        $this->kantorId = $id;

        $this->kode = $kantor->kode;
        $this->nama_kantor = $kantor->nama_kantor;
        $this->provinsi_id = $kantor->provinsi_id;
        $this->kota_id = $kantor->kota_id;
        $this->kecamatan_id = $kantor->kecamatan_id;
        $this->kelurahan_id = $kantor->kelurahan_id;

        $this->provinces  = Province::all();
        $this->cities     = City::where('province_code', $this->provinsi_id)->get();
        $this->districts  = District::where('city_code', $this->kota_id)->get();
        $this->villages   = Village::where('district_code', $this->kecamatan_id)->get();
        $this->alamat_kantor = $kantor->alamat_kantor;
        $this->pejabat = $kantor->pejabat;
        $this->jabatan = $kantor->jabatan;
        $this->bendahara = $kantor->bendahara;
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

    public function messages()
    {
        return [
            'kode.required' => 'Kode kantor wajib diisi.',
            'kode.unique' => 'Kode kantor sudah terdaftar.',
            'nama_kantor.required' => 'Nama kantor wajib diisi.',
            'alamat_kantor.required' => 'Alamat kantor wajib diisi.',
            'provinsi_id.required' => 'Provinsi wajib dipilih.',
            'kota_id.required' => 'Kota/Kabupaten wajib dipilih.',
            'kecamatan_id.required' => 'Kecamatan wajib dipilih.',
            'kelurahan_id.required' => 'Kelurahan wajib dipilih.',
            'pejabat.required' => 'Nama pejabat wajib diisi.',
            'jabatan.required' => 'Jabatan pejabat wajib diisi.',
            'bendahara.required' => 'Nama bendahara wajib diisi.',
        ];
    }

    public function update()
    {


        // Validasi dinamis untuk email unique: except ID saat ini
        $this->validate([
            'kode' => 'required|string|unique:kantor,kode,' . $this->kantorId,
            'nama_kantor' => 'required|string|max:255',
            'alamat_kantor' => 'required|string|max:500',
            'provinsi_id' => 'required',
            'kota_id' => 'required',
            'kecamatan_id' => 'required',
            'kelurahan_id' => 'required',
            'pejabat' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'bendahara' => 'required|string|max:255',
        ]);

        $kantor = Kantor::findOrFail($this->kantorId);

        $kantor->update([
            'kode'          => $this->kode,
            'nama_kantor'   => $this->nama_kantor,
            'alamat_kantor' => $this->alamat_kantor,
            'provinsi_id'   => $this->provinsi_id,
            'kota_id'       => $this->kota_id,
            'kecamatan_id'  => $this->kecamatan_id,
            'kelurahan_id'  => $this->kelurahan_id,
            'pejabat'       => $this->pejabat,
            'jabatan'       => $this->jabatan,
            'bendahara'     => $this->bendahara,
        ]);

        // Kirim event ke UserIndex
        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Kantor berhasil diupdate!',
            'icon' => 'success',
            'confirmButtonText' => 'Oke'
        ]);

        return $this->redirect('/superadmin/kantor', navigate: true);
    }

    public function render()
    {

        return view('livewire.superadmin.kantor.edit', [
            'title' => 'Edit Kantor',
            'provinces' => Province::orderBy('name', 'asc')->get(),
        ]);
    }
}
