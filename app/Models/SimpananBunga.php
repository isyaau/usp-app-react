<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SimpananBunga extends Model
{
    protected $table = 'simpanan_bunga';

    protected $fillable = [
        'jenis_id',
        'minimal',
        'maksimal',
        'bunga',
        'user_id',
    ];


    public function jenis_simpanan()
    {
        return $this->belongsTo(SimpananJenis::class, 'jenis_id');
    }
}
