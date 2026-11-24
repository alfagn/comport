<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            $exists = DB::table('settings')->where('key', 'facebook')->exists();
            if (! $exists) {
                DB::table('settings')->insert([
                    'group' => 'social',
                    'key' => 'facebook',
                    'value' => 'https://www.facebook.com',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            DB::table('settings')->where('key', 'facebook')->delete();
        }
    }
};
