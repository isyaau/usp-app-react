<?php

namespace App\Livewire\Superadmin\SimpananProduk;

use Livewire\Component;
use App\Models\SimpananJenis;

class Show extends Component
{
    public $userLogin;

    public $produkId;

    // field utama
    public $kode, $nama, $account_id;
    public $minimum, $mengendap;

    // bunga
    public $bunga_id;
    public $jenis_bunga;
    public $bungaJenis;
    public $tingkat = [];

    // rumus
    public $rumus_bunga;
    public $rumus_satu_bulan = false;

    // account lain
    public $account_bunga;
    public $biaya_id;
    public $biaya;
    public $account_biaya;
    public $pajak_id;
    public $pajak;
    public $saldo_pajak;
    public $account_pajak;

    // jenis
    public $jenis;
    public $saham;
    public $setor_id;
    public $tarik_id;
    public $nominal;
    public $insentif;

    // android
    public $android;
    public $nominal_android;
    public $account_android;

    // simpanan kode
    public $kodeRows = [];

    /* =======================
     * MOUNT
     * ======================= */
    public function mount($id)
    {
        $this->userLogin = auth()->user();
        $this->produkId = $id;

        // Load produk beserta relasi
        $produk = SimpananJenis::with([
            'tingkat',
            'simpananKodes.debetAccount',
            'simpananKodes.kreditAccount',
        ])->findOrFail($id);

        // ---------- field utama ----------
        $this->kode       = $produk->kode;
        $this->nama       = $produk->nama;
        $this->account_id = $produk->account_id;
        $this->minimum    = $produk->minimum;
        $this->mengendap  = $produk->mengendap;

        // ---------- bunga ----------
        $this->bunga_id    = $produk->bunga_id;
        $this->jenis_bunga = $produk->jenis_bunga;
        $this->account_bunga = $produk->account_bunga;
        $this->bungaJenis  = $produk->bunga;

        // ---------- bunga bertingkat ----------
        $this->tingkat = $produk->tingkat->map(fn($t) => [
            'minimal'  => $t->minimal,
            'maksimal' => $t->maksimal,
            'bunga'    => $t->bunga,
        ])->toArray();

        // ---------- rumus ----------
        $this->rumus_bunga      = $produk->rumus_bunga;
        $this->rumus_satu_bulan = (bool) $produk->rumus_satu_bulan;

        // ---------- biaya ----------
        $this->biaya_id      = $produk->biaya_id;
        $this->biaya         = $produk->biaya;
        $this->account_biaya = $produk->account_biaya;

        // ---------- pajak ----------
        $this->pajak_id      = $produk->pajak_id;
        $this->pajak         = $produk->pajak;
        $this->saldo_pajak   = $produk->saldo_pajak;
        $this->account_pajak = $produk->account_pajak;

        // ---------- jenis ----------
        $this->jenis     = $produk->jenis;
        $this->saham     = $produk->saham;
        $this->setor_id  = $produk->setor_id;
        $this->tarik_id  = $produk->tarik_id;
        $this->nominal   = $produk->nominal;
        $this->insentif  = $produk->insentif;
        $this->created_at = $produk->created_at;
        $this->updated_at = $produk->updated_at;


        // ---------- android ----------
        $this->android          = $produk->android;
        $this->nominal_android  = $produk->nominal_android;
        $this->account_android  = $produk->account_android;


        // ---------- tabel SimpananKode ----------
        $this->kodeRows = $produk->simpananKodes->map(fn($k) => [
            'kode'          => $k->kode,
            'nama'          => $k->nama,
            'account_debet' => $k->debetAccount?->no_account,
            'account_kredit' => $k->kreditAccount?->no_account,
        ])->toArray();
    }

    public function render()
    {
        return view('livewire.superadmin.simpananproduk.show', [
            'title' => 'Detail Produk Simpanan',
        ]);
    }
}
