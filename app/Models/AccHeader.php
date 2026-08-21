<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccHeader extends Model
{
    protected $table = 'acc_header';

    protected $fillable = [
        'no_header',
        'nama',
        'group_id',
        'jenis',
        'modal',
        'keterangan',
        'user_id',
    ];

    public function group()
    {
        return $this->belongsTo(AccGroup::class, 'group_id');
    }
}
