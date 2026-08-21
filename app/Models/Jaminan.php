<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jaminan extends Model
{
    protected $table = 'jaminan';

    protected $fillable = [
        'nama',
        'user_id',
    ];

    public function details()
    {
        return $this->hasMany(JaminanDetail::class);
    }
}
