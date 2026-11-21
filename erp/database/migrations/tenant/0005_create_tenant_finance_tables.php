<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('tenant')->create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('crm_clients')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->date('issue_date');
            $table->date('due_date');
            $table->unsignedInteger('subtotal_minor')->default(0);
            $table->unsignedInteger('tax_minor')->default(0);
            $table->unsignedInteger('discount_minor')->default(0);
            $table->unsignedInteger('total_minor')->default(0);
            $table->unsignedInteger('amount_paid_minor')->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->string('status')->default('draft'); // draft, sent, partial, paid, overdue, void
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });

        Schema::connection('tenant')->create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('description');
            $table->float('quantity')->default(1);
            $table->unsignedInteger('unit_price_minor');
            $table->unsignedInteger('total_minor');
            $table->timestamps();
        });

        Schema::connection('tenant')->create('estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('crm_clients')->cascadeOnDelete();
            $table->string('estimate_number')->unique();
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->unsignedInteger('subtotal_minor')->default(0);
            $table->unsignedInteger('tax_minor')->default(0);
            $table->unsignedInteger('total_minor')->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->string('status')->default('draft'); // draft, sent, accepted, declined
            $table->timestamps();
        });

        Schema::connection('tenant')->create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->unsignedInteger('amount_minor');
            $table->string('currency', 3)->default('EGP');
            $table->string('payment_method')->default('bank_transfer');
            $table->string('transaction_reference')->nullable();
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('title');
            $table->unsignedInteger('amount_minor');
            $table->string('currency', 3)->default('EGP');
            $table->date('date');
            $table->string('receipt_path')->nullable();
            $table->string('vendor_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('expenses');
        Schema::connection('tenant')->dropIfExists('expense_categories');
        Schema::connection('tenant')->dropIfExists('payments');
        Schema::connection('tenant')->dropIfExists('estimates');
        Schema::connection('tenant')->dropIfExists('invoice_items');
        Schema::connection('tenant')->dropIfExists('invoices');
    }
};
