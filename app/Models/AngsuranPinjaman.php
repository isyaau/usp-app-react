<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AngsuranPinjaman extends Model
{
    protected $table = 'angsuran_pinjaman';
    protected $fillable = [
        'no_transaksi', 'tgl_transaksi', 'pinjaman_id', 'angsuran_ke',
        'nominal_pokok', 'nominal_bunga', 'total_angsuran', 'denda',
        'keterangan', 'user_id', 'kantor_id', 'status',
    ];

    public function pinjaman() { return $this->belongsTo(Pinjaman::class, 'pinjaman_id'); }
    public function anggota() { return $this->hasOneThrough(Anggota::class, Pinjaman::class, 'id', 'id', 'pinjaman_id', 'anggota_id'); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function kantor() { return $this->belongsTo(Kantor::class, 'kantor_id'); }
}
