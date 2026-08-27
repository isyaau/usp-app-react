<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanBiaya extends Model
{
    protected $table = 'pinjaman_biaya';

    protected $fillable = [
        'pinjaman_id',
        'nama',
        'nominal',
        'persen',
        'account_id',
        'user_id',
    ];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id');
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
