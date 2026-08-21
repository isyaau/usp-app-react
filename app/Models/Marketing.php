<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marketing extends Model
{
    protected $table = 'marketing';

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'no_ktp',
        'telepon',
        'no_hp',
        'aktif',
        'kantor_id',
        'user_id',
    ];


    public function kantor()
    {
        return $this->belongsTo(Kantor::class, 'kantor_id');
    }
}
