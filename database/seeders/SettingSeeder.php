<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Seed the application's database with default settings.
     */
    public function run(): void
    {
        // Platform Settings
        Setting::updateOrCreate(
            ['key' => 'platform.name'],
            ['value' => 'My Ecommerce', 'type' => 'string', 'category' => 'platform', 'description' => 'Your store display name']
        );
        
        Setting::updateOrCreate(
            ['key' => 'platform.email'],
            ['value' => 'support@example.com', 'type' => 'string', 'category' => 'platform', 'description' => 'Support email address']
        );
        
        Setting::updateOrCreate(
            ['key' => 'platform.phone'],
            ['value' => '+1234567890', 'type' => 'string', 'category' => 'platform', 'description' => 'Support phone number']
        );
        
        Setting::updateOrCreate(
            ['key' => 'platform.currency'],
            ['value' => 'BDT', 'type' => 'string', 'category' => 'platform', 'description' => 'Platform currency']
        );
        
        Setting::updateOrCreate(
            ['key' => 'platform.timezone'],
            ['value' => 'Asia/Dhaka', 'type' => 'string', 'category' => 'platform', 'description' => 'Platform timezone']
        );
        
        Setting::updateOrCreate(
            ['key' => 'platform.maintenance_mode'],
            ['value' => 'false', 'type' => 'boolean', 'category' => 'platform', 'description' => 'Enable/disable maintenance mode']
        );

        // Commission Settings
        Setting::updateOrCreate(
            ['key' => 'commission.default_rate'],
            ['value' => '0.10', 'type' => 'float', 'category' => 'commission', 'description' => 'Default vendor commission rate (10%)']
        );
        
        Setting::updateOrCreate(
            ['key' => 'commission.min_payout'],
            ['value' => '500', 'type' => 'float', 'category' => 'commission', 'description' => 'Minimum amount for vendor payout']
        );
        
        Setting::updateOrCreate(
            ['key' => 'commission.payout_method'],
            ['value' => 'bank', 'type' => 'string', 'category' => 'commission', 'description' => 'Default payout method']
        );
        
        Setting::updateOrCreate(
            ['key' => 'commission.auto_payout_enabled'],
            ['value' => 'false', 'type' => 'boolean', 'category' => 'commission', 'description' => 'Enable automatic vendor payouts']
        );
        
        Setting::updateOrCreate(
            ['key' => 'commission.auto_payout_day'],
            ['value' => '1', 'type' => 'integer', 'category' => 'commission', 'description' => 'Day of month for auto payout']
        );

        // Tax Settings
        Setting::updateOrCreate(
            ['key' => 'tax.enabled'],
            ['value' => 'true', 'type' => 'boolean', 'category' => 'tax', 'description' => 'Enable tax calculation']
        );
        
        Setting::updateOrCreate(
            ['key' => 'tax.default_rate'],
            ['value' => '0.15', 'type' => 'float', 'category' => 'tax', 'description' => 'Default tax rate (15% VAT)']
        );
        
        Setting::updateOrCreate(
            ['key' => 'tax.tax_label'],
            ['value' => 'VAT', 'type' => 'string', 'category' => 'tax', 'description' => 'Tax label for receipts']
        );
        
        Setting::updateOrCreate(
            ['key' => 'tax.tax_number'],
            ['value' => '', 'type' => 'string', 'category' => 'tax', 'description' => 'Tax registration number']
        );

        // Shipping Settings
        Setting::updateOrCreate(
            ['key' => 'shipping.default_gateway'],
            ['value' => 'steadfast', 'type' => 'string', 'category' => 'shipping', 'description' => 'Default shipping gateway']
        );
        
        Setting::updateOrCreate(
            ['key' => 'shipping.gateways.steadfast.enabled'],
            ['value' => 'false', 'type' => 'boolean', 'category' => 'shipping', 'description' => 'Enable Steadfast shipping']
        );
        
        Setting::updateOrCreate(
            ['key' => 'shipping.gateways.steadfast.api_key'],
            ['value' => '', 'type' => 'string', 'category' => 'shipping', 'description' => 'Steadfast API key']
        );
        
        Setting::updateOrCreate(
            ['key' => 'shipping.gateways.steadfast.sandbox'],
            ['value' => 'true', 'type' => 'boolean', 'category' => 'shipping', 'description' => 'Steadfast sandbox mode']
        );
        
        Setting::updateOrCreate(
            ['key' => 'shipping.gateways.pathao.enabled'],
            ['value' => 'false', 'type' => 'boolean', 'category' => 'shipping', 'description' => 'Enable Pathao shipping']
        );

        // Payment Settings
        Setting::updateOrCreate(
            ['key' => 'payment.default_gateway'],
            ['value' => 'sslcommerz', 'type' => 'string', 'category' => 'payment', 'description' => 'Default payment gateway']
        );
        
        Setting::updateOrCreate(
            ['key' => 'payment.gateways.sslcommerz.enabled'],
            ['value' => 'false', 'type' => 'boolean', 'category' => 'payment', 'description' => 'Enable SSLCommerz']
        );
        
        Setting::updateOrCreate(
            ['key' => 'payment.gateways.stripe.enabled'],
            ['value' => 'false', 'type' => 'boolean', 'category' => 'payment', 'description' => 'Enable Stripe']
        );
        
        Setting::updateOrCreate(
            ['key' => 'payment.gateways.paypal.enabled'],
            ['value' => 'false', 'type' => 'boolean', 'category' => 'payment', 'description' => 'Enable PayPal']
        );
        
        Setting::updateOrCreate(
            ['key' => 'payment.gateways.bkash.enabled'],
            ['value' => 'false', 'type' => 'boolean', 'category' => 'payment', 'description' => 'Enable bKash']
        );

        // Feature Toggles
        Setting::updateOrCreate(
            ['key' => 'platform.enable_multivendor'],
            ['value' => 'true', 'type' => 'boolean', 'category' => 'platform', 'description' => 'Enable multi-vendor marketplace']
        );

        Setting::updateOrCreate(
            ['key' => 'features.multi_vendor_enabled'],
            ['value' => 'true', 'type' => 'boolean', 'category' => 'features', 'description' => 'Enable multi-vendor marketplace']
        );
        
        Setting::updateOrCreate(
            ['key' => 'features.wishlist'],
            ['value' => 'true', 'type' => 'boolean', 'category' => 'features', 'description' => 'Enable wishlist feature']
        );
        
        Setting::updateOrCreate(
            ['key' => 'features.product_reviews'],
            ['value' => 'true', 'type' => 'boolean', 'category' => 'features', 'description' => 'Enable product reviews']
        );
        
        Setting::updateOrCreate(
            ['key' => 'features.vendor_reviews'],
            ['value' => 'true', 'type' => 'boolean', 'category' => 'features', 'description' => 'Enable vendor reviews']
        );
        
        Setting::updateOrCreate(
            ['key' => 'features.coupons'],
            ['value' => 'true', 'type' => 'boolean', 'category' => 'features', 'description' => 'Enable coupon system']
        );
        
        Setting::updateOrCreate(
            ['key' => 'features.guest_checkout'],
            ['value' => 'true', 'type' => 'boolean', 'category' => 'features', 'description' => 'Allow guest checkout']
        );
        
        Setting::updateOrCreate(
            ['key' => 'features.social_login'],
            ['value' => 'false', 'type' => 'boolean', 'category' => 'features', 'description' => 'Enable social login']
        );
        
        Setting::updateOrCreate(
            ['key' => 'features.email_notifications'],
            ['value' => 'true', 'type' => 'boolean', 'category' => 'features', 'description' => 'Enable email notifications']
        );
        
        Setting::updateOrCreate(
            ['key' => 'features.sms_notifications'],
            ['value' => 'false', 'type' => 'boolean', 'category' => 'features', 'description' => 'Enable SMS notifications']
        );
        
        Setting::updateOrCreate(
            ['key' => 'features.low_stock_alerts'],
            ['value' => 'true', 'type' => 'boolean', 'category' => 'features', 'description' => 'Enable low stock alerts']
        );

        // Inventory Settings
        Setting::updateOrCreate(
            ['key' => 'inventory.low_stock_threshold'],
            ['value' => '10', 'type' => 'integer', 'category' => 'inventory', 'description' => 'Low stock threshold']
        );
        
        Setting::updateOrCreate(
            ['key' => 'inventory.prevent_overselling'],
            ['value' => 'true', 'type' => 'boolean', 'category' => 'inventory', 'description' => 'Prevent selling more than available']
        );

        // Security Settings
        Setting::updateOrCreate(
            ['key' => 'security.session_lifetime'],
            ['value' => '120', 'type' => 'integer', 'category' => 'security', 'description' => 'Session lifetime in minutes']
        );
        
        Setting::updateOrCreate(
            ['key' => 'rate_limiting.api_requests'],
            ['value' => '100', 'type' => 'integer', 'category' => 'security', 'description' => 'API rate limit per minute']
        );

        $this->command->info('✅ Settings seeded successfully!');
    }
}
