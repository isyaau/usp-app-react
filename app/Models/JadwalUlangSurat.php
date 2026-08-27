<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalUlangSurat extends Model
{
    protected $table = 'jadwal_ulang_surat';

    protected $fillable = [
        'jadwal_ulang_id',
        'surat_id',
        'keterangan',
        'surat',
        'user_id',
    ];

    public function jadwalUlang()
    {
        return $this->belongsTo(JadwalUlang::class, 'jadwal_ulang_id');
    }
}
