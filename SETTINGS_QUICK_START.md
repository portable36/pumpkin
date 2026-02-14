# ⚡ Settings System - Quick Start (5 Minutes)

**Goal:** Get settings system running and make your first dynamic configuration  
**Time:** 5 minutes  
**Prerequisites:** Run migrations from IMPLEMENTATION_COMPLETE.md  

---

## Step 1: Verify Migrations (30 seconds)

Check your database has the settings table:

```bash
# Connect to MySQL
mysql -u root -p your_database

# Check table exists
SHOW TABLES LIKE 'settings';

# See structure
DESCRIBE settings;
```

Should show: `id`, `key`, `value`, `type`, `category`, `description`

---

## Step 2: Navigate to Admin Settings (1 minute)

1. Go to `http://yoursite.com/admin`
2. Click **Settings** in sidebar
3. See 10 tabs: Platform, Commission, Tax, etc.

That's it! The UI is ready.

---

## Step 3: Test First Setting (2 minutes)

### Option A: Via Admin UI
1. Click **Settings**
2. Go to **Features** tab
3. Toggle **Wishlist** on/off
4. Click **Save**
5. Check database changed:
   ```bash
   SELECT * FROM settings WHERE key='features.wishlist';
   ```

### Option B: Via Tinker
```bash
php artisan tinker

# Set a value
> Setting::set('test.my_setting', 'hello', 'string')

# Get it back
> Setting::get('test.my_setting')
=> "hello"

# Verify in database
> Setting::get('features.wishlist')
=> true
```

---

## Step 4: Use in Your Code (1 minute)

### In a Controller
```php
<?php
namespace App\Http\Controllers;

use App\Models\Setting;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $canReview = Setting::get('features.product_reviews', true);
        
        return view('products.show', [
            'product' => $product,
            'canReview' => $canReview,
        ]);
    }
}
```

### In a Livewire Component
```php
<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Setting;

class Cart extends Component
{
    public function render()
    {
        return view('livewire.cart', [
            'requireAccount' => Setting::get('cart.require_account', false),
            'cartExpiresIn' => Setting::get('cart.expiration_days', 7),
        ]);
    }
}
```

### In a Service
```php
<?php
namespace App\Services;

use App\Models\Setting;

class CommissionService
{
    public function calculate($order)
    {
        // Get rate from admin dashboard
        $rate = Setting::get('commission.default_rate', 0.10);
        
        return $order->total * $rate;
    }
}
```

### In Blade Template
```blade
@if(Setting::get('features.wishlist', true))
    <button wire:click="addToWishlist">
        ❤️ Wishlist
    </button>
@endif

TAX: {{ Setting::get('tax.tax_label', 'VAT') }}
```

---

## Step 5: Seed Initial Values (1 minute)

Create a seeder for default settings:

```bash
php artisan make:seeder SettingSeeder
```

Edit `database/seeders/SettingSeeder.php`:

```php
<?php
namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        // Platform
        Setting::set('platform.name', 'My Store', 'string');
        Setting::set('platform.email', 'admin@mystore.com', 'string');
        Setting::set('platform.currency', 'BDT', 'string');
        
        // Commission
        Setting::set('commission.default_rate', 0.10, 'float');
        Setting::set('commission.min_payout', 500, 'float');
        
        // Features
        Setting::set('features.wishlist', true, 'boolean');
        Setting::set('features.product_reviews', true, 'boolean');
        Setting::set('features.coupons', true, 'boolean');
        
        // Shipping
        Setting::set('shipping.gateways.steadfast.enabled', false, 'boolean');
        Setting::set('shipping.gateways.pathao.enabled', false, 'boolean');
        
        // Payment
        Setting::set('payment.gateways.sslcommerz.enabled', false, 'boolean');
        Setting::set('payment.default_gateway', 'sslcommerz', 'string');
        
        // Tax
        Setting::set('tax.enabled', true, 'boolean');
        Setting::set('tax.default_rate', 0.15, 'float');
        Setting::set('tax.tax_label', 'VAT', 'string');
    }
}
```

Run it:
```bash
php artisan db:seed --class=SettingSeeder
```

---

## Common Operations

### Check if Feature is Enabled
```php
if (Setting::get('features.wishlist', true)) {
    // Show wishlist button
}
```

### Get a Config Value
```php
$storeName = Setting::get('platform.name', 'My Store');
$taxRate = Setting::get('tax.default_rate', 0.15);
$currency = Setting::get('platform.currency', 'USD');
```

### Update from Code (Usually in Admin UI)
```php
Setting::set('commission.default_rate', 0.15, 'float');
```

### Toggle a Boolean
```php
$newState = Setting::toggle('features.email_notifications');
// Returns true or false
```

### Increase a Number
```php
Setting::increment('platform.daily_revenue', 1000);
```

### Get All Settings in a Category
```php
$shipping = Setting::getByCategory('shipping');
// Returns:
// [
//     'gateways.steadfast.enabled' => false,
//     'gateways.steadfast.api_key' => '...',
//     ...
// ]
```

---

## ❌ Common Mistakes

### ❌ No Default
```php
// BAD - returns null if not set
$rate = Setting::get('commission.rate');

// GOOD - has fallback
$rate = Setting::get('commission.rate', 0.10);
```

### ❌ Wrong Type
```php
// BAD - type='string'
Setting::set('enabled', 'true', 'string');
$enabled = Setting::get('enabled'); // Returns string "true"
if ($enabled) { } // Always true, even if "false" string!

// GOOD - type='boolean'
Setting::set('enabled', true, 'boolean');
$enabled = Setting::get('enabled'); // Returns boolean true
if ($enabled) { } // Works correctly
```

### ❌ Missing Seeding
```bash
# If settings don't appear in admin:
php artisan db:seed --class=SettingSeeder
```

### ❌ Cache Stale
```php
// If setting changed but old value cached:
Cache::forget('settings.all');
```

---

## Access Control

**Who can change settings?**

By default, only **Admin users** can access `/admin/settings`.

Set in `app/Filament/Resources/SettingResource.php`:
```php
protected static ?string $navigationGroup = 'Admin';

public static function canAccess(): bool
{
    return auth()->user()?->isAdmin(); // Only admins
}
```

---

## Real-World Scenarios

### Scenario 1: Enable Steadfast Shipping
1. Go to `/admin/settings`
2. Click **Shipping** tab
3. Toggle **Steadfast Enabled** on
4. Paste API key & secret
5. Save

**Code automatically uses it:**
```php
if (Setting::get('shipping.gateways.steadfast.enabled', false)) {
    // Steadfast shipping calculations work
}
```

### Scenario 2: Change Commission Rate
1. Go to `/admin/settings`
2. Click **Commission** tab
3. Change **Default Rate** to 0.20 (20%)
4. Save

**Automatic in PayoutService:**
```php
$commission = $order->total * Setting::get('commission.default_rate', 0.10);
```

### Scenario 3: Disable Reviews Temporarily
1. Go to `/admin/settings`
2. Click **Features** tab
3. Toggle **Product Reviews** off
4. Save

**Frontend automatically hides reviews section:**
```blade
@if(Setting::get('features.product_reviews', true))
    <!-- Reviews section hidden -->
@endif
```

---

## Database Queries

### View All Settings
```sql
SELECT key, value, type, category FROM settings ORDER BY category;
```

### Find a Specific Setting
```sql
SELECT * FROM settings WHERE key = 'commission.default_rate';
```

### See All Payment Settings
```sql
SELECT * FROM settings WHERE category = 'payment';
```

### Update Via Database (Rare)
```sql
UPDATE settings SET value = 'true' WHERE key = 'features.wishlist';
```

---

## Caching (Optional Performance)

Cache all settings to avoid DB queries:

```php
// In AppServiceProvider
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

public function boot()
{
    // Cache all settings for 24 hours
    $settings = Cache::remember('settings.all', 86400, function () {
        return Setting::allSettings();
    });
}
```

Clear cache after admin update:
```php
// In SettingResource observe() hook
Cache::forget('settings.all');
```

---

## Testing

### Test Setting Exists
```php
public function test_setting_exists()
{
    Setting::set('test.key', 'value', 'string');
    $this->assertEquals('value', Setting::get('test.key'));
}
```

### Test Type Casting
```php
public function test_boolean_type_casting()
{
    Setting::set('test.flag', true, 'boolean');
    $value = Setting::get('test.flag');
    $this->assertIsBool($value);
    $this->assertTrue($value);
}
```

### Test Feature Toggle
```php
public function test_feature_can_be_toggled()
{
    Setting::set('features.test', false, 'boolean');
    
    $new = Setting::toggle('features.test');
    $this->assertTrue($new);
    $this->assertTrue(Setting::get('features.test'));
}
```

---

## 🆘 Troubleshooting

| Problem | Solution |
|---------|----------|
| Settings don't appear in admin | Run `php artisan migrate` + `php artisan db:seed` |
| Setting returns null | Add default: `Setting::get('key', 'default')` |
| Boolean always true | Verify `type='boolean'` in database |
| Admin shows "Access Denied" | Verify user is admin role |
| Cache shows old value | Run `php artisan cache:clear` |
| Can't find settings tab | Verify `SettingResource.php` exists |

---

## Next Steps

1. ✅ Verify migrations ran
2. ✅ Test in admin UI
3. ✅ Seed initial values
4. ✅ Use in one controller
5. → Continue with [FEATURE_IMPLEMENTATION_ROADMAP.md](FEATURE_IMPLEMENTATION_ROADMAP.md)

---

**Status:** Ready to use  
**Time to implement:** 5 minutes  
**Time to ROI:** Immediate (admin controls features)  
**Hostinger:** ✅ Perfect  

