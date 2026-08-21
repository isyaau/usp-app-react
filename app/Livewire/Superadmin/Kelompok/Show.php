<?php

namespace App\Livewire\Superadmin\Kelompok;

use App\Models\Kelompok;
use Livewire\Component;
use Livewire\Attributes\Title;

class Show extends Component
{
    #[Title('Detail Kelompok')]
    public $kelompokId;
    public $kelompok;

    public function mount($id)
    {
        $this->kelompokId = $id;
        $this->kelompok = Kelompok::findOrFail($id); // Ambil data kelompok sesuai id
    }

    public function render()
    {
        return view('livewire.superadmin.kelompok.show', [
            'title' => 'Detail Kelompok',
            'kelompok' => $this->kelompok, // kirim data kelompok ke view
        ]);
    }
}
