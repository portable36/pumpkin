<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Helpers\Settings;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Process payment via selected gateway
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'method' => 'required|in:bkash,ssl_commerz,paypal,stripe',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Check if payment already exists and is pending
        $existingPayment = Payment::where('order_id', $order->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPayment) {
            return redirect()->route('payments.process', $existingPayment->id);
        }

        // Create payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'gateway' => $request->method,
            'amount' => $order->total_amount,
            'status' => 'pending',
            'transaction_id' => null,
        ]);

        return redirect()->route('payments.process', $payment->id);
    }

    /**
     * Process payment based on gateway
     */
    public function process(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        switch ($payment->gateway) {
            case 'bkash':
                return $this->processBkash($payment);
            case 'ssl_commerz':
                return $this->processSSLCommerz($payment);
            case 'paypal':
                return $this->processPayPal($payment);
            case 'stripe':
                return $this->processStripe($payment);
            default:
                abort(422, 'Invalid payment gateway');
        }
    }

    /**
     * Handle bKash payment
     */
    private function processBkash(Payment $payment)
    {
        // Integrate bKash API here
        // For now, return test form
        return view('payments.bkash', compact('payment'));
    }

    /**
     * Handle SSLCommerz payment
     */
    private function processSSLCommerz(Payment $payment)
    {
        // Integrate SSLCommerz API here
        // For now, return test form
        return view('payments.ssl_commerz', compact('payment'));
    }

    /**
     * Handle PayPal payment
     */
    private function processPayPal(Payment $payment)
    {
        return view('payments.paypal', compact('payment'));
    }

    /**
     * Handle Stripe payment
     */
    private function processStripe(Payment $payment)
    {
        return view('payments.stripe', compact('payment'));
    }

    /**
     * Webhook for payment verification
     */
    public function webhook(Request $request)
    {
        $gateway = $request->get('gateway');

        switch ($gateway) {
            case 'bkash':
                return $this->handleBkashWebhook($request);
            case 'ssl_commerz':
                return $this->handleSSLCommerz($request);
            case 'paypal':
                return $this->handlePayPalWebhook($request);
            case 'stripe':
                return $this->handleStripeWebhook($request);
        }
    }

    /**
     * Handle bKash webhook
     */
    private function handleBkashWebhook(Request $request)
    {
        // Verify webhook signature
        // Update payment status based on response
        $transactionId = $request->get('trxID');
        $status = $request->get('statusCode');

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if ($payment) {
            $payment->update([
                'status' => $status === '0000' ? 'completed' : 'failed',
                'gateway_response' => json_encode($request->all()),
            ]);

            if ($payment->status === 'completed') {
                $payment->order->update(['payment_status' => 'completed']);
                // Trigger order processing
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Handle SSLCommerz webhook
     */
    private function handleSSLCommerz(Request $request)
    {
        // Verify and process SSLCommerz response
        $status = $request->get('status');
        $transactionId = $request->get('tran_id');

        $payment = Payment::where('transaction_id', $transactionId)->first();

        if ($payment) {
            $payment->update([
                'status' => $status === 'VALID' ? 'completed' : 'failed',
                'gateway_response' => json_encode($request->all()),
            ]);

            if ($payment->status === 'completed') {
                $payment->order->update(['payment_status' => 'completed']);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Handle PayPal webhook
     */
    private function handlePayPalWebhook(Request $request)
    {
        // Implement PayPal IPN handling
        return response()->json(['success' => true]);
    }

    /**
     * Handle Stripe webhook
     */
    private function handleStripeWebhook(Request $request)
    {
        // Implement Stripe webhook handling
        return response()->json(['success' => true]);
    }

    /**
     * Verify payment status
     */
    public function verify(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        // Verify with gateway and update status if needed
        // This should call the payment gateway API to check current status

        return response()->json([
            'status' => $payment->status,
            'order_id' => $payment->order_id,
        ]);
    }

    /**
     * Refund payment
     */
    public function refund(Request $request, Payment $payment)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($payment->status !== 'completed') {
            return response()->json(['error' => 'Only completed payments can be refunded'], 422);
        }

        // Call gateway refund API
        $refunded = $this->processRefund($payment);

        if ($refunded) {
            $payment->update(['status' => 'refunded']);
            $payment->order->update(['payment_status' => 'refunded']);

            return response()->json(['message' => 'Refund processed successfully']);
        }

        return response()->json(['error' => 'Refund processing failed'], 422);
    }

    /**
     * Process refund with gateway
     */
    private function processRefund(Payment $payment)
    {
        // Implement gateway-specific refund logic
        // For now, return true for demo
        return true;
    }
}
