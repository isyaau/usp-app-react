<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamanProduk extends Model
{
    protected $table = 'pinj_jenis';

    protected $fillable = [
        'kode',
        'nama',
        'account_id',
        'bunga',
        'account_bunga',
        'ditangguhkan',
        'account_ditangguhkan',
        'kas',
        'account_bank',
        'insentif',
        'simpanan',
        'swp_cair',
        'swp_angsur',
        'swp_persen',
        'nominal_simpanan',
        'simpanan_pokok',
        'nominal_simpanan_pokok',
        'toleransi',
        'angsuran',
        'user_id',
    ];

    /**
     * Get the account associated with the pinjaman produk.
     */
    public function account()
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
