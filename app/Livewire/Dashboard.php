<?php

namespace App\Livewire;

use App\Models\AccGroup;
use App\Models\AccHeader;
use App\Models\Account;
use App\Models\Anggota;
use App\Models\Kantor;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kelompok;
use App\Models\User;

class Dashboard extends Component
{
    use WithPagination;

    public $userLogin;


    public function mount()
    {
        $this->userLogin = auth()->guard('web')->user();
    }

    public function render()
    {


        // Hitung total kelompok
        $totalKelompok = Kelompok::count();
        $totalUsers = User::count();
        $totalKantor = Kantor::count();
        $totalAnggota = Anggota::count();
        $totalAccgroup = AccGroup::count();
        $totalAccheader = AccHeader::count();
        $totalAccount = Account::count();

        return view('livewire.dashboard', [
            'title' => 'Dashboard',

            'totalKelompok' => $totalKelompok,
            'totalUsers' => $totalUsers,
            'totalKantor' => $totalKantor,
            'totalAnggota' => $totalAnggota,
            'totalAccgroup' => $totalAccgroup,
            'totalAccheader' => $totalAccheader,
            'totalAccount' => $totalAccount,
        ]);
    }
}
