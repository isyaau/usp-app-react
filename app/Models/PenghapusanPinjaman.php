<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenghapusanPinjaman extends Model
{
    protected $table = 'penghapusan_pinjaman';

    protected $fillable = [
        'no_transaksi',
        'tgl_transaksi',
        'pinjaman_id',
        'sisa_pokok',
        'keterangan',
        'user_id',
        'kantor_id',
        'status',
    ];

    protected $casts = [
        'tgl_transaksi' => 'date',
        'sisa_pokok' => 'decimal:2',
    ];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kantor()
    {
        return $this->belongsTo(Kantor::class, 'kantor_id');
    }
}