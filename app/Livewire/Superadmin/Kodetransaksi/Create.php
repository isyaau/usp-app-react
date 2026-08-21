<?php

namespace App\Livewire\Superadmin\Kodetransaksi;

use App\Models\Account;
use App\Models\AccHeader;
use App\Models\SimpananKode;
use Livewire\Component;
use Illuminate\Validation\Rule;

class Create extends Component
{
    public $userLogin, $kode, $nama, $account_kredit, $account_debet;
    public $setoran = false;
    public $tarikan = false;
    public $transfer = false;
    public $pokok = false;
    public $wajib = false;
    public $sukarela = false;
    public $pinjaman = false;
    public $saham = false;
    public $pokok_pinjaman = false;
    public $rencana = false;
    public $keterangan;
    public $debet = [];
    public $kredit = [];

    /* =======================
     * MOUNT
     * ======================= */
    public function mount()
    {
        $this->userLogin = auth()->user();
        $this->debet = Account::where('tipe', 'Debet')->orderBy('no_account')->get();
        $this->kredit = Account::where('tipe', 'Kredit')->orderBy('no_account')->get();
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
            'account_debet.required' => 'Account Debet wajib dipilih.',
            'account_debet.exists' => 'Account Debet tidak valid.',
            'account_kredit.required' => 'Account Kredit wajib dipilih.',
            'account_kredit.exists' => 'Account Kredit tidak valid.',

        ];
    }

    /* =======================
     * STORE
     * ======================= */
    public function store()
    {
        $validated = $this->validate([
            'kode' => ['required', 'string', 'max:255', 'unique:simpanan_kode,kode'],
            'nama' => ['required', 'string', 'max:255', 'unique:simpanan_kode,nama'],
            'account_debet' => ['required', 'exists:account,id'],
            'account_kredit' => ['required', 'exists:account,id'],

            // checkbox
            'setoran' => ['nullable', 'boolean'],
            'tarikan' => ['nullable', 'boolean'],
            'transfer' => ['nullable', 'boolean'],
            'pokok' => ['nullable', 'boolean'],
            'wajib' => ['nullable', 'boolean'],
            'sukarela' => ['nullable', 'boolean'],
            'pinjaman' => ['nullable', 'boolean'],
            'saham' => ['nullable', 'boolean'],
            'pokok_pinjaman' => ['nullable', 'boolean'],
            'rencana' => ['nullable', 'boolean'],

            'keterangan' => ['nullable', 'string'],
        ]);


        SimpananKode::create([
            'kode' => $validated['kode'],
            'nama' => $validated['nama'],
            'account_debet' => $validated['account_debet'],
            'account_kredit' => $validated['account_kredit'],

            // boolean → 0 / 1
            'setoran' => $validated['setoran'] ? 1 : 0,
            'tarikan' => $validated['tarikan'] ? 1 : 0,
            'transfer' => $validated['transfer'] ? 1 : 0,
            'pokok' => $validated['pokok'] ? 1 : 0,
            'wajib' => $validated['wajib'] ? 1 : 0,
            'sukarela' => $validated['sukarela'] ? 1 : 0,
            'pinjaman' => $validated['pinjaman'] ? 1 : 0,
            'saham' => $validated['saham'] ? 1 : 0,
            'pokok_pinjaman' => $validated['pokok_pinjaman'] ? 1 : 0,
            'rencana' => $validated['rencana'] ? 1 : 0,

            'keterangan' => $validated['keterangan'],
            'user_id' => $this->userLogin->id,
        ]);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Account berhasil dibuat!',
            'icon' => 'success',
        ]);

        return redirect()->route('superadmin.simpanan.kode-transaksi');
    }

    /* =======================
     * RENDER
     * ======================= */
    public function render()
    {
        return view('livewire.superadmin.kodetransaksi.create', [
            'title' => 'Tambah Kode Transaksi',
        ]);
    }
}
