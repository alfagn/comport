<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Free-form numbered specification sections for a job post
 * ([{"heading": ..., "items": [...]}]) — mirrors the six-part layout of
 * the original static job pages without hardcoding section names.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->json('sections')->nullable()->after('requirements');
        });
    }

    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropColumn('sections');
        });
    }
};
