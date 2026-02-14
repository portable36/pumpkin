<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payment\SSLCommerzGateway;
use App\Services\Payment\StripeGateway;
use App\Services\Payment\PayPalGateway;
use App\Services\Payment\BkashGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentGatewayController extends Controller
{
    /**
     * Initiate payment intent with selected gateway
     */
    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'gateway' => 'required|in:sslcommerz,stripe,paypal,bkash',
        ]);

        $order = auth()->user()->orders()->findOrFail($validated['order_id']);

        // Create payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'gateway' => $validated['gateway'],
            'amount' => $order->total,
            'currency' => $order->currency ?? 'BDT',
            'status' => 'pending',
        ]);

        // Get gateway and create intent
        $gateway = $this->getGateway($validated['gateway']);

        $result = $gateway->createPaymentIntent([
            'order_id' => $order->id,
            'amount' => $order->total,
            'currency' => $order->currency ?? 'BDT',
            'customer_name' => auth()->user()->name,
            'customer_email' => auth()->user()->email,
            'customer_phone' => auth()->user()->phone ?? '',
            'success_url' => route('payments.success'),
            'fail_url' => route('payments.fail'),
            'cancel_url' => route('payments.cancel'),
            'callback_url' => route('webhooks.payment'),
            'ipn_url' => route('webhooks.payment'),
        ]);

        if (!$result['success']) {
            return response()->json(['error' => $result['error']], 422);
        }

        // Store gateway response
        $payment->update([
            'external_id' => $result['transaction_id'] ?? null,
            'gateway_response' => $result,
        ]);

        return response()->json([
            'payment_id' => $payment->id,
            'redirect_url' => $result['url'] ?? null,
            'client_secret' => $result['client_secret'] ?? null,
            'transaction_id' => $result['transaction_id'] ?? null,
        ]);
    }

    /**
     * Webhook receiver for all payment gateways
     */
    public function webhook(Request $request)
    {
        $gateway = $request->route('gateway');

        try {
            $gatewayService = $this->getGateway($gateway);

            // Verify webhook signature
            if (!$gatewayService->verifyWebhook($request->headers->all(), $request->getContent())) {
                Log::warning("Failed webhook signature verification for {$gateway}");
                return response()->json(['success' => false], 403);
            }

            // Handle webhook
            $result = $gatewayService->handleWebhook($request->all());

            return response()->json(['success' => $result]);
        } catch (\Exception $e) {
            Log::error("Payment webhook error for {$gateway}", ['error' => $e->getMessage()]);
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Payment success page
     */
    public function success(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $payment = Payment::findOrFail($paymentId);

        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        return view('payments.success', ['payment' => $payment]);
    }

    /**
     * Payment failure page
     */
    public function fail(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $payment = Payment::find($paymentId);

        return view('payments.fail', ['payment' => $payment]);
    }

    /**
     * Payment cancellation page
     */
    public function cancel(Request $request)
    {
        return view('payments.cancel');
    }

    /**
     * Get gateway instance
     */
    protected function getGateway(string $gateway)
    {
        return match($gateway) {
            'sslcommerz' => new SSLCommerzGateway(),
            'stripe' => new StripeGateway(),
            'paypal' => new PayPalGateway(),
            'bkash' => new BkashGateway(),
            default => throw new \InvalidArgumentException("Unknown gateway: {$gateway}"),
        };
    }
}
