<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The hero slider's docked tab bar shows a label, short title and
 * descriptor per slide; these columns feed it from the CMS.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->string('tab_label')->nullable()->after('meta');   // e.g. OVERVIEW
            $table->string('tab_title')->nullable()->after('tab_label');
            $table->string('tab_sub')->nullable()->after('tab_title');
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn(['tab_label', 'tab_title', 'tab_sub']);
        });
    }
};
