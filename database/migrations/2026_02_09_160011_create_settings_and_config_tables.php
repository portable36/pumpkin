<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // System Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group'); // general, shipping, payment, email, sms
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, number, boolean, json, file
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });

        // Product SKU Configuration
        Schema::create('sku_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->integer('length')->default(8);
            $table->integer('next_number')->default(1);
            $table->boolean('auto_generate')->default(true);
            $table->boolean('include_category_code')->default(false);
            $table->boolean('include_vendor_code')->default(false);
            $table->timestamps();
        });

        // Barcode Configuration
        Schema::create('barcode_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('CODE128'); // CODE128, EAN13, QR
            $table->string('prefix')->nullable();
            $table->integer('length')->default(12);
            $table->integer('next_number')->default(1);
            $table->boolean('auto_generate')->default(true);
            $table->timestamps();
        });

        // User Addresses
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('shipping'); // shipping, billing
            $table->string('label')->nullable(); // home, office, other
            $table->string('name');
            $table->string('phone');
            $table->string('address');
            $table->string('apartment')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('country');
            $table->string('postal_code');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Currency Configuration
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 3)->unique();
            $table->string('symbol');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // FAQ
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('question');
            $table->text('answer');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pages (Static content)
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Banners / Sliders
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->string('mobile_image')->nullable();
            $table->string('link')->nullable();
            $table->string('position')->default('home_slider'); // home_slider, home_banner, sidebar
            $table->integer('sort_order')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('user_addresses');
        Schema::dropIfExists('barcode_configurations');
        Schema::dropIfExists('sku_configurations');
        Schema::dropIfExists('settings');
    }
};
