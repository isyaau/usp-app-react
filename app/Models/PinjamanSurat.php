<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanSurat extends Model
{
    protected $table = 'pinjaman_surat';

    protected $fillable = [
        'pinjaman_id',
        'surat_id',
        'keterangan',
        'surat',
        'user_id',
    ];

    /**
     * Get the account associated with the pinjaman produk.
     */
    public function proposal()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function anggota()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * Get the kolektabilitas records for the pinjaman produk.
     */
    public function kolektabilitas()
    {
        return $this->hasMany(PinjamanKolektabilitas::class, 'pinj_jenis_id');
    }

    /**
     * Get the komponen records for the pinjaman produk.
     */
    public function komponen()
    {
        return $this->hasMany(PinjamanKomponen::class, 'pinj_jenis_id');
    }

    /**
     * Get the user that created the pinjaman produk.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
