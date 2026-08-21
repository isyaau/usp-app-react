<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanKolektabilitas extends Model
{
    protected $table = 'pinj_jenis_kolektabilitas';

    protected $fillable = [
        'pinj_jenis_id',
        'kualitas_id',
        'keterangan',
        'user_id',
    ];
}
