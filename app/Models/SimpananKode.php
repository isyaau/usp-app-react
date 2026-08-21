<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SimpananKode extends Model
{
    protected $table = 'simpanan_kode';

    protected $fillable = [
        'kode',
        'nama',
        'account_debet',
        'account_kredit',
        'setoran',
        'tarikan',
        'transfer',
        'pokok',
        'wajib',
        'sukarela',
        'pinjaman',
        'saham',
        'pokok_pinjaman',
        'rencana',
        'keterangan',
        'user_id',
    ];


    public function debetAccount()
    {
        return $this->belongsTo(Account::class, 'account_debet');
    }

    public function kreditAccount()
    {
        return $this->belongsTo(Account::class, 'account_kredit');
    }
}
