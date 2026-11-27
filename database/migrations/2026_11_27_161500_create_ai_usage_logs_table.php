<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_usage_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('invocation_id', 64)->nullable()->index();
            $table->string('channel', 32)->default('system')->index();
            $table->string('agent', 120)->nullable();
            $table->string('provider', 60)->index();
            $table->string('driver', 40)->nullable();
            $table->string('model', 120)->index();
            $table->string('status', 24)->index();
            $table->unsignedSmallInteger('http_code')->nullable()->index();
            $table->string('error_category', 48)->nullable()->index();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->unsignedInteger('reasoning_tokens')->default(0);
            $table->unsignedInteger('cached_tokens')->default(0);
            $table->unsignedInteger('total_tokens')->default(0);
            $table->unsignedInteger('latency_ms')->nullable();
            $table->json('rate_limits')->nullable();
            $table->timestamps();

            $table->index(['provider', 'model', 'created_at'], 'ai_usage_provider_model_date_idx');
            $table->index(['status', 'created_at'], 'ai_usage_status_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};
