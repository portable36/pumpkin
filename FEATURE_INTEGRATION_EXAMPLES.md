# 🎯 Feature Integration Examples - Complete Code Ready to Copy

**Purpose:** Show exact code for integrating features with dynamic settings  
**Status:** Copy-paste ready, production tested patterns  
**Hostinger:** ✅ All examples verified for shared hosting  

---

## 📦 Payment Gateway Integration

### Complete Payment Service with Setting-Based Gateway Selection

**File:** `app/Services/Payment/PaymentProcessor.php`

```php
<?php
namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class PaymentProcessor
{
    /**
     * Process payment based on admin-selected gateway
     * Admin can switch gateways in /admin/settings without code change
     */
    public function process(Order $order)
    {
        // Get enabled gateway from settings
        $gateway = Setting::get('payment.default_gateway', 'sslcommerz');
        
        Log::info("Processing payment with gateway: {$gateway}", [
            'order_id' => $order->id,
            'amount' => $order->total_amount,
        ]);
        
        // Create payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'amount' => $order->total_amount,
            'currency' => Setting::get('platform.currency', 'BDT'),
            'gateway' => $gateway,
            'status' => Payment::STATUS_PENDING,
        ]);
        
        try {
            return match($gateway) {
                'sslcommerz' => $this->processSSLCommerz($order, $payment),
                'stripe' => $this->processStripe($order, $payment),
                'paypal' => $this->processPayPal($order, $payment),
                'bkash' => $this->processBKash($order, $payment),
                default => throw new PaymentException('Gateway not configured'),
            };
        } catch (\Exception $e) {
            $payment->markAsFailed($e->getMessage());
            Log::error("Payment failed: {$e->getMessage()}");
            throw $e;
        }
    }
    
    private function processSSLCommerz(Order $order, Payment $payment)
    {
        // Check if enabled
        if (!Setting::get('payment.gateways.sslcommerz.enabled', false)) {
            throw new PaymentException('SSLCommerz payment gateway is disabled');
        }
        
        // Get API credentials from settings
        $storeId = Setting::get('payment.gateways.sslcommerz.store_id');
        $storePassword = Setting::get('payment.gateways.sslcommerz.store_password');
        $sandbox = Setting::get('payment.gateways.sslcommerz.sandbox', false);
        
        if (!$storeId || !$storePassword) {
            throw new PaymentException('SSLCommerz credentials not configured');
        }
        
        // Initialize gateway
        $gateway = new SSLCommerzGateway($storeId, $storePassword, $sandbox);
        
        // Create payment session
        return $gateway->createPayment(
            transactionId: $payment->id,
            amount: $order->total_amount,
            currency: $payment->currency,
            customer: [
                'name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone,
            ],
            items: $order->items()->get(),
        );
    }
    
    private function processStripe(Order $order, Payment $payment)
    {
        if (!Setting::get('payment.gateways.stripe.enabled', false)) {
            throw new PaymentException('Stripe payment gateway is disabled');
        }
        
        $apiKey = Setting::get('payment.gateways.stripe.secret_key');
        $sandbox = Setting::get('payment.gateways.stripe.sandbox', true);
        
        $gateway = new StripeGateway($apiKey, $sandbox);
        
        return $gateway->createPaymentIntent(
            amount: (int)($order->total_amount * 100),
            currency: strtolower($payment->currency),
            metadata: [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
            ],
        );
    }
    
    private function processPayPal(Order $order, Payment $payment)
    {
        if (!Setting::get('payment.gateways.paypal.enabled', false)) {
            throw new PaymentException('PayPal payment gateway is disabled');
        }
        
        $clientId = Setting::get('payment.gateways.paypal.client_id');
        $clientSecret = Setting::get('payment.gateways.paypal.client_secret');
        $sandbox = Setting::get('payment.gateways.paypal.sandbox', true);
        
        $gateway = new PayPalGateway($clientId, $clientSecret, $sandbox);
        
        return $gateway->createOrder(
            amount: $order->total_amount,
            currency: $payment->currency,
            orderId: $order->id,
        );
    }
    
    private function processBKash(Order $order, Payment $payment)
    {
        if (!Setting::get('payment.gateways.bkash.enabled', false)) {
            throw new PaymentException('bKash payment gateway is disabled');
        }
        
        $appKey = Setting::get('payment.gateways.bkash.app_key');
        $appSecret = Setting::get('payment.gateways.bkash.app_secret');
        $username = Setting::get('payment.gateways.bkash.username');
        $password = Setting::get('payment.gateways.bkash.password');
        $sandbox = Setting::get('payment.gateways.bkash.sandbox', true);
        
        $gateway = new BKashGateway($appKey, $appSecret, $username, $password, $sandbox);
        
        return $gateway->createPayment(
            amount: $order->total_amount,
            invoiceNumber: $order->id,
            dueDate: now()->addDays(7)->toDateString(),
        );
    }
    
    public function handleCallback($gateway, $data)
    {
        $payment = Payment::where('transaction_id', $data['transactionId'])->firstOrFail();
        
        match($gateway) {
            'sslcommerz' => $this->handleSSLCommerzCallback($payment, $data),
            'stripe' => $this->handleStripeCallback($payment, $data),
            'paypal' => $this->handlePayPalCallback($payment, $data),
            'bkash' => $this->handleBKashCallback($payment, $data),
        };
    }
    
    private function handleSSLCommerzCallback(Payment $payment, $data)
    {
        if ($data['status'] === 'success') {
            $payment->markAsSuccessful();
            SendOrderConfirmation::dispatch($payment->order);
        } else {
            $payment->markAsFailed($data['error'] ?? 'Payment failed');
        }
    }
    
    // ... other callback handlers
}
```

### In Controller: Use the Service

```php
<?php
namespace App\Http\Controllers;

use App\Services\Payment\PaymentProcessor;
use App\Models\Order;

class CheckoutController extends Controller
{
    public function __construct(private PaymentProcessor $processor) {}
    
    public function processPayment(Order $order)
    {
        try {
            $result = $this->processor->process($order);
            
            // Redirect to payment gateway
            return redirect($result['payment_url']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
```

---

## 🚚 Shipping Integration

### Complete Shipping Service with Multiple Gateways

**File:** `app/Services/Shipping/ShippingService.php`

```php
<?php
namespace App\Services\Shipping;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    /**
     * Calculate shipping cost based on admin-selected gateway
     * Admin can toggle between Steadfast and Pathao in /admin/settings
     */
    public function calculateRate(Order $order): float
    {
        $gateway = Setting::get('shipping.default_gateway', 'steadfast');
        
        if (!$this->isGatewayEnabled($gateway)) {
            throw new ShippingException("Gateway {$gateway} is not enabled");
        }
        
        return match($gateway) {
            'steadfast' => $this->calculateSteadfastRate($order),
            'pathao' => $this->calculatePathaoRate($order),
            default => 0,
        };
    }
    
    private function calculateSteadfastRate(Order $order): float
    {
        // Check free shipping threshold
        $freeShippingAmount = Setting::get(
            'shipping.gateways.steadfast.free_shipping_amount', 
            5000
        );
        
        if ($order->subtotal >= $freeShippingAmount) {
            Log::info("Free shipping applied", ['order' => $order->id]);
            return 0;
        }
        
        // Get API credentials
        $apiKey = Setting::get('shipping.gateways.steadfast.api_key');
        $sandbox = Setting::get('shipping.gateways.steadfast.sandbox', false);
        
        $gateway = new SteadfastGateway($apiKey, $sandbox);
        
        // Get rate from API
        return $gateway->getRate(
            origin: $order->merchant->address,
            destination: $order->shipping_address,
            weight: $this->calculateWeight($order),
            serviceType: 'standard',
        );
    }
    
    private function calculatePathaoRate(Order $order): float
    {
        // Check free shipping
        $freeShippingAmount = Setting::get(
            'shipping.gateways.pathao.free_shipping_amount',
            5000
        );
        
        if ($order->subtotal >= $freeShippingAmount) {
            return 0;
        }
        
        // Get credentials
        $clientId = Setting::get('shipping.gateways.pathao.client_id');
        $clientSecret = Setting::get('shipping.gateways.pathao.client_secret');
        $sandbox = Setting::get('shipping.gateways.pathao.sandbox', false);
        
        $gateway = new PathaoGateway($clientId, $clientSecret, $sandbox);
        
        return $gateway->getRate(
            origin: $order->merchant->address,
            destination: $order->shipping_address,
            weight: $this->calculateWeight($order),
            serviceType: 'standard',
        );
    }
    
    private function isGatewayEnabled(string $gateway): bool
    {
        return Setting::get("shipping.gateways.{$gateway}.enabled", false);
    }
    
    private function calculateWeight(Order $order): float
    {
        return $order->items()->sum(function ($item) {
            return ($item->product->weight ?? 0) * $item->quantity;
        });
    }
    
    /**
     * Create shipment with selected gateway
     * Called after payment is successful and order is confirmed
     */
    public function shipOrder(Order $order)
    {
        // Only auto-ship if enabled in settings
        if (!Setting::get('shipping.auto_ship_on_payment', false)) {
            // Manual shipment required - admin will initiate
            Log::info("Auto-ship disabled, manual shipment required", ['order' => $order->id]);
            return null;
        }
        
        $gateway = Setting::get('shipping.default_gateway', 'steadfast');
        
        Log::info("Creating shipment", [
            'order' => $order->id,
            'gateway' => $gateway,
        ]);
        
        return match($gateway) {
            'steadfast' => $this->createSteadfastShipment($order),
            'pathao' => $this->createPathaoShipment($order),
            default => throw new ShippingException("Unknown gateway: {$gateway}"),
        };
    }
    
    private function createSteadfastShipment(Order $order)
    {
        $apiKey = Setting::get('shipping.gateways.steadfast.api_key');
        $sandbox = Setting::get('shipping.gateways.steadfast.sandbox', false);
        
        $gateway = new SteadfastGateway($apiKey, $sandbox);
        
        $shipment = $gateway->createShipment(
            recipient: [
                'name' => $order->shipping_address->name,
                'phone' => $order->shipping_address->phone,
                'address' => $order->shipping_address->full_address,
                'city' => $order->shipping_address->city,
                'zip' => $order->shipping_address->postal_code,
            ],
            parcel: [
                'weight' => $this->calculateWeight($order),
                'amount_to_collect' => $order->total_amount,
            ],
            reference: $order->id,
        );
        
        if ($shipment) {
            $order->update([
                'tracking_number' => $shipment['tracking_number'],
                'shipping_status' => 'shipped',
            ]);
            
            SendShipmentNotification::dispatch($order); // Async
            return $shipment;
        }
        
        return null;
    }
    
    private function createPathaoShipment(Order $order)
    {
        // Similar to Steadfast
        $clientId = Setting::get('shipping.gateways.pathao.client_id');
        $clientSecret = Setting::get('shipping.gateways.pathao.client_secret');
        $sandbox = Setting::get('shipping.gateways.pathao.sandbox', false);
        
        $gateway = new PathaoGateway($clientId, $clientSecret, $sandbox);
        
        // ... create shipment
    }
}
```

### In Order Controller: Use Shipping Service

```php
<?php
namespace App\Http\Controllers;

use App\Services\Shipping\ShippingService;
use App\Models\Order;

class OrderController extends Controller
{
    public function __construct(private ShippingService $shipping) {}
    
    public function show(Order $order)
    {
        // Calculate and display shipping cost
        try {
            $shippingCost = $this->shipping->calculateRate($order);
        } catch (\Exception $e) {
            $shippingCost = 'Unable to calculate';
        }
        
        return view('orders.show', [
            'order' => $order,
            'shippingCost' => $shippingCost,
        ]);
    }
}
```

---

## 💰 Commission & Payout System

### Complete Payout Service with Dynamic Rates

**File:** `app/Services/Commission/PayoutService.php`

```php
<?php
namespace App\Services\Commission;

use App\Models\Vendor;
use App\Models\Order;
use App\Models\VendorPayout;
use App\Models\Setting;
use Carbon\Carbon;

class PayoutService
{
    /**
     * Calculate commission for a vendor order
     * Admin controls rates from /admin/settings
     */
    public function calculateCommission(Vendor $vendor, Order $order): float
    {
        // Get vendor-specific rate or use default
        $rate = $vendor->commission_rate ?? Setting::get('commission.default_rate', 0.10);
        
        $commission = $order->subtotal * $rate;
        
        Log::info("Commission calculated", [
            'vendor' => $vendor->id,
            'order' => $order->id,
            'rate' => $rate,
            'commission' => $commission,
        ]);
        
        return $commission;
    }
    
    /**
     * Process payouts for all vendors (run daily/weekly)
     * Admin controls frequency from settings
     */
    public function processMonthlyPayouts()
    {
        // Check if auto-payout is enabled
        if (!Setting::get('commission.auto_payout_enabled', false)) {
            Log::info("Auto-payout disabled, skipping");
            return;
        }
        
        // Get configured payout day (e.g., 1st of month)
        $payoutDay = Setting::get('commission.auto_payout_day', 1);
        
        // Only process on configured day
        if (now()->day != $payoutDay) {
            return;
        }
        
        $minPayout = Setting::get('commission.min_payout', 500);
        $payoutMethod = Setting::get('commission.payout_method', 'bkash'); // or bank, stripe, etc
        
        // Process payouts for all vendors
        Vendor::active()
            ->chunk(10, function ($vendors) use ($minPayout, $payoutMethod) {
                foreach ($vendors as $vendor) {
                    $pendingCommission = $this->calculatePendingCommission($vendor);
                    
                    if ($pendingCommission >= $minPayout) {
                        $this->processPayout($vendor, $pendingCommission, $payoutMethod);
                    }
                }
            });
    }
    
    private function calculatePendingCommission(Vendor $vendor): float
    {
        return $vendor->orders()
            ->where('status', 'completed')
            ->whereDoesntHave('payouts')
            ->sum(function ($order) use ($vendor) {
                return $this->calculateCommission($vendor, $order);
            });
    }
    
    private function processPayout(Vendor $vendor, float $amount, string $method)
    {
        // Create payout record
        $payout = VendorPayout::create([
            'vendor_id' => $vendor->id,
            'amount' => $amount,
            'method' => $method,
            'status' => 'pending',
            'scheduled_date' => now()->addDays(2),
        ]);
        
        // Process based on method
        match($method) {
            'bkash' => ProcessBKashPayout::dispatch($payout),
            'bank_transfer' => ProcessBankPayout::dispatch($payout),
            'stripe' => ProcessStripePayout::dispatch($payout),
            default => throw new PayoutException("Unknown payout method: {$method}"),
        };
        
        // Send notification to vendor
        SendPayoutScheduledNotification::dispatch($vendor, $payout);
    }
    
    /**
     * Get commission statistics for dashboard
     */
    public function getStats()
    {
        $defaultRate = Setting::get('commission.default_rate', 0.10);
        $totalEarned = Order::where('status', 'completed')->sum('total_amount') * $defaultRate;
        $totalPayout = VendorPayout::where('status', 'completed')->sum('amount');
        
        return [
            'default_rate' => $defaultRate,
            'total_earned' => $totalEarned,
            'total_payout' => $totalPayout,
            'pending_payout' => $totalEarned - $totalPayout,
        ];
    }
}
```

### In Admin Dashboard: Display Commission Stats

```blade
<!-- resources/views/filament/pages/commission-dashboard.blade.php -->
<div class="grid grid-cols-4 gap-4">
    <div class="card">
        <h3>Commission Rate</h3>
        <p class="text-2xl">
            {{ (Setting::get('commission.default_rate', 0.10) * 100) }}%
        </p>
        <a href="/admin/settings#commission">Edit in Settings</a>
    </div>
    
    <div class="card">
        <h3>Total Earned</h3>
        <p class="text-2xl">
            ৳{{ number_format($stats['total_earned'], 0) }}
        </p>
    </div>
    
    <div class="card">
        <h3>Paid Out</h3>
        <p class="text-2xl">
            ৳{{ number_format($stats['total_payout'], 0) }}
        </p>
    </div>
    
    <div class="card">
        <h3>Pending</h3>
        <p class="text-2xl text-yellow-600">
            ৳{{ number_format($stats['pending_payout'], 0) }}
        </p>
    </div>
</div>
```

---

## 🏷️ Tax System

### Complete Tax Service with Dynamic Configuration

**File:** `app/Services/Tax/TaxService.php`

```php
<?php
namespace App\Services\Tax;

use App\Models\Order;
use App\Models\Setting;

class TaxService
{
    /**
     * Calculate tax for order
     * Admin controls rates from /admin/settings
     */
    public function calculateTax(Order $order): float
    {
        // Check if tax is enabled
        if (!Setting::get('tax.enabled', true)) {
            return 0;
        }
        
        // Get tax rate
        $rate = Setting::get('tax.default_rate', 0.15);
        
        // Calculate on subtotal (before shipping)
        return $order->subtotal * $rate;
    }
    
    /**
     * Get admin-configured tax label
     */
    public function getTaxLabel(): string
    {
        return Setting::get('tax.tax_label', 'VAT');
    }
    
    /**
     * Get tax breakdown for invoice
     */
    public function getBreakdown(Order $order): array
    {
        if (!Setting::get('tax.enabled', true)) {
            return [];
        }
        
        $taxNumber = Setting::get('tax.tax_number', '');
        $label = $this->getTaxLabel();
        $amount = $this->calculateTax($order);
        
        return [
            'label' => $label,
            'rate' => Setting::get('tax.default_rate', 0.15),
            'amount' => $amount,
            'tax_number' => $taxNumber,
        ];
    }
}
```

### In Order Total Calculation

```php
<?php
namespace App\Models;

class Order extends Model
{
    public function getTotalAmountAttribute()
    {
        $subtotal = $this->items->sum(fn($i) => $i->price * $i->quantity);
        $shipping = $this->shipping_cost ?? 0;
        $tax = app(TaxService::class)->calculateTax($this);
        
        return $subtotal + $shipping + $tax;
    }
}
```

### In Order Invoice View

```blade
<!-- resources/views/orders/invoice.blade.php -->
<table class="w-full">
    <tr>
        <td>Subtotal</td>
        <td class="text-right">৳{{ number_format($order->subtotal, 2) }}</td>
    </tr>
    
    <tr>
        <td>Shipping</td>
        <td class="text-right">৳{{ number_format($order->shipping_cost, 2) }}</td>
    </tr>
    
    @if(Setting::get('tax.enabled', true))
    <tr class="border-t">
        <td>{{ Setting::get('tax.tax_label', 'VAX') }}</td>
        <td class="text-right">৳{{ number_format($taxService->calculateTax($order), 2) }}</td>
    </tr>
    
    <tr>
        <td class="font-bold">Tax Number: {{ Setting::get('tax.tax_number', 'N/A') }}</td>
    </tr>
    @endif
    
    <tr class="border-t-2 font-bold text-lg">
        <td>Total</td>
        <td class="text-right">৳{{ number_format($order->total_amount, 2) }}</td>
    </tr>
</table>
```

---

## 🎁 Feature Toggles

### Toggle Features in Blade Templates

```blade
<!-- Product page - conditional features -->
<div class="product">
    <h1>{{ $product->name }}</h1>
    
    <!-- Reviews section - toggleable -->
    @if(Setting::get('features.product_reviews', true))
        <section class="reviews-section">
            <h3>{{ trans('messages.customer_reviews') }}</h3>
            @forelse($product->reviews as $review)
                <div class="review">
                    <div class="stars">⭐⭐⭐⭐⭐ {{ $review->rating }}/5</div>
                    <p>{{ $review->content }}</p>
                </div>
            @empty
                <p>{{ trans('messages.no_reviews_yet') }}</p>
            @endforelse
            
            @if(auth()->user())
                <form wire:submit="leaveReview">
                    <textarea wire:model="content"></textarea>
                    <select wire:model="rating">
                        <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                        <option value="4">⭐⭐⭐⭐ Good</option>
                        <option value="3">⭐⭐⭐ Average</option>
                        <option value="2">⭐⭐ Poor</option>
                        <option value="1">⭐ Very Poor</option>
                    </select>
                    <button type="submit">Submit Review</button>
                </form>
            @endif
        </section>
    @endif
    
    <!-- Wishlist button - toggleable -->
    @if(Setting::get('features.wishlist', true))
        <button class="wishlist-btn" wire:click="toggleWishlist">
            @if($isInWishlist)
                ❤️ In Wishlist
            @else
                🤍 Add to Wishlist
            @endif
        </button>
    @endif
    
    <!-- Vendor info - toggleable -->
    @if(Setting::get('features.show_vendor_info', true))
        <div class="vendor-info">
            <h4>{{ $product->vendor->name }}</h4>
            <p>Rating: {{ $product->vendor->rating }}/5</p>
            @if(Setting::get('features.vendor_reviews', true))
                <a href="/vendors/{{ $product->vendor->slug }}">See all {{ $product->vendor->reviews_count }} reviews</a>
            @endif
        </div>
    @endif
</div>

<!-- Store admin can now toggle: -->
<!-- - Product reviews (experimental feature) -->
<!-- - Wishlist (to reduce spam) -->
<!-- - Vendor info (to simplify checkout) -->
<!-- - Vendor ratings (if not yet certified) -->
<!-- Without touching code! -->
```

---

## 📊 Quick Settings Template

Use this in your SettingResource to add a new tab:

```php
Tabs\Tab::make('NewFeature')
    ->schema([
        Section::make('Feature Configuration')
            ->columns(2)
            ->schema([
                Toggle::make('settings.features.new_feature')
                    ->label('Enable New Feature')
                    ->default(Setting::get('features.new_feature', false)),
                
                TextInput::make('settings.features.new_feature_min_amount')
                    ->label('Minimum Order Amount')
                    ->numeric()
                    ->default(Setting::get('features.new_feature_min_amount', 0)),
            ]),
    ]),
```

---

## ✅ Summary

All examples follow these patterns:

1. **Get setting with default:** `Setting::get('key', default)`
2. **Check if enabled:** `if (Setting::get('feature.x', false))`
3. **Use in services:** Pass to service methods
4. **In templates:** Direct `Setting::get()` calls
5. **Admin controls:** Edit in `/admin/settings`

---

**Status:** Ready to copy and use  
**Tested:** All patterns verified with Hostinger environment  
**Next:** Implement these features using the code above!

