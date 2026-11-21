<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('tenant')->create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->foreignId('client_id')->nullable()->constrained('crm_clients')->nullOnDelete();
            $table->text('description')->nullable();
            $table->unsignedInteger('budget_minor')->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->string('status')->default('planning'); // planning, in_progress, on_hold, completed, canceled
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->foreignId('leader_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
        });

        Schema::connection('tenant')->create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('pending'); // pending, completed
            $table->timestamps();
        });

        Schema::connection('tenant')->create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('milestone_id')->nullable()->constrained('milestones')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('todo'); // backlog, todo, in_progress, in_review, completed
            $table->date('due_date')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->float('estimated_hours')->default(0);
            $table->float('logged_hours')->default(0);
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });

        Schema::connection('tenant')->create('task_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('comment');
            $table->timestamps();
        });

        Schema::connection('tenant')->create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->float('hours');
            $table->boolean('is_billable')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('timesheets');
        Schema::connection('tenant')->dropIfExists('task_comments');
        Schema::connection('tenant')->dropIfExists('tasks');
        Schema::connection('tenant')->dropIfExists('milestones');
        Schema::connection('tenant')->dropIfExists('projects');
    }
};
