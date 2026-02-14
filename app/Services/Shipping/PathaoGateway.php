<?php

namespace App\Services\Shipping;

use App\Models\Shipment;
use App\Models\Address;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Cache;

class PathaoGateway implements ShippingGatewayInterface
{
    /**
     * @var string
     */
    protected string $apiKey;

    /**
     * @var string
     */
    protected string $baseUrl = 'https://api-staging.pathao.com';

    /**
     * @var bool
     */
    protected bool $sandbox;

    /**
     * Initialize Pathao gateway
     */
    public function __construct()
    {
        $cfg = config('services.shipping.pathao', []);

        $this->apiKey = $cfg['client_id'] ?? $cfg['api_key'] ?? \App\Models\Setting::get('shipping.gateways.pathao.api_key', '');
        $this->sandbox = $cfg['sandbox'] ?? \App\Models\Setting::get('shipping.gateways.pathao.sandbox', true);

        if (!empty($cfg['base_url_sandbox']) && !empty($cfg['base_url_live'])) {
            $this->baseUrl = $this->sandbox ? $cfg['base_url_sandbox'] : $cfg['base_url_live'];
        } else {
            $this->baseUrl = $this->sandbox ? 'https://api-staging.pathao.com' : 'https://api.pathao.com';
        }

        // Token cache key
        $this->tokenCacheKey = 'pathao_access_token';
    }

    /**
     * Get OAuth access token (uses password grant for sandbox if configured)
     */
    protected function getAccessToken(): string
    {
        // Return cached token if present
        $cached = Cache::get($this->tokenCacheKey);
        if ($cached) {
            return $cached;
        }

        $cfg = config('services.shipping.pathao', []);

        // If explicit client credentials and username/password provided, perform token exchange
        $clientId = $cfg['client_id'] ?? env('PATHAO_SANDBOX_CLIENT_ID');
        $clientSecret = $cfg['client_secret'] ?? env('PATHAO_SANDBOX_CLIENT_SECRET');
        $username = $cfg['username'] ?? env('PATHAO_SANDBOX_USERNAME');
        $password = $cfg['password'] ?? env('PATHAO_SANDBOX_PASSWORD');

        if ($clientId && $clientSecret && $username && $password) {
            $tokenUrl = rtrim($this->baseUrl, '/') . '/oauth/token';
            $response = Http::asForm()->post($tokenUrl, [
                'grant_type' => 'password',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'username' => $username,
                'password' => $password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $access = $data['access_token'] ?? null;
                $ttl = $data['expires_in'] ?? 3600;
                if ($access) {
                    Cache::put($this->tokenCacheKey, $access, now()->addSeconds($ttl - 30));
                    return $access;
                }
            }

            throw new Exception('Failed to obtain Pathao access token: ' . $response->body());
        }

        // Fallback: if an api key is configured, return it
        if ($this->apiKey) {
            return $this->apiKey;
        }

        throw new Exception('No Pathao credentials configured');
    }

    /**
     * Register shipment with Pathao
     */
    public function registerShipment(Shipment $shipment, Address $address): array
    {
        try {
            $payload = [
                'merchant_order_id' => "MO-{$shipment->order_id}-" . now()->timestamp,
                'sender_name' => \App\Models\Setting::get('platform.name', 'Pumpkin Store'),
                'sender_phone' => \App\Models\Setting::get('platform.phone', ''),
                'sender_address' => \App\Models\Setting::get('platform.address', ''),
                'recipient_name' => $address->first_name . ' ' . $address->last_name,
                'recipient_phone' => $address->phone,
                'recipient_address' => $address->address_line_1,
                'recipient_city' => $address->city,
                'delivery_type' => 48, // Standard delivery
                'item_type' => 2, // Item
                'special_instruction' => $shipment->notes ?? '',
                'item_quantity' => 1,
                'amount_to_collect' => $shipment->order ? $shipment->order->total : 0,
            ];

            $response = $this->request('POST', '/aladdin/api/v2/orders/create/instant-pickup', $payload);

            if (!isset($response['data']['delivery_id'])) {
                throw new Exception($response['message'] ?? 'Failed to create order');
            }

            return [
                'tracking_number' => $response['data']['delivery_id'],
                'reference_id' => $response['data']['merchant_order_id'] ?? null,
                'status' => 'pending',
                'gateway_id' => $response['data']['delivery_id'],
                'response' => $response,
            ];
        } catch (Exception $e) {
            Log::error('Pathao shipment registration failed', [
                'error' => $e->getMessage(),
                'shipment_id' => $shipment->id,
            ]);
            throw $e;
        }
    }

    /**
     * Track shipment status
     */
    public function trackShipment(Shipment $shipment): array
    {
        try {
            if (!$shipment->tracking_number) {
                return ['error' => 'No tracking number available'];
            }

            $response = $this->request('GET', "/aladdin/api/v2/orders/{$shipment->tracking_number}", []);

            return [
                'tracking_number' => $shipment->tracking_number,
                'status' => $this->mapPathaoStatus($response['data']['status'] ?? ''),
                'current_location' => $response['data']['current_location'] ?? null,
                'delivery_date' => $response['data']['delivery_date'] ?? null,
                'response' => $response,
            ];
        } catch (Exception $e) {
            Log::error('Pathao track failed', [
                'error' => $e->getMessage(),
                'tracking_number' => $shipment->tracking_number,
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Calculate shipping rate
     */
    public function calculateRate(string $district, int $quantity = 1, float $weight = 0.5): array
    {
        try {
            // Pathao pricing is typically based on delivery type and zone
            $payload = [
                'delivery_type' => 48,
                'recipient_city' => $district,
                'recipient_zone' => $this->mapCityToZone($district),
            ];

            $response = $this->request('POST', '/aladdin/api/v2/orders/calculate-delivery-fee', $payload);

            return [
                'base_rate' => $response['data']['delivery_fee']['primary_charge'] ?? 0,
                'additional_rate' => $response['data']['delivery_fee']['additional_charge'] ?? 0,
                'total_rate' => $response['data']['delivery_fee']['total_charge'] ?? 0,
                'gateway' => 'pathao',
            ];
        } catch (Exception $e) {
            Log::error('Pathao rate calculation failed', [
                'error' => $e->getMessage(),
                'district' => $district,
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Cancel shipment
     */
    public function cancelShipment(Shipment $shipment): bool
    {
        try {
            if (!$shipment->tracking_number) {
                return false;
            }

            $response = $this->request('POST', "/aladdin/api/v2/orders/{$shipment->tracking_number}/cancel", []);

            return isset($response['data']) && $response['data']['status'] === 'cancelled';
        } catch (Exception $e) {
            Log::error('Pathao cancel failed', [
                'error' => $e->getMessage(),
                'tracking_number' => $shipment->tracking_number,
            ]);
            return false;
        }
    }

    /**
     * Handle webhook from Pathao
     */
    public function handleWebhook(array $payload): bool
    {
        try {
            if (!isset($payload['delivery_id'])) {
                return false;
            }

            $shipment = Shipment::where('tracking_number', $payload['delivery_id'])->first();
            if (!$shipment) {
                return false;
            }

            // Map Pathao status to our status
            $status = $this->mapPathaoStatus($payload['latest_status'] ?? '');

            if ($status) {
                $shipment->update([
                    'status' => $status,
                    'gateway_response' => $payload,
                ]);
            }

            return true;
        } catch (Exception $e) {
            Log::error('Pathao webhook handling failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Make API request
     */
    protected function request(string $method, string $endpoint, array $data): array
    {
        try {
            $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

            // Ensure we have an access token
            $token = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
            ])->$method($url, $data);

            if (!$response->successful()) {
                throw new Exception("API Error: {$response->status()} - {$response->body()}");
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Pathao API request failed', [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Map city to Pathao zone
     */
    protected function mapCityToZone(string $city): int
    {
        $zoneMap = [
            'Dhaka' => 1,
            'Chattogram' => 2,
            'Chittagong' => 2,
            'Sylhet' => 3,
            'Khulna' => 4,
            'Barisal' => 5,
            'Rajshahi' => 6,
            'Rangpur' => 7,
            'Mymensingh' => 8,
        ];

        return $zoneMap[$city] ?? 99;
    }

    /**
     * Map Pathao status codes to internal status
     */
    protected function mapPathaoStatus(string $pathaoStatus): ?string
    {
        // Pathao status codes and meanings
        $statusMap = [
            '1' => Shipment::STATUS_PENDING,
            '2' => Shipment::STATUS_PICKED_UP,
            '3' => Shipment::STATUS_IN_TRANSIT,
            '4' => Shipment::STATUS_OUT_FOR_DELIVERY,
            '5' => Shipment::STATUS_DELIVERED,
            '6' => Shipment::STATUS_FAILED,
            '7' => Shipment::STATUS_RETURNED,
            '8' => Shipment::STATUS_CANCELLED,
        ];

        return $statusMap[$pathaoStatus] ?? null;
    }
}
