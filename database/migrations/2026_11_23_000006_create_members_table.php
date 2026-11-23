<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * People: founders and team members share one table, split by `kind`.
 * Founders get a richer profile (long bio paragraphs + detail page),
 * regular members show name/role/focus on the team grid.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('role')->nullable();
            $table->string('kind')->default('member')->index();  // founder | member
            $table->string('focus')->nullable();                  // one-line specialty for team cards
            $table->json('bio')->nullable();                      // array of bio paragraphs (founder pages)
            $table->string('photo')->nullable();
            $table->string('email')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
