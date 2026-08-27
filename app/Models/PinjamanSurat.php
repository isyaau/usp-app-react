<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanSurat extends Model
{
    protected $table = 'pinjaman_surat';

    protected $fillable = [
        'pinjaman_id',
        'surat_id',
        'keterangan',
        'surat',
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
