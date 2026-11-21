<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // CRM Tables
        Schema::connection('tenant')->create('crm_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('currency', 3)->default('EGP');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('crm_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_client_id')->constrained('crm_clients')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('position')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('source')->default('website'); // facebook, google, website, referral, linkedin
            $table->string('status')->default('new'); // new, contacted, qualified, proposal_sent, won, lost
            $table->unsignedInteger('estimated_value_minor')->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->string('contact_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::connection('tenant')->create('crm_deals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('crm_client_id')->constrained('crm_clients')->cascadeOnDelete();
            $table->string('stage')->default('discovery'); // discovery, proposal, negotiation, closed_won, closed_lost
            $table->unsignedInteger('amount_minor')->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->date('expected_close_date')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('stage');
        });

        Schema::connection('tenant')->create('crm_activities', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->string('type')->default('call'); // call, meeting, email, note, task
            $table->dateTime('due_date');
            $table->string('status')->default('pending'); // pending, completed
            $table->foreignId('crm_client_id')->nullable()->constrained('crm_clients')->nullOnDelete();
            $table->foreignId('crm_lead_id')->nullable()->constrained('crm_leads')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Marketing Tables
        Schema::connection('tenant')->create('marketing_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_name');
            $table->string('industry')->nullable();
            $table->unsignedInteger('monthly_retainer_minor')->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->foreignId('account_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('active'); // active, paused, churned
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('marketing_client_id')->nullable()->constrained('marketing_clients')->nullOnDelete();
            $table->string('channel')->default('meta'); // meta, google_ads, tiktok, seo, email, linkedin, influencer
            $table->unsignedInteger('budget_minor')->default(0);
            $table->unsignedInteger('spent_minor')->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->string('target_audience')->nullable();
            $table->string('status')->default('planning'); // planning, active, paused, completed
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::connection('tenant')->create('marketing_social_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_client_id')->constrained('marketing_clients')->cascadeOnDelete();
            $table->string('platform')->default('facebook'); // facebook, instagram, linkedin, x, tiktok
            $table->text('caption');
            $table->string('media_url')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->string('status')->default('draft'); // draft, scheduled, published, failed
            $table->timestamps();
        });

        Schema::connection('tenant')->create('marketing_kpi_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_campaign_id')->constrained('marketing_campaigns')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedInteger('leads_generated')->default(0);
            $table->unsignedInteger('conversions')->default(0);
            $table->unsignedInteger('spend_minor')->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->timestamps();

            $table->index(['marketing_campaign_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('marketing_kpi_logs');
        Schema::connection('tenant')->dropIfExists('marketing_social_posts');
        Schema::connection('tenant')->dropIfExists('marketing_campaigns');
        Schema::connection('tenant')->dropIfExists('marketing_clients');
        Schema::connection('tenant')->dropIfExists('crm_activities');
        Schema::connection('tenant')->dropIfExists('crm_deals');
        Schema::connection('tenant')->dropIfExists('crm_leads');
        Schema::connection('tenant')->dropIfExists('crm_contacts');
        Schema::connection('tenant')->dropIfExists('crm_clients');
    }
};
