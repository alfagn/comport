<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Small display fields the original static site shows: the "01" service
 * and "SECTOR 01" industry numbers, and the job requisition code badge.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('num')->nullable()->after('id');          // "01".."05"
        });
        Schema::table('industries', function (Blueprint $table) {
            $table->string('num')->nullable()->after('id');          // "SECTOR 01"...
        });
        Schema::table('job_posts', function (Blueprint $table) {
            $table->string('ref_code')->nullable()->after('slug');   // e.g. REQ-2026-014
        });
    }

    public function down(): void
    {
        Schema::table('services', fn (Blueprint $table) => $table->dropColumn('num'));
        Schema::table('industries', fn (Blueprint $table) => $table->dropColumn('num'));
        Schema::table('job_posts', fn (Blueprint $table) => $table->dropColumn('ref_code'));
    }
};
