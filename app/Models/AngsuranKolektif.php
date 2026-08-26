<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AngsuranKolektif extends Model
{
    protected $table = 'angsuran_kolektif';
    protected $fillable = [
        'no_transaksi', 'tgl_transaksi', 'kelompok_id', 'jenis',
        'metode_pembayaran', 'nominal_total', 'jumlah_anggota',
        'keterangan', 'user_id', 'kantor_id', 'status',
    ];

    public function kelompok() { return $this->belongsTo(Kelompok::class, 'kelompok_id'); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function kantor() { return $this->belongsTo(Kantor::class, 'kantor_id'); }
    public function details() { return $this->hasMany(AngsuranKolektifDetail::class, 'angsuran_kolektif_id'); }
}
