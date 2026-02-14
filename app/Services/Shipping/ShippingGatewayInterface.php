<?php

namespace App\Services\Shipping;

use App\Models\Shipment;
use App\Models\Address;

interface ShippingGatewayInterface
{
    /**
     * Initialize gateway with configuration
     */
    public function __construct();

    /**
     * Register/create shipment with the gateway
     * Returns gateway response with tracking number
     */
    public function registerShipment(Shipment $shipment, Address $address): array;

    /**
     * Get shipment tracking status
     */
    public function trackShipment(Shipment $shipment): array;

    /**
     * Calculate shipping rate
     */
    public function calculateRate(string $district, int $quantity = 1, float $weight = 0.5): array;

    /**
     * Cancel shipment
     */
    public function cancelShipment(Shipment $shipment): bool;

    /**
     * Handle incoming webhook from gateway
     */
    public function handleWebhook(array $payload): bool;
}
