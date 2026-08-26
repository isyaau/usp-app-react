<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AngsuranKolektifDetail extends Model
{
    protected $table = 'angsuran_kolektif_detail';
    protected $fillable = [
        'angsuran_kolektif_id', 'pinjaman_id', 'anggota_id', 'angsuran_ke',
        'nominal_pokok', 'nominal_bunga', 'total_angsuran', 'setoran_simpanan',
        'denda', 'keterangan',
    ];

    public function angsuranKolektif() { return $this->belongsTo(AngsuranKolektif::class, 'angsuran_kolektif_id'); }
    public function pinjaman() { return $this->belongsTo(Pinjaman::class, 'pinjaman_id'); }
    public function anggota() { return $this->belongsTo(Anggota::class, 'anggota_id'); }
}
