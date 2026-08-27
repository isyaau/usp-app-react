<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanJaminan extends Model
{
    protected $table = 'pinjaman_jaminan';

    protected $fillable = [
        'pinjaman_id',
        'nama',
        'keterangan',
        'nominal',
        'user_id',
    ];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
