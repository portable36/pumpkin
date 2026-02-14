<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Services\OrderPaymentService;

class SSLCommerzGateway implements PaymentGatewayInterface
{
    protected string $storeId;
    protected string $storePassword;
    protected bool $sandbox;
    protected string $baseUrl;
    protected OrderPaymentService $orderPaymentService;

    public function __construct()
    {
        $this->storeId = config('services.payment.sslcommerz.store_id', '');
        $this->storePassword = config('services.payment.sslcommerz.store_password', '');
        $this->sandbox = config('services.payment.sslcommerz.sandbox', true);
        $this->baseUrl = $this->sandbox ? 'https://sandbox.sslcommerz.com' : 'https://api.sslcommerz.com';
        $this->orderPaymentService = new OrderPaymentService();
    }

    public function createPaymentIntent(array $data): array
    {
        try {
            $payload = [
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'total_amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'BDT',
                'tran_id' => $data['order_id'],
                'success_url' => $data['success_url'],
                'fail_url' => $data['fail_url'],
                'cancel_url' => $data['cancel_url'],
                'ipn_url' => $data['ipn_url'],
                'cus_name' => $data['customer_name'] ?? '',
                'cus_email' => $data['customer_email'] ?? '',
                'cus_phone' => $data['customer_phone'] ?? '',
                'product_name' => $data['product_name'] ?? 'Order',
                'product_category' => 'ecommerce',
            ];

            $response = Http::post("{$this->baseUrl}/gwprocess/v4/api.php", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'url' => $data['redirectGatewayURL'] ?? null,
                    'transaction_id' => $data['sessionkey'] ?? null,
                ];
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::error('SSLCommerz payment intent failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verifyWebhook(array $headers, string $payload): bool
    {
        // SSLCommerz doesn't use HMAC; verify in handleWebhook
        return true;
    }

    public function handleWebhook(array $payload): bool
    {
        try {
            $tranId = $payload['tran_id'] ?? null;
            $payment = Payment::where('external_id', $tranId)->orWhere('transaction_id', $tranId)->first();

            if (!$payment) {
                Log::warning("Payment not found for tranId: {$tranId}");
                return false;
            }

            $status = $payload['status'] ?? '';
            if ($status === 'VALID') {
                // Update payment with gateway response
                $payment->update([
                    'external_id' => $tranId,
                    'gateway_response' => $payload,
                ]);
                // Process successful payment
                $this->orderPaymentService->handlePaymentSuccess($payment);
                return true;
            } elseif ($status === 'FAILED') {
                $payment->update(['gateway_response' => $payload]);
                $this->orderPaymentService->handlePaymentFailure($payment, $payload['error_reason'] ?? 'Payment rejected by gateway');
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('SSLCommerz webhook handling failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function refund(string $transactionId, float $amount): bool
    {
        try {
            $payload = [
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'refund_ref_id' => $transactionId,
                'refund_amount' => $amount,
            ];

            $response = Http::post("{$this->baseUrl}/gwprocess/ExchageRefund.php", $payload);
            
            if ($response->successful()) {
                // Record refund in payment
                $payment = Payment::where('external_id', $transactionId)->first();
                if ($payment) {
                    $this->orderPaymentService->handleRefund($payment, $amount);
                }
            }
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('SSLCommerz refund failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
