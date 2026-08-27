<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanPenjamin extends Model
{
    protected $table = 'pinjaman_penjamin';

    protected $fillable = [
        'pinjaman_id',
        'nama',
        'alamat',
        'no_ktp',
        'hubungan',
        'ibu',
        'telepon',
        'tampil',
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
