<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
            $table->index('status');
            $table->index('payment_status');
            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex('status');
            $table->dropIndex('payment_status');
            $table->dropIndex('vendor_id');
        });
    }
};

