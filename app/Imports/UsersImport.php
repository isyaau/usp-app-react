<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    protected static $password;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        return new
            User([
                'nama'  => $row['nama'],
                'email' => $row['email'],
                'email_verified_at' => now(),
                'password' => static::$password ??= Hash::make('password'),
                'role' => $row['role'] ?? 'user',
                'avatar' => $row['avatar'] ?? 'avatar/avatar-default.jpg',
                'remember_token' => Str::random(10),
            ]);
    }
}
