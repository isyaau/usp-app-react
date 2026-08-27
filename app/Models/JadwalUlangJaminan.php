<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalUlangJaminan extends Model
{
    protected $table = 'jadwal_ulang_jaminan';

    protected $fillable = [
        'jadwal_ulang_id',
        'nama',
        'keterangan',
        'nominal',
        'user_id',
    ];

    public function jadwalUlang()
    {
        return $this->belongsTo(JadwalUlang::class, 'jadwal_ulang_id');
    }
}
