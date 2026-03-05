<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            \App\Models\Customer::whereNull('phone')->get()->each(function ($customer) {
                $customer->phone = '0000000000' . $customer->id;
                $customer->save();
            });

            // Make phone required (not nullable)
            $table->string('phone', 15)->nullable(false)->change();

            // Name will be nullable
            $table->string('name')->nullable()->change();

            // Add composite unique constraint on phone and vendor_id
            $table->unique(['phone', 'vendor_id'], 'customers_phone_vendor_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('customers_phone_vendor_id_unique');

            // Revert phone back to nullable
            $table->string('phone', 15)->nullable()->change();
        });
    }
};
