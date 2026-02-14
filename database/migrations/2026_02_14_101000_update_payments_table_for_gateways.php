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
        Schema::table('payments', function (Blueprint $table) {
            // Add external_id column if not exists (for gateway transaction IDs)
            if (!Schema::hasColumn('payments', 'external_id')) {
                $table->string('external_id')->nullable()->index()->after('gateway');
            }

            // Add parent_order_id for multi-vendor order tracking
            if (!Schema::hasColumn('payments', 'parent_order_id')) {
                $table->unsignedBigInteger('parent_order_id')->nullable()->after('order_id');
                $table->foreign('parent_order_id')->references('id')->on('orders')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['parent_order_id']);
            $table->dropColumnIfExists(['external_id', 'parent_order_id']);
        });
    }
};
