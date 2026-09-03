<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccGroup extends Model
{
    protected $table = 'acc_group';

    protected $fillable = [
        'nama',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
