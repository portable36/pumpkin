<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

/**
 * Webhook signature verification service
 * Implements HMAC-SHA256 verification for secure webhook handling
 */
class WebhookSecurityService
{
    /**
     * Verify webhook HMAC signature
     */
    public static function verifySignature(
        string $payload,
        string $providedSignature,
        string $secret
    ): bool {
        $computed = hash_hmac('sha256', $payload, $secret);
        return hash_equals($computed, $providedSignature);
    }

    /**
     * Generate HMAC signature for payload
     */
    public static function generateSignature(string $payload, string $secret): string
    {
        return hash_hmac('sha256', $payload, $secret);
    }

    /**
     * Verify Steadfast webhook signature
     */
    public static function verifySteadfastWebhook(array $headers, string $payload, string $apiSecret): bool
    {
        $signature = $headers['X-Steadfast-Signature'] ?? null;
        if (!$signature) {
            return false;
        }

        return static::verifySignature($payload, $signature, $apiSecret);
    }

    /**
     * Verify Pathao webhook signature
     */
    public static function verifyPathaoWebhook(array $headers, string $payload, string $webhookSecret): bool
    {
        $signature = $headers['X-Pathao-Signature'] ?? null;
        if (!$signature) {
            return false;
        }

        return static::verifySignature($payload, $signature, $webhookSecret);
    }
}
