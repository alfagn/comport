<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Application entry seeder: the dashboard user first, then the full
 * site content carried over from the static site.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ContentSeeder::class,
            PageSectionSeeder::class,
            NavItemSeeder::class,
            ContentListSeeder::class,
        ]);
    }
}
