<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'account';

    protected $fillable = [
        'no_account',
        'nama',
        'header_id',
        'tipe',
        'user_id',

    ];

    public function header()
    {
        return $this->belongsTo(AccHeader::class, 'header_id');
    }
}
