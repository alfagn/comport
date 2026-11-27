<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_chat_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('support_chat_sessions', 'visitor_username')) {
                $table->string('visitor_username', 120)->nullable()->after('visitor_name');
            }
            if (! Schema::hasColumn('support_chat_sessions', 'visitor_avatar')) {
                $table->string('visitor_avatar', 500)->nullable()->after('visitor_username');
            }
        });

        // Widen last_message_preview to TEXT so AES-256 encrypted ciphertexts never truncate on MySQL
        Schema::table('support_chat_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('support_chat_sessions', 'last_message_preview')) {
                $table->text('last_message_preview')->nullable()->change();
            }
        });

        Schema::table('whatsapp_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('whatsapp_messages', 'idempotency_key')) {
                $table->string('idempotency_key', 100)->nullable()->unique()->after('wa_message_id');
            }
        });

        // Change metadata from strict JSON to LONGTEXT so AES-256 encrypted payloads store cleanly on MySQL & SQLite
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            if (Schema::hasColumn('whatsapp_messages', 'metadata')) {
                $table->longText('metadata')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            if (Schema::hasColumn('whatsapp_messages', 'idempotency_key')) {
                $table->dropUnique(['idempotency_key']);
                $table->dropColumn('idempotency_key');
            }
        });

        Schema::table('support_chat_sessions', function (Blueprint $table) {
            $drop = array_filter([
                'visitor_username',
                'visitor_avatar',
            ], fn (string $col): bool => Schema::hasColumn('support_chat_sessions', $col));

            if ($drop !== []) {
                $table->dropColumn($drop);
            }
        });
    }
};
