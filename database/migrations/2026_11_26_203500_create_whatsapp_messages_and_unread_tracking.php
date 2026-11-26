<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_chat_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('support_chat_sessions', 'unread_count')) {
                $table->unsignedInteger('unread_count')->default(0)->after('message_count');
            }
            if (! Schema::hasColumn('support_chat_sessions', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('ai_paused');
            }
            if (! Schema::hasColumn('support_chat_sessions', 'last_message_preview')) {
                $table->string('last_message_preview', 255)->nullable()->after('last_external_message_id');
            }
            if (! Schema::hasColumn('support_chat_sessions', 'contact_notes')) {
                $table->text('contact_notes')->nullable()->after('handoff_summary');
            }
            if (! Schema::hasColumn('support_chat_sessions', 'last_admin_read_at')) {
                $table->timestamp('last_admin_read_at')->nullable()->after('last_message_at');
            }
            if (! Schema::hasColumn('support_chat_sessions', 'last_customer_message_at')) {
                $table->timestamp('last_customer_message_at')->nullable()->after('last_admin_read_at');
            }
            if (! Schema::hasColumn('support_chat_sessions', 'last_unread_notified_at')) {
                $table->timestamp('last_unread_notified_at')->nullable()->after('last_customer_message_at');
            }
        });

        if (! Schema::hasTable('whatsapp_messages')) {
            Schema::create('whatsapp_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('support_chat_session_id')
                    ->constrained('support_chat_sessions')
                    ->cascadeOnDelete();
                $table->string('wa_message_id', 190)->nullable()->index();
                $table->string('reply_to_wa_message_id', 190)->nullable()->index();
                $table->string('direction', 16)->default('inbound');
                $table->string('sender_type', 20)->default('customer');
                $table->foreignId('sender_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('type', 24)->default('text');
                $table->text('body')->nullable();
                $table->string('media_id', 160)->nullable();
                $table->string('media_disk', 32)->nullable();
                $table->string('media_path', 500)->nullable();
                $table->string('media_mime', 120)->nullable();
                $table->string('media_filename', 255)->nullable();
                $table->unsignedBigInteger('media_size')->nullable();
                $table->unsignedInteger('media_duration')->nullable();
                $table->string('reaction_emoji', 32)->nullable();
                $table->json('metadata')->nullable();
                $table->string('status', 20)->default('sent');
                $table->timestamp('delivered_at')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamp('notified_email_at')->nullable();
                $table->timestamps();

                $table->index(['support_chat_session_id', 'created_at']);
                $table->index(['direction', 'read_at', 'notified_email_at'], 'wa_unread_alert_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');

        Schema::table('support_chat_sessions', function (Blueprint $table) {
            $drop = array_filter([
                'unread_count',
                'is_pinned',
                'last_message_preview',
                'contact_notes',
                'last_admin_read_at',
                'last_customer_message_at',
                'last_unread_notified_at',
            ], fn (string $col): bool => Schema::hasColumn('support_chat_sessions', $col));

            if ($drop !== []) {
                $table->dropColumn($drop);
            }
        });
    }
};
