<?php

namespace App\Livewire\Superadmin\Account;

use App\Models\Account;
use App\Models\AccHeader;
use Livewire\Component;
use Illuminate\Validation\Rule;

class Create extends Component
{
    public $userLogin, $header_id, $no_header_prefix, $no_account, $nama, $tipe, $header;

    /* =======================
     * MOUNT
     * ======================= */
    public function mount()
    {
        $this->userLogin = auth()->user();
        $this->header = AccHeader::orderBy('no_header')->get();
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
            'header_id.exists' => 'Header akun tidak ditemukan.',
            'no_account.required' => 'Nomor akun harus diisi.',
            'no_account.numeric' => 'Nomor akun harus berupa angka.',
            'no_account.unique' => 'Nomor akun sudah digunakan.',
            'nama.required' => 'Nama akun harus diisi.',
            'nama.string' => 'Nama akun harus berupa teks.',
            'nama.max' => 'Nama akun maksimal 255 karakter.',
            'nama.unique' => 'Nama akun sudah digunakan.',
            'tipe.required' => 'Tipe akun harus dipilih.',
        ];
    }

    /* =======================
     * STORE
     * ======================= */
    public function store()
    {
        $this->validate([
            'header_id' => ['required', 'exists:acc_header,id'],
            'no_account' => ['required', 'numeric'],
            'nama' => ['required', 'string', 'max:255', 'unique:account,nama'],
            'tipe' => ['required', Rule::in(['Debet', 'Kredit'])],
        ]);

        // Gabungkan nomor account
        $fullNoAccount = $this->no_header_prefix . '-' . str_pad(
            $this->no_account,
            2,
            '0',
            STR_PAD_LEFT
        );

        // 🔒 VALIDASI UNIQUE FULL no_account
        if (Account::where('no_account', $fullNoAccount)->exists()) {
            $this->addError('no_account', 'Nomor akun sudah digunakan.');
            return;
        }

        Account::create([
            'header_id' => $this->header_id,
            'no_account' => $fullNoAccount,
            'nama' => $this->nama,
            'tipe' => ucfirst($this->tipe),
            'user_id' => $this->userLogin->id,
        ]);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Account berhasil dibuat!',
            'icon' => 'success',
        ]);

        return redirect()->route('superadmin.account');
    }

    /* =======================
     * RENDER
     * ======================= */
    public function render()
    {
        return view('livewire.superadmin.account.create', [
            'title' => 'Tambah Account',
        ]);
    }
}
