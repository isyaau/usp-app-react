<?php

namespace App\Livewire\Superadmin\Marketing;

use App\Models\Account;
use App\Models\Kantor;
use App\Models\Marketing;
use App\Models\SimpananKode;
use Livewire\Component;
use Illuminate\Validation\Rule;

class Edit extends Component
{
    public $userLogin;
    public $marketingId;

    // form fields
    public $kode;
    public $nama;
    public $alamat;
    public $no_ktp;
    public $telepon;
    public $no_hp;
    public $kantor_id;
    public $accountKredit;

    // checkbox
    public bool $aktif = false;
    public $keterangan;

    // option select
    public $kantors = [];
    public $kredit = [];

    /* =======================
     * MOUNT
     * ======================= */
    public function mount($id)
    {
        $this->userLogin = auth()->user();

        $this->kantors  = Kantor::orderBy('kode')->get();


        $marketing = Marketing::findOrFail($id);

        $this->marketingId = $marketing->id;

        // fill form
        $this->kode = $marketing->kode;
        $this->nama = $marketing->nama;
        $this->alamat = $marketing->alamat;
        $this->no_ktp = $marketing->no_ktp;
        $this->no_hp = $marketing->no_hp;
        $this->telepon = $marketing->telepon;
        $this->kantor_id  = $marketing->kantor_id;


        // boolean casting
        $this->aktif = (bool) $marketing->aktif;
    }

    /* =======================
     * VALIDATION
     * ======================= */
    protected function rules()
    {
        return [
            'kode' => [
                'required',
                'string',
                'max:255',
                Rule::unique('marketing', 'kode')->ignore($this->marketingId),
            ],
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('marketing', 'nama')->ignore($this->marketingId),
            ],
            'kantor_id'  => ['required', 'integer', 'exists:kantor,id'],
            'aktif' => 'boolean',
            'alamat' => 'nullable|string|max:255',
            'no_ktp' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:255',
        ];
    }

    protected function messages()
    {
        return [
            'kode.required' => 'Kode wajib diisi.',
            'kode.unique' => 'Kode sudah digunakan.',
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique' => 'Nama sudah digunakan.',
            'kantor_id.required' => 'Kantor wajib dipilih.',
            'kantor_id.exists' => 'Kantor tidak valid.',
            'alamat.max' => 'Alamat maksimal 255 karakter.',
            'no_ktp.max' => 'No KTP maksimal 255 karakter.',
            'telepon.max' => 'Telepon maksimal 255 karakter.',
            'no_hp.max' => 'No HP maksimal 255 karakter.',
        ];
    }

    /* =======================
     * UPDATE
     * ======================= */
    public function update()
    {
        $this->validate();

        $marketing = Marketing::findOrFail($this->marketingId);

        $marketing->update([
            'kode' => $this->kode,
            'nama' => $this->nama,
            'alamat' => $this->alamat,
            'no_ktp' => $this->no_ktp,
            'telepon' => $this->telepon,
            'no_hp' => $this->no_hp,
            'kantor_id' => $this->kantor_id,
            'aktif' => $this->aktif ? 1 : 0,
            'user_id' => $this->userLogin->id,
        ]);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Marketing berhasil diperbarui.',
            'icon' => 'success',
        ]);

        return redirect()->route('superadmin.marketing');
    }

    /* =======================
     * RENDER
     * ======================= */
    public function render()
    {
        return view('livewire.superadmin.marketing.edit', [
            'title' => 'Edit Marketing',
        ]);
    }
}
