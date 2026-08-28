<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPeringatan extends Model
{
    protected $table = 'surat_peringatan';

    protected $fillable = [
        'no_transaksi',
        'tgl_transaksi',
        'pinjaman_id',
        'tahap',
        'isi',
        'user_id',
        'kantor_id',
        'status',
    ];

    protected $casts = [
        'tgl_transaksi' => 'date',
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