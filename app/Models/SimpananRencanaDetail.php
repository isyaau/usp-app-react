<?php

namespace App\Models;

use App\Models\User;
use App\Models\Kantor;
use Illuminate\Database\Eloquent\Model;


class SimpananRencanaDetail extends Model
{
    protected $table = 'simpanan_rencana_detail';

    protected $fillable = [
        'rencana_id',
        'simpanan_id',
        'user_id',
    ];

    public function rencana()
    {
        return $this->belongsTo(SimpananRencana::class, 'rencana_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
