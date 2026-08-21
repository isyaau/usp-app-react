<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SimpananJenisKode extends Model
{
    protected $table = 'simpanan_jenis_kode';

    protected $fillable = [
        'kode_id',
        'jenis_id',
        'user_id',
    ];


    public function tingkat()
    {
        return $this->belongsTo(SimpananJenis::class, 'jenis_id');
    }
}
