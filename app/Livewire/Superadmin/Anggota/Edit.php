<?php

namespace App\Livewire\Superadmin\Anggota;

use App\Models\Kantor;
use App\Models\Anggota;
use Livewire\Component;
use App\Models\Kelompok;
use Illuminate\Support\Str;
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


class Edit extends Component
{
    use WithSweetAlert;
    use WithFileUploads;

    public $anggotaId;
    public $oldFoto;

    // =========================
    // IDENTITAS UTAMA
    // =========================
    #[Validate('required|string|max:255')]
    public $no_anggota;

    #[Validate('required|string|max:255')]
    public $nama;

    #[Validate('required|string|max:255')]
    public $alamat;

    // =========================
    // RELASI & LOKASI
    // =========================
    #[Validate('required|integer')]
    public $kelompok_id;

    #[Validate('required|integer')]
    public $kantor_id;

    #[Validate('required')]
    public $provinsi_id;

    #[Validate('required')]
    public $kota_id;

    #[Validate('required')]
    public $kecamatan_id;

    #[Validate('required')]
    public $kelurahan_id;

    // =========================
    // KONTAK & IDENTITAS
    // =========================

    public $email;


    #[Validate('required|string|max:255')]
    public $telepon;

    #[Validate('required|string|max:255')]
    public $no_hp;

    #[Validate('required|string|max:255')]
    public $jenis_identitas;

    #[Validate('required|string|max:255')]
    public $no_identitas;

    #[Validate('required|string|max:255')]
    public $pin;


    #[Validate('required|string|max:255')]
    public $npwp;

    // =========================
    // DATA PRIBADI
    // =========================
    #[Validate('required|string|max:255')]
    public $tempat_lahir;

    #[Validate('required|date')]
    public $tgl_lahir;

    #[Validate('required|in:Laki-laki,Perempuan')]
    public $jenis_kelamin;

    #[Validate('required|string|max:255')]
    public $agama;

    #[Validate('required|string|max:255')]
    public $pekerjaan;

    #[Validate('required|string|max:255')]
    public $pendidikan;

    #[Validate('required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati')]
    public $status_perkawinan;

    #[Validate('nullable|string|max:255')]
    public $pasangan;

    // =========================
    // ORANG TUA
    // =========================
    #[Validate('required|string|max:255')]
    public $ibu;

    // =========================
    // PENGURUS
    // =========================
    #[Validate('boolean')]
    public $pengurus = false;

    #[Validate('nullable|string|max:255')]
    public $pengurus_jabatan;

    #[Validate('nullable|date')]
    public $tgl_pengurus_diangkat;

    #[Validate('nullable|date')]
    public $tgl_pengurus_berhenti;

    #[Validate('nullable|string|max:255')]
    public $pengurus_berhenti;

    // =========================
    // PENGAWAS
    // =========================
    #[Validate('boolean')]
    public $pengawas = false;

    #[Validate('nullable|string|max:255')]
    public $pengawas_jabatan;

    #[Validate('nullable|date')]
    public $tgl_pengawas_diangkat;

    #[Validate('nullable|date')]
    public $tgl_pengawas_berhenti;

    #[Validate('nullable|string|max:255')]
    public $pengawas_berhenti;

    // =========================
    // WARIS
    // =========================
    #[Validate('nullable|string|max:255')]
    public $waris1;

    #[Validate('nullable|string|max:255')]
    public $hubungan_waris1;

    #[Validate('nullable|string|max:255')]
    public $waris2;

    #[Validate('nullable|string|max:255')]
    public $hubungan_waris2;

    // =========================
    // STATUS ANGGOTA
    // =========================
    #[Validate('boolean')]
    public $status = true;

    #[Validate('nullable|date')]
    public $tgl_anggota_berhenti;

    #[Validate('nullable|string|max:255')]
    public $anggota_berhenti;

    // =========================
    // FOTO (EDIT)
    // =========================
    #[Validate('nullable|image|mimes:jpg,jpeg,png,webp|max:2048')]
    public $foto;





    public $kelompoks;
    public $kantors;


    public $userLogin;

    // Data dropdown
    public $cities = [];
    public $districts = [];
    public $villages = [];
    public $provinces = [];

    #[Validate('nullable|integer|exists:users,id')]
    public $ketua_id;


    public $activeTab = 'keanggotaan';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function mount($id)
    {
        // Ambil data user yang sedang login
        $this->userLogin = auth()->guard('web')->user();

        // Ambil data anggota berdasarkan ID
        $anggota = Anggota::findOrFail($id);
        $this->kelompoks = Kelompok::all();
        $this->kantors = Kantor::orderBy('nama_kantor')->get();

        // Set data anggota ke properti Livewire sesuai dengan validasi yang ada
        $this->anggotaId = $anggota->id;
        $this->no_anggota = $anggota->no_anggota;
        $this->nama = $anggota->nama;
        $this->alamat = $anggota->alamat;
        $this->kelompok_id = $anggota->kelompok_id;
        $this->pin = $anggota->pin;
        $this->provinsi_id = $anggota->provinsi_id;
        $this->kota_id = $anggota->kota_id;
        $this->kecamatan_id = $anggota->kecamatan_id;
        $this->kelurahan_id = $anggota->kelurahan_id;
        $this->email = $anggota->email;
        $this->tempat_lahir = $anggota->tempat_lahir;
        $this->tgl_lahir = $anggota->tgl_lahir;
        $this->jenis_kelamin = $anggota->jenis_kelamin;
        $this->agama = $anggota->agama;
        $this->pekerjaan = $anggota->pekerjaan;
        $this->pendidikan = $anggota->pendidikan;
        $this->status_perkawinan = $anggota->status_perkawinan;
        $this->pasangan = $anggota->pasangan;
        $this->telepon = $anggota->telepon;
        $this->no_hp = $anggota->no_hp;
        $this->jenis_identitas = $anggota->jenis_identitas;
        $this->no_identitas = $anggota->no_identitas;
        $this->npwp = $anggota->npwp;
        $this->ibu = $anggota->ibu;

        // Properti nullable yang bisa diisi saat edit
        $this->pengurus = $anggota->pengurus ?? 0;
        $this->pengurus_jabatan = $anggota->pengurus_jabatan;
        $this->tgl_pengurus_diangkat = $anggota->tgl_pengurus_diangkat;
        $this->tgl_pengurus_berhenti = $anggota->tgl_pengurus_berhenti;
        $this->pengurus_berhenti = $anggota->pengurus_berhenti;

        $this->pengawas = $anggota->pengawas ?? 0;
        $this->pengawas_jabatan = $anggota->pengawas_jabatan;
        $this->tgl_pengawas_diangkat = $anggota->tgl_pengawas_diangkat;
        $this->tgl_pengawas_berhenti = $anggota->tgl_pengawas_berhenti;
        $this->pengawas_berhenti = $anggota->pengawas_berhenti;

        $this->waris1 = $anggota->waris1;
        $this->hubungan_waris1 = $anggota->hubungan_waris1;
        $this->waris2 = $anggota->waris2;
        $this->hubungan_waris2 = $anggota->hubungan_waris2;
        $this->status = $anggota->status ?? 1; // Default 1 jika tidak ada data
        $this->tgl_anggota_berhenti = $anggota->tgl_anggota_berhenti;
        $this->kantor_id = $anggota->kantor_id;
        $this->anggota_berhenti = $anggota->anggota_berhenti;

        // Untuk foto, Anda bisa mengambil data foto dari model jika ada
        $this->foto = null; // Bisa disesuaikan sesuai dengan field foto yang ada
        $this->oldFoto    = $anggota->foto;

        // Ambil data provinsi, kota, kecamatan, dan kelurahan sesuai dengan ID yang ada
        $this->provinces = Province::all();
        $this->cities = City::where('province_code', $this->provinsi_id)->get();
        $this->districts = District::where('city_code', $this->kota_id)->get();
        $this->villages = Village::where('district_code', $this->kecamatan_id)->get();



        // Logika tambahan jika ada
    }


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
        return view('livewire.superadmin.anggota.edit', [
            'title' => 'Tambah Anggota',
            'provinces' => Province::orderBy('name', 'asc')->get(),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:anggota,email,' . $this->anggotaId,
        ];
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


    public function update()
    {
        // 1. Validasi
        $validated = $this->validate();

        $anggota = Anggota::findOrFail($this->anggotaId);

        // 2. Default foto = foto lama
        $path = $this->oldFoto;

        // 3. Upload foto baru jika ada
        if ($this->foto) {

            // Hapus foto lama (jika bukan default)
            if (
                $this->oldFoto &&
                $this->oldFoto !== 'anggota/foto-default.jpg' &&
                Storage::disk('public')->exists($this->oldFoto)
            ) {
                Storage::disk('public')->delete($this->oldFoto);
            }

            // Generate nama file
            $name   = Str::slug($validated['nama']);
            $random = rand(10000, 99999);
            $ext    = strtolower($this->foto->getClientOriginalExtension());
            $filename = "{$name}-{$random}.{$ext}";
            $path = "anggota/{$filename}";

            // Resize & compress
            $manager = new ImageManager(new Driver());
            $image   = $manager->read($this->foto->getRealPath());
            $image->scaleDown(2000);

            $encoder = match ($ext) {
                'jpg', 'jpeg' => new JpegEncoder(quality: 70),
                'png'         => new PngEncoder(),
                'webp'        => new WebpEncoder(quality: 70),
                default       => new JpegEncoder(quality: 70),
            };

            $encoded = $image->encode($encoder);
            Storage::disk('public')->put($path, (string) $encoded);
        }

        // 4. Update data anggota
        $anggota->update([
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
        ]);

        // 5. Flash message
        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text'  => 'Data anggota berhasil diupdate!',
            'icon'  => 'success',
            'confirmButtonText' => 'Oke'
        ]);

        // 6. Redirect
        return $this->redirect('/superadmin/anggota', navigate: true);
    }
}
