<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Each opening can name its hiring manager from the team roster (members
 * table) instead of hardcoding a person on the job page.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->foreignId('hiring_manager_id')->nullable()->after('ref_code')
                ->constrained('members')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hiring_manager_id');
        });
    }
};
