<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'member_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('member_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('members')
                    ->cascadeOnDelete();
            });
        }

        // Backfill users.member_id from existing members.user_id links
        $linkedMembers = DB::table('members')->whereNotNull('user_id')->get(['id', 'user_id']);
        foreach ($linkedMembers as $m) {
            DB::table('users')
                ->where('id', $m->user_id)
                ->whereNull('member_id')
                ->update(['member_id' => $m->id]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'member_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['member_id']);
                $table->dropColumn('member_id');
            });
        }
    }
};
