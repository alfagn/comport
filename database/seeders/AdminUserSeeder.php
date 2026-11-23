<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds a dashboard login ONLY when ADMIN_INITIAL_PASSWORD is set in the
 * environment (CI/tests set it explicitly). For real installs, create the
 * user interactively instead so no credential ever touches code or logs:
 *
 *   php artisan empetix:admin
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_INITIAL_PASSWORD');

        if (! $password) {
            $this->command->warn('ADMIN_INITIAL_PASSWORD not set — skipping admin seed. Run `php artisan empetix:admin` to create your dashboard login.');

            return;
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
