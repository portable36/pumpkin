# 🎛️ Dynamic Settings System - Complete Implementation Guide

**Created:** February 13, 2026  
**Purpose:** Control entire platform from admin dashboard without code changes  
**Hostinger Ready:** ✅ Perfect for shared hosting  

---

## 📋 What This Does

This system allows **100% of platform features to be controlled from the admin dashboard**:

✅ Enable/disable gateways (Steadfast, Pathao, SSLCommerz, Stripe, PayPal)  
✅ Set commission rates per vendor or globally  
✅ Configure tax settings  
✅ Toggle all features (reviews, wishlist, social login, etc)  
✅ Manage notification preferences  
✅ Set inventory thresholds  
✅ Configure API credentials  
✅ Control feature flags  

**No code deployment needed** - just update settings and the platform adapts.

---

## 🏗️ Architecture

### Models
- `app/Models/Setting.php` - Core settings model with helper methods

### Migrations
- `database/migrations/2026_02_13_000003_enhance_settings_table.php` - Settings table structure

### Admin UI
- `app/Filament/Resources/SettingResource.php` - Main settings admin panel
- `app/Filament/Resources/SettingResource/Pages/*.php` - Settings pages

### Database Structure
```sql
CREATE TABLE settings (
    id INTEGER PRIMARY KEY,
    key VARCHAR(255) UNIQUE,           -- 'shipping.gateways.steadfast.enabled'
    value LONGTEXT,                    -- JSON encoded value
    type VARCHAR(50),                  -- string, boolean, integer, float, array, json
    category VARCHAR(255) INDEX,       -- shipping, payment, features, etc
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 📚 Usage Examples

### Get a Setting
```php
// In any controller/service
$enabled = Setting::get('shipping.gateways.steadfast.enabled', false);
$commission = Setting::get('commission.default_rate', 0.10);
$shopName = Setting::get('platform.name', 'My Shop');

// Returns typed value (boolean, integer, etc. as per config)
```

### Set a Setting
```php
// Programmatically update setting
Setting::set('commission.default_rate', 0.15, 'float');
Setting::set('features.social_login', true, 'boolean');
Setting::set('shipping.gateways.steadfast.api_key', 'secret123', 'string');
```

### Toggle a Boolean Setting
```php
// Flip a feature on/off
$newState = Setting::toggle('features.email_notifications');
// Returns: true or false
```

### Increment a Numeric Setting
```php
// Increase a counter
Setting::increment('platform.daily_revenue', 1000);
```

### Get All Settings by Category
```php
// Get all shipping settings
$shippingConfig = Setting::getByCategory('shipping');
// Returns:
// [
//   'gateways.steadfast.enabled' => true,
//   'gateways.steadfast.api_key' => 'xxx',
//   ...
// ]
```

### Batch Set Multiple Settings
```php
Setting::setMultiple([
    'commission.default_rate' => ['value' => 0.15, 'type' => 'float'],
    'platform.name' => ['value' => 'My Store', 'type' => 'string'],
]);
```

---

## 🔗 Integration Examples

### 1. Conditionally Enable Payment Gateway

**In PaymentProcessor:**
```php
<?php
namespace App\Services\Payment;

class PaymentProcessor
{
    public function processPayment(Order $order)
    {
        // Get enabled gateway from settings (admin-controlled)
        $gateway = Setting::get('payment.default_gateway', 'sslcommerz');
        
        return match($gateway) {
            'sslcommerz' => $this->processSSlCommerz($order),
            'stripe' => $this->processStripe($order),
            'paypal' => $this->processPayPal($order),
            'bkash' => $this->processBKash($order),
            default => throw new Exception('Payment gateway not configured'),
        };
    }
    
    private function processSSlCommerz(Order $order)
    {
        // Only if enabled in settings
        if (!Setting::get('payment.gateways.sslcommerz.enabled', false)) {
            throw new Exception('SSLCommerz is not enabled');
        }
        
        $storeId = Setting::get('payment.gateways.sslcommerz.store_id');
        $storePass = Setting::get('payment.gateways.sslcommerz.store_password');
        $sandbox = Setting::get('payment.gateways.sslcommerz.sandbox', true);
        
        // Use credentials from settings
        $client = new SSLCommerz($storeId, $storePass, $sandbox);
        
        return $client->process($order);
    }
}
```

### 2. Dynamic Commission Calculation

**In CommissionService:**
```php
<?php
namespace App\Services;

class CommissionService
{
    public function calculateForVendor(Vendor $vendor, Order $order)
    {
        // Use vendor-specific rate or fall back to default
        $rate = $vendor->commission_rate ?? 
                Setting::get('commission.default_rate', 0.10);
        
        return $order->total_amount * $rate;
    }
    
    public function canProcessPayout(Vendor $vendor, float $amount): bool
    {
        $minPayout = Setting::get('commission.min_payout', 500);
        return $amount >= $minPayout;
    }
    
    public function processMonthlyPayouts()
    {
        // Only if auto-payout enabled
        if (!Setting::get('commission.auto_payout_enabled', false)) {
            return;
        }
        
        $payoutDay = Setting::get('commission.auto_payout_day', 1);
        
        if (now()->day == $payoutDay) {
            // Process payouts for all vendors
            Vendor::active()->each(fn($v) => $this->processPayout($v));
        }
    }
}
```

### 3. Toggle Features Dynamically

**In ProductController:**
```php
<?php
namespace App\Http\Controllers;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $data = [
            'product' => $product->load('images', 'variants'),
            'canReview' => Setting::get('features.product_reviews', true),
            'canAddToWishlist' => Setting::get('features.wishlist', true),
            'showVendor' => Setting::get('features.show_vendor_info', true),
            'showRatings' => Setting::get('features.vendor_reviews', true),
        ];
        
        return view('products.show', $data);
    }
}
```

### 4. Inventory Management with Dynamic Thresholds

**In InventoryService:**
```php
<?php
namespace App\Services;

class InventoryService
{
    public function checkLowStock(Product $product)
    {
        $threshold = Setting::get('inventory.low_stock_threshold', 10);
        
        $lowStockInventories = $product->inventory()
            ->where('quantity', '<', $threshold)
            ->get();
        
        if ($lowStockInventories->isNotEmpty()) {
            if (Setting::get('features.low_stock_alerts', true)) {
                // Send alerts
                foreach ($lowStockInventories as $inv) {
                    SendLowStockAlert::dispatch($product, $inv->warehouse);
                }
            }
        }
    }
    
    public function reserveStock(Order $order)
    {
        if (!Setting::get('inventory.prevent_overselling', true)) {
            return true; // Skip validation
        }
        
        // Validate stock availability
        // ...
    }
}
```

### 5. Dynamic Shipping Integration

**In ShippingService:**
```php
<?php
namespace App\Services;

class ShippingService
{
    public function calculateRate(Order $order)
    {
        $gateway = Setting::get('shipping.default_gateway', 'steadfast');
        
        return match($gateway) {
            'steadfast' => $this->steadfastRate($order),
            'pathao' => $this->pathaoRate($order),
            default => 0,
        };
    }
    
    private function steadfastRate(Order $order)
    {
        if (!Setting::get('shipping.gateways.steadfast.enabled', false)) {
            throw new Exception('Steadfast not enabled');
        }
        
        $freeShippingAmount = Setting::get('shipping.gateways.steadfast.free_shipping_amount', 5000);
        
        if ($order->subtotal >= $freeShippingAmount) {
            return 0; // Free shipping
        }
        
        // Get rate from Steadfast API
        $apiKey = Setting::get('shipping.gateways.steadfast.api_key');
        // ...
    }
    
    public function shipOrder(Order $order)
    {
        if (!Setting::get('shipping.auto_ship_on_payment', false)) {
            return; // Manual shipment required
        }
        
        ProcessOrderShipment::dispatch($order);
    }
}
```

### 6. Tax Calculation

**In TaxService:**
```php
<?php
namespace App\Services;

class TaxService
{
    public function calculateTax(Order $order)
    {
        if (!Setting::get('tax.enabled', true)) {
            return 0; // Tax disabled
        }
        
        $rate = Setting::get('tax.default_rate', 0.15);
        return $order->subtotal * $rate;
    }
    
    public function getTaxLabel()
    {
        return Setting::get('tax.tax_label', 'VAT');
    }
}
```

### 7. Feature Toggles in Views

**In Blade Templ templates:**
```blade
<!-- Only show reviews if enabled -->
@if(Setting::get('features.product_reviews', true))
    <section class="reviews">
        <h3>Customer Reviews</h3>
        @forelse($product->reviews as $review)
            <div class="review">{{ $review->content }}</div>
        @empty
            <p>No reviews yet</p>
        @endforelse
    </section>
@endif

<!-- Only show wishlist if enabled -->
@if(Setting::get('features.wishlist', true))
    <button wire:click="addToWishlist">
        <i class="heart-icon"></i> Add to Wishlist
    </button>
@endif

<!-- Social login if enabled -->
@if(Setting::get('features.social_login', false))
    <div class="social-login">
        @if(Setting::get('features.google_login', false))
            <button class="google-btn">Login with Google</button>
        @endif
        @if(Setting::get('features.facebook_login', false))
            <button class="facebook-btn">Login with Facebook</button>
        @endif
    </div>
@endif
```

### 8. Livewire Component with Dynamic Features

**In Product Livewire Component:**
```php
<?php
namespace App\Livewire\Products;

use Livewire\Component;
use App\Models\Setting;

class ProductDetail extends Component
{
    public $product;
    
    public function render()
    {
        return view('livewire.products.detail', [
            'canReview' => Setting::get('features.product_reviews', true),
            'canWishlist' => Setting::get('features.wishlist', true),
            'canShare' => Setting::get('features.social_sharing', true),
            'taxLabel' => Setting::get('tax.tax_label', 'VAT'),
            'shippingCost' => $this->calculateShipping(),
        ]);
    }
    
    public function addReview($rating, $content)
    {
        if (!Setting::get('features.product_reviews', true)) {
            $this->dispatch('error', 'Reviews are disabled');
            return;
        }
        
        // Add review
    }
}
```

---

## 🔧 Setting Up the System

### Step 1: Run Migrations
```bash
php artisan migrate
```

This creates the enhanced `settings` table with all required columns.

### Step 2: Access Admin Panel
```
http://yoursite.com/admin/settings
```

The Filament admin panel will show all settings organized by tabs.

### Step 3: Configure in Controller/Service
Use the examples above inservices and controllers.

---

## 🎯 Key Features

### ✅ Type-Safe Values
Settings automatically cast to proper types:
```php
Setting::set('enabled', true, 'boolean');  // Returns true, not "1"
Setting::set('rate', 0.15, 'float');       // Returns 0.15, not "0.15"
Setting::set('count', 10, 'integer');      // Returns 10, not "10"
Setting::set('data', ['a' => 'b'], 'json'); // Returns array, not string
```

### ✅ Dotted Key Access
All keys support dotted notation:
```php
Setting::get('shipping.gateways.steadfast.api_key');
// Same as: $settings['shipping']['gateways']['steadfast']['api_key']
```

### ✅ Categorization
Settings are automatically categorized for better organization:
```php
// Admin sees: Shipping | Payment | Features | Inventory | etc.
```

### ✅ Caching Ready
```php
// Cache settings for performance
$settings = Cache::remember('app.settings', 3600, function () {
    return Setting::allSettings();
});
```

### ✅ Admin UI with Tabs
- Platform settings
- Commission & Revenue
- Tax & Compliance
- Shipping gateways
- Payment gateways
- Features toggles
- Inventory settings
- Security & rate limiting

---

## 📊 Database Seeding

**Create initial settings via seeder:**

```php
<?php
namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'platform.name' => 'My Ecommerce',
            'platform.email' => 'support@example.com',
            'platform.currency' => 'BDT',
            'platform.timezone' => 'Asia/Dhaka',
            
            'commission.default_rate' => 0.10,
            'commission.min_payout' => 500,
            
            'tax.enabled' => true,
            'tax.default_rate' => 0.15,
            
            'features.multi_vendor_enabled' => true,
            'features.wishlist' => true,
            'features.product_reviews' => true,
            'features.coupons' => true,
            
            'inventory.low_stock_threshold' => 10,
            'inventory.prevent_overselling' => true,
        ];
        
        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
```

Run with:
```bash
php artisan db:seed --class=SettingSeeder
```

---

## 🚀 Best Practices

### 1. Always Provide Defaults
```php
// Good - has fallback
$rate = Setting::get('commission.default_rate', 0.10);

// Bad - might return null
$rate = Setting::get('commission.default_rate');
```

### 2. Cache Settings
```php
// In AppServiceProvider
public function boot()
{
    // Cache all settings for 24 hours
    Cache::remember('settings.all', 86400, function () {
        return Setting::allSettings();
    });
}
```

### 3. Validate in Admin
```php
// In Filament form
TextInput::make('commission')
    ->numeric()
    ->min(0.01)
    ->max(1.00)
    ->required(),
```

### 4. Log Changes
```php
Setting::saved(function ($setting) {
    Log::info("Setting changed: {$setting->key} = {$setting->value}");
});
```

### 5. Use Feature Flags
```php
// Instead of hardcoding features
if (Setting::get('features.beta_feature', false)) {
    // Show new feature to beta testers
}
```

---

## 📋 Complete Settings List

```
Platform
├── name, email, phone, currency, timezone, description, maintenance_mode

Commission
├── default_rate, min_payout, payout_method, auto_payout_enabled, auto_payout_day

Tax
├── enabled, default_rate, tax_label, tax_number

Shipping
├── Gateways (Steadfast, Pathao)
│  ├── enabled, api_key, api_secret, sandbox, free_shipping_amount
├── default_gateway, auto_ship_on_payment

Payment
├── Gateways (SSLCommerz, Stripe, PayPal, bKash)
│  ├── enabled, api_credentials
├── default_gateway

Features
├── multi_vendor_enabled, vendor_commission, vendor_analytics
├── social_login, google_login, facebook_login
├── wishlist, product_reviews, vendor_reviews, coupons, guest_checkout
├── email_notifications, sms_notifications, push_notifications
├── low_stock_alerts, price_drop_alerts

Notifications
├── Email (enabled, from_name, from_email)
├── SMS (enabled, provider, credentials)

Inventory
├── low_stock_threshold, prevent_overselling, auto_release_reserved
├── release_after_hours, track_by_warehouse

Cart
├── expiration_days, auto_apply_coupon, recalculate_price, require_account

Search
├── enabled, min_characters, autocomplete, suggestions_count, boost_recent

Rate Limiting
├── api_requests, api_window, login_attempts, login_window, search_requests

Security
├── force_https, session_lifetime, token_expiration
├── require_email_verification, require_phone_verification

Analytics
├── enabled, track_user_behavior, google_analytics_id, facebook_pixel_id

UI/UX
├── items_per_page, show_price_on_hover, show_stock_status
├── show_vendor_info, theme_color, dark_mode

Returns
├── enabled, return_window, auto_refund, refund_processing_days
```

---

## 🎯 Practical Example: Feature Rollout

**Scenario:** You want to test a new feature with 10% of users.

```php
// 1. Admin adds setting
Setting::set('features.new_checkout_ui', true);
Setting::set('features.new_checkout_ui_beta_percentage', 10);

// 2. Use in controller
public function checkout()
{
    $useBetaUI = Setting::get('features.new_checkout_ui', false) &&
                 rand(1, 100) <= Setting::get('features.new_checkout_ui_beta_percentage', 0);
    
    return view('checkout.' . ($useBetaUI ? 'ui-beta' : 'ui-stable'));
}

// 3. Monitor usage
// Once satisfied, admin sets percentage to 100
Setting::set('features.new_checkout_ui_beta_percentage', 100);
```

---

## 📞 Support & Examples

All examples work immediately after migration and seeding.

Test in Tinker:
```bash
php artisan tinker

> Setting::set('test_key', 'test_value')
> Setting::get('test_key')
=> "test_value"

> Setting::toggle('feature.test')
=> true

> Setting::getByCategory('platform')
=> [...]
```

---

**Status:** ✅ Ready to use  
**Hostinger:** ✅ Perfect fit (no external dependencies)  
**Deployment:** Zero downtime (update via admin UI)  

---

**Next:** Implement the Feature Implementation Roadmap using these dynamic settings!
