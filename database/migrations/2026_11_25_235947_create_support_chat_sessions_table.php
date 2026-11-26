<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One public visitor's conversation with the AI support chatbot.
 *
 * The row is the conversation participant for the Laravel AI SDK (the
 * transcript itself lives in agent_conversation_messages). It records the
 * contact details the visitor shared and whether the chat was handed off
 * to the team through the Inbox.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique();
            $table->string('conversation_id', 36)->nullable()->index();
            $table->string('visitor_name')->nullable();
            $table->string('visitor_email')->nullable();
            $table->string('status', 20)->default('open')->index(); // open | handed_off | closed
            $table->text('handoff_summary')->nullable();
            $table->foreignId('contact_message_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('message_count')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('landing_page')->nullable();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_chat_sessions');
    }
};
