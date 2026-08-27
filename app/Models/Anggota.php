<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Village;
use Illuminate\Database\Eloquent\Model;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    protected $fillable = [
        'no_anggota',
        'nama',
        'pin',
        'kelompok_id',
        'kantor_id',
        'alamat',
        'provinsi_id',
        'kota_id',
        'kecamatan_id',
        'kelurahan_id',
        'email',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'agama',
        'pekerjaan',
        'pendidikan',
        'status_perkawinan',
        'pasangan',
        'telepon',
        'no_hp',
        'jenis_identitas',
        'no_identitas',
        'npwp',
        'ibu',
        'hutang',
        'harga_id',
        'foto',
        'pengurus',
        'pengurus_jabatan',
        'pengurus_diangkat',
        'tgl_pengurus_diangkat',
        'pengurus_berhenti',
        'user_id',
        'pengawas',
        'pengawas_jabatan',
        'tgl_pengawas_diangkat',
        'tgl_pengawas_berhenti',
        'pengawas_berhenti',
        'waris1',
        'hubungan_waris1',
        'waris2',
        'hubungan_waris2',
        'status',
        'tgl_anggota_berhenti',
        'anggota_berhenti',
    ];





    public function simpanan()
    {
        return $this->hasMany(Simpanan::class, 'anggota_id');
    }

    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class, 'anggota_id');
    }

    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id');
    }

    public function kantor()
    {
        return $this->belongsTo(Kantor::class, 'kantor_id');
    }

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
