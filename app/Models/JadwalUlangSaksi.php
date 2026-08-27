<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalUlangSaksi extends Model
{
    protected $table = 'jadwal_ulang_saksi';

    protected $fillable = [
        'jadwal_ulang_id',
        'nama',
        'tempat_lahir',
        'tgl_lahir',
        'no_ktp',
        'alamat',
        'pekerjaan_id',
        'user_id',
    ];

    public function jadwalUlang()
    {
        return $this->belongsTo(JadwalUlang::class, 'jadwal_ulang_id');
    }
}
