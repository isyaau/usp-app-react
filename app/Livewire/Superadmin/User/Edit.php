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

class Edit extends Component
{
    use WithSweetAlert;
    use WithFileUploads;

    #[Title('Edit User')]

    public $userId;

    #[Validate('required|string|max:255')]
    public $nama;

    #[Validate('required|email')]
    public $email;

    #[Validate('required|string')]
    public $role;

    #[Validate('nullable|string|min:8|confirmed')]
    public $password;

    public $password_confirmation;

    public $avatar;       // avatar baru
    public $oldAvatar;    // avatar 

    #[Validate('required|string')]
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

    public function mount($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;

        $this->nama = $user->nama;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role = $user->role;

        // Avatar lama
        $this->oldAvatar    = $user->avatar;

        $this->password = '';
        $this->password_confirmation = '';
        $this->avatar = null;
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
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'avatar.image' => 'File avatar harus berupa gambar.',
            'avatar.max' => 'Ukuran avatar maksimal 2MB.',
        ];
    }

    public function update()
    {
        // Validasi dinamis untuk email unique: except ID saat ini
        $this->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'username' => 'required|string|unique:users,username,' . $this->userId,
            'role' => 'required|string',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar'   => 'nullable|image|max:2048', // max 2MB
        ]);

        $user = User::findOrFail($this->userId);

        $avatarPath = $this->oldAvatar; // default: avatar lama tetap

        if ($this->avatar) {

            // Hapus avatar lama jika bukan default
            if (
                $this->oldAvatar &&
                $this->oldAvatar !== 'avatar/avatar-default.jpg' &&
                Storage::disk('public')->exists($this->oldAvatar)
            ) {
                Storage::disk('public')->delete($this->oldAvatar);
            }

            // Generate nama file
            $ext    = strtolower($this->avatar->getClientOriginalExtension());
            $filename = "avatar-{$user->id}-" . time() . ".{$ext}";

            $avatarPath = "avatar/{$filename}";

            // Resize & compress pakai Intervention Image
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

            Storage::disk('public')->put($avatarPath, (string) $encoded);
        }

        $user->update([
            'nama' => $this->nama,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'password' => $this->password ? bcrypt($this->password) : $user->password,
            'avatar'   => $avatarPath,
        ]);

        // Kirim event ke UserIndex
        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'User berhasil diupdate!',
            'icon' => 'success',
            'confirmButtonText' => 'Oke'
        ]);

        return $this->redirect('/superadmin/user', navigate: true);
    }

    public function render()
    {
        return view('livewire.superadmin.user.edit', [
            'title' => 'Edit User',
        ]);
    }
}
