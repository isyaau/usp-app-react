<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;

/**
 * Controller CRUD User untuk frontend Inertia (React + TypeScript).
 * Menggantikan Livewire Superadmin\User.
 */
class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('search'), fn ($q) => $q->where('nama', 'LIKE', '%'.$request->string('search').'%'))
            ->orderBy('created_at', 'DESC')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/User/Index', [
            'users' => $users,
            'filters' => ['search' => $request->input('search', '')],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/User/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|in:superadmin,admin,user',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'avatar.mimes' => 'Format avatar harus jpg, jpeg, png, atau webp.',
            'avatar.max' => 'Ukuran avatar maksimal 2MB.',
        ]);

        $path = 'avatar/avatar-default.jpg';

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $name = Str::slug($validated['nama']);
            $random = rand(10000, 99999);
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = "{$name}-{$random}.{$ext}";
            $path = "avatar/{$filename}";

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            $image->scaleDown(2000);

            $encoder = match ($ext) {
                'png' => new PngEncoder(),
                'webp' => new WebpEncoder(quality: 70),
                default => new JpegEncoder(quality: 70),
            };

            Storage::disk('public')->put($path, (string) $image->encode($encoder));
        }

        User::create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => bcrypt($validated['password']),
            'avatar' => $path,
        ]);

        return redirect()
            ->route('superadmin.user')
            ->with('flash.status', 'User berhasil dibuat!');
    }

    public function show(User $user)
    {
        return inertia('Superadmin/User/Show', ['userData' => $user]);
    }

    public function edit(User $user)
    {
        return inertia('Superadmin/User/Edit', ['userData' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|in:superadmin,admin,user',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ], $this->messages(update: true));

        $data = [
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika bukan default
            if ($user->avatar && $user->avatar !== 'avatar/avatar-default.jpg') {
                Storage::disk('public')->delete($user->avatar);
            }

            $file = $request->file('avatar');
            $name = Str::slug($validated['nama']);
            $random = rand(10000, 99999);
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = "{$name}-{$random}.{$ext}";
            $path = "avatar/{$filename}";

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            $image->scaleDown(2000);

            $encoder = match ($ext) {
                'png' => new PngEncoder(),
                'webp' => new WebpEncoder(quality: 70),
                default => new JpegEncoder(quality: 70),
            };

            Storage::disk('public')->put($path, (string) $image->encode($encoder));
            $data['avatar'] = $path;
        }

        $user->update($data);

        return redirect()
            ->route('superadmin.user')
            ->with('flash.status', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if ($user->avatar && $user->avatar !== 'avatar/avatar-default.jpg') {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()
            ->route('superadmin.user')
            ->with('flash.status', 'User berhasil dihapus!');
    }

    private function messages(bool $update = false): array
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'avatar.mimes' => 'Format avatar harus jpg, jpeg, png, atau webp.',
            'avatar.max' => 'Ukuran avatar maksimal 2MB.',
        ];
    }
}
