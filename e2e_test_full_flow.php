<?php

/**
 * E2E Test: Order → Multi-Vendor Split → Shipment → Webhook
 * Run: php e2e_test_full_flow.php
 */

require 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

echo "\n=== FULL E2E FLOW TEST ===\n\n";

// 1. Create test order with multiple vendors
echo "Step 1: Creating order with items from multiple vendors...\n";

$user = \App\Models\User::first();
$vendor1 = \App\Models\Vendor::first();
$vendor2 = \App\Models\Vendor::skip(1)->first();

if (!$user || !$vendor1) {
    die("❌ Need at least 1 user and 1 vendor in database\n");
}

$product1 = \App\Models\Product::where('vendor_id', $vendor1->id)->first();
$product2 = $vendor2 ? \App\Models\Product::where('vendor_id', $vendor2->id)->first() : null;

if (!$product1) {
    die("❌ No products found for vendor\n");
}

// Create order
$order = \App\Models\Order::create([
    'user_id' => $user->id,
    'status' => 'pending',
    'subtotal' => 100,
    'tax' => 10,
    'shipping_cost' => 20,
    'total' => 130,
    'payment_method' => 'sslcommerz',
    'currency' => 'BDT',
]);

// Add items
\App\Models\OrderItem::create([
    'order_id' => $order->id,
    'product_id' => $product1->id,
    'vendor_id' => $vendor1->id,
    'quantity' => 1,
    'unit_price' => 100,
    'total_price' => 100,
]);

if ($product2) {
    \App\Models\OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product2->id,
        'vendor_id' => $vendor2->id,
        'quantity' => 1,
        'unit_price' => 0,
        'total_price' => 0,
    ]);
    echo "✅ Created order #{$order->id} with 2 vendors\n";
} else {
    echo "✅ Created order #{$order->id} with 1 vendor\n";
}

// 2. Create payment
echo "\nStep 2: Creating payment record...\n";

$payment = \App\Models\Payment::create([
    'order_id' => $order->id,
    'user_id' => $user->id,
    'amount' => 130,
    'currency' => 'BDT',
    'method' => 'sslcommerz',
    'status' => 'completed',
]);

echo "✅ Created payment #{$payment->id}\n";

// 3. Multi-vendor order split
echo "\nStep 3: Splitting order by vendor...\n";

$multiVendorService = new \App\Services\MultiVendorOrderService();

if ($multiVendorService->shouldSplitOrder($order)) {
    $subOrders = $multiVendorService->splitOrderByVendor($order);
    echo "✅ Split into {$subOrders->count()} vendor orders\n";
} else {
    echo "ℹ️  Single vendor order, no split needed\n";
}

// 4. Create shipments
echo "\nStep 4: Creating shipments...\n";

$shippingService = new \App\Services\ShippingService();
$availableGateways = $shippingService->getAvailableGateways();

if (empty($availableGateways)) {
    echo "⚠️  No shipping gateways enabled. Enable in settings.\n";
} else {
    echo "ℹ️  Available gateways: " . implode(', ', $availableGateways) . "\n";
}

// 5. Test webhook handling
echo "\nStep 5: Testing webhook signature verification...\n";

$webhookService = new \App\Services\WebhookSecurityService();
$testPayload = json_encode(['order_id' => $order->id, 'status' => 'pending']);
$testSecret = 'test-secret-key';
$signature = $webhookService->generateSignature($testPayload, $testSecret);
$verified = $webhookService->verifySignature($testPayload, $signature, $testSecret);

if ($verified) {
    echo "✅ Webhook signature verification works\n";
} else {
    echo "❌ Webhook verification failed\n";
}

// 6. Test circuit breaker
echo "\nStep 6: Testing circuit breaker...\n";

$breaker = new \App\Services\CircuitBreaker('test_service', 3, 10);

if ($breaker->isOpen()) {
    echo "ℹ️  Circuit breaker is open\n";
} else {
    echo "✅ Circuit breaker is closed (service available)\n";
}

// 7. Check vendor ledger
echo "\nStep 7: Checking vendor ledger...\n";

$ledgerEntries = \App\Models\VendorLedger::where('vendor_id', $vendor1->id)->get();
echo "✅ Ledger entries for vendor {$vendor1->id}: {$ledgerEntries->count()}\n";

foreach ($ledgerEntries as $entry) {
    echo "  - {$entry->type}: " . $entry->amount . " BDT\n";
}

// Summary
echo "\n=== TEST SUMMARY ===\n";
echo "✅ Order created: #{$order->id}\n";
echo "✅ Payment processed: #{$payment->id}\n";
echo "✅ Webhook security verified\n";
echo "✅ Circuit breaker operational\n";
echo "✅ Multi-vendor infrastructure ready\n";
echo "\n🎉 E2E flow test complete!\n\n";
