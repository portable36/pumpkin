<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SMS Gateway Configuration
        Schema::create('sms_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('credentials')->nullable(); // encrypted
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // SMS Logs
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sms_gateway_id')->nullable()->constrained();
            $table->string('to');
            $table->text('message');
            $table->string('type'); // otp, order_update, promotional, alert
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->text('response')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // Email Templates
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subject');
            $table->text('body');
            $table->json('variables')->nullable(); // available template variables
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Notification Preferences
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('channel'); // email, sms, push, database
            $table->string('event_type'); // order_update, price_drop, stock_alert, etc.
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            
            $table->unique(['user_id', 'channel', 'event_type']);
        });

        // Push Notification Subscriptions
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('endpoint');
            $table->string('public_key')->nullable();
            $table->string('auth_token')->nullable();
            $table->string('device_type')->nullable(); // web, ios, android
            $table->timestamps();
        });

        // Price Drop Alerts
        Schema::create('price_drop_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('original_price', 10, 2);
            $table->decimal('target_price', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_notified')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
        });

        // Stock Alerts
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_notified')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_alerts');
        Schema::dropIfExists('price_drop_alerts');
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('sms_gateways');
    }
};
