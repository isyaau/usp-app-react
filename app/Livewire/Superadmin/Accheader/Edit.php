<?php

namespace App\Livewire\Superadmin\Accheader;

use App\Models\AccGroup;
use App\Models\AccHeader;
use Livewire\Component;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    /* =======================
     * PROPERTIES FORM
     * ======================= */

    public $headerId;

    #[Validate('required|integer|exists:acc_group,id')]
    public $group_id;

    #[Validate('required|string|max:255')]
    public $nama;

    #[Validate('required|string|max:50')]
    public $no_header;

    #[Validate('required|string')]
    public $keterangan;

    #[Validate('required|string')]
    public $jenis;

    /* =======================
     * PROPERTIES TAMBAHAN
     * ======================= */

    public $group;
    public $group_name;
    public $radioItems = [];
    public $userLogin;

    /* =======================
     * MOUNT (LOAD DATA EDIT)
     * ======================= */

    public function mount($id)
    {
        $header = AccHeader::findOrFail($id);
        $this->headerId = $id;

        // Isi form
        $this->group_id   = $header->group_id;
        $this->nama       = $header->nama;
        $this->no_header  = $header->no_header;
        $this->keterangan = $header->keterangan;
        $this->jenis      = $header->jenis;

        // Ambil grup & radio
        $group = AccGroup::find($header->group_id);
        $this->group_name = $group?->nama;
        $this->radioItems = $this->getRadioByGroup($this->group_name);

        // Dropdown grup
        $this->group = AccGroup::orderBy('nama', 'asc')->get();

        // User login
        $this->userLogin = auth()->user();
    }

    /* =======================
     * UPDATE GROUP (DROPDOWN)
     * ======================= */

    public function updatedGroupId($value)
    {
        $group = AccGroup::find($value);
        $this->group_name = $group?->nama;

        $this->jenis = null; // reset radio
        $this->radioItems = $this->getRadioByGroup($this->group_name);
    }

    /* =======================
     * RADIO BY GROUP
     * ======================= */

    protected function getRadioByGroup($groupName)
    {
        $groupName = strtoupper($groupName);
        $items = [];

        if (in_array($groupName, ['AKTIVA LANCAR', 'AKTIVA TETAP', 'AKTIVA LAIN'])) {
            $items['Aktiva'] = [
                ['value' => 'Kas', 'label' => 'Kas'],
                ['value' => 'Bank', 'label' => 'Bank'],
                ['value' => 'Tabungan & Simpanan Berjangka', 'label' => 'Tabungan & Simpanan Berjangka'],
                ['value' => 'Surat-surat berharga', 'label' => 'Surat-surat berharga'],
                ['value' => 'Piutang', 'label' => 'Piutang'],
                ['value' => 'Pinjaman yang diberikan', 'label' => 'Pinjaman yang diberikan'],
                ['value' => 'BMPP kepada calon anggota, koperasi lain dan anggotanya', 'label' => 'BMPP kepada calon anggota, koperasi lain dan anggotanya'],
                ['value' => 'Pendapatan yang masih harus diterima', 'label' => 'Pendapatan yang masih harus diterima'],
                ['value' => 'Penyertaan pada non koperasi', 'label' => 'Penyertaan pada non koperasi'],
                ['value' => 'Aktiva Tetap', 'label' => 'Aktiva Tetap'],
            ];
        }

        if (in_array($groupName, ['HUTANG LANCAR', 'HUTANG JANGKA PANJANG'])) {
            $items['Kewajiban'] = [
                ['value' => 'Kewajiban Tertimbang', 'label' => 'Kewajiban Tertimbang'],
            ];
        }

        if (in_array($groupName, ['MODAL'])) {
            $items['Modal'] = [
                ['value' => 'Modal Anggota', 'label' => 'Modal Anggota'],
                ['value' => 'Modal Penyetaraan', 'label' => 'Modal Penyetaraan'],
                ['value' => 'Modal Penyertaan', 'label' => 'Modal Penyertaan'],
                ['value' => 'Cadangan Umum', 'label' => 'Cadangan Umum'],
                ['value' => 'Cadangan Tujuan Resiko', 'label' => 'Cadangan Tujuan Resiko'],
                ['value' => 'Modal Sumbangan', 'label' => 'Modal Sumbangan'],
                ['value' => 'SHU Yang belum dibagi', 'label' => 'SHU Yang belum dibagi'],
            ];
        }

        if (in_array($groupName, ['PENDAPATAN'])) {
            $items['Pendapatan'] = [
                ['value' => 'Partisipasi Anggota', 'label' => 'Partisipasi Anggota'],
            ];
        }
        if (in_array($groupName, ['BIAYA'])) {
            $items['Biaya'] = [
                ['value' => 'Biaya Operasional', 'label' => 'Biaya Operasional'],
                ['value' => 'Gaji dan Honorarium Karyawan', 'label' => 'Gaji dan Honorarium Karyawan'],
            ];
        }

        if (in_array($groupName, ['AKTIVA LANCAR', 'AKTIVA TETAP', 'AKTIVA LAIN', 'HUTANG LANCAR', 'HUTANG JANGKA PANJANG', 'MODAL'])) {
            $items['Cadangan'] = [
                ['value' => 'Cadangan Penghapusan Pinjaman', 'label' => 'Cadangan Penghapusan Pinjaman'],
                ['value' => 'Cadangan Penghapusan Pinjaman dari SHU', 'label' => 'Cadangan Penghapusan Pinjaman dari SHU'],
            ];
        }

        return $items;
    }

    /* =======================
     * VALIDATION MESSAGE
     * ======================= */

    public function messages()
    {
        return [
            'group_id.required' => 'Grup akun wajib dipilih.',
            'nama.required' => 'Nama akun wajib diisi.',
            'no_header.required' => 'Nomor header wajib diisi.',
            'jenis.required' => 'Jenis akun wajib dipilih.',
            'keterangan.required' => 'Keterangan wajib diisi.',
        ];
    }

    /* =======================
     * UPDATE DATA
     * ======================= */

    public function update()
    {
        $this->validate([
            'group_id'  => 'required|integer|exists:acc_group,id',
            'nama'      => 'required|string|max:255|unique:acc_header,nama,' . $this->headerId,
            'no_header' => 'required|string|max:50|unique:acc_header,no_header,' . $this->headerId,
            'keterangan' => 'required|string',
            'jenis'     => 'required|string',
        ]);

        $header = AccHeader::findOrFail($this->headerId);

        $header->update([
            'group_id'   => $this->group_id,
            'nama'       => $this->nama,
            'no_header'  => $this->no_header,
            'keterangan' => $this->keterangan,
            'jenis'      => $this->jenis,
            'user_id'    => $this->userLogin->id,
        ]);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Account Header berhasil diperbarui!',
            'icon' => 'success',
        ]);

        return $this->redirect('/superadmin/account-header', navigate: true);
    }

    /* =======================
     * RENDER
     * ======================= */

    public function render()
    {
        return view('livewire.superadmin.accheader.edit', [
            'title' => 'Edit Account Header',
        ]);
    }
}
