<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanTemplate extends Model
{
    protected $table = 'pinjaman_template';

    protected $fillable = [
        'pinjaman_id',
        'surat',
        'jenis',
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
