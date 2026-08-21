<?php

namespace App\Livewire\Superadmin\Anggota;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\Anggota;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;

class Show extends Component
{
    #[Title('Detail Anggota')]
    public Anggota $anggota;

    // Data lokasi
    public $province;
    public $city;
    public $district;
    public $village;

    public function mount($id)
    {
        // Ambil data anggota
        $this->anggota = Anggota::findOrFail($id);

        // Ambil data lokasi berdasarkan kode
        $this->province = Province::where('code', $this->anggota->provinsi_id)->first();
        $this->city = City::where('code', $this->anggota->kota_id)->first();
        $this->district = District::where('code', $this->anggota->kecamatan_id)->first();
        $this->village = Village::where('code', $this->anggota->kelurahan_id)->first();
    }

    public function render()
    {
        return view('livewire.superadmin.anggota.show', [
            'title' => 'Detail Anggota',
            'anggota' => $this->anggota,
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'village' => $this->village,
        ]);
    }
}
