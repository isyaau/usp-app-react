<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenarikanDanaTitipanAnggota extends Model
{
    protected $table = "penarikan_dana_titipan_anggota";
    protected $fillable = [
        "no_transaksi", "tgl_transaksi", "anggota_id",
        "nominal_penarikan", "keterangan", "user_id", "kantor_id", "status",
    ];
    protected $casts = ["tgl_transaksi" => "date", "nominal_penarikan" => "decimal:2"];

    public function anggota() { return $this->belongsTo(Anggota::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function kantor() { return $this->belongsTo(Kantor::class); }
}
