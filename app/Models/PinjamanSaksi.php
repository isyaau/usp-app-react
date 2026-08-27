<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanSaksi extends Model
{
    protected $table = 'pinjaman_saksi';

    protected $fillable = [
        'pinjaman_id',
        'nama',
        'tempat_lahir',
        'tgl_lahir',
        'no_ktp',
        'alamat',
        'pekerjaan_id',
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
