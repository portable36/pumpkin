<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('vendor_ledgers')) {
            Schema::create('vendor_ledgers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id');
                $table->enum('type', ['sale', 'commission', 'refund', 'payout', 'adjustment']); // income or deduction
                $table->unsignedBigInteger('reference_id')->nullable(); // order_id, payment_id, etc
                $table->decimal('amount', 15, 2);
                $table->text('description')->nullable();
                $table->string('balance_after', 15)->nullable(); // running balance
                $table->timestamps();
                $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_ledgers');
    }
};
