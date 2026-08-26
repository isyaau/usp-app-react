<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LaporanKasHarian extends Model {
    protected $table = "laporan_kas_harian";
    protected $fillable = [
        "no_laporan", "tgl_laporan", "saldo_awal", "total_pemasukan",
        "total_pengeluaran", "saldo_akhir", "keterangan", "user_id", "kantor_id", "status",
    ];
    protected $casts = ["tgl_laporan" => "date"];
    public function user() { return $this->belongsTo(User::class); }
    public function kantor() { return $this->belongsTo(Kantor::class); }
}