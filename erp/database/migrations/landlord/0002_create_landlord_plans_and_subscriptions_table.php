<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('landlord')->create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('price_monthly_minor')->default(0);
            $table->unsignedInteger('price_yearly_minor')->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->unsignedSmallInteger('trial_days')->default(14);
            $table->json('features')->nullable(); // hr, crm, marketing, projects, finance
            $table->json('limits')->nullable(); // max_users, max_employees, max_projects
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::connection('landlord')->create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
            $table->string('status')->default('trialing'); // trialing, active, past_due, canceled, expired
            $table->string('billing_interval')->default('monthly'); // monthly, yearly
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::connection('landlord')->create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->unsignedInteger('amount_minor');
            $table->string('currency', 3)->default('EGP');
            $table->string('status')->default('pending'); // pending, paid, rejected, refunded
            $table->string('payment_method')->default('manual_bank_transfer');
            $table->string('receipt_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::connection('landlord')->create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedSmallInteger('discount_percent')->nullable();
            $table->unsignedInteger('discount_amount_minor')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('times_used')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('coupons');
        Schema::connection('landlord')->dropIfExists('subscription_invoices');
        Schema::connection('landlord')->dropIfExists('subscriptions');
        Schema::connection('landlord')->dropIfExists('plans');
    }
};
