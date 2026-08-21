<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;

class Kantor extends Model
{
    protected $table = 'kantor';

    protected $fillable = [
        'kode',
        'nama_kantor',
        'alamat_kantor',
        'provinsi_id',
        'kota_id',
        'kecamatan_id',
        'kelurahan_id',
        'pejabat',
        'jabatan',
        'bendahara',
        'user_id',
    ];


    public function provinsi()
    {
        return $this->belongsTo(Province::class, 'provinsi_id', 'code');
    }

    public function kota()
    {
        return $this->belongsTo(City::class, 'kota_id', 'code');
    }

    public function kecamatan()
    {
        return $this->belongsTo(District::class, 'kecamatan_id', 'code');
    }

    public function kelurahan()
    {
        return $this->belongsTo(Village::class, 'kelurahan_id', 'code');
    }
}
