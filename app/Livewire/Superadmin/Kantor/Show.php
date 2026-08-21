<?php

namespace App\Livewire\Superadmin\Kantor;

use App\Models\Kantor;
use Livewire\Component;
use Livewire\Attributes\Title;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Village;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;

class Show extends Component
{
    #[Title('Detail Kantor')]


    public $kode;


    public $nama_kantor;


    public $alamat_kantor;


    public $provinsi_id;


    public $kota_id;


    public $kecamatan_id;

    public $kelurahan_id;

    public $pejabat;


    public $jabatan;


    public $bendahara;

    public $userLogin;
    public $kantor;
    public $kantorId;

    public $cities = [];
    public $provinces = [];
    public $districts = [];
    public $villages = [];
    public $query = '';
    public $users = [];
    public $showDropdown = false;
    public $selectedUser = null;

    public function mount($id)
    {
        $this->kantor = Kantor::findOrFail($id);   // 🔥 WAJIB

        $this->kantorId = $id;

        $this->kode = $this->kantor->kode;
        $this->nama_kantor = $this->kantor->nama_kantor;
        $this->provinsi_id = $this->kantor->provinsi_id;
        $this->kota_id = $this->kantor->kota_id;
        $this->kecamatan_id = $this->kantor->kecamatan_id;
        $this->kelurahan_id = $this->kantor->kelurahan_id;

        $this->provinces  = Province::all();
        $this->cities     = City::where('province_code', $this->provinsi_id)->get();
        $this->districts  = District::where('city_code', $this->kota_id)->get();
        $this->villages   = Village::where('district_code', $this->kecamatan_id)->get();

        $this->alamat_kantor = $this->kantor->alamat_kantor;
        $this->pejabat = $this->kantor->pejabat;
        $this->jabatan = $this->kantor->jabatan;
        $this->bendahara = $this->kantor->bendahara;
    }



    public function render()
    {
        return view('livewire.superadmin.kantor.show', [
            'title' => 'Detail Kantor',
            'kantor' => $this->kantor, // kirim data kantor ke view
        ]);
    }
}
