<?php

namespace App\Livewire\Superadmin\Kodetransaksi;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\SimpananKode;
use App\Models\Account;

class Show extends Component
{
    #[Title('Detail Kode Transaksi')]
    public $userLogin;
    public $data; // semua data disimpan di sini

    public function mount($id)
    {
        $this->userLogin = auth()->user();

        $simpanan = SimpananKode::with(['debetAccount', 'kreditAccount'])->findOrFail($id);

        $this->data = (object)[
            'id' => $simpanan->id,
            'kode' => $simpanan->kode,
            'nama' => $simpanan->nama,
            'accountDebet' => $simpanan->debetAccount,
            'accountKredit' => $simpanan->kreditAccount,
            'setoran' => (bool) $simpanan->setoran,
            'tarikan' => (bool) $simpanan->tarikan,
            'transfer' => (bool) $simpanan->transfer,
            'pokok' => (bool) $simpanan->pokok,
            'wajib' => (bool) $simpanan->wajib,
            'sukarela' => (bool) $simpanan->sukarela,
            'pinjaman' => (bool) $simpanan->pinjaman,
            'saham' => (bool) $simpanan->saham,
            'pokok_pinjaman' => (bool) $simpanan->pokok_pinjaman,
            'rencana' => (bool) $simpanan->rencana,
            'keterangan' => $simpanan->keterangan,
            'created_at' => $simpanan->created_at,
            'updated_at' => $simpanan->updated_at,
        ];
    }


    public function render()
    {
        return view('livewire.superadmin.kodetransaksi.show', [
            'title' => 'Detail Kode Transaksi',
            'data'  => $this->data,
        ]);
    }
}
