<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update Vendors table
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('store_name');
            $table->string('email')->nullable()->after('slug');
            $table->string('phone')->nullable()->after('email');
            $table->text('description')->nullable()->after('phone');
            $table->string('logo')->nullable()->after('description');
            $table->string('banner')->nullable()->after('logo');
            $table->text('address')->nullable()->after('banner');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->nullable()->after('state');
            $table->string('postal_code')->nullable()->after('country');
            $table->string('tax_number')->nullable()->after('postal_code');
            $table->string('bank_name')->nullable()->after('tax_number');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_name')->nullable()->after('bank_account_number');
            $table->string('bank_routing_number')->nullable()->after('bank_account_name');
            $table->json('business_documents')->nullable()->after('bank_routing_number');
            $table->decimal('commission_rate', 5, 2)->default(10.00)->after('approved_at'); // Default 10%
            $table->boolean('is_active')->default(true)->after('commission_rate');
            $table->timestamp('suspended_at')->nullable()->after('is_active');
            $table->text('suspension_reason')->nullable()->after('suspended_at');
        });

        // Vendor Settings
        Schema::create('vendor_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();
            
            $table->unique(['vendor_id', 'key']);
        });

        // Commission Rules
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('commission_rate', 5, 2); // percentage
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->decimal('max_order_amount', 10, 2)->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // Vendor Payouts
        Schema::create('vendor_payouts', function (Blueprint $table) {
            $table->id();
            $table->string('payout_number')->unique();
            $table->foreignId('vendor_id')->constrained();
            $table->decimal('amount', 10, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('net_amount', 10, 2);
            $table->string('status')->default('pending'); // pending, processing, paid, failed
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('vendor_payout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_payout_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained();
            $table->foreignId('order_item_id')->constrained();
            $table->decimal('item_amount', 10, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('payout_amount', 10, 2);
            $table->timestamps();
        });

        // Vendor Analytics/Metrics
        Schema::create('vendor_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('orders_count')->default(0);
            $table->integer('items_sold')->default(0);
            $table->decimal('revenue', 10, 2)->default(0);
            $table->decimal('commission', 10, 2)->default(0);
            $table->integer('returns_count')->default(0);
            $table->integer('products_views')->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['vendor_id', 'date']);
        });

        // Vendor Compliance
        Schema::create('vendor_compliance_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained();
            $table->string('check_type'); // document, tax, performance, quality
            $table->string('status'); // passed, failed, pending
            $table->text('details')->nullable();
            $table->timestamp('checked_at');
            $table->foreignId('checked_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_compliance_checks');
        Schema::dropIfExists('vendor_metrics');
        Schema::dropIfExists('vendor_payout_items');
        Schema::dropIfExists('vendor_payouts');
        Schema::dropIfExists('commission_rules');
        Schema::dropIfExists('vendor_settings');
        
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'business_name', 'email', 'phone', 'description', 'logo', 'banner',
                'address', 'city', 'state', 'country', 'postal_code', 'tax_number',
                'bank_name', 'bank_account_number', 'bank_account_name', 'bank_routing_number',
                'business_documents', 'commission_rate', 'is_active', 'suspended_at', 'suspension_reason'
            ]);
        });
    }
};
