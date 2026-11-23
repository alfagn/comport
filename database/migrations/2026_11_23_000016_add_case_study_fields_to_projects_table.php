<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Structured case-study content so the dashboard can control every block
 * of a project page without editing HTML: meta facts, outcome stats,
 * numbered narrative sections, an optional pull quote and a video block.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('location')->nullable()->after('kicker');
            $table->json('facts')->nullable()->after('excerpt');       // [{"label": ..., "value": ...}]
            $table->json('stats')->nullable()->after('facts');         // [{"target": ..., "decimals": ..., "suffix": ..., "label": ...}]
            $table->json('sections')->nullable()->after('stats');      // [{"eyebrow": ..., "heading": ..., "items": [...]}]
            $table->text('quote')->nullable()->after('sections');
            $table->string('quote_cite')->nullable()->after('quote');
            $table->string('video_poster')->nullable()->after('quote_cite');
            $table->string('video_src')->nullable()->after('video_poster');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['location', 'facts', 'stats', 'sections', 'quote', 'quote_cite', 'video_poster', 'video_src']);
        });
    }
};
