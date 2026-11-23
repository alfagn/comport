<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tech/skill tags shown on each job card ("Laravel 13", "PHP 8.5"...).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->json('tags')->nullable()->after('pills');
        });
    }

    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropColumn('tags');
        });
    }
};
