<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalUlangDetail extends Model
{
    protected $table = 'jadwal_ulang_detail';

    protected $fillable = [
        'jadwal_ulang_id',
        'angsuran_ke',
        'nominal_pokok',
        'nominal_bunga',
        'total_angsuran',
        'sisa_pokok',
    ];

    public function jadwalUlang()
    {
        return $this->belongsTo(JadwalUlang::class, 'jadwal_ulang_id');
    }
}
