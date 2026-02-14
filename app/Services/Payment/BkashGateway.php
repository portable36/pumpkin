<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Services\OrderPaymentService;

class BkashGateway implements PaymentGatewayInterface
{
    protected string $appKey;
    protected string $appSecret;
    protected string $username;
    protected string $password;
    protected bool $sandbox;
    protected string $baseUrl;
    protected OrderPaymentService $orderPaymentService;

    public function __construct()
    {
        $this->appKey = config('services.bkash.app_key', '');
        $this->appSecret = config('services.bkash.app_secret', '');
        $this->username = config('services.bkash.username', '');
        $this->password = config('services.bkash.password', '');
        $this->sandbox = config('services.bkash.sandbox', true);
        $this->baseUrl = $this->sandbox ? 'https://sandbox.payment.bkash.com' : 'https://api.payment.bkash.com';
        $this->orderPaymentService = new OrderPaymentService();
    }

    protected function getAccessToken(): string
    {
        $response = Http::post("{$this->baseUrl}/v1.2.0/tokenized/checkout/token/request", [
            'app_key' => $this->appKey,
            'app_secret' => $this->appSecret,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if ($data['statusCode'] === '0000') {
                return $data['id_token'];
            }
        }

        throw new \Exception('Failed to get bKash access token');
    }

    public function createPaymentIntent(array $data): array
    {
        try {
            $token = $this->getAccessToken();
            $trxId = 'BKH' . time() . rand(1000, 9999);

            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
            ])->post("{$this->baseUrl}/v1.2.0/tokenized/checkout/create", [
                'mode' => '0011',
                'payerReference' => $trxId,
                'callbackURL' => $data['callback_url'],
                'amount' => (string)$data['amount'],
                'currency' => $data['currency'] ?? 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $trxId,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if ($result['statusCode'] === '0000') {
                    return [
                        'success' => true,
                        'url' => "{$this->baseUrl}/v1.2.0/tokenized/checkout?paymentID=" . $result['paymentID'],
                        'transaction_id' => $result['paymentID'],
                    ];
                }
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::error('bKash payment intent failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verifyWebhook(array $headers, string $payload): bool
    {
        return true;
    }

    public function handleWebhook(array $payload): bool
    {
        try {
            $paymentId = $payload['paymentID'] ?? null;
            $status = $payload['paymentStatus'] ?? '';

            $payment = Payment::where('external_id', $paymentId)->first();
            if (!$payment) {
                Log::warning("bKash payment not found for paymentID: {$paymentId}");
                return false;
            }

            if ($status === 'Completed') {
                $payment->update([
                    'external_id' => $paymentId,
                    'gateway_response' => $payload,
                ]);
                $this->orderPaymentService->handlePaymentSuccess($payment);
                return true;
            } elseif ($status === 'Failed') {
                $payment->update(['gateway_response' => $payload]);
                $this->orderPaymentService->handlePaymentFailure(
                    $payment,
                    $payload['failureReason'] ?? 'Payment rejected by gateway'
                );
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('bKash webhook handling failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function refund(string $transactionId, float $amount): bool
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $this->appKey,
            ])->post("{$this->baseUrl}/v1.2.0/tokenized/checkout/refund", [
                'paymentID' => $transactionId,
                'amount' => (string)$amount,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if ($result['statusCode'] === '0000') {
                    // Record refund
                    $payment = Payment::where('external_id', $transactionId)->first();
                    if ($payment) {
                        $this->orderPaymentService->handleRefund($payment, $amount);
                    }
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error('bKash refund failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
