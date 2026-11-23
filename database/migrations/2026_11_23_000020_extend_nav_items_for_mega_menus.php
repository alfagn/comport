<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extends navigation links so the header mega-menu is editable too:
 * a "column" row (parent_id null) groups child links, which can carry a
 * sub-line, an image and a meta line for feature cards.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nav_items', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('menu')->constrained('nav_items')->nullOnDelete();
            $table->string('sub', 200)->nullable()->after('url');
            $table->string('meta', 160)->nullable()->after('sub');
            $table->string('image', 255)->nullable()->after('meta');
        });
    }

    public function down(): void
    {
        Schema::table('nav_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn(['sub', 'meta', 'image']);
        });
    }
};
