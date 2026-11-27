<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_chat_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('support_chat_sessions', 'is_archived')) {
                $table->boolean('is_archived')->default(false)->index()->after('is_pinned');
            }
        });
    }

    public function down(): void
    {
        Schema::table('support_chat_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('support_chat_sessions', 'is_archived')) {
                $table->dropColumn('is_archived');
            }
        });
    }
};
