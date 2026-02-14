<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Services\OrderPaymentService;

class StripeGateway implements PaymentGatewayInterface
{
    protected string $apiKey;
    protected string $webhookSecret;
    protected OrderPaymentService $orderPaymentService;

    public function __construct()
    {
        $this->apiKey = config('services.stripe.secret_key', '');
        $this->webhookSecret = config('services.stripe.webhook_secret', '');
        $this->orderPaymentService = new OrderPaymentService();
    }

    public function createPaymentIntent(array $data): array
    {
        try {
            $response = Http::withToken($this->apiKey)->post('https://api.stripe.com/v1/payment_intents', [
                'amount' => (int)($data['amount'] * 100), // Convert to cents
                'currency' => strtolower($data['currency'] ?? 'bdt'),
                'metadata' => [
                    'order_id' => $data['order_id'],
                ],
            ]);

            if ($response->successful()) {
                $intent = $response->json();
                return [
                    'success' => true,
                    'client_secret' => $intent['client_secret'],
                    'transaction_id' => $intent['id'],
                ];
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::error('Stripe payment intent failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verifyWebhook(array $headers, string $payload): bool
    {
        $signature = $headers['Stripe-Signature'] ?? '';
        if (!$signature) {
            return false;
        }

        try {
            \Stripe\Webhook::constructEvent($payload, $signature, $this->webhookSecret);
            return true;
        } catch (\Exception $e) {
            Log::warning('Stripe webhook verification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function handleWebhook(array $payload): bool
    {
        try {
            $event = $payload['type'] ?? '';
            $data = $payload['data']['object'] ?? [];

            if ($event === 'payment_intent.succeeded') {
                $orderId = $data['metadata']['order_id'] ?? null;
                $payment = Payment::where('order_id', $orderId)->orWhere('external_id', $data['id'])->first();
                if ($payment) {
                    $payment->update([
                        'external_id' => $data['id'],
                        'gateway_response' => $data,
                    ]);
                    $this->orderPaymentService->handlePaymentSuccess($payment);
                }
                return true;
            } elseif ($event === 'payment_intent.payment_failed') {
                $orderId = $data['metadata']['order_id'] ?? null;
                $payment = Payment::where('order_id', $orderId)->orWhere('external_id', $data['id'])->first();
                if ($payment) {
                    $payment->update(['gateway_response' => $data]);
                    $this->orderPaymentService->handlePaymentFailure(
                        $payment,
                        $data['last_payment_error']['message'] ?? 'Payment failed'
                    );
                }
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Stripe webhook handling failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function refund(string $transactionId, float $amount): bool
    {
        try {
            $response = Http::withToken($this->apiKey)->post("https://api.stripe.com/v1/charges/{$transactionId}/refunds", [
                'amount' => (int)($amount * 100),
            ]);

            if ($response->successful()) {
                // Find and record refund
                $payment = Payment::where('external_id', $transactionId)->first();
                if ($payment) {
                    $this->orderPaymentService->handleRefund($payment, $amount);
                }
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Stripe refund failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
