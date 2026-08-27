<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalUlangPenjamin extends Model
{
    protected $table = 'jadwal_ulang_penjamin';

    protected $fillable = [
        'jadwal_ulang_id',
        'nama',
        'alamat',
        'no_ktp',
        'hubungan',
        'ibu',
        'telepon',
        'tampil',
        'user_id',
    ];

    public function jadwalUlang()
    {
        return $this->belongsTo(JadwalUlang::class, 'jadwal_ulang_id');
    }
}
