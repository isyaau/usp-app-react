<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat perubahan data (audit log).
 *
 * Diisi otomatis oleh listener global pada AppServiceProvider
 * saat sebuah Eloquent model dibuat, diubah, atau dihapus oleh user login.
 */
class HistoryLog extends Model
{
    protected $fillable = [
        'user_id',
        'table',
        'record_id',
        'action',
        'changes',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}