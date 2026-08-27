<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalUlang extends Model
{
    protected $table = 'jadwal_ulang';

    protected $fillable = [
        'no_transaksi',
        'no_pinjaman_lama',
        'no_pinjaman',
        'tanggal',
        'tgl_transaksi',
        'pinjaman_id',
        'anggota_id',
        'jenis_id',
        'jaminan_id',
        'marketing_id',
        'sektor_id',
        'plafon',
        'sisa_pokok',
        'bunga',
        'jangka_waktu',
        'satuan',
        'metode',
        'jenis_angsuran',
        'bayar_pokok_per',
        'pembayaran',
        'jatuh_tempo',
        'manual',
        'tabungan_id',
        'kode_id',
        'kode_koreksi',
        'swp_id',
        'spp_id',
        'periode',
        'nominal_angsuran',
        'total_bunga',
        'cair_simpanan',
        'sms',
        'rekening_koran',
        'aktif',
        'keterangan',
        'user_id',
        'kantor_id',
        'status',
    ];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id');
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    public function jenisPinjaman()
    {
        return $this->belongsTo(PinjamanProduk::class, 'jenis_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kantor()
    {
        return $this->belongsTo(Kantor::class, 'kantor_id');
    }

    public function details()
    {
        return $this->hasMany(JadwalUlangDetail::class, 'jadwal_ulang_id')->orderBy('angsuran_ke');
    }

    public function biaya()
    {
        return $this->hasMany(JadwalUlangBiaya::class, 'jadwal_ulang_id');
    }

    public function jaminan()
    {
        return $this->hasMany(JadwalUlangJaminan::class, 'jadwal_ulang_id');
    }

    public function saksi()
    {
        return $this->hasMany(JadwalUlangSaksi::class, 'jadwal_ulang_id');
    }

    public function surat()
    {
        return $this->hasMany(JadwalUlangSurat::class, 'jadwal_ulang_id');
    }

    public function penjamin()
    {
        return $this->hasMany(JadwalUlangPenjamin::class, 'jadwal_ulang_id');
    }
}
