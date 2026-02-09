<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Shipping Zones
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('countries')->nullable();
            $table->json('states')->nullable();
            $table->json('cities')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Shipping Methods
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // flat_rate, free, per_item, weight_based, courier
            $table->decimal('cost', 10, 2)->default(0);
            $table->integer('min_delivery_days')->nullable();
            $table->integer('max_delivery_days')->nullable();
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('shipping_method_zone', function (Blueprint $table) {
            $table->foreignId('shipping_method_id')->constrained()->onDelete('cascade');
            $table->foreignId('shipping_zone_id')->constrained()->onDelete('cascade');
            $table->decimal('cost', 10, 2)->default(0);
            $table->primary(['shipping_method_id', 'shipping_zone_id'], 'sm_zone_primary');
        });

        // Courier Services (Pathao, Steadfast)
        Schema::create('courier_services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Pathao, Steadfast
            $table->string('slug')->unique();
            $table->json('credentials')->nullable(); // encrypted API keys
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_sandbox')->default(true);
            $table->timestamps();
        });

        // Shipments
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('warehouse_id')->nullable()->constrained();
            $table->foreignId('shipping_method_id')->nullable()->constrained();
            $table->foreignId('courier_service_id')->nullable()->constrained();
            $table->string('courier_tracking_id')->nullable();
            $table->string('status')->default('pending'); // pending, picked_up, in_transit, out_for_delivery, delivered, failed, returned
            $table->decimal('shipping_cost', 10, 2);
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('carrier_name')->nullable();
            $table->json('tracking_history')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained();
            $table->integer('quantity');
            $table->timestamps();
        });

        // Tracking Updates
        Schema::create('tracking_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_updates');
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('courier_services');
        Schema::dropIfExists('shipping_method_zone');
        Schema::dropIfExists('shipping_methods');
        Schema::dropIfExists('shipping_zones');
    }
};
