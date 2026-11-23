<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /** Additive content repair runs during the normal non-destructive upgrade. */
    public function up(): void
    {
        (new \Database\Seeders\EditorialDefaultsBackfillSeeder)->run();
    }

    /** Content may have been edited since upgrade; never delete it on rollback. */
    public function down(): void {}
};
