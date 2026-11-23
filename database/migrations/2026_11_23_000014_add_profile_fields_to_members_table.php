<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extra founder-profile fields: a lead paragraph under the role and an
 * expertise list (title + description pairs) for the profile page.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->text('lead')->nullable()->after('focus');
            $table->json('expertise')->nullable()->after('bio');  // [{"title": ..., "desc": ...}]
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['lead', 'expertise']);
        });
    }
};
