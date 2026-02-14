<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    public function createPaymentIntent(array $data): array;
    public function verifyWebhook(array $headers, string $payload): bool;
    public function handleWebhook(array $payload): bool;
    public function refund(string $transactionId, float $amount): bool;
}
