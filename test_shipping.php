<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ShippingService;
use App\Models\Setting;

echo "\n=== SHIPPING INTEGRATION SMOKE TEST ===\n\n";

// Enable Steadfast gateway for testing
Setting::set('shipping.gateways.steadfast.enabled', true, 'boolean');

$service = new ShippingService();

// Available gateways
$available = $service->getAvailableGateways();
echo "Available gateways: " . (empty($available) ? '(none)' : implode(', ', $available)) . "\n";

// Local shipping cost calc
$cost = $service->calculateShippingCost('Dhaka', 2.5);
echo "Calculated shipping cost for 2.5kg to Dhaka: BDT {$cost}\n";

// Rate (will attempt to call gateway API, may return error if API keys not configured)
echo "Attempting gateway rate (may error if API not configured):\n";
try {
    $rate = $service->getRate('Dhaka', 1, 2.5, 'steadfast');
    echo 'Rate result: ' . var_export($rate, true) . "\n";
} catch (\Exception $e) {
    echo "Rate call failed: " . $e->getMessage() . "\n";
}

echo "\n✅ Shipping smoke test complete.\n";
