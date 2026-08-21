<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Kelompok extends Model
{
    protected $table = 'kelompok';

    protected $fillable = [
        'kode',
        'nama',
        'ketua_id',
        'user_id',
    ];


    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_id');
    }
}
