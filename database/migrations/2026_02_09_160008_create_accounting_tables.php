<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Account Types / Chart of Accounts
        Schema::create('account_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('category'); // asset, liability, equity, income, expense
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Ledger Entries
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number')->unique();
            $table->foreignId('account_type_id')->constrained();
            $table->string('type'); // debit, credit
            $table->decimal('amount', 10, 2);
            $table->string('reference_type')->nullable(); // order, payout, expense, refund
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->date('transaction_date');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['reference_type', 'reference_id']);
        });

        // Expenses
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique();
            $table->foreignId('expense_category_id')->constrained();
            $table->foreignId('vendor_id')->nullable()->constrained(); // for vendor-specific expenses
            $table->decimal('amount', 10, 2);
            $table->string('type'); // marketing, delivery, product_cost, operational, other
            $table->text('description')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('receipt')->nullable();
            $table->date('expense_date');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('vendor_id')->nullable()->constrained();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('status')->default('unpaid'); // unpaid, paid, cancelled
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });

        // Tax/VAT Configuration
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('rate', 5, 2); // percentage
            $table->string('type'); // vat, sales_tax, gst
            $table->json('applicable_countries')->nullable();
            $table->json('applicable_states')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Financial Reports Summary
        Schema::create('financial_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('period_type'); // daily, weekly, monthly, quarterly, yearly
            $table->decimal('revenue', 10, 2)->default(0);
            $table->decimal('expenses', 10, 2)->default(0);
            $table->decimal('commissions', 10, 2)->default(0);
            $table->decimal('refunds', 10, 2)->default(0);
            $table->decimal('taxes', 10, 2)->default(0);
            $table->decimal('net_profit', 10, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['vendor_id', 'period_start', 'period_end', 'period_type'], 'vendor_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_summaries');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('account_types');
    }
};
