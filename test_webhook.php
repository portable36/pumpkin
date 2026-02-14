<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\ShippingWebhookController;

$payload = ['tracking_code' => 'TEST-TRACK-123', 'status' => 'delivered'];
$request = Request::create('/api/webhook/shipping/steadfast', 'POST', $payload);

$controller = new ShippingWebhookController();
$response = $controller->handle($request, 'steadfast', app()->make(App\Services\ShippingService::class));

echo "Response: " . $response->getContent() . "\n";
