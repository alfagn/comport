<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Extends support_chat_sessions for multi-channel WhatsApp Business Cloud API
 * threads, human takeover (ai_paused), and webhook idempotency.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_chat_sessions', function (Blueprint $table) {
            $table->string('channel', 20)->default('web')->after('conversation_id')->index();
            $table->string('visitor_phone', 40)->nullable()->after('visitor_email')->index();
            $table->string('external_thread_id', 80)->nullable()->after('visitor_phone')->index();
            $table->boolean('ai_paused')->default(false)->after('status');
            $table->string('last_external_message_id', 140)->nullable()->after('landing_page');
        });

        $defaults = [
            'whatsapp_enabled' => '1',
            'whatsapp_verify_token' => 'empetix-wa-'.Str::lower(Str::random(12)),
        ];

        foreach ($defaults as $key => $value) {
            if (! DB::table('settings')->where('key', $key)->exists()) {
                DB::table('settings')->insert([
                    'group' => 'whatsapp',
                    'key' => $key,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('group', 'whatsapp')->delete();

        Schema::table('support_chat_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'channel',
                'visitor_phone',
                'external_thread_id',
                'ai_paused',
                'last_external_message_id',
            ]);
        });
    }
};
