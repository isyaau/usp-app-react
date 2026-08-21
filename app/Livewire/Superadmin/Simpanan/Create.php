<?php

namespace App\Livewire\Superadmin\Simpanan;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Kantor;
use App\Models\Anggota;
use Livewire\Component;
use App\Models\Kelompok;
use App\Models\Marketing;
use App\Models\Simpanan;
use App\Models\SimpananJenis;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Livewire\WithFileUploads;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use SweetAlert2\Laravel\Traits\WithSweetAlert;

#[Title('Tambah Simpanan')]
class Create extends Component
{
    use WithSweetAlert;
    use WithFileUploads;


    public $no_rekening;


    public $anggota_id;


    public $jenis_id;


    public $marketing_id;


    public $qq;


    public $bunga;


    public $baris;


    public $nominal_blokir;


    public $tgl_blokir;

    // Checkbox
    public $aktif = false;
    public $blokir_tgl = false;
    public $blokir_simpanan = false;
    public $blokir_nominal = false;
    public $sms = false;

    public $nominal_setor;



    public $user_id;


    #[Validate('nullable|integer|exists:kantor,id')]
    public $kantor_id;




    public string|null $signatureBase64 = null;

    public $uploadedSignature;

    protected $listeners = ['updateSignature'];



    public $userLogin;
    public $tanggal;
    public $kantors;
    public $jenis;
    public $mode = 'draw'; // default draw





    public $query = '';
    public $users = [];
    public $showDropdown = false;
    public $selectedAnggota = null;

    public $queryAnggota = '';
    public $anggotas = [];
    public $showDropdownAnggota = false;
    public $selectedUser = null;

    public $queryMarketing = '';
    public $marketings = [];
    public $showDropdownMarketing = false;
    public $selectedMarketing = null;

    public function updateSignature($data)
    {
        $this->signatureBase64 = $data;
    }

    protected function saveSignature()
    {
        // MODE DRAW (BASE64)
        if ($this->mode === 'draw' && $this->signatureBase64) {

            $image = ImageManager::withDriver(Driver::class)
                ->read($this->signatureBase64)
                ->encode(new PngEncoder());

            $filename = 'ttd_' . Str::uuid() . '.png';
            $path = 'ttd/' . $filename;

            Storage::disk('public')->put($path, $image);

            return $path;
        }

        // MODE UPLOAD
        if ($this->mode === 'upload' && $this->uploadedSignature) {

            $filename = 'ttd_' . Str::uuid() . '.' . $this->uploadedSignature->getClientOriginalExtension();
            $path = $this->uploadedSignature->storeAs('ttd', $filename, 'public');

            return $path;
        }

        return null;
    }


    // Ketika mengetik
    public function updatedQueryAnggota()
    {
        $this->showDropdownAnggota = true;

        $this->anggotas = Anggota::where('nama', 'like', '%' . $this->queryAnggota . '%')
            ->orWhere('no_anggota', 'like', '%' . $this->queryAnggota . '%')
            ->limit(8)
            ->get();
    }

    // Ketika memilih anggota
    public function selectAnggota($id)
    {
        $anggota = Anggota::find($id);

        $this->selectedAnggota = $anggota;
        $this->query = $anggota->nama;

        $this->anggota_id = $anggota->id;


        $this->showDropdownAnggota = false;   // Tutup dropdown
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
        $this->query = $marketing->nama;

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






    public function mount()
    {
        $this->userLogin = auth()->guard('web')->user();
        $this->kantors = Kantor::orderBy('nama_kantor')->get();
        $this->jenis = SimpananJenis::orderBy('kode')->get();
        $this->tanggal = Carbon::now()->format('d-m-Y');
    }


    public function render()
    {
        return view('livewire.superadmin.simpanan.create', [
            'title' => 'Tambah Simpanan',
        ]);
    }




    public function store()
    {

        $validated = $this->validate([
            'no_rekening'     => ['required', 'string', 'max:255', 'unique:simpanan,no_rekening'],
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



        if ($this->mode === 'draw' && empty($this->signatureBase64)) {
            $this->addError('signatureBase64', 'Tanda tangan wajib diisi.');
            return;
        }

        $ttdPath = $this->saveSignature(); // konversi Base64 ke file

        $user_id = $this->userLogin->id;

        Simpanan::create([
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
            'user_id'    => $user_id

        ]);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Simpanan berhasil dibuat!',
            'icon' => 'success',
            'confirmButtonText' => 'Oke'
        ]);

        return redirect()->route('superadmin.simpanan');
    }
}
