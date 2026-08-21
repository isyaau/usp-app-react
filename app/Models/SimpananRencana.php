<?php

namespace App\Models;

use App\Models\User;
use App\Models\Kantor;
use Illuminate\Database\Eloquent\Model;


class SimpananRencana extends Model
{
    protected $table = 'simpanan_rencana';

    protected $fillable = [
        'tanggal_mulai',
        'tanggal_jatuhtempo',
        'no_bukti',
        'jangka_waktu',
        'satuan',
        'nominal',
        'bunga',
        'keterangan',
        'kantor_id',
        'user_id',
    ];


    public function kantor()
    {
        return $this->belongsTo(Kantor::class, 'kantor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(SimpananRencanaDetail::class, 'rencana_id');
    }
}
