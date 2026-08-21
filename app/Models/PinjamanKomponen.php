<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanKomponen extends Model
{
    protected $table = 'pinj_jenis_komponen';

    protected $fillable = [
        'pinj_jenis_id',
        'nama',
        'nominal',
        'persen',
        'account_id',
        'cair',
        'tunggakan',
        'denda_t',
        'denda_h',
        'angsuran',
        'penalti',
        'rumus_c',
        'rumus_a',
        'rumus_p',
        'user_id',
    ];
}
