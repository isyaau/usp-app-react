<?php

namespace App\Models;

use App\Models\Anggota;
use App\Models\DepositoJenis;
use App\Models\Kantor;
use App\Models\Marketing;
use Illuminate\Database\Eloquent\Model;


class Deposito extends Model
{
    protected $table = 'deposito';

    protected $fillable = [
        'tanggal',
        'no_deposito',
        'anggota_id',
        'jenis_id',
        'marketing_id',
        'qq',
        'jangka_waktu',
        'bunga',
        'nominal',
        'otomatis',
        'bayar_bunga',
        'diawal',
        'bunga_accrual',
        'account_bungaaccrual',
        'tabunganbunga_id',
        'tabungantempo_id',
        'bayar_jatuhtempo',
        'blokir',
        'kantor_id',
        'user_id',
    ];


    public function produk()
    {
        return $this->belongsTo(DepositoJenis::class, 'jenis_id');
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'marketing_id');
    }

    public function kantor()
    {
        return $this->belongsTo(Kantor::class, 'kantor_id');
    }
}
