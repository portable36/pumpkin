<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Marketing Campaigns
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // email, sms, banner, discount
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Campaign Tracking
        Schema::create('campaign_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('session_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->timestamps();
        });

        // Conversion Tracking
        Schema::create('conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_campaign_id')->nullable()->constrained();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('order_id')->nullable()->constrained();
            $table->string('session_id')->nullable();
            $table->string('conversion_type'); // purchase, signup, add_to_cart
            $table->decimal('value', 10, 2)->default(0);
            $table->timestamps();
        });

        // Tracking Scripts (GTM, FB Pixel, etc.)
        Schema::create('tracking_scripts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // google_tag_manager, facebook_pixel, google_analytics, custom
            $table->text('script_code');
            $table->string('placement'); // head, body_start, body_end
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Customer Behavior Analytics
        Schema::create('customer_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('session_id')->unique();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('page_views')->default(0);
            $table->timestamps();
        });

        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained();
            $table->string('url');
            $table->string('page_type')->nullable(); // home, category, product, cart, checkout
            $table->integer('time_spent')->nullable(); // seconds
            $table->timestamps();
        });

        // Product Views for Analytics
        Schema::create('product_view_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('session_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('referrer')->nullable();
            $table->timestamps();
            
            $table->index(['product_id', 'created_at']);
        });

        // Search Analytics
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('session_id')->nullable();
            $table->string('query');
            $table->integer('results_count')->default(0);
            $table->boolean('has_clicked')->default(false);
            $table->foreignId('clicked_product_id')->nullable()->constrained('products');
            $table->timestamps();
            
            $table->index(['query', 'created_at']);
        });

        // Sales Funnel Tracking
        Schema::create('funnel_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained();
            $table->string('event_type'); // view, add_to_cart, checkout, purchase
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funnel_events');
        Schema::dropIfExists('search_logs');
        Schema::dropIfExists('product_view_logs');
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('customer_sessions');
        Schema::dropIfExists('tracking_scripts');
        Schema::dropIfExists('conversions');
        Schema::dropIfExists('campaign_clicks');
        Schema::dropIfExists('marketing_campaigns');
    }
};
