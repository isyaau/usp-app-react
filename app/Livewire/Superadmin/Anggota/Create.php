<?php

namespace App\Livewire\Superadmin\Anggota;

use Livewire\Component;

use App\Models\Kantor;
use App\Models\Anggota;
use App\Models\Kelompok;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Laravolt\Indonesia\Models\City;
use Intervention\Image\ImageManager;
use Laravolt\Indonesia\Models\Village;
use Illuminate\Support\Facades\Storage;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Intervention\Image\Drivers\Gd\Driver;
use Livewire\WithFileUploads;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use SweetAlert2\Laravel\Traits\WithSweetAlert;

#[Title('Tambah User')]
class Create extends Component
{
    use WithSweetAlert;
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public $no_anggota;

    #[Validate('required|string|max:255')]
    public $nama;

    #[Validate('required|string|max:255')]
    public $alamat;

    #[Validate('required|string|max:255')]
    public $kelompok_id;

    #[Validate('required|string|max:255')]
    public $pin;

    #[Validate('required|string')]
    public $provinsi_id;

    #[Validate('required|string')]
    public $kota_id;

    #[Validate('required|string')]
    public $kecamatan_id;

    #[Validate('required|string')]
    public $kelurahan_id;

    #[Validate('required|email|unique:anggota,email')]
    public $email;

    #[Validate('required|string|max:255')]
    public $tempat_lahir;

    #[Validate('required|string|max:255')]
    public $tgl_lahir;

    #[Validate('required|string|max:255')]
    public $jenis_kelamin;

    #[Validate('required|string|max:255')]
    public $agama;

    #[Validate('required|string|max:255')]
    public $pekerjaan;

    #[Validate('required|string|max:255')]
    public $pendidikan;

    #[Validate('required|string|max:255')]
    public $status_perkawinan;

    #[Validate('nullable|string|max:255')]
    public $pasangan;

    #[Validate('required|string|max:255')]
    public $telepon;

    #[Validate('required|string|max:255')]
    public $no_hp;

    #[Validate('required|string|max:255')]
    public $jenis_identitas;

    #[Validate('required|string|max:255')]
    public $no_identitas;

    #[Validate('required|string|max:255')]
    public $npwp;

    #[Validate('required|string|max:255')]
    public $ibu;

    #[Validate('nullable')]
    public $pengurus = 0;

    #[Validate('nullable|string|max:255')]
    public $pengurus_jabatan;

    #[Validate('nullable|string|max:255')]
    public $tgl_pengurus_diangkat;

    #[Validate('nullable|string|max:255')]
    public $tgl_pengurus_berhenti;

    #[Validate('nullable|string|max:255')]
    public $pengurus_berhenti;

    #[Validate('nullable')]
    public $pengawas = 0;

    #[Validate('nullable|string|max:255')]
    public $pengawas_jabatan;

    #[Validate('nullable|string|max:255')]
    public $tgl_pengawas_diangkat;

    #[Validate('nullable|string|max:255')]
    public $tgl_pengawas_berhenti;

    #[Validate('nullable|string|max:255')]
    public $pengawas_berhenti;

    #[Validate('nullable|string|max:255')]
    public $waris1;

    #[Validate('nullable|string|max:255')]
    public $hubungan_waris1;

    #[Validate('nullable|string|max:255')]
    public $waris2;

    #[Validate('nullable|string|max:255')]
    public $hubungan_waris2;

    #[Validate('nullable')]
    public $status = 1;

    #[Validate('nullable|string|max:255')]
    public $tgl_anggota_berhenti;

    #[Validate('nullable|string|max:255')]
    public $kantor_id;

    #[Validate('nullable|string|max:255')]
    public $anggota_berhenti;

    #[Validate('required|file|mimes:jpg,jpeg,png,pdf|max:2048')]
    public $foto;

    public $userLogin;

    // Data dropdown
    public $cities = [];
    public $districts = [];
    public $villages = [];

    #[Validate('nullable|integer|exists:users,id')]
    public $ketua_id;

    public $activeTab = 'keanggotaan';

    public $kelompoks;
    public $kantors;

    public array $listAgama = [
        'ISLAM',
        'KRISTEN',
        'KATOLIK',
        'HINDU',
        'BUDDHA',
        'KONGHUCU',
    ];

    public array $listPendidikan = [
        'SD',
        'SMP',
        'SMA/SMK',
        'D3',
        'S1',
        'S2',
        'S3',
    ];


    public array $listPekerjaan = [
        'PELAJAR / MAHASISWA',
        'PNS',
        'TNI / POLRI',
        'KARYAWAN SWASTA',
        'WIRASWASTA',
        'PETANI',
        'NELAYAN',
        'BURUH',
        'GURU / DOSEN',
        'TENAGA KESEHATAN',
        'IBU RUMAH TANGGA',
        'BELUM BEKERJA',
        'LAINNYA',
    ];



    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }


    public function mount()
    {
        $this->userLogin = auth()->guard('web')->user();
        $this->kelompoks = Kelompok::all();
        $this->kantors = Kantor::orderBy('nama_kantor')->get();
    }

    // Togle Form




    public function updatedStatusAnggota($value)
    {
        if ($value) {
            $this->tgl_anggota_berhenti = null;
            $this->anggota_berhenti = null;
            $this->status = 1;
        } else {
            $this->status = 0;
        }
    }


    public function updatedStatusPengurus($value)
    {
        if ($value) {

            $this->tgl_pengurus_berhenti = null;
            $this->pengurus_berhenti = null;
            $this->pengurus = 1;
        } else {
            $this->tgl_pengurus_diangkat = null;
            $this->pengurus = 0;
        }
    }



    public function updatedStatusPengawas($value)
    {
        if ($value) {

            $this->tgl_pengawas_berhenti = null;
            $this->pengawas_berhenti = null;
            $this->pengawas = 1;
        } else {
            $this->tgl_pengawas_diangkat = null;
            $this->pengawas = 0;
        }
    }


    // ====================== Dropdown Berantai ===========================
    public function updatedProvinsiId()
    {
        $this->cities = City::where('province_code', $this->provinsi_id)->orderBy('name', 'asc')->get(); // Urutkan kota berdasarkan nama
        $this->kota_id = null;
        $this->districts = [];
        $this->villages = [];
        $this->kecamatan_id = null;
        $this->kelurahan_id = null;
    }

    public function updatedKotaId()
    {
        $this->districts = District::where('city_code', $this->kota_id)->orderBy('name', 'asc')->get(); // Urutkan kecamatan berdasarkan nama
        $this->kecamatan_id = null;
        $this->villages = [];
        $this->kelurahan_id = null;
    }

    public function updatedKecamatanId()
    {
        $this->villages = Village::where('district_code', $this->kecamatan_id)->orderBy('name', 'asc')->get(); // Urutkan kelurahan berdasarkan nama
        $this->kelurahan_id = null;
    }


    public function updated($propertyName)
    {

        $this->validateOnly($propertyName);
    }

    public function render()
    {
        logger()->info('STATUS SEKARANG = ' . json_encode($this->status));
        return view('livewire.superadmin.anggota.create', [
            'title' => 'Tambah Anggota',
            'provinces' => Province::orderBy('name', 'asc')->get(),
        ]);
    }

    public function messages()
    {
        return [
            // ===================== IDENTITAS ANGGOTA =====================
            'no_anggota.required' => 'Nomor anggota wajib diisi.',
            'no_anggota.string' => 'Nomor anggota harus berupa teks.',
            'no_anggota.max' => 'Nomor anggota maksimal 255 karakter.',

            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama maksimal 255 karakter.',

            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'alamat.max' => 'Alamat maksimal 255 karakter.',

            'kelompok_id.required' => 'Kelompok wajib dipilih.',
            'kelompok_id.string' => 'Kelompok harus berupa teks.',
            'kelompok_id.max' => 'Kelompok maksimal 255 karakter.',

            'pin.required' => 'PIN wajib dipilih.',
            'pin.string' => 'PIN harus berupa teks.',
            'pin.max' => 'PIN maksimal 255 karakter.',

            'kantor_id.required' => 'Kantor wajib dipilih.',
            'kantor_id.string' => 'Kantor harus berupa teks.',
            'kantor_id.max' => 'Kantor maksimal 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',

            'telepon.required' => 'Telepon wajib diisi.',
            'telepon.string' => 'Telepon harus berupa teks.',
            'telepon.max' => 'Telepon maksimal 255 karakter.',

            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.string' => 'Nomor HP harus berupa teks.',
            'no_hp.max' => 'Nomor HP maksimal 255 karakter.',

            // ===================== DATA LOKASI =====================
            'provinsi_id.required' => 'Provinsi wajib dipilih.',
            'kota_id.required' => 'Kota/Kabupaten wajib dipilih.',
            'kecamatan_id.required' => 'Kecamatan wajib dipilih.',
            'kelurahan_id.required' => 'Kelurahan wajib dipilih.',

            // ===================== DATA PRIBADI =====================
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tempat_lahir.string' => 'Tempat lahir harus berupa teks.',
            'tempat_lahir.max' => 'Tempat lahir maksimal 255 karakter.',

            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tgl_lahir.string' => 'Tanggal lahir harus berupa teks.',
            'tgl_lahir.max' => 'Tanggal lahir maksimal 255 karakter.',

            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'agama.required' => 'Agama wajib diisi.',
            'agama.string' => 'Agama harus berupa teks.',
            'agama.max' => 'Agama maksimal 255 karakter.',

            'pekerjaan.required' => 'Pekerjaan wajib diisi.',
            'pekerjaan.string' => 'Pekerjaan harus berupa teks.',
            'pekerjaan.max' => 'Pekerjaan maksimal 255 karakter.',

            'pendidikan.required' => 'Pendidikan wajib diisi.',
            'pendidikan.string' => 'Pendidikan harus berupa teks.',
            'pendidikan.max' => 'Pendidikan maksimal 255 karakter.',

            'status_perkawinan.required' => 'Status perkawinan wajib diisi.',
            'status_perkawinan.string' => 'Status perkawinan harus berupa teks.',
            'status_perkawinan.max' => 'Status perkawinan maksimal 255 karakter.',

            'pasangan.string' => 'Nama pasangan harus berupa teks.',
            'pasangan.max' => 'Nama pasangan maksimal 255 karakter.',

            'ibu.required' => 'Nama ibu kandung wajib diisi.',
            'ibu.string' => 'Nama ibu kandung harus berupa teks.',
            'ibu.max' => 'Nama ibu kandung maksimal 255 karakter.',

            // ===================== IDENTITAS RESMI =====================
            'jenis_identitas.required' => 'Jenis identitas wajib dipilih.',
            'no_identitas.required' => 'Nomor identitas wajib diisi.',
            'no_identitas.string' => 'Nomor identitas harus berupa teks.',
            'no_identitas.max' => 'Nomor identitas maksimal 255 karakter.',

            'npwp.required' => 'NPWP wajib diisi.',
            'npwp.string' => 'NPWP harus berupa teks.',
            'npwp.max' => 'NPWP maksimal 255 karakter.',

            'foto.required' => 'Foto wajib diunggah.',
            'foto.mimes' => 'Foto harus berformat jpg, jpeg, png, atau pdf.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',

            // ===================== PENGURUS =====================
            'pengurus.required' => 'Nama pengurus wajib diisi.',
            'tgl_pengurus_diangkat.required' => 'Tanggal pengangkatan pengurus wajib diisi.',
            'tgl_pengurus_berhenti.required' => 'Tanggal berhenti pengurus wajib diisi.',
            'pengurus_berhenti.required' => 'Status berhenti pengurus wajib diisi.',

            // ===================== PENGAWAS =====================
            'pengawas.required' => 'Nama pengawas wajib diisi.',
            'tgl_pengawas_diangkat.required' => 'Tanggal pengangkatan pengawas wajib diisi.',
            'tgl_pengawas_berhenti.required' => 'Tanggal berhenti pengawas wajib diisi.',
            'pengawas_berhenti.required' => 'Status berhenti pengawas wajib diisi.',

            // ===================== WARIS =====================
            'waris1.required' => 'Nama waris 1 wajib diisi.',
            'hubungan_waris1.required' => 'Hubungan waris 1 wajib diisi.',
            'waris2.required' => 'Nama waris 2 wajib diisi.',
            'hubungan_waris2.required' => 'Hubungan waris 2 wajib diisi.',

            // ===================== STATUS ANGGOTA =====================
            'status.required' => 'Status anggota wajib diisi.',
            'tgl_anggota_berhenti.required' => 'Tanggal berhenti anggota wajib diisi.',
            'anggota_berhenti.required' => 'Status berhenti anggota wajib diisi.',
        ];
    }


    public function store()
    {
        // 1. Validasi data

        try {
            $validated = $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors());
        }


        // 2. Penanganan upload foto
        if ($this->foto) {
            $name   = Str::slug($validated['nama']); // slug nama
            $random = rand(10000, 99999);
            $ext    = strtolower($this->foto->getClientOriginalExtension());
            $filename = "{$name}-{$random}.{$ext}";

            $path = "anggota/{$filename}";

            $manager = new ImageManager(new Driver());
            $image   = $manager->read($this->foto->getRealPath());

            // Resize atau scale
            $image->scaleDown(2000);

            $encoder = match ($ext) {
                'jpg', 'jpeg' => new JpegEncoder(quality: 70),
                'png'         => new PngEncoder(),
                'webp'        => new WebpEncoder(quality: 70),
                default       => new JpegEncoder(quality: 70),
            };

            $encoded = $image->encode($encoder);

            Storage::disk('public')->put($path, (string) $encoded);
        } else {
            $path = 'anggota/foto-default.jpg';
        }

        $user_id = $this->userLogin->id;

        // 3. Simpan data anggota ke database
        Anggota::create([
            'no_anggota'            => $validated['no_anggota'],
            'nama'                  => $validated['nama'],
            'alamat'                => $validated['alamat'],
            'kelompok_id'           => $validated['kelompok_id'],
            'kantor_id'             => $validated['kantor_id'],
            'pin'                   => $validated['pin'],
            'provinsi_id'           => $validated['provinsi_id'],
            'kota_id'               => $validated['kota_id'],
            'kecamatan_id'          => $validated['kecamatan_id'],
            'kelurahan_id'          => $validated['kelurahan_id'],
            'email'                 => $validated['email'],
            'tempat_lahir'          => $validated['tempat_lahir'],
            'tgl_lahir'             => $validated['tgl_lahir'],
            'jenis_kelamin'         => $validated['jenis_kelamin'],
            'agama'                 => $validated['agama'],
            'pekerjaan'             => $validated['pekerjaan'],
            'pendidikan'            => $validated['pendidikan'],
            'status_perkawinan'     => $validated['status_perkawinan'],
            'pasangan'              => $validated['pasangan'] ?? null,
            'telepon'               => $validated['telepon'],
            'no_hp'                 => $validated['no_hp'],
            'jenis_identitas'       => $validated['jenis_identitas'],
            'no_identitas'          => $validated['no_identitas'],
            'npwp'                  => $validated['npwp'],
            'ibu'                   => $validated['ibu'],
            'pengurus'              => $validated['pengurus'],
            'pengurus_jabatan'      => $validated['pengurus_jabatan'],
            'tgl_pengurus_diangkat' => $validated['tgl_pengurus_diangkat'],
            'tgl_pengurus_berhenti' => $validated['tgl_pengurus_berhenti'],
            'pengurus_berhenti'     => $validated['pengurus_berhenti'],
            'pengawas'              => $validated['pengawas'],
            'pengawas_jabatan'      => $validated['pengawas_jabatan'],
            'tgl_pengawas_diangkat' => $validated['tgl_pengawas_diangkat'],
            'tgl_pengawas_berhenti' => $validated['tgl_pengawas_berhenti'],
            'pengawas_berhenti'     => $validated['pengawas_berhenti'],
            'waris1'                => $validated['waris1'],
            'hubungan_waris1'       => $validated['hubungan_waris1'],
            'waris2'                => $validated['waris2'],
            'hubungan_waris2'       => $validated['hubungan_waris2'],
            'status'                => $validated['status'],
            'tgl_anggota_berhenti'  => $validated['tgl_anggota_berhenti'],
            'anggota_berhenti'      => $validated['anggota_berhenti'],
            'foto'                  => $path,
            'user_id'               => $user_id
        ]);



        // 4. Flash message sukses
        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text'  => 'Data anggota berhasil disimpan!',
            'icon'  => 'success',
            'confirmButtonText' => 'Oke'
        ]);

        // 5. Redirect ke halaman anggota
        return $this->redirect('/superadmin/anggota', navigate: true);
    }
}
