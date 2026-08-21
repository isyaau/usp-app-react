<?php

namespace App\Livewire\Superadmin\SimpananProduk;

use App\Models\Account;
use App\Models\AccHeader;
use App\Models\SimpananBunga;
use App\Models\SimpananJenis;
use App\Models\SimpananJenisKode;
use App\Models\SimpananKode;
use Livewire\Component;
use Illuminate\Validation\Rule;

class Create extends Component
{
    public $userLogin, $kode, $nama, $account_id, $minimum, $mengendap, $bunga_id, $bunga, $account_bunga, $rumus_bunga, $bulan, $biaya_id, $biaya, $account_biaya, $pajak_id, $pajak, $account_pajak, $saldo_pajak, $android, $nominal_android, $account_android, $nominal, $jenis, $setor_id, $tarik_id, $insentif, $saham, $bungaJenis;

    public $jenis_id, $minimal, $maksimal, $kode_id, $debetAccount, $kreditAccount;

    public $jenis_bunga = null;
    public $rumus_satu_bulan = false;
    public $accounts, $kodes, $bungas, $biayas, $pajaks, $setors, $tariks;

    public $tingkat = [];


    public $kodeRows = []; // Tabel utama untuk SimpananKode yang dipilih
    public $emptyKodeRow = [
        'kode' => null,
        'nama' => null,
    ];

    public $allKodes = [];        // Semua SimpananKode dari DB
    public $selectedKodes = [];   // Checkbox yang dipilih di modal
    public $showKodeModal = false;
    public $selectAllKodes = false;


    protected $emptyRow = [
        'minimal'  => null,
        'maksimal' => null,
        'bunga'    => null,
    ];

    public function updatedRumus($value)
    {
        // Jika bukan saldo terendah → reset checkbox
        if ((int) $value !== 1) {
            $this->rumus_satu_bulan = false;
        }
    }

    public function updatedRumusSatuBulan($value)
    {
        // Jika checkbox diubah tapi rumus bukan 1 → langsung reset
        if ((int) $this->rumus_bunga !== 1) {
            $this->rumus_satu_bulan = false;
        }
    }


    /* =======================
     * MOUNT
     * ======================= */
    public function mount()
    {
        $this->userLogin = auth()->user();

        $this->accounts = Account::orderBy('no_account')->get();
        $this->kodes = SimpananKode::orderBy('kode')->get();
        $this->bungas = SimpananKode::orderBy('kode')->get();
        $this->biayas = SimpananKode::orderBy('kode')->get();
        $this->pajaks = SimpananKode::orderBy('kode')->get();
        $this->tariks = SimpananKode::orderBy('kode')->get();
        $this->setors = SimpananKode::orderBy('kode')->get();

        // 🔥 PENTING: load relasi
        $this->allKodes = SimpananKode::with([
            'debetAccount',
            'kreditAccount'
        ])->orderBy('kode')->get();

        $this->kodeRows = [];

        $this->jenis_bunga = 1;
        $this->bunga = null;
        $this->tingkat = array_fill(0, 3, $this->emptyRow);
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
     * STORE
     * ======================= */
    public function store()
    {
        $this->validate([
            'kode' => 'required|unique:simpanan_jenis,kode',
            'nama' => 'required|unique:simpanan_jenis,nama',
            'account_id' => 'required',
        ]);

        /* ==========================
     * 1. SIMPAN PRODUK SIMPANAN
     * ========================== */
        $jenis = SimpananJenis::create([
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
            'user_id' => $this->userLogin->id,
        ]);

        /* ==========================
     * 2. SIMPAN BUNGA
     * ========================== */
        if ($this->jenis_bunga == 2) {
            // 🔹 Bertingkat
            foreach ($this->tingkat as $row) {
                if (
                    filled($row['minimal']) &&
                    filled($row['maksimal']) &&
                    filled($row['bunga'])
                ) {
                    SimpananBunga::create([
                        'jenis_id' => $jenis->id,
                        'minimal' => $row['minimal'],
                        'maksimal' => $row['maksimal'],
                        'bunga' => $row['bunga'],
                        'user_id' => $this->userLogin->id,
                    ]);
                }
            }
        } else {
            // 🔹 Flat
            SimpananBunga::create([
                'jenis_id' => $jenis->id,
                'minimal' => null,
                'maksimal' => null,
                'bunga' => $this->bunga,
                'user_id' => $this->userLogin->id,
            ]);
        }

        /* ==========================
     * 3. SIMPAN KODE TRANSAKSI
     * ========================== */
        foreach ($this->kodeRows as $row) {

            $kode = SimpananKode::where('kode', $row['kode'])->first();

            if ($kode) {
                SimpananJenisKode::create([
                    'jenis_id' => $jenis->id,
                    'kode_id' => $kode->id,
                    'user_id' => $this->userLogin->id,
                ]);
            }
        }

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Produk Simpanan berhasil dibuat',
            'icon' => 'success',
        ]);

        return redirect()->route('superadmin.simpanan.produk-simpanan');
    }


    /* =======================
     * RENDER
     * ======================= */
    public function render()
    {
        return view('livewire.superadmin.simpananproduk.create', [
            'title' => 'Tambah Produk Simpanan',
        ]);
    }
}
