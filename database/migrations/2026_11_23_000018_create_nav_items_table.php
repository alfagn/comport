<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Editable navigation links. Each link belongs to a named menu so the
 * dashboard can control the utility bar and the mobile drawer separately.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nav_items', function (Blueprint $table) {
            $table->id();
            // Which menu this link appears in: "utility" (top bar) or "drawer" (mobile).
            $table->string('menu', 40)->index();
            $table->string('label', 120);
            // Internal path (/work) or absolute URL — rendered as-is.
            $table->string('url', 255);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nav_items');
    }
};
