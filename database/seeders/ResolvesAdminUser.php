<?php

namespace Database\Seeders;

use App\Models\User;

/**
 * Resolusi user admin ("pembuat/pengubah data") untuk semua seeder.
 *
 * Prioritas:
 *   1. user dengan role superadmin (ID terkecil),
 *   2. user dengan email admin@admin.com,
 *   3. user pertama.
 *
 * Pemakaian:
 *   $this->adminUserId()  // int id
 *   $this->adminUser()    // model User
 */
trait ResolvesAdminUser
{
    protected function adminUser(): User
    {
        $superadmin = User::query()
            ->where('role', 'superadmin')
            ->orderBy('id')
            ->first();

        return $superadmin
            ?? User::where('email', 'admin@admin.com')->first()
            ?? User::orderBy('id')->first();
    }

    protected function adminUserId(): int
    {
        return (int) (($user = $this->adminUser()) ? $user->id : 0);
    }
}