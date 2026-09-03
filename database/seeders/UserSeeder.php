<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    protected static ?string $password;

    public function run(): void
    {
        User::factory(1000)->create();

        User::factory()->create([
            'nama' => 'admin',
            'email' => 'admin@admin.com',
            'role' => 'superadmin',
            'avatar' => 'avatar/avatar-default.jpg',
            'password' => static::$password ??= Hash::make('password'),
        ]);
    }
}
