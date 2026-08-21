<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    protected $table = 'simpanan';

    protected $fillable = [
        'no_rekening',
        'anggota_id',
        'jenis_id',
        'marketing_id',
        'qq',
        'bunga',
        'baris',
        'ttd',
        'blokir_simpanan',
        'blokir_nominal',
        'nominal_blokir',
        'blokir_tgl',
        'tgl_blokir',
        'nominal_setor',
        'sms',
        'aktif',
        'kantor_id',
        'user_id',
    ];


    public function jenis_simpanan()
    {
        return $this->belongsTo(SimpananJenis::class, 'jenis_id');
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    public function kantor()
    {
        return $this->belongsTo(Kantor::class, 'kantor_id');
    }

    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'marketing_id');
    }
}
