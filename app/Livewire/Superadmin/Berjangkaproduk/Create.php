<?php

namespace App\Livewire\Superadmin\Berjangkaproduk;

use App\Models\Account;
use App\Models\AccHeader;
use App\Models\DepositoJenis;
use App\Models\SimpananKode;
use Livewire\Component;
use Illuminate\Validation\Rule;

class Create extends Component
{
    public $userLogin, $kode, $nama, $account_id, $jangka_waktu, $bunga, $account_bunga, $rumus_bunga, $penalti, $account_penalti, $pajak, $account_pajak, $saldo_pajak, $insentif;
    public $rumus = false;
    public $accounts = [];
    public $bungas = [];
    public $penalties = [];
    public $pajaks = [];


    /* =======================
     * MOUNT
     * ======================= */
    public function mount()
    {
        $this->userLogin = auth()->user();
        $this->accounts = Account::where('tipe', 'Debet')->orderBy('no_account')->get();
        $this->bungas = Account::where('tipe', 'Debet')->orderBy('no_account')->get();
        $this->penalties = Account::where('tipe', 'Debet')->orderBy('no_account')->get();
        $this->pajaks = Account::where('tipe', 'Debet')->orderBy('no_account')->get();
    }


    /* =======================
     * VALIDATION MESSAGE
     * ======================= */
    protected function messages()
    {
        return [
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode sudah digunakan.',

            'nama.required' => 'Nama wajib diisi.',
            'nama.unique' => 'Nama sudah digunakan.',

            'account_id.required' => 'Account wajib dipilih.',
            'account_id.exists' => 'Account tidak valid.',

            'account_bunga.required' => 'Account bunga wajib dipilih.',
            'account_bunga.exists' => 'Account bunga tidak valid.',

            'account_pajak.required' => 'Account pajak wajib dipilih.',
            'account_pajak.exists' => 'Account pajak tidak valid.',

            'account_penalti.required' => 'Account penalti wajib dipilih.',
            'account_penalti.exists' => 'Account penalti tidak valid.',
        ];
    }


    /* =======================
     * STORE
     * ======================= */
    public function store()
    {
        $validated = $this->validate([
            'kode' => ['required', 'string', 'max:255', 'unique:deposito_jenis,kode'],
            'nama' => ['required', 'string', 'max:255', 'unique:deposito_jenis,nama'],
            'account_id' => ['required', 'exists:account,id'],
            'account_bunga' => ['required', 'exists:account,id'],
            'account_pajak' => ['required', 'exists:account,id'],
            'account_penalti' => ['required', 'exists:account,id'],

            'rumus_bunga' => ['nullable', 'string'],
            'jangka_waktu' => ['nullable', 'string'],
            'bunga' => ['nullable', 'string'],
            'rumus_bunga' => ['nullable', 'string'],
            'penalti' => ['nullable', 'string'],
            'pajak' => ['nullable', 'string'],
            'saldo_pajak' => ['nullable', 'string'],
            'insentif' => ['nullable', 'string'],
        ]);


        DepositoJenis::create([
            'kode' => $validated['kode'],
            'nama' => $validated['nama'],
            'account_id' => $validated['account_id'],
            'jangka_waktu' => $validated['jangka_waktu'],
            'bunga' => $validated['bunga'],
            'account_bunga' => $validated['account_bunga'],
            'rumus_bunga' => $validated['rumus_bunga'],
            'penalti' => $validated['penalti'],
            'account_penalti' => $validated['account_penalti'],
            'pajak' => $validated['pajak'],
            'account_pajak' => $validated['account_pajak'],
            'saldo_pajak' => $validated['saldo_pajak'],
            'insentif' => $validated['insentif'],
            'user_id' => $this->userLogin->id,
        ]);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Produk Simpanan Berjangka berhasil dibuat!',
            'icon' => 'success',
        ]);

        return redirect()->route('superadmin.simpanan-berjangka.produk');
    }

    /* =======================
     * RENDER
     * ======================= */
    public function render()
    {
        return view('livewire.superadmin.berjangkaproduk.create', [
            'title' => 'Tambah Produk Simpanan Berjangka',
        ]);
    }
}
