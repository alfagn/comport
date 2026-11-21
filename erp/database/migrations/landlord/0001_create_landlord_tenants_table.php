<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('landlord')->create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('database_name');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_internal')->default(false);
            $table->string('locale')->default('ar');
            $table->string('currency')->default('EGP');
            $table->string('timezone')->default('Africa/Cairo');
            $table->json('settings')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('is_internal');
        });

        Schema::connection('landlord')->create('tenant_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('domain')->unique();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_custom')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('verification_token')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('domain');
            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('tenant_domains');
        Schema::connection('landlord')->dropIfExists('tenants');
    }
};
