<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Creates the initial dashboard login.
 *
 * The password comes from the ADMIN_INITIAL_PASSWORD environment
 * variable. When it is not set (local setup), a random one is generated
 * and printed once to the seeder console output. No default password is
 * ever shipped in the codebase.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_INITIAL_PASSWORD');

        if (! $password) {
            $password = Str::random(24);
            $this->command->warn('No ADMIN_INITIAL_PASSWORD set. Generated a random admin password for this install:');
            $this->command->line($password);
            $this->command->warn('Save it now — it is shown only once. Set ADMIN_INITIAL_PASSWORD in .env to choose your own.');
        }

        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@empetix.com')],
            [
                'name' => env('ADMIN_NAME', 'Empetix Admin'),
                'password' => Hash::make($password),
            ]
        );
    }
}
