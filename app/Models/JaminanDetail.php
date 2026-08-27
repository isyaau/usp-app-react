<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JaminanDetail extends Model
{
    protected $table = 'jaminan_detail';

    protected $fillable = [
        'jaminan_id',
        'detail',
        'user_id',
    ];

    public function jaminan()
    {
        return $this->belongsTo(Jaminan::class, 'jaminan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
