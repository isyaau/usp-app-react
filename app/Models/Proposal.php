<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    protected $table = 'proposal';

    protected $fillable = [
        'tanggal',
        'no_bukti',
        'anggota_id',
        'jenis_id',
        'marketing_id',
        'plafon',
        'bunga',
        'jangka_waktu',
        'satuan',
        'bayar_pokok_per',
        'pembayaran',
        'setiap_saat',
        'jenis_angsuran',
        'nominal_angsuran',
        'penggunaan_kredit',
        'jaminan',
        'total_biaya',
        'total_terima',
        'status',
        'kantor_id',
        'user_id',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    public function jenisPinjaman()
    {
        return $this->belongsTo(PinjamanProduk::class, 'jenis_id');
    }

    public function marketing()
    {
        return $this->belongsTo(Marketing::class, 'marketing_id');
    }

    public function kantor()
    {
        return $this->belongsTo(Kantor::class, 'kantor_id');
    }

    public function biaya()
    {
        return $this->hasMany(ProposalBiaya::class, 'proposal_id');
    }
}
