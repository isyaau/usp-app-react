<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LaporanTransaksiPinjaman extends Model {
    protected $table = "laporan_transaksi_pinjaman";
    protected $fillable = [
        "no_laporan", "tgl_laporan", "anggota_id", "jenis_transaksi",
        "nominal", "keterangan", "user_id", "kantor_id", "status",
    ];
    protected $casts = ["tgl_laporan" => "date", "nominal" => "decimal:2"];
    public function anggota() { return $this->belongsTo(Anggota::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function kantor() { return $this->belongsTo(Kantor::class); }
}