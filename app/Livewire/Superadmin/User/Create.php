<?php

namespace App\Livewire\Superadmin\User;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use SweetAlert2\Laravel\Traits\WithSweetAlert;

#[Title('Tambah User')]
class Create extends Component
{
    use WithSweetAlert;
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public $nama;

    #[Validate('required|email|unique:users,email')]
    public $email;

    #[Validate('required|string')]
    public $role;

    #[Validate('required|string|min:8|confirmed')]
    public $password;

    #[Validate('required|string|min:8')]
    public $password_confirmation;

    #[Validate('required|file|mimes:jpg,jpeg,png,pdf|max:2048')]
    public $avatar;

    #[Validate('required|string|unique:users,username')]
    public $username;

    public function updatedNama($value)
    {
        // Buat username dasar dari nama
        $base = Str::slug($value, '');

        $username = $base;
        $i = 1;

        // Cek ke database sampai username unik
        while (User::where('username', $username)->exists()) {
            $username = $base . $i;
            $i++;
        }

        $this->username = $username;
    }


    public function updated($propertyName)
    {

        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.superadmin.user.create', [
            'title' => 'Tambah User',
        ]);
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'role.required' => 'Role wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'avatar.required' => 'File wajib diunggah.',
            'avatar.mimes' => 'Format avatar harus berupa jpg, jpeg, png, atau pdf.',
            'avatar.max' => 'Ukuran avatar maksimal 2MB.',
        ];
    }

    public function store()
    {

        $validated = $this->validate();

        if ($this->avatar) {
            $name   = Str::slug($validated['nama']);
            $random = rand(10000, 99999);
            $ext    = strtolower($this->avatar->getClientOriginalExtension());
            $filename = "{$name}-{$random}.{$ext}";

            $path = "avatar/" . $filename;

            $manager = new ImageManager(new Driver());
            $image   = $manager->read($this->avatar->getRealPath());


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
            $path = 'avatar/avatar-default.jpg';
        }


        User::create([
            'nama'     => $validated['nama'],
            'username'     => $validated['username'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => bcrypt($validated['password']),
            'avatar'   => $path,
        ]);


        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'User berhasil dibuat!',
            'icon' => 'success',
            'confirmButtonText' => 'Oke'
        ]);

        return $this->redirect('/superadmin/user', navigate: true);
    }
}
