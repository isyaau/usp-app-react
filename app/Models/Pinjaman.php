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
        'nominal_angsuran',
        'bunga',
        'jangka_waktu',
        'periode',
        'bayar_pokok_per',
        'jatuh_tempo',
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

    /** Produk pinjaman terkait (pinj_jenis). */
    public function jenisPinjaman()
    {
        return $this->belongsTo(PinjamanProduk::class, 'jenis_id');
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    public function kantor()
    {
        return $this->belongsTo(Kantor::class, 'kantor_id');
    }

    /**
     * Kolektabilitas milik jenis pinjaman terkait.
     */
    public function kolektabilitas()
    {
        return $this->hasManyThrough(PinjamanKolektabilitas::class, PinjamanProduk::class, 'id', 'pinj_jenis_id', 'jenis_id', 'id');
    }

    /**
     * Pencairan pinjaman terkait.
     */
    public function pencairan()
    {
        return $this->hasMany(PencairanPinjaman::class, 'pinjaman_id');
    }

    /**
     * Komponen milik jenis pinjaman terkait.
     */
    public function komponen()
    {
        return $this->hasManyThrough(PinjamanKomponen::class, PinjamanProduk::class, 'id', 'pinj_jenis_id', 'jenis_id', 'id');
    }

    /**
     * Get the user that created the pinjaman produk.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function biaya()
    {
        return $this->hasMany(PinjamanBiaya::class, 'pinjaman_id');
    }

    public function jaminan()
    {
        return $this->hasMany(PinjamanJaminan::class, 'pinjaman_id');
    }

    public function saksi()
    {
        return $this->hasMany(PinjamanSaksi::class, 'pinjaman_id');
    }

    public function surat()
    {
        return $this->hasMany(PinjamanSurat::class, 'pinjaman_id');
    }

    public function penjamin()
    {
        return $this->hasMany(PinjamanPenjamin::class, 'pinjaman_id');
    }
}
