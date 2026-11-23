<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Job applications submitted through the public careers form.
 *
 * CV files are NOT stored here: cv_path points at a file on the private
 * `local` disk (storage/app/private), reachable only through the
 * authenticated admin download route. No applicant rows are seeded.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('location');
            $table->string('linkedin')->nullable();
            $table->string('portfolio')->nullable();
            $table->string('experience_years', 20);
            $table->string('notice_period', 20);
            $table->text('achievement');
            $table->string('cv_path');
            $table->string('status', 20)->default('new'); // new | reviewed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
