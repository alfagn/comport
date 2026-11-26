<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Replaces the placeholder landline in the `phone` contact setting with the
 * real customer contact number. This is the number shown on the contact page
 * and in the public footer (tel: link) and quoted by the AI support concierge
 * when visitors ask how to reach Empetix. Stays editable from the dashboard
 * settings like any other Setting value.
 */
return new class extends Migration
{
    /**
     * Customer-facing contact number in international format.
     */
    private const CONTACT_PHONE = '+20 105 520 9612';

    public function up(): void
    {
        $query = DB::table('settings')->where('key', 'phone');

        if ($query->exists()) {
            // Replace the placeholder landline on existing installs.
            $query->update([
                'value' => self::CONTACT_PHONE,
                'updated_at' => now(),
            ]);

            return;
        }

        DB::table('settings')->insert([
            'group' => 'contact',
            'key' => 'phone',
            'value' => self::CONTACT_PHONE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'phone')->delete();
    }
};
