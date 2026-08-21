<?php

namespace App\Livewire\Superadmin\Account;

use App\Models\Account;
use App\Models\AccHeader;
use Livewire\Component;
use Illuminate\Validation\Rule;

class Edit extends Component
{
    public $userLogin;

    public $account_id;

    public $header_id;
    public $no_header_prefix;
    public $no_account;

    public $nama;
    public $tipe;

    public $headers;

    /* =======================
     * MOUNT
     * ======================= */
    public function mount($id)
    {
        $account = Account::findOrFail($id);

        $this->account_id = $account->id;
        $this->header_id  = $account->header_id;

        $header = AccHeader::find($account->header_id);
        $this->no_header_prefix = $header?->no_header;

        // Pisahkan nomor account (232-001 → 001)
        if (str_contains($account->no_account, '-')) {
            [, $this->no_account] = explode('-', $account->no_account);
        } else {
            $this->no_account = $account->no_account;
        }

        $this->nama = $account->nama;
        $this->tipe = $account->tipe; // FIX

        $this->headers = AccHeader::orderBy('no_header')->get();
        $this->userLogin = auth()->user();
    }

    /* =======================
     * HEADER CHANGE
     * ======================= */
    public function updatedHeaderId($value)
    {
        $header = AccHeader::find($value);

        if ($header) {
            $this->no_header_prefix = $header->no_header;
            $this->no_account = null;
        }
    }

    /* =======================
     * VALIDATION MESSAGE
     * ======================= */
    protected function messages()
    {
        return [
            'header_id.required' => 'Header akun harus dipilih.',
            'header_id.exists'   => 'Header akun tidak ditemukan.',
            'no_account.required' => 'Nomor akun harus diisi.',
            'no_account.numeric' => 'Nomor akun harus berupa angka.',
            'no_account.unique'  => 'Nomor akun sudah digunakan.',
            'nama.required'      => 'Nama akun harus diisi.',
            'nama.string'        => 'Nama akun harus berupa teks.',
            'nama.max'           => 'Nama akun maksimal 255 karakter.',
            'nama.unique'        => 'Nama akun sudah digunakan.',
            'tipe.required'      => 'Tipe akun harus dipilih.',
        ];
    }

    /* =======================
     * UPDATE
     * ======================= */
    public function update()
    {
        $this->validate([
            'header_id' => ['required', 'exists:acc_header,id'],
            'no_account' => ['required', 'numeric'],
            'nama' => ['required', 'string', 'max:255'],
            'tipe' => ['required', Rule::in(['Debet', 'Kredit'])],
        ]);

        // Gabungkan nomor account
        $fullNoAccount = $this->no_header_prefix . '-' . str_pad($this->no_account, 2, '0', STR_PAD_LEFT);

        // VALIDASI MANUAL: cek duplicate
        $exists = Account::where('no_account', $fullNoAccount)
            ->where('id', '!=', $this->account_id)
            ->exists();

        if ($exists) {
            $this->addError('no_account', 'Nomor account sudah digunakan.');
            return;
        }

        // UPDATE DATA
        Account::where('id', $this->account_id)->update([
            'header_id' => $this->header_id,
            'no_account' => $fullNoAccount,
            'nama'      => $this->nama,
            'tipe'     => $this->tipe,
            'user_id'   => $this->userLogin->id,
        ]);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text'  => 'Account berhasil diperbarui!',
            'icon'  => 'success',
        ]);

        return redirect()->route('superadmin.account');
    }


    /* =======================
     * RENDER
     * ======================= */
    public function render()
    {
        return view('livewire.superadmin.account.edit', [
            'title' => 'Edit Account',
        ]);
    }
}
