<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanKomponen extends Model
{
    protected $table = 'pinj_jenis_komponen';

    protected $fillable = [
        'pinj_jenis_id',
        'nama',
        'nominal',
        'persen',
        'account_id',
        'cair',
        'tunggakan',
        'denda_t',
        'denda_h',
        'angsuran',
        'penalti',
        'rumus_c',
        'rumus_a',
        'rumus_p',
        'user_id',
    ];

    public function jenisPinjaman()
    {
        return $this->belongsTo(PinjamanProduk::class, 'pinj_jenis_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
