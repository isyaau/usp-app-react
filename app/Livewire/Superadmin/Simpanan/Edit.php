<?php

namespace App\Livewire\Superadmin\Simpanan;

use Carbon\Carbon;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\Marketing;
use App\Models\Simpanan;
use App\Models\SimpananJenis;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\PngEncoder;
use Livewire\WithFileUploads;
use SweetAlert2\Laravel\Traits\WithSweetAlert;

#[Title('Edit Simpanan')]
class Edit extends Component
{
    use WithSweetAlert, WithFileUploads;

    public Simpanan $simpanan;

    public $created_at;
    public $no_rekening;
    public $anggota_id;
    public $jenis_id;
    public $marketing_id;
    public $qq;
    public $bunga;
    public $nominal_blokir;
    public $tgl_blokir;
    public $nominal_setor;
    public $kantor_id;

    public $aktif;
    public $blokir_simpanan;
    public $blokir_nominal;
    public $blokir_tgl;
    public $sms;

    public $no_anggota;
    public $nama_anggota;

    public $queryAnggota = '';
    public $anggotas = [];
    public $showDropdownAnggota = false;
    public $selectedAnggota = null;

    public $queryMarketing = '';
    public $kode_marketing = '';
    public $marketings = [];
    public $showDropdownMarketing = false;
    public $selectedMarketing = null;

    // Signature
    public string|null $signatureBase64 = null;

    public $uploadedSignature;
    public $existingSignature;

    protected $listeners = ['updateSignature'];

    public $mode = 'draw'; // default draw

    // Dropdown data
    public $kantors;
    public $jenis;
    public $userLogin;


    public function mount($id)
    {
        $this->simpanan = Simpanan::findOrFail($id);

        // Data simpanan
        $this->created_at = $this->simpanan->created_at->format('d-m-Y');
        $this->no_rekening = $this->simpanan->no_rekening;

        $anggota = $this->simpanan->anggota;
        $this->anggota_id = $anggota?->id;
        $this->no_anggota = $anggota?->no_anggota;
        $this->nama_anggota = $anggota?->nama;
        $this->queryAnggota = $anggota?->no_anggota;

        $this->jenis_id = $this->simpanan->jenis_id;

        $marketing = $this->simpanan->marketing;
        $this->marketing_id = $marketing?->id;
        $this->kode_marketing = $marketing?->nama;
        $this->queryMarketing = $marketing?->nama;

        $this->qq = $this->simpanan->qq;
        $this->bunga = $this->simpanan->bunga;
        $this->nominal_blokir = $this->simpanan->nominal_blokir;
        $this->tgl_blokir = $this->simpanan->tgl_blokir;
        $this->nominal_setor = $this->simpanan->nominal_setor;
        $this->kantor_id = $this->simpanan->kantor_id;

        $this->aktif = (bool) $this->simpanan->aktif;
        $this->blokir_simpanan = (bool) $this->simpanan->blokir_simpanan;
        $this->blokir_nominal = (bool) $this->simpanan->blokir_nominal;
        $this->blokir_tgl = (bool) $this->simpanan->blokir_tgl;
        $this->sms = (bool) $this->simpanan->sms;

        // Tanda tangan existing
        $this->existingSignature = $this->simpanan->ttd
            ? asset('storage/' . $this->simpanan->ttd)
            : null;

        // Master data
        $this->kantors = Kantor::orderBy('nama_kantor')->get();
        $this->jenis = SimpananJenis::orderBy('kode')->get();

        // Default mode

    }


    public function updateSignature($data)
    {
        $this->signatureBase64 = $data;
    }



    protected function saveSignature()
    {
        // 1️⃣ TIDAK ADA PERUBAHAN → PAKAI FILE LAMA
        if (
            $this->signatureBase64 === null &&
            $this->uploadedSignature === null
        ) {
            return $this->simpanan->ttd;
        }

        // 🔥 HAPUS FILE LAMA (KARENA ADA PERUBAHAN)
        if (
            $this->simpanan->ttd &&
            Storage::disk('public')->exists($this->simpanan->ttd)
        ) {
            Storage::disk('public')->delete($this->simpanan->ttd);
        }

        // 2️⃣ MODE DRAW (BASE64)
        if ($this->mode === 'draw' && $this->signatureBase64) {

            $image = ImageManager::withDriver(Driver::class)
                ->read($this->signatureBase64)
                ->encode(new PngEncoder());

            $filename = 'ttd_' . Str::uuid() . '.png';
            $path = 'ttd/' . $filename;

            Storage::disk('public')->put($path, $image);

            return $path;
        }

        // 3️⃣ MODE UPLOAD
        if ($this->mode === 'upload' && $this->uploadedSignature) {

            $filename = 'ttd_' . Str::uuid() . '.' .
                $this->uploadedSignature->getClientOriginalExtension();

            return $this->uploadedSignature->storeAs(
                'ttd',
                $filename,
                'public'
            );
        }

        // 4️⃣ FALLBACK (AMAN)
        return $this->simpanan->ttd;
    }





    public function updatedQueryAnggota()
    {
        if (strlen($this->queryAnggota) < 2) {
            $this->anggotas = [];
            return;
        }

        $this->showDropdownAnggota = true;

        $this->anggotas = Anggota::where('no_anggota', 'like', '%' . $this->queryAnggota . '%')
            ->orWhere('nama', 'like', '%' . $this->queryAnggota . '%')
            ->limit(10)
            ->get();
    }


    // Ketika memilih anggota
    public function selectAnggota($id)
    {
        $anggota = Anggota::findOrFail($id);

        $this->anggota_id   = $anggota->id;
        $this->no_anggota   = $anggota->no_anggota;
        $this->nama_anggota = $anggota->nama;

        $this->queryAnggota = $anggota->no_anggota;
        $this->showDropdownAnggota = false;
    }


    public function updatedQueryMarketing()
    {
        $this->showDropdownMarketing = true;

        $this->marketings = Marketing::where('nama', 'like', '%' . $this->queryMarketing . '%')
            ->orWhere('kode', 'like', '%' . $this->queryMarketing . '%')
            ->limit(8)
            ->get();
    }


    public function selectMarketing($id)
    {
        $marketing = Marketing::find($id);

        $this->selectedMarketing = $marketing;
        $this->queryMarketing = $marketing->nama;

        $this->marketing_id = $marketing->id;


        $this->showDropdownMarketing = false;   // Tutup dropdown
    }

    // Saat input kehilangan fokus → tutup dropdown
    public function hideDropdown()
    {
        $this->showDropdownAnggota = false;
        $this->showDropdownMarketing = false;
    }

    public function updatedNama()
    {
        $this->validateOnly('nama');
    }

    public function update()
    {



        $validated = $this->validate([
            'no_rekening' => 'required|string|max:255|unique:simpanan,no_rekening,' . $this->simpanan->id,
            'anggota_id'     => ['nullable', 'exists:anggota,id'],
            'jenis_id'     => ['nullable', 'exists:simpanan_jenis,id'],
            'marketing_id'     => ['nullable', 'exists:marketing,id'],
            'qq'     => ['nullable', 'string', 'max:255'],
            'bunga'     => ['nullable', 'string', 'max:255'],
            'blokir_simpanan'     => ['nullable', 'boolean'],
            'blokir_nominal'     => ['nullable', 'boolean'],
            'nominal_blokir'     => ['nullable', 'string', 'max:255'],
            'blokir_tgl'     => ['nullable', 'boolean'],
            'tgl_blokir'     => ['nullable', 'date'],
            'nominal_setor'     => ['nullable', 'string', 'max:255'],
            'sms'     => ['nullable', 'boolean'],
            'aktif'     => ['nullable', 'boolean'],
            'kantor_id'     => ['nullable', 'exists:kantor,id'],
        ]);

        $ttdPath = $this->saveSignature();

        $this->simpanan->update([
            'no_rekening'     => $validated['no_rekening'],
            'anggota_id'     => $validated['anggota_id'],
            'jenis_id'     => $validated['jenis_id'],
            'marketing_id'     => $validated['marketing_id'],
            'qq'     => $validated['qq'],
            'bunga'     => $validated['bunga'],
            'baris'     => 0,
            'ttd' => $ttdPath,
            'blokir_simpanan'     => $validated['blokir_simpanan'] ? 1 : 0,
            'blokir_nominal'     => $validated['blokir_nominal'] ? 1 : 0,
            'nominal_blokir'     => $validated['nominal_blokir'],
            'blokir_tgl'     => $validated['blokir_tgl'] ? 1 : 0,
            'tgl_blokir'     => $validated['tgl_blokir'],
            'nominal_setor'     => $validated['nominal_setor'],
            'sms'     => $validated['sms'] ? 1 : 0,
            'aktif'     => $validated['aktif'] ? 1 : 0,
            'kantor_id'     => $validated['kantor_id'],
            'user_id'    =>  auth()->id(),
        ]);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Data simpanan berhasil diperbarui',
            'icon' => 'success',
        ]);

        return redirect()->route('superadmin.simpanan');
    }

    public function render()
    {
        return view('livewire.superadmin.simpanan.edit', [
            'title' => 'Tambah Edit Simpanan',
        ]);
    }
}
