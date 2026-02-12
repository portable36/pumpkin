<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('payment_method');
            $table->decimal('amount', 12, 2);
            $table->string('transaction_id')->unique();
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->json('gateway_response')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
            $table->index('order_id');
        });

        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('tracking_number')->unique();
            $table->string('courier_name');
            $table->enum('status', ['pending', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed'])->default('pending');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->date('estimated_delivery_date')->nullable();
            $table->json('carrier_response')->nullable();
            $table->timestamps();
            $table->index('order_id');
        });

        Schema::create('order_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->string('reason');
            $table->text('description')->nullable();
            $table->enum('status', ['requested', 'approved', 'rejected', 'returned', 'refunded'])->default('requested');
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->index('order_id');
        });

        Schema::create('order_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('reason');
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->string('refund_method');
            $table->string('transaction_id')->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_refunds');
        Schema::dropIfExists('order_returns');
        Schema::dropIfExists('order_shipments');
        Schema::dropIfExists('order_payments');
    }
};
