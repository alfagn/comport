<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Owner-confirmed intended domain. Never replace an existing choice, including blank. */
    public function up(): void
    {
        if (! DB::table('settings')->where('key', 'seo_canonical_base')->exists()) {
            DB::table('settings')->insert([
                'group' => 'seo', 'key' => 'seo_canonical_base', 'value' => 'https://empetix.com',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    /** Settings are owner content. Rollback must not delete them. */
    public function down(): void {}
};
