<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DepositoJenis extends Model
{
    protected $table = 'deposito_jenis';

    protected $fillable = [
        'kode',
        'nama',
        'account_id',
        'jangka_waktu',
        'bunga',
        'account_bunga',
        'rumus_bunga',
        'penalti',
        'account_penalti',
        'pajak',
        'account_pajak',
        'saldo_pajak',
        'insentif',
        'user_id',
    ];


    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function bunga()
    {
        return $this->belongsTo(Account::class, 'account_bunga');
    }

    public function penalti()
    {
        return $this->belongsTo(Account::class, 'account_penalti');
    }

    public function pajak()
    {
        return $this->belongsTo(Account::class, 'account_pajak');
    }
}
