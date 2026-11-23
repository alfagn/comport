<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Repeating content lists (principles, timeline milestones, benefits,
 * hiring steps, career tracks, culture cards). One row per card keeps the
 * data normalized — no delimiter-joined blobs in text columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_list_items', function (Blueprint $table) {
            $table->id();
            // Which list this card belongs to, e.g. "about.principles".
            $table->string('group', 60)->index();
            $table->string('title', 200);
            $table->text('body')->nullable();
            // Small top label: timeline year, step duration, track code.
            $table->string('meta', 80)->nullable();
            // Secondary small label, e.g. the role count on a track card.
            $table->string('badge', 80)->nullable();
            // Link text, e.g. "Filter Engineering Jobs".
            $table->string('cta', 160)->nullable();
            // Link target or filter key, e.g. "/work" or "engineering".
            $table->string('url', 255)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_list_items');
    }
};
