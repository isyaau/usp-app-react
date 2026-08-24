<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Transaksi tarikan simpanan (tabel tarikan_simpanan).
 * Satu baris = satu penarikan tunai dari rekening simpanan anggota.
 */
class TarikanSimpanan extends Model
{
    protected $table = 'tarikan_simpanan';

    protected $fillable = [
        'no_transaksi',
        'tgl_transaksi',
        'anggota_id',
        'simpanan_id',
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

    public function simpanan()
    {
        return $this->belongsTo(Simpanan::class, 'simpanan_id');
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
