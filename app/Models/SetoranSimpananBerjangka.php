<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SetoranSimpananBerjangka extends Model {
    protected $table = "setoran_simpanan_berjangka";
    protected $fillable = ["no_transaksi","tgl_transaksi","anggota_id","deposito_id","nominal","keterangan","user_id","kantor_id","status"];
    protected $casts = ["tgl_transaksi" => "date", "nominal" => "decimal:2"];
    public function anggota() { return $this->belongsTo(Anggota::class); }
    public function deposito() { return $this->belongsTo(Deposito::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function kantor() { return $this->belongsTo(Kantor::class); }
}