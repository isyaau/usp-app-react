<?php

namespace App\Livewire\Superadmin\Kodetransaksi;

use App\Models\Account;
use App\Models\SimpananKode;
use Livewire\Component;
use Illuminate\Validation\Rule;

class Edit extends Component
{
    public $userLogin;
    public $simpananKodeId;

    // form fields
    public $kode;
    public $nama;
    public $accountDebet;
    public $accountKredit;

    // checkbox
    public bool $setoran = false;
    public bool $tarikan = false;
    public bool $transfer = false;
    public bool $pokok = false;
    public bool $wajib = false;
    public bool $sukarela = false;
    public bool $pinjaman = false;
    public bool $saham = false;
    public bool $pokok_pinjaman = false;
    public bool $rencana = false;

    public $keterangan;

    // option select
    public $debet = [];
    public $kredit = [];

    /* =======================
     * MOUNT
     * ======================= */
    public function mount($id)
    {
        $this->userLogin = auth()->user();

        $this->debet  = Account::where('tipe', 'Debet')->orderBy('no_account')->get();
        $this->kredit = Account::where('tipe', 'Kredit')->orderBy('no_account')->get();

        $simpanan = SimpananKode::findOrFail($id);

        $this->simpananKodeId = $simpanan->id;

        // fill form
        $this->kode = $simpanan->kode;
        $this->nama = $simpanan->nama;
        $this->accountDebet  = $simpanan->account_debet;
        $this->accountKredit = $simpanan->account_kredit;

        // boolean casting
        $this->setoran = (bool) $simpanan->setoran;
        $this->tarikan = (bool) $simpanan->tarikan;
        $this->transfer = (bool) $simpanan->transfer;
        $this->pokok = (bool) $simpanan->pokok;
        $this->wajib = (bool) $simpanan->wajib;
        $this->sukarela = (bool) $simpanan->sukarela;
        $this->pinjaman = (bool) $simpanan->pinjaman;
        $this->saham = (bool) $simpanan->saham;
        $this->pokok_pinjaman = (bool) $simpanan->pokok_pinjaman;
        $this->rencana = (bool) $simpanan->rencana;

        $this->keterangan = $simpanan->keterangan;
    }

    /* =======================
     * VALIDATION
     * ======================= */
    protected function rules()
    {
        return [
            'kode' => [
                'required',
                'string',
                'max:255',
                Rule::unique('simpanan_kode', 'kode')->ignore($this->simpananKodeId),
            ],
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('simpanan_kode', 'nama')->ignore($this->simpananKodeId),
            ],
            'accountDebet'  => ['required', 'integer', 'exists:account,id'],
            'accountKredit' => ['required', 'integer', 'exists:account,id'],

            'setoran' => 'boolean',
            'tarikan' => 'boolean',
            'transfer' => 'boolean',
            'pokok'   => 'boolean',
            'wajib'   => 'boolean',
            'sukarela' => 'boolean',
            'pinjaman' => 'boolean',
            'saham'   => 'boolean',
            'pokok_pinjaman' => 'boolean',
            'rencana' => 'boolean',

            'keterangan' => 'nullable|string',
        ];
    }

    protected function messages()
    {
        return [
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode sudah digunakan.',
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique' => 'Nama sudah digunakan.',
            'accountDebet.required' => 'Account Debet wajib dipilih.',
            'accountKredit.required' => 'Account Kredit wajib dipilih.',
        ];
    }

    /* =======================
     * UPDATE
     * ======================= */
    public function update()
    {
        $this->validate();

        $simpanan = SimpananKode::findOrFail($this->simpananKodeId);

        $simpanan->update([
            'kode' => $this->kode,
            'nama' => $this->nama,
            'account_debet'  => $this->accountDebet,
            'account_kredit' => $this->accountKredit,

            'setoran' => $this->setoran ? 1 : 0,
            'tarikan' => $this->tarikan ? 1 : 0,
            'transfer' => $this->transfer ? 1 : 0,
            'pokok'   => $this->pokok ? 1 : 0,
            'wajib'   => $this->wajib ? 1 : 0,
            'sukarela' => $this->sukarela ? 1 : 0,
            'pinjaman' => $this->pinjaman ? 1 : 0,
            'saham'   => $this->saham ? 1 : 0,
            'pokok_pinjaman' => $this->pokok_pinjaman ? 1 : 0,
            'rencana' => $this->rencana ? 1 : 0,

            'keterangan' => $this->keterangan,
            'user_id' => $this->userLogin->id,
        ]);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Kode transaksi berhasil diperbarui.',
            'icon' => 'success',
        ]);

        return redirect()->route('superadmin.simpanan.kode-transaksi');
    }

    /* =======================
     * RENDER
     * ======================= */
    public function render()
    {
        return view('livewire.superadmin.kodetransaksi.edit', [
            'title' => 'Edit Kode Transaksi',
        ]);
    }
}
