<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalUlangBiaya extends Model
{
    protected $table = 'jadwal_ulang_biaya';

    protected $fillable = [
        'jadwal_ulang_id',
        'nama',
        'nominal',
        'persen',
        'account_id',
        'user_id',
    ];

    public function jadwalUlang()
    {
        return $this->belongsTo(JadwalUlang::class, 'jadwal_ulang_id');
    }
}
