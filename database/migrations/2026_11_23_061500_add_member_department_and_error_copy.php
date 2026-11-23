<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Existing people remain unassigned. The owner chooses each department.
            $table->string('department', 32)->nullable();
        });
        (new \Database\Seeders\EditorialDefaultsBackfillSeeder)->run();
    }

    public function down(): void
    {
        Schema::table('members', fn (Blueprint $table) => $table->dropColumn('department'));
        // Keep editorial rows because the owner may already have edited them.
    }
};
