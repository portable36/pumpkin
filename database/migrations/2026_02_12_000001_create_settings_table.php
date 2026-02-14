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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'app_name', 'value' => 'Pumpkin Marketplace'],
            ['key' => 'app_tagline', 'value' => 'Your trusted marketplace for quality products'],
            ['key' => 'admin_email', 'value' => 'admin@pumpkin.com'],
            ['key' => 'support_email', 'value' => 'support@pumpkin.com'],
            ['key' => 'commission_rate', 'value' => '15'],
            ['key' => 'tax_rate', 'value' => '10'],
            ['key' => 'standard_shipping', 'value' => '9.99'],
            ['key' => 'express_shipping', 'value' => '24.99'],
            ['key' => 'overnight_shipping', 'value' => '49.99'],
            ['key' => 'vendor_approval_required', 'value' => 'yes'],
            ['key' => 'product_approval_required', 'value' => 'no'],
            ['key' => 'user_registration_enabled', 'value' => 'yes'],
            ['key' => 'vendor_registration_enabled', 'value' => 'yes'],
            ['key' => 'max_products_per_vendor', 'value' => '10000'],
            ['key' => 'min_order_amount', 'value' => '0'],
            ['key' => 'payment_methods', 'value' => 'credit_card,paypal,bank_transfer'],
            ['key' => 'currency_symbol', 'value' => '$'],
            ['key' => 'timezone', 'value' => 'America/New_York'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
