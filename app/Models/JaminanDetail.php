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
        return $this->belongsTo(JaminanDetail::class, 'jaminan_id');
    }
}
