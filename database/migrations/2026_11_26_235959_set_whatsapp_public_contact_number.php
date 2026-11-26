<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Points the website's floating WhatsApp button (wa.me click-to-chat) at the
 * real customer contact number instead of the placeholder that shipped with
 * the WhatsApp support setup. Stored in the `settings` table so the value
 * stays editable from Dashboard -> Support Chats -> WhatsApp settings; the
 * public URL is rendered by WhatsappCloudApiService::publicClickToChatUrl(),
 * which normalizes the digits for wa.me.
 */
return new class extends Migration
{
    /**
     * Customer-facing WhatsApp contact number in international format.
     */
    private const PUBLIC_WHATSAPP_NUMBER = '+201055209612';

    public function up(): void
    {
        $query = DB::table('settings')->where('key', 'whatsapp_public_number');

        if ($query->exists()) {
            // Replace the placeholder number on existing installs.
            $query->update([
                'value' => self::PUBLIC_WHATSAPP_NUMBER,
                'updated_at' => now(),
            ]);

            return;
        }

        DB::table('settings')->insert([
            'group' => 'whatsapp',
            'key' => 'whatsapp_public_number',
            'value' => self::PUBLIC_WHATSAPP_NUMBER,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'whatsapp_public_number')->delete();
    }
};
