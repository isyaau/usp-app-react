<?php

namespace App\Livewire\Superadmin\Account;

use Livewire\Component;
use App\Models\Account;

class Show extends Component
{
    public $accountId;
    public $account;

    public $header_name;
    public $nama;
    public $no_account;
    public $tipe; // <-- field Account yang benar

    public $userLogin;

    public function mount($id)
    {
        // Ambil account lengkap dengan relasi header
        $this->account = Account::with('header')->findOrFail($id);
        $this->accountId = $id;

        // Isi properti dari account
        $this->nama       = $this->account->nama;
        $this->no_account = $this->account->no_account;
        $this->tipe       = $this->account->tipe; // <-- field yang benar
        $this->header_name = $this->account->header?->nama ?? '-'; // relasi header

        // User login
        $this->userLogin = auth()->user();
    }

    public function render()
    {
        return view('livewire.superadmin.account.show', [
            'title'   => 'Detail Account',
            'account' => $this->account,
        ]);
    }
}
