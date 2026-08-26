<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PencairanPinjaman extends Model
{
    protected $table = 'pencairan_pinjaman';

    protected $fillable = [
        'pinjaman_id',
        'tanggal_cair',
        'nominal_cair',
        'metode_cair',
        'no_rekening',
        'nama_rekening',
        'bank_id',
        'biaya_admin',
        'potongan_simpanan',
        'keterangan',
        'status',
        'approved_by',
        'approved_at',
        'cair_oleh',
        'cair_at',
        'created_by',
        'kantor_id',
    ];

    protected $casts = [
        'tanggal_cair' => 'date',
        'nominal_cair' => 'decimal:2',
        'biaya_admin' => 'decimal:2',
        'potongan_simpanan' => 'decimal:2',
        'approved_at' => 'datetime',
        'cair_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke pinjaman.
     */
    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id');
    }

    /**
     * Relasi ke user yang menyetujui.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relasi ke user yang mencairkan.
     */
    public function cairOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cair_oleh');
    }

    /**
     * Relasi ke user yang menyetujui (alias).
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relasi ke user yang membuat (alias).
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke kantor.
     */
    public function kantor(): BelongsTo
    {
        return $this->belongsTo(Kantor::class, 'kantor_id');
    }

    /**
     * Accessor untuk nominal_cair yang sudah diformat.
     */
    public function getNominalCairFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal_cair, 0, ',', '.');
    }

    /**
     * Accessor untuk biaya_admin yang sudah diformat.
     */
    public function getBiayaAdminFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya_admin, 0, ',', '.');
    }

    /**
     * Accessor untuk potongan_simpanan yang sudah diformat.
     */
    public function getPotonganSimpananFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->potongan_simpanan, 0, ',', '.');
    }

    /**
     * Accessor untuk status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Menunggu',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'dicairkan' => 'Dicairkan',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Accessor untuk metode_cair label.
     */
    public function getMetodeCairLabelAttribute(): string
    {
        $labels = [
            'transfer' => 'Transfer',
            'tunai' => 'Tunai',
            'cek' => 'Cek',
            'giro' => 'Giro',
        ];

        return $labels[$this->metode_cair] ?? ucfirst($this->metode_cair);
    }

    /**
     * Accessor untuk net cash flow.
     */
    public function getNetCashFlowAttribute(): float
    {
        return (float) $this->nominal_cair - (float) $this->biaya_admin - (float) $this->potongan_simpanan;
    }

    /**
     * Scope untuk pencairan yang sudah dicairkan.
     */
    public function scopeDicairkan($query)
    {
        return $query->where('status', 'dicairkan');
    }

    /**
     * Scope untuk pencairan yang pending.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk pencairan yang disetujui.
     */
    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }
}
