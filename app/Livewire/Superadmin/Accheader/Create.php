<?php

namespace App\Livewire\Superadmin\Accheader;

use App\Models\AccGroup;
use App\Models\AccHeader;
use Livewire\Attributes\Validate;

use Livewire\Component;

class Create extends Component
{
    public $userLogin;

    // Data dropdown
    public $cities = [];
    public $districts = [];
    public $villages = [];

    #[Validate('required|integer|exists:acc_group,id')]
    public $group_id;

    #[Validate('required|string|max:255|unique:acc_header,nama')]
    public $nama;

    #[Validate('required|string|max:50|unique:acc_header,no_header')]
    public $no_header;

    #[Validate('required|string')]
    public $keterangan;

    #[Validate('required|string')]
    public $jenis;



    public $group;


    public $group_name;
    public $radioItems = [];


    public function updatedGroupId($value)
    {
        $group = AccGroup::find($value);
        $this->group_name = $group?->nama;

        $this->jenis = null; // reset radio
        $this->radioItems = $this->getRadioByGroup($this->group_name);
    }

    public function updatedGroupName($value)
    {
        $this->jenis = null;
        $this->radioItems = $this->getRadioByGroup(trim($value));
    }

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





    public function render()
    {
        return view('livewire.superadmin.accheader.create', [
            'title' => 'Tambah Account Header',
        ]);
    }

    public function mount()
    {
        $this->userLogin = auth()->guard('web')->user();
        $this->group = AccGroup::all();
    }
    public function messages()
    {
        return [
            'group_id.required' => 'Group wajib diisi.',
            'group_id.exists' => 'Group tidak ditemukan di database.',

            'nama.required' => 'Nama Header wajib diisi.',
            'nama.unique' => 'Nama Header sudah ada di database.',

            'no_header.required' => 'Nomor Header wajib diisi.',
            'no_header.unique' => 'Nomor Header sudah ada di database.',

            'keterangan.required' => 'Keterangan wajib diisi.',

            'jenis.required' => 'Jenis wajib diisi.',
        ];
    }

    public function store()
    {
        $validated = $this->validate();

        $user_id = $this->userLogin->id;

        AccHeader::create([
            'group_id'   => $validated['group_id'],
            'nama'       => $validated['nama'],
            'no_header'  => $validated['no_header'],
            'keterangan' => $validated['keterangan'],
            'jenis'      => $validated['jenis'],
            'user_id'    => $user_id,
        ]);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Account Header berhasil dibuat!',
            'icon' => 'success',
            'confirmButtonText' => 'Oke'
        ]);

        return $this->redirect('/superadmin/account-header', navigate: true);
    }
}
