<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Service offerings ("Solutions"). Each service has a tab title for the
 * home-page tab strip and a full panel (kicker, heading, description,
 * deliverables list and a proof metric).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('tag')->nullable();            // e.g. "Engineering" / "Growth"
            $table->string('kicker')->nullable();         // pillar label shown above the heading
            $table->string('heading')->nullable();        // full panel heading
            $table->text('description')->nullable();
            $table->json('deliverables')->nullable();     // bullet list shown inside the panel
            $table->string('metric_num')->nullable();     // proof metric, e.g. "99.96%"
            $table->string('metric_label')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
