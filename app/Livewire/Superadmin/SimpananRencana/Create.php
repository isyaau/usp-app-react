<?php

namespace App\Livewire\Superadmin\SimpananRencana;

use App\Models\User;
use App\Models\Kantor;
use App\Models\Simpanan;
use App\Models\SimpananJenis;
use App\Models\SimpananRencana;
use App\Models\SimpananRencanaDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Title;
use SweetAlert2\Laravel\Traits\WithSweetAlert;

#[Title('Tambah Simpanan Rencana')]
class Create extends Component
{
    use WithSweetAlert;

    /* ===============================
    |  PROPERTIES
    |===============================*/

    public $kantors;
    public $userLogin;

    // Search Simpanan
    public $search = '';
    public $jenisSimpananId = '';

    // User autocomplete
    public $query = '';
    public $users = [];
    public $showDropdown = false;
    public $selectedUser = null;
    public $ketua_id = null;

    // Rencana ID
    public $simpananRencanaId;
    public $selectedSimpanan = [];
    public $selectAll = false;
    public $detailSementara = [];

    // Field input
    public $tanggal_mulai;
    public $tanggal_jatuhtempo;
    public $no_bukti;
    public $jangka_waktu;
    public $satuan;
    public $bagi_hasil;
    public $nominal;
    public $bunga;
    public $keterangan;
    public $kantor_id;
    public $user_id;

    public $rencana_id;
    public $simpanan_id;

    /* ===============================
    |  MOUNT
    |===============================*/

    public function mount()
    {
        $this->userLogin = auth()->guard('web')->user();
        $this->kantors = Kantor::orderBy('nama_kantor')->get();
    }

    /* ===============================
    |  LIVE SEARCH USER
    |===============================*/

    public function updatedQuery()
    {
        $this->showDropdown = true;
        $this->users = User::where('nama', 'like', '%' . $this->query . '%')
            ->limit(8)
            ->get();
    }

    public function selectUser($id)
    {
        $user = User::find($id);
        if (!$user) return;

        $this->selectedUser = $user;
        $this->query = $user->nama;
        $this->ketua_id = $user->id;
        $this->showDropdown = false;
    }

    public function hideDropdown()
    {
        $this->showDropdown = false;
    }

    /* ===============================
    |  SIMPANAN LIST
    |===============================*/

    protected function getSimpananList()
    {
        return Simpanan::query()
            ->with(['jenis_simpanan:id,nama', 'anggota:id,nama,alamat'])
            ->when($this->search, function ($query) {
                $search = '%' . $this->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('no_rekening', 'like', $search)
                        ->orWhereHas('jenis_simpanan', fn($j) => $j->where('nama', 'like', $search))
                        ->orWhereHas('anggota', fn($a) => $a->where('nama', 'like', $search)
                            ->orWhere('alamat', 'like', $search));
                });
            })
            ->orderBy('no_rekening')
            ->get();
    }
    public function closeModal()
    {
        $this->selectedSimpanan = [];
    }


    protected function getJenisSimpananList()
    {
        return SimpananJenis::select('id', 'nama')->orderBy('nama')->get();
    }

    public function updatedSelectAll($value)
    {
        $this->selectedSimpanan = $value ? $this->getSimpananList()->pluck('id')->toArray() : [];
    }

    protected function isValidJenisSimpanan($simpananId): bool
    {
        if (!$this->jenisSimpananId) return true;

        return Simpanan::where('id', $simpananId)
            ->whereHas('produkSimpanan', fn($q) => $q->where('jenis_simpanan_id', $this->jenisSimpananId))
            ->exists();
    }

    /* ===============================
    |  STORE
    |===============================*/

    public function store()
    {
        $validated = $this->validate([
            'tanggal_mulai'           => 'required|date_format:d-m-Y',
            'tanggal_jatuhtempo'      => 'required|date_format:d-m-Y|after_or_equal:tanggal_mulai',
            'no_bukti'                => 'required|unique:simpanan_rencana,no_bukti',
            'jangka_waktu'            => 'required|numeric|min:1',
            'satuan'                  => 'required',
            'nominal'                 => 'required|numeric',
            'bagi_hasil'              => 'nullable|numeric',
            'kantor_id'               => 'required|exists:kantor,id',
            'keterangan'              => 'nullable|string',
        ]);

        if (empty($this->detailSementara)) {
            $this->dispatch('swal', [
                'title' => 'Peringatan!',
                'text'  => 'Silakan pilih minimal 1 simpanan.',
                'icon'  => 'warning',
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            // Simpan data utama SimpananRencana
            $rencana = SimpananRencana::create(array_merge($validated, [
                'user_id' => $this->userLogin->id,
                'bunga'   => $validated['bagi_hasil'] ?? 0,
            ]));

            // Simpan detail SimpananRencanaDetail dengan user_id juga
            foreach ($this->detailSementara as $simpananId) {
                if (Simpanan::find($simpananId)) {
                    SimpananRencanaDetail::create([
                        'rencana_id'  => $rencana->id,
                        'simpanan_id' => $simpananId,
                        'user_id'     => $this->userLogin->id, // tambahkan user_id di sini
                    ]);
                }
            }

            DB::commit();

            $this->dispatch('swal', [
                'title' => 'Berhasil!',
                'text'  => 'Simpanan Rencana berhasil dibuat!',
                'icon'  => 'success',
            ]);

            return redirect()->route('superadmin.simpanan.rencana');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', [
                'title' => 'Error!',
                'text'  => 'Terjadi kesalahan: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }


    /* ===============================
    |  DETAIL SIMPANAN
    |===============================*/

    public function addDetail()
    {
        if (!empty($this->selectedSimpanan)) {
            foreach ($this->selectedSimpanan as $id) {
                if (!in_array($id, $this->detailSementara)) {
                    $this->detailSementara[] = $id;
                }
            }
            $this->selectedSimpanan = [];
            $this->dispatch('closeModal', ['modalId' => 'modalPilihSimpanan']);
        }
    }

    public function removeDetail($index)
    {
        unset($this->detailSementara[$index]);
        $this->detailSementara = array_values($this->detailSementara);
    }

    /* ===============================
    |  RENDER
    |===============================*/

    public function render()
    {
        return view('livewire.superadmin.simpananrencana.create', [
            'title' => 'Tambah Kelompok',
            'simpananList'      => $this->getSimpananList(),
            'jenisSimpananList' => $this->getJenisSimpananList(),
        ]);
    }

    /* ===============================
    |  VALIDATION MESSAGES
    |===============================*/

    public function messages()
    {
        return [
            'tanggal_mulai.required'            => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date_format'         => 'Format tanggal mulai harus dd-mm-yyyy.',

            'tanggal_jatuhtempo.required'       => 'Tanggal jatuh tempo wajib diisi.',
            'tanggal_jatuhtempo.date_format'    => 'Format tanggal jatuh tempo harus dd-mm-yyyy.',
            'tanggal_jatuhtempo.after_or_equal' => 'Tanggal jatuh tempo harus sama atau lebih besar dari tanggal mulai.',

            'no_bukti.required'                 => 'No Bukti wajib diisi.',
            'no_bukti.unique'                   => 'No Bukti sudah digunakan.',

            'jangka_waktu.required'             => 'Jangka waktu wajib diisi.',
            'jangka_waktu.numeric'              => 'Jangka waktu harus berupa angka.',
            'jangka_waktu.min'                  => 'Jangka waktu minimal 1.',

            'satuan.required'                   => 'Satuan wajib dipilih.',

            'nominal.required'                  => 'Nominal wajib diisi.',
            'nominal.numeric'                   => 'Nominal harus berupa angka.',

            'bagi_hasil.numeric'                => 'Bagi hasil harus berupa angka.',

            'kantor_id.required'                => 'Kantor wajib dipilih.',
            'kantor_id.exists'                  => 'Kantor tidak ditemukan.',

            'keterangan.string'                 => 'Keterangan harus berupa teks.',
        ];
    }
}
