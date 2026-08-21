<?php

namespace App\Livewire\Superadmin\Simpanan;

use App\Models\Simpanan;
use App\Models\Kantor;
use App\Models\SimpananJenis;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Detail Simpanan')]
class Show extends Component
{
    public Simpanan $simpanan;

    // Data simpanan
    public $created_at;
    public $no_rekening;
    public $anggota_id;
    public $no_anggota;
    public $nama_anggota;

    public $jenis_id;
    public $jenis_nama;

    public $marketing_id;
    public $marketing_nama;

    public $qq;
    public $bunga;
    public $nominal_blokir;
    public $tgl_blokir;
    public $nominal_setor;
    public $kantor_id;
    public $kantor_nama;

    // Status
    public $aktif;
    public $blokir_simpanan;
    public $blokir_nominal;
    public $blokir_tgl;
    public $sms;

    // Signature
    public $signature;

    // Master (optional kalau mau dipakai di view)
    public $kantors;
    public $jenis;

    public function mount($id)
    {
        $this->simpanan = Simpanan::with([
            'anggota',
            'marketing',
            'kantor',
            'jenis_simpanan'
        ])->findOrFail($id);

        // Basic data
        $this->created_at   = $this->simpanan->created_at->format('d-m-Y');
        $this->no_rekening  = $this->simpanan->no_rekening;

        // Anggota
        $this->anggota_id   = $this->simpanan->anggota?->id;
        $this->no_anggota   = $this->simpanan->anggota?->no_anggota;
        $this->nama_anggota = $this->simpanan->anggota?->nama;

        // Jenis simpanan
        $this->jenis_id   = $this->simpanan->jenis_id;
        $this->jenis_nama = $this->simpanan->jenis?->nama;

        // Marketing
        $this->marketing_id   = $this->simpanan->marketing_id;
        $this->marketing_nama = $this->simpanan->marketing?->nama;

        // Detail
        $this->qq             = $this->simpanan->qq;
        $this->bunga          = $this->simpanan->bunga;
        $this->nominal_blokir = $this->simpanan->nominal_blokir;
        $this->tgl_blokir     = $this->simpanan->tgl_blokir;
        $this->nominal_setor  = $this->simpanan->nominal_setor;

        // Kantor
        $this->kantor_id   = $this->simpanan->kantor_id;
        $this->kantor_nama = $this->simpanan->kantor?->nama_kantor;

        // Status boolean
        $this->aktif            = (bool) $this->simpanan->aktif;
        $this->blokir_simpanan  = (bool) $this->simpanan->blokir_simpanan;
        $this->blokir_nominal   = (bool) $this->simpanan->blokir_nominal;
        $this->blokir_tgl       = (bool) $this->simpanan->blokir_tgl;
        $this->sms              = (bool) $this->simpanan->sms;

        // Signature
        $this->signature = $this->simpanan->ttd
            ? asset('storage/' . $this->simpanan->ttd)
            : null;

        // Optional master data
        $this->kantors = Kantor::orderBy('nama_kantor')->get();
        $this->jenis   = SimpananJenis::orderBy('kode')->get();
    }

    public function render()
    {
        return view('livewire.superadmin.simpanan.show', [
            'title' => 'Detail Simpanan',
        ]);
    }
}
