<?php

namespace App\Livewire\Superadmin\User;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;

class Show extends Component
{
    #[Title('Detail User')]
    public $userId;
    public $user;

    public function mount($id)
    {
        $this->userId = $id;
        $this->user = User::findOrFail($id); // Ambil data user sesuai id
    }

    public function render()
    {
        return view('livewire.superadmin.user.show', [
            'title' => 'Detail User',
            'user' => $this->user, // kirim data user ke view
        ]);
    }
}
