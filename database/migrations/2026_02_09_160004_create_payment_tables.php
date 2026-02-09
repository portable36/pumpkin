<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Payment Gateways
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // bKash, SSLCommerz, Stripe, PayPal
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('credentials')->nullable(); // encrypted
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_sandbox')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('payment_gateway_id')->constrained();
            $table->string('gateway_name'); // cached for history
            $table->string('transaction_id')->nullable()->unique();
            $table->string('payment_intent_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->string('status')->default('pending'); // pending, processing, completed, failed, refunded
            $table->json('gateway_response')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // Payment Webhooks
        Schema::create('payment_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_gateway_id')->constrained();
            $table->string('event_type');
            $table->json('payload');
            $table->string('status')->default('pending'); // pending, processed, failed
            $table->integer('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // Refunds
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->string('refund_number')->unique();
            $table->foreignId('payment_id')->constrained();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('return_id')->nullable()->constrained();
            $table->decimal('amount', 10, 2);
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->string('gateway_refund_id')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payment_webhooks');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_gateways');
    }
};
