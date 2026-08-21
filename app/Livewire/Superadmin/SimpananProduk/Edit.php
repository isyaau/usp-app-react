<?php

namespace App\Livewire\Superadmin\SimpananProduk;

use App\Models\Account;
use Livewire\Component;
use App\Models\AccHeader;
use App\Models\SimpananKode;
use App\Models\SimpananBunga;
use App\Models\SimpananJenis;
use Illuminate\Validation\Rule;
use App\Models\SimpananJenisKode;

class Edit extends Component
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

    public $accounts, $kodes, $bungas, $biayas, $pajaks, $setors, $tariks;

    // account lain
    public $account_bunga;
    public $biaya_id, $biaya, $account_biaya;
    public $pajak_id, $pajak, $saldo_pajak, $account_pajak;

    // jenis
    public $jenis, $saham, $setor_id, $tarik_id, $nominal, $insentif;

    // android
    public $android, $nominal_android, $account_android;

    // simpanan kode
    public $kodeRows = [];
    public $allKodes = [];
    public $selectedKodes = [];
    public $showKodeModal = false;

    /* =======================
     * MOUNT
     * ======================= */
    public function mount($id = null)
    {

        $this->userLogin = auth()->user();
        // load master data
        $this->accounts = Account::orderBy('no_account')->get();
        $this->kodes = SimpananKode::orderBy('kode')->get();
        $this->bungas = SimpananKode::orderBy('kode')->get();
        $this->biayas = SimpananKode::orderBy('kode')->get();
        $this->pajaks = SimpananKode::orderBy('kode')->get();
        $this->tariks = SimpananKode::orderBy('kode')->get();
        $this->setors = SimpananKode::orderBy('kode')->get();

        // semua simpanan kode (untuk modal)
        $this->allKodes = SimpananKode::with(['debetAccount', 'kreditAccount'])->get();

        // ============================
        // MODE CREATE
        // ============================
        if (!$id) {
            $this->tingkat = [
                ['minimal' => null, 'maksimal' => null, 'bunga' => null]
            ];
            return;
        }

        // ============================
        // MODE EDIT
        // ============================
        $produk = SimpananJenis::with([
            'tingkat',
            'bungaKode',
            'biayaKode',
            'pajakKode',
            'androidKode',
            'idAccount',
            'bungaAccount',
            'biayaAccount',
            'pajakAccount',
            'androidAccount',
        ])->findOrFail($id);

        $this->produkId = $produk->id;

        // ---------- field utama ----------
        $this->kode       = $produk->kode;
        $this->nama       = $produk->nama;
        $this->account_id = $produk->account_id;
        $this->minimum    = $produk->minimum;
        $this->mengendap  = $produk->mengendap;

        // ---------- bunga ----------
        $this->bunga_id    = $produk->bunga_id;
        $this->jenis_bunga = $produk->jenis_bunga;
        $this->bungaJenis  = $produk->bunga_flat;

        // ---------- bunga bertingkat ----------
        $this->tingkat = $produk->tingkat->map(fn($t) => [
            'minimal'  => $t->minimal,
            'maksimal' => $t->maksimal,
            'bunga'    => $t->bunga,
        ])->toArray();

        // ---------- rumus ----------
        $this->rumus_bunga      = $produk->rumus_bunga;
        $this->account_bunga      = $produk->account_bunga;
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

    public function updatedRumusBunga($value)
    {
        if ($value != 1) {
            $this->rumus_satu_bulan = false;
        }
    }

    public function updatedJenisBunga($value)
    {
        if ($value == 1) {
            // Flat → reset tabel
            $this->tingkat = array_fill(0, 3, $this->emptyRow);
        }

        if ($value == 2) {
            // Bertingkat → reset flat
            $this->bunga = null;
        }
    }

    public function updated($property)
    {
        if ($this->jenis_bunga != 2) return;

        if (str_starts_with($property, 'tingkat.')) {
            $lastIndex = count($this->tingkat) - 1;
            $lastRow   = $this->tingkat[$lastIndex];

            if (
                filled($lastRow['minimal']) &&
                filled($lastRow['maksimal']) &&
                filled($lastRow['bunga'])
            ) {
                $this->tingkat[] = $this->emptyRow;
            }
        }
    }

    public function removeKodeRow($index)
    {
        unset($this->kodeRows[$index]);
        $this->kodeRows = array_values($this->kodeRows);
    }

    public function addSelectedKodes()
    {
        foreach ($this->selectedKodes as $id) {

            $item = $this->allKodes->firstWhere('id', $id);

            if (!$item) continue;

            $exists = collect($this->kodeRows)
                ->contains(fn($row) => $row['kode'] === $item->kode);

            if (!$exists) {
                $this->kodeRows[] = [
                    'kode' => $item->kode,
                    'nama' => $item->nama,
                    'account_debet' => $item->debetAccount?->no_account ?? '-',
                    'account_kredit' => $item->kreditAccount?->no_account ?? '-',
                ];
            }
        }

        // reset modal
        $this->selectedKodes = [];
        $this->selectAllKodes = false;
        $this->showKodeModal = false;
    }




    public function updatedSelectAllKodes($value)
    {
        if ($value) {
            $this->selectedKodes = $this->allKodes->pluck('id')->toArray();
        } else {
            $this->selectedKodes = [];
        }
    }


    /* =======================
     * HEADER CHANGE
     * ======================= */


    /* =======================
     * VALIDATION MESSAGE
     * ======================= */
    protected function messages()
    {
        return [
            'kode.required' => 'Kode produk simpanan wajib diisi.',
            'nama.required' => 'Nama produk simpanan wajib diisi.',
            'account_id.required' => 'Akun wajib diisi.',
            'account_id.exists' => 'Akun tidak ditemukan.',
            'kode.unique' => 'Kode produk simpanan sudah digunakan.',
            'nama.unique' => 'Nama produk simpanan sudah digunakan.',
            'max' => 'Melebihi batas karakter maksimal.',
            'string' => 'Format harus berupa teks.',
            'required' => 'Field ini wajib diisi.',
        ];
    }

    /* =======================
     * UPDATE
     * ======================= */
    public function update()
    {
        $this->validate([
            'kode'       => ['required', Rule::unique('simpanan_jenis', 'kode')->ignore($this->produkId)],
            'nama'       => ['required', Rule::unique('simpanan_jenis', 'nama')->ignore($this->produkId)],
            'account_id' => ['required'],
        ]);

        $jenis = SimpananJenis::findOrFail($this->produkId);

        /* ==========================
     * 1. SIMPAN PRODUK SIMPANAN
     * ========================== */
        $jenis->update([
            'kode' => $this->kode,
            'nama' => $this->nama,
            'account_id' => $this->account_id,
            'minimum' => $this->minimum,
            'mengendap' => $this->mengendap,
            'bunga_id' => $this->bunga_id,
            'jenis_bunga' => $this->jenis_bunga,
            'bunga' => $this->bungaJenis,
            'account_bunga' => $this->account_bunga,
            'rumus_bunga' => $this->rumus_bunga,
            'bulan' => $this->rumus_satu_bulan,
            'biaya_id' => $this->biaya_id,
            'biaya' => $this->biaya,
            'account_biaya' => $this->account_biaya,
            'pajak_id' => $this->pajak_id,
            'pajak' => $this->pajak,
            'account_pajak' => $this->account_pajak,
            'saldo_pajak' => $this->saldo_pajak,
            'android' => $this->android,
            'nominal_android' => $this->nominal_android,
            'account_android' => $this->account_android,
            'nominal' => $this->nominal,
            'jenis' => $this->jenis,
            'setor_id' => $this->setor_id,
            'tarik_id' => $this->tarik_id,
            'insentif' => $this->insentif,
            'saham' => $this->saham,
            'user_id' => auth()->id(),
        ]);


        /* ==========================
     * 2. RESET & SIMPAN BUNGA
     * ========================== */
        SimpananBunga::where('jenis_id', $jenis->id)->delete();

        if ($this->jenis_bunga == 2) {
            foreach ($this->tingkat as $row) {
                if (filled($row['minimal']) && filled($row['maksimal']) && filled($row['bunga'])) {
                    SimpananBunga::create([
                        'jenis_id' => $jenis->id,
                        'minimal'  => $row['minimal'],
                        'maksimal' => $row['maksimal'],
                        'bunga'    => $row['bunga'],
                        'user_id'  => auth()->id(),
                    ]);
                }
            }
        } else {
            SimpananBunga::create([
                'jenis_id' => $jenis->id,
                'bunga'    => $this->bungaJenis,
                'user_id'  => auth()->id(),
            ]);
        }

        /* ==========================
     * 3. SYNC SIMPANAN KODE
     * ========================== */
        $kodeIds = [];

        foreach ($this->kodeRows as $row) {
            $kode = SimpananKode::where('kode', $row['kode'])->first();
            if ($kode) {
                $kodeIds[] = $kode->id;
            }
        }

        $jenis->simpananKodes()->sync($kodeIds);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text'  => 'Produk Simpanan berhasil diperbarui',
            'icon'  => 'success',
        ]);

        return redirect()->route('superadmin.simpanan.produk-simpanan');
    }


    /* =======================
     * RENDER
     * ======================= */
    public function render()
    {
        return view('livewire.superadmin.simpananproduk.edit', [
            'title' => 'Edit Account',
        ]);
    }
}
