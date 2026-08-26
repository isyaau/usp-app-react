<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasHarian extends Model
{
    protected $table = 'kas_harian';

    protected $fillable = [
        'tanggal',
        'kas_awal',
        'kas_masuk',
        'kas_keluar',
        'kas_akhir',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'kas_awal' => 'decimal:2',
        'kas_masuk' => 'decimal:2',
        'kas_keluar' => 'decimal:2',
        'kas_akhir' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
