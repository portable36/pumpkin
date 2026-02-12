<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('account_holder_name');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('routing_number')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('iban')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->index('vendor_id');
        });

        Schema::create('vendor_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status', ['pending', 'processing', 'processed', 'failed'])->default('pending');
            $table->foreignId('bank_account_id')->nullable()->constrained('vendor_bank_details');
            $table->string('transaction_id')->nullable()->unique();
            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('vendor_id');
            $table->index('status');
        });

        Schema::create('vendor_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->enum('type', ['credit', 'debit'])->default('credit');
            $table->decimal('amount', 12, 2);
            $table->string('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->text('description');
            $table->decimal('running_balance', 12, 2);
            $table->timestamps();
            $table->index('vendor_id');
        });

        Schema::create('vendor_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['admin', 'manager', 'staff'])->default('staff');
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['vendor_id', 'user_id']);
        });

        Schema::create('vendor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->between(1, 5);
            $table->string('title');
            $table->text('review');
            $table->boolean('is_verified_buyer')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            $table->index(['vendor_id', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_reviews');
        Schema::dropIfExists('vendor_staff');
        Schema::dropIfExists('vendor_ledgers');
        Schema::dropIfExists('vendor_payouts');
        Schema::dropIfExists('vendor_bank_details');
    }
};
