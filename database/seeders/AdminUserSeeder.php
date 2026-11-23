<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Creates the initial dashboard login. The password comes from the
 * ADMIN_INITIAL_PASSWORD environment variable so nothing sensitive is
 * committed; it falls back to a documented local-development default
 * that must be changed before any real deployment.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@empetix.com')],
            [
                'name' => env('ADMIN_NAME', 'Empetix Admin'),
                'password' => Hash::make(env('ADMIN_INITIAL_PASSWORD', 'empetix-local-admin')),
            ]
        );
    }
}
