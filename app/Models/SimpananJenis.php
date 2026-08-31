<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SimpananJenis extends Model
{
    protected $table = 'simpanan_jenis';

    protected $fillable = [
        'kode',
        'nama',
        'account_id',
        'minimum',
        'mengendap',
        'bunga_id',
        'jenis_bunga',
        'bunga',
        'account_bunga',
        'rumus_bunga',
        'bulan',
        'biaya_id',
        'biaya',
        'account_biaya',
        'pajak_id',
        'pajak',
        'account_pajak',
        'saldo_pajak',
        'android',
        'nominal_android',
        'account_android',
        'nominal',
        'harga_saham',
        'jenis',
        'setor_id',
        'tarik_id',
        'insentif',
        'saham',
        'pajak_saldo',
        'update_bagi_hasil',
        'user_id',
    ];

    public function tingkat()
    {
        return $this->hasMany(SimpananBunga::class, 'jenis_id');
    }


    public function bungaKode()
    {
        return $this->belongsTo(SimpananKode::class, 'bunga_id');
    }
    public function biayaKode()
    {
        return $this->belongsTo(SimpananKode::class, 'biaya_id');
    }
    public function pajakKode()
    {
        return $this->belongsTo(SimpananKode::class, 'pajak_id');
    }
    public function androidKode()
    {
        return $this->belongsTo(SimpananKode::class, 'android');
    }
    public function setorKode()
    {
        return $this->belongsTo(SimpananKode::class, 'setor_id');
    }
    public function tarikKode()
    {
        return $this->belongsTo(SimpananKode::class, 'tarik_id');
    }


    public function idAccount()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    public function bungaAccount()
    {
        return $this->belongsTo(Account::class, 'account_bunga');
    }
    public function biayaAccount()
    {
        return $this->belongsTo(Account::class, 'account_biaya');
    }
    public function pajakAccount()
    {
        return $this->belongsTo(Account::class, 'account_pajak');
    }
    public function androidAccount()
    {
        return $this->belongsTo(Account::class, 'account_android');
    }

    public function simpananKodes()
    {
        return $this->belongsToMany(
            SimpananKode::class,
            'simpanan_jenis_kode', // tabel pivot
            'jenis_id',
            'kode_id'
        );
    }

    public function getJenisLabelAttribute()
    {
        return [
            1 => 'Pokok',
            2 => 'Wajib',
            3 => 'Sukarela',
            4 => 'Wajib Pinjaman',
            5 => 'Saham',
            6 => 'Pokok Pinjaman',
            7 => 'Rencana',
        ][$this->jenis] ?? '-';
    }
}
