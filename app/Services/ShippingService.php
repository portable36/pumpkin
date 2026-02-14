<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\Order;
use App\Models\Setting;
use App\Services\Shipping\SteadyfastGateway;
use App\Services\Shipping\PathaoGateway;
use App\Services\Shipping\ShippingGatewayInterface;
use Illuminate\Support\Facades\Log;

/**
 * Main shipping service with multi-gateway support
 * Handles shipment creation, tracking, and management
 */
class ShippingService
{
    /**
     * @var ShippingGatewayInterface
     */
    protected ShippingGatewayInterface $gateway;

    /**
     * @var string
     */
    protected string $activeGateway;

    /**
     * Initialize with default gateway
     */
    public function __construct()
    {
        // Prefer environment/config values, fall back to dynamic settings
        $this->activeGateway = config('services.shipping.default_gateway') ?? Setting::get('shipping.default_gateway', 'steadfast');
        $this->initializeGateway($this->activeGateway);
    }

    /**
     * Initialize a specific gateway
     */
    public function initializeGateway(string $gateway): self
    {
        $this->activeGateway = $gateway;
        
        $this->gateway = match($gateway) {
            Shipment::GATEWAY_STEADFAST => new SteadyfastGateway(),
            Shipment::GATEWAY_PATHAO => new PathaoGateway(),
            default => throw new \InvalidArgumentException("Unknown shipping gateway: {$gateway}"),
        };

        return $this;
    }

    /**
     * Get the current active gateway
     */
    public function getGateway(): ShippingGatewayInterface
    {
        return $this->gateway;
    }

    /**
     * Create a shipment from an order
     */
    public function shipOrder(Order $order, array $options = []): Shipment
    {
        try {
            // Choose gateway
            $gateway = $options['gateway'] ?? $this->activeGateway;
            $this->initializeGateway($gateway);

            // Check if gateway is enabled
            if (!$this->isGatewayEnabled($gateway)) {
                throw new \Exception("Shipping gateway '{$gateway}' is not enabled");
            }

            // Get shipping address
            $address = $order->shippingAddress ?? $order->address;
            if (!$address) {
                throw new \Exception("Order does not have a shipping address");
            }

            // Prepare shipment data
            $shipmentData = [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'gateway' => $gateway,
                'weight' => $order->getTotalWeight() ?? 0.5,
                'status' => Shipment::STATUS_PENDING,
                'cost' => $options['cost'] ?? 0,
            ];

            // Create shipment record
            $shipment = Shipment::create($shipmentData);

            // Register shipment with gateway
            $gatewayResponse = $this->gateway->registerShipment($shipment, $address);

            // Update shipment with gateway response
            $shipment->update([
                'tracking_number' => $gatewayResponse['tracking_number'] ?? null,
                'status' => $gatewayResponse['status'] ?? Shipment::STATUS_PENDING,
                'gateway_response' => $gatewayResponse['response'] ?? $gatewayResponse,
            ]);

            // Log success
            Log::info("Shipment created for order {$order->id}", [
                'shipment_id' => $shipment->id,
                'gateway' => $gateway,
                'tracking_number' => $shipment->tracking_number ?? 'pending',
            ]);

            return $shipment;
        } catch (\Exception $e) {
            Log::error("Failed to create shipment for order {$order->id}", [
                'error' => $e->getMessage(),
                'gateway' => $gateway ?? $this->activeGateway,
            ]);
            throw $e;
        }
    }

    /**
     * Get shipment tracking information
     */
    public function trackShipment(Shipment $shipment): array
    {
        if (!$shipment->isTrackable()) {
            return [
                'status' => $shipment->status,
                'message' => 'Shipment is in final state',
            ];
        }

        try {
            $this->initializeGateway($shipment->gateway);
            return $this->gateway->trackShipment($shipment);
        } catch (\Exception $e) {
            Log::error("Failed to track shipment {$shipment->id}", [
                'error' => $e->getMessage(),
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Cancel a shipment
     */
    public function cancelShipment(Shipment $shipment, string $reason = ''): bool
    {
        try {
            $this->initializeGateway($shipment->gateway);
            
            // Call gateway to cancel
            $this->gateway->cancelShipment($shipment);

            // Update shipment status
            $shipment->update([
                'status' => Shipment::STATUS_CANCELLED,
                'notes' => $reason,
            ]);

            Log::info("Shipment {$shipment->id} cancelled", ['reason' => $reason]);
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to cancel shipment {$shipment->id}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get rate quote from gateway
     */
    public function getRate(
        string $district,
        int $quantity = 1,
        float $weight = 0.5,
        string $gateway = null
    ): array {
        try {
            $gateway = $gateway ?? $this->activeGateway;
            
            if (!$this->isGatewayEnabled($gateway)) {
                return ['error' => "Gateway '{$gateway}' is not enabled"];
            }

            $this->initializeGateway($gateway);
            return $this->gateway->calculateRate($district, $quantity, $weight);
        } catch (\Exception $e) {
            Log::error("Failed to get shipping rate", [
                'error' => $e->getMessage(),
                'gateway' => $gateway,
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get available gateways
     */
    public function getAvailableGateways(): array
    {
        $gateways = [];

        if ($this->isGatewayEnabled(Shipment::GATEWAY_STEADFAST)) {
            $gateways[] = Shipment::GATEWAY_STEADFAST;
        }

        if ($this->isGatewayEnabled(Shipment::GATEWAY_PATHAO)) {
            $gateways[] = Shipment::GATEWAY_PATHAO;
        }

        return $gateways;
    }

    /**
     * Calculate shipping cost (simple local estimator)
     */
    public function calculateShippingCost(string $destination, float $weight): float
    {
        $baseCost = 50; // Base shipping cost
        $perKgCost = 10; // Cost per kg

        return $baseCost + ($weight * $perKgCost);
    }

    /**
     * Check if gateway is enabled
     */
    public function isGatewayEnabled(string $gateway): bool
    {
        $cfg = config("services.shipping.{$gateway}");
        if (is_array($cfg) && array_key_exists('enabled', $cfg)) {
            return (bool)$cfg['enabled'];
        }

        return Setting::get("shipping.gateways.{$gateway}.enabled", false);
    }

    /**
     * Get gateway configuration
     */
    public function getGatewayConfig(string $gateway): array
    {
        $cfg = config("services.shipping.{$gateway}", []);

        return [
            'enabled' => $this->isGatewayEnabled($gateway),
            'api_key' => $cfg['api_key'] ?? $cfg['client_id'] ?? Setting::get("shipping.gateways.{$gateway}.api_key"),
            'api_secret' => $cfg['api_secret'] ?? $cfg['client_secret'] ?? Setting::get("shipping.gateways.{$gateway}.api_secret"),
            'sandbox' => $cfg['sandbox'] ?? Setting::get("shipping.gateways.{$gateway}.sandbox", true),
            'base_url' => ($cfg['sandbox'] ?? true) ? ($cfg['base_url_sandbox'] ?? null) : ($cfg['base_url_live'] ?? null),
        ];
    }

    /**
     * Find shipment by tracking number
     */
    public function findByTrackingNumber(string $trackingNumber): ?Shipment
    {
        return Shipment::where('tracking_number', $trackingNumber)->first();
    }

    /**
     * Handle incoming webhook
     */
    public function handleWebhook(string $gateway, array $payload): bool
    {
        try {
            $this->initializeGateway($gateway);
            return $this->gateway->handleWebhook($payload);
        } catch (\Exception $e) {
            Log::error("Webhook handling failed for gateway: {$gateway}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
