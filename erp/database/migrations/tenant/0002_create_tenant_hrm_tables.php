<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('tenant')->create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('employee_code')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('gender')->default('male'); // male, female
            $table->date('date_of_birth')->nullable();
            $table->date('joining_date')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained('designations')->nullOnDelete();
            $table->string('employment_status')->default('active'); // active, probation, resigned, terminated
            $table->unsignedInteger('basic_salary_minor')->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('iban')->nullable();
            $table->timestamps();

            $table->index('employment_status');
        });

        Schema::connection('tenant')->create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->string('status')->default('present'); // present, absent, late, half_day, on_leave
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'date']);
        });

        Schema::connection('tenant')->create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedSmallInteger('days_allowed')->default(21);
            $table->boolean('is_paid')->default(true);
            $table->timestamps();
        });

        Schema::connection('tenant')->create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('days_count')->default(1);
            $table->text('reason')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('basic_salary_minor');
            $table->unsignedInteger('allowances_total_minor')->default(0);
            $table->unsignedInteger('deductions_total_minor')->default(0);
            $table->unsignedInteger('net_salary_minor');
            $table->string('currency', 3)->default('EGP');
            $table->string('status')->default('draft'); // draft, approved, paid
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'month', 'year']);
        });

        Schema::connection('tenant')->create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->cascadeOnDelete();
            $table->string('type'); // allowance, deduction
            $table->string('title');
            $table->unsignedInteger('amount_minor');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('payroll_items');
        Schema::connection('tenant')->dropIfExists('payrolls');
        Schema::connection('tenant')->dropIfExists('leave_requests');
        Schema::connection('tenant')->dropIfExists('leave_types');
        Schema::connection('tenant')->dropIfExists('attendances');
        Schema::connection('tenant')->dropIfExists('employees');
        Schema::connection('tenant')->dropIfExists('designations');
        Schema::connection('tenant')->dropIfExists('departments');
    }
};
