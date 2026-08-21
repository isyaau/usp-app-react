<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    protected $table = 'pinjaman';

    protected $fillable = [
        'tanggal',
        'no_pinjaman',
        'proposal_id',
        'anggota_id',
        'jaminan_id',
        'jenis_id',
        'marketing_id',
        'sektor_id',
        'angsuran',
        'plafon',
        'noinal_angsuran',
        'bunga',
        'jangka_waktu',
        'periode',
        'satuan',
        'pembayaran',
        'manual',
        'tabungan_id',
        'kode_id',
        'kode_koreksi',
        'swp_id',
        'spp_id',
        'angsuranke',
        'rekening_koran',
        'cair_simpanan',
        'sms',
        'aktif',
        'kantor_id',
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
