<?php

namespace App\Http\Controllers;

use App\Services\ShippingService;
use App\Services\WebhookSecurityService;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class ShippingWebhookController extends Controller
{
    public function handle(Request $request, string $gateway, ShippingService $shipping)
    {
        $payload = $request->getContent();

        // Verify webhook signature
        $secret = Setting::get("shipping.gateways.{$gateway}.webhook_secret", '');
        $signature = $request->header('X-Signature', '');

        if ($secret && !WebhookSecurityService::verifySignature($payload, $signature, $secret)) {
            Log::warning("Invalid webhook signature for {$gateway}");
            return response()->json(['success' => false], 403);
        }

        Log::info("Received shipping webhook for {$gateway}", ['payload' => $request->all()]);

        $ok = $shipping->handleWebhook($gateway, $request->all());

        return response()->json(['success' => (bool) $ok]);
    }
}
