<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Editable content blocks for the public pages.
 *
 * Every piece of copy the CMS exposes (headings, paragraphs, button
 * labels, image paths, stat numbers...) is a row keyed by page + key,
 * with a typed editor in the dashboard. Blade templates read values
 * through the sec() helper with a hard-coded fallback, so the site
 * renders even before seeding.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('page')->index();             // home, about, work, footer...
            $table->string('key');
            $table->string('label');                     // human label for the dashboard editor
            $table->string('type')->default('text');     // text | textarea | image | url | number
            $table->longText('value')->nullable();       // current value; null falls back to the template default
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['page', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};
