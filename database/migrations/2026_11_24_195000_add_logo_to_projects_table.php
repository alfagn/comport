<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('projects', 'logo')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->string('logo')->nullable()->after('slug');
            });
        }

        // Backfill official project logos for existing case studies if not set yet
        DB::table('projects')
            ->where('slug', 'alfagn')
            ->whereNull('logo')
            ->update(['logo' => 'assets/img/work/alfagn.png']);

        DB::table('projects')
            ->where('slug', 'western-elearning-academy')
            ->whereNull('logo')
            ->update(['logo' => 'assets/img/work/westvale/logo.png']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('projects', 'logo')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropColumn('logo');
            });
        }
    }
};
