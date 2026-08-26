<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PenaltiSimpananBerjangka extends Model {
    protected $table = "penalti_simpanan_berjangka";
    protected $fillable = ["no_transaksi","tgl_transaksi","anggota_id","deposito_id","nominal_penalti","nominal_pajak","total_penalti","keterangan","user_id","kantor_id","status"];
    protected $casts = ["tgl_transaksi" => "date"];
    public function anggota() { return $this->belongsTo(Anggota::class); }
    public function deposito() { return $this->belongsTo(Deposito::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function kantor() { return $this->belongsTo(Kantor::class); }
}