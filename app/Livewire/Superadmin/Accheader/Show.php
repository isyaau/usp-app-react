<?php

namespace App\Livewire\Superadmin\Accheader;

use Livewire\Component;
use App\Models\AccGroup;
use App\Models\AccHeader;
use Livewire\Attributes\Title;

class Show extends Component
{
    #[Title('Detail Header')]
    public $headerId;
    public $header;

    public $group_id;
    public $nama;
    public $no_header;
    public $keterangan;
    public $jenis;

    /* =======================
     * PROPERTIES TAMBAHAN
     * ======================= */
    public $group;       // semua group untuk dropdown atau referensi
    public $group_name;  // nama group dari header
    public $radioItems = [];
    public $userLogin;

    public function mount($id)
    {
        // Ambil header
        $this->header = AccHeader::findOrFail($id);
        $this->headerId = $id;

        // Isi properti dari header
        $this->group_id   = $this->header->group_id;
        $this->nama       = $this->header->nama;
        $this->no_header  = $this->header->no_header;
        $this->keterangan = $this->header->keterangan;
        $this->jenis      = $this->header->jenis;

        // Ambil group terkait
        $group = AccGroup::find($this->header->group_id);
        $this->group_name = $group?->nama;

        // Semua group (misal untuk dropdown)
        $this->group = AccGroup::orderBy('nama', 'asc')->get();

        // User login
        $this->userLogin = auth()->user();
    }

    public function render()
    {
        return view('livewire.superadmin.accheader.show', [
            'title'  => 'Detail Header',
            'header' => $this->header, // kirim data header ke view
        ]);
    }
}
