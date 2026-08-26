<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenaltiPinjaman extends Model
{
    protected $table = 'penalti_pinjaman';
    protected $fillable = [
        'no_transaksi', 'tgl_transaksi', 'pinjaman_id', 'nominal_penalti',
        'denda', 'keterangan', 'user_id', 'kantor_id', 'status',
    ];

    public function pinjaman() { return $this->belongsTo(Pinjaman::class, 'pinjaman_id'); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function kantor() { return $this->belongsTo(Kantor::class, 'kantor_id'); }
}
