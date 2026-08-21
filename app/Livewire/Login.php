<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use SweetAlert2\Laravel\Traits\WithSweetAlert;

class Login extends Component
{
    use WithSweetAlert;


    #[Layout('components.layouts.auth')]


    public $email;

    public $password;

    // Validasi input
    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];


    public function render()
    {
        return view('livewire.login');
    }

    public function login()
    {
        $this->validate();

        if (Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ])) {
            return $this->redirect('/superadmin/dashboard', navigate: true);
        };
        session()->flash('swal', [
            'icon' => 'error',
            'title' => 'Gagal!',
            'text' => 'Email atau password yang Anda masukkan salah.',
        ]);
        return $this->redirect('/login', navigate: true);
    }
}
