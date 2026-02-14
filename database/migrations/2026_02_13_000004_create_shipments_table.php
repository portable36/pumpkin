<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('gateway'); // steadfast, pathao, etc
            $table->string('tracking_number')->nullable()->unique();
            $table->timestamp('pickup_date')->nullable();
            $table->timestamp('delivery_date')->nullable();
            $table->string('status')->default('pending'); // pending, picked_up, in_transit, out_for_delivery, delivered, failed, cancelled, returned
            $table->decimal('weight', 8, 3)->nullable();
            $table->json('dimensions')->nullable(); // length, width, height
            $table->decimal('cost', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('user_id');
            $table->index('gateway');
            $table->index('status');
            $table->index('tracking_number');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
