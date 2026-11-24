<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'live_url')) {
                $table->string('live_url')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('projects', 'video_url')) {
                $table->string('video_url')->nullable()->after('video_src');
            }
            if (!Schema::hasColumn('projects', 'gallery')) {
                $table->json('gallery')->nullable()->after('image');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            if (!Schema::hasColumn('members', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['live_url', 'video_url', 'gallery']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id']);
        });
    }
};
