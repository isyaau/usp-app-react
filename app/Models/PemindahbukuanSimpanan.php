<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Transaksi pemindahbukuan simpanan (tabel pemindahbukuan_simpanan).
 * Memindahkan dana antar dua rekening milik anggota yang sama.
 */
class PemindahbukuanSimpanan extends Model
{
    protected $table = 'pemindahbukuan_simpanan';

    protected $fillable = [
        'no_transaksi',
        'tgl_transaksi',
        'anggota_id',
        'simpanan_dari_id',
        'simpanan_ke_id',
        'kode_transaksi_id',
        'nominal',
        'keterangan',
        'user_id',
        'kantor_id',
        'status',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    public function simpananDari()
    {
        return $this->belongsTo(Simpanan::class, 'simpanan_dari_id');
    }

    public function simpananKe()
    {
        return $this->belongsTo(Simpanan::class, 'simpanan_ke_id');
    }

    public function kodeTransaksi()
    {
        return $this->belongsTo(SimpananKode::class, 'kode_transaksi_id');
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
