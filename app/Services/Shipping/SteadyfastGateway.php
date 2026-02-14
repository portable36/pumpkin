<?php

namespace App\Services\Shipping;

use App\Models\Shipment;
use App\Models\Address;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SteadyfastGateway implements ShippingGatewayInterface
{
    /**
     * @var string
     */
    protected string $apiKey;

    /**
     * @var string
     */
    protected string $baseUrl;

    /**
     * @var string
     */
    protected string $apiSecret;

    /**
     * @var bool
     */
    protected bool $sandbox;

    /**
     * Initialize Steadfast gateway
     */
    public function __construct()
    {
        $cfg = config('services.shipping.steadfast', []);

        $this->apiKey = $cfg['api_key'] ?? \App\Models\Setting::get('shipping.gateways.steadfast.api_key', '');
        $this->apiSecret = $cfg['api_secret'] ?? \App\Models\Setting::get('shipping.gateways.steadfast.api_secret', '');
        $this->sandbox = $cfg['sandbox'] ?? \App\Models\Setting::get('shipping.gateways.steadfast.sandbox', true);

        // Set base URL based on configuration
        if (!empty($cfg['base_url_sandbox']) && !empty($cfg['base_url_live'])) {
            $this->baseUrl = $this->sandbox ? $cfg['base_url_sandbox'] : $cfg['base_url_live'];
        } else {
            $this->baseUrl = $this->sandbox 
                ? 'https://api-staging.steadfast.com.bd/api/v1'
                : 'https://api.steadfast.com.bd/api/v1';
        }
    }

    /**
     * Register shipment with Steadfast
     */
    public function registerShipment(Shipment $shipment, Address $address): array
    {
        try {
            $payload = [
                'invoice' => "INV-{$shipment->order_id}-" . now()->timestamp,
                'recipient_name' => $address->first_name . ' ' . $address->last_name,
                'recipient_phone' => $address->phone,
                'recipient_address' => $address->address_line_1,
                'recipient_city' => $address->city,
                'recipient_zone' => $this->mapCityToZone($address->city),
                'weight' => $shipment->weight ?? 0.5,
                'cod' => $shipment->order ? ($shipment->order->payment_method === 'cod' ? 1 : 0) : 0,
                'note' => $shipment->notes ?? '',
            ];

            $response = $this->request('POST', '/create_order', $payload);

            if ($response['status'] != 200) {
                throw new Exception($response['message'] ?? 'Failed to create order');
            }

            return [
                'tracking_number' => $response['data']['tracking_code'] ?? null,
                'reference_id' => $response['data']['id'] ?? null,
                'status' => 'pending',
                'gateway_id' => $response['data']['id'] ?? null,
                'response' => $response,
            ];
        } catch (Exception $e) {
            Log::error('Steadfast shipment registration failed', [
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

            $response = $this->request('GET', "/track_order/{$shipment->tracking_number}", []);

            return [
                'tracking_number' => $shipment->tracking_number,
                'status' => $response['data']['status'] ?? 'unknown',
                'current_location' => $response['data']['current_location'] ?? null,
                'delivery_date' => $response['data']['delivery_date'] ?? null,
                'response' => $response,
            ];
        } catch (Exception $e) {
            Log::error('Steadfast track failed', [
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
            $payload = [
                'zone_id' => $this->mapCityToZone($district),
                'weight' => $weight,
                'quantity' => $quantity,
            ];

            $response = $this->request('POST', '/price_plan', $payload);

            return [
                'base_rate' => $response['data']['base_rate'] ?? 0,
                'weight_rate' => $response['data']['weight_rate'] ?? 0,
                'total_rate' => $response['data']['total_rate'] ?? 0,
                'gateway' => 'steadfast',
            ];
        } catch (Exception $e) {
            Log::error('Steadfast rate calculation failed', [
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

            $response = $this->request('POST', '/cancel_order', [
                'tracking_code' => $shipment->tracking_number,
            ]);

            return $response['status'] == 200;
        } catch (Exception $e) {
            Log::error('Steadfast cancel failed', [
                'error' => $e->getMessage(),
                'tracking_number' => $shipment->tracking_number,
            ]);
            return false;
        }
    }

    /**
     * Handle webhook from Steadfast
     */
    public function handleWebhook(array $payload): bool
    {
        try {
            if (!isset($payload['tracking_code'])) {
                return false;
            }

            $shipment = Shipment::where('tracking_number', $payload['tracking_code'])->first();
            if (!$shipment) {
                return false;
            }

            // Map Steadfast status to our status
            $status = $this->mapStatus($payload['status'] ?? '');

            if ($status) {
                $shipment->update([
                    'status' => $status,
                    'gateway_response' => $payload,
                ]);
            }

            return true;
        } catch (Exception $e) {
            Log::error('Steadfast webhook handling failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Make API request with circuit breaker protection
     */
    protected function request(string $method, string $endpoint, array $data): array
    {
        try {
            $url = $this->baseUrl . $endpoint;

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->$method($url, $data);

            if (!$response->successful()) {
                throw new Exception("API Error: {$response->status()} - {$response->body()}");
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Steadfast API request failed', [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Map city to Steadfast zone ID
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
     * Map Steadfast status to internal status
     */
    protected function mapStatus(string $steadfastStatus): ?string
    {
        $statusMap = [
            'pending' => Shipment::STATUS_PENDING,
            'picked' => Shipment::STATUS_PICKED_UP,
            'in_transit' => Shipment::STATUS_IN_TRANSIT,
            'delivered' => Shipment::STATUS_DELIVERED,
            'delivery_failed' => Shipment::STATUS_FAILED,
            'returned' => Shipment::STATUS_RETURNED,
        ];

        return $statusMap[$steadfastStatus] ?? null;
    }
}
