<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Services\OrderPaymentService;

class PayPalGateway implements PaymentGatewayInterface
{
    protected string $clientId;
    protected string $clientSecret;
    protected bool $sandbox;
    protected string $baseUrl;
    protected OrderPaymentService $orderPaymentService;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id', '');
        $this->clientSecret = config('services.paypal.client_secret', '');
        $this->sandbox = config('services.paypal.sandbox', true);
        $this->baseUrl = $this->sandbox ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com';
        $this->orderPaymentService = new OrderPaymentService();
    }

    protected function getAccessToken(): string
    {
        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception('Failed to get PayPal access token');
    }

    public function createPaymentIntent(array $data): array
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)->post("{$this->baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => $data['currency'] ?? 'USD',
                            'value' => (string)$data['amount'],
                        ],
                    ],
                ],
                'application_context' => [
                    'return_url' => $data['success_url'],
                    'cancel_url' => $data['cancel_url'],
                ],
            ]);

            if ($response->successful()) {
                $order = $response->json();
                $approvalUrl = collect($order['links'])->firstWhere('rel', 'approve')['href'] ?? null;
                return [
                    'success' => true,
                    'url' => $approvalUrl,
                    'transaction_id' => $order['id'],
                ];
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::error('PayPal payment intent failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verifyWebhook(array $headers, string $payload): bool
    {
        // PayPal verification would require checking signature
        return true;
    }

    public function handleWebhook(array $payload): bool
    {
        try {
            $eventType = $payload['event_type'] ?? '';

            if ($eventType === 'CHECKOUT.ORDER.COMPLETED') {
                $order = $payload['resource'] ?? [];
                $payment = Payment::where('external_id', $order['id'])->first();
                if ($payment) {
                    $payment->update([
                        'external_id' => $order['id'],
                        'gateway_response' => $order,
                    ]);
                    $this->orderPaymentService->handlePaymentSuccess($payment);
                }
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('PayPal webhook handling failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function refund(string $transactionId, float $amount): bool
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)->post(
                "{$this->baseUrl}/v2/payments/captures/{$transactionId}/refund",
                ['amount' => ['value' => (string)$amount]]
            );

            if ($response->successful()) {
                // Record refund
                $payment = Payment::where('external_id', $transactionId)->first();
                if ($payment) {
                    $this->orderPaymentService->handleRefund($payment, $amount);
                }
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('PayPal refund failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
