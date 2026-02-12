<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    /**
     * Process Pathao shipment
     */
    public function createPathaoShipment(Order $order): array
    {
        try {
            $shipmentData = [
                'order_id' => $order->id,
                'recipient_name' => $order->deliveryAddress->fullName(),
                'recipient_phone' => $order->deliveryAddress->phone,
                'recipient_address' => $order->deliveryAddress->fullAddress(),
                'recipient_city' => $order->deliveryAddress->city,
                'weight' => $this->calculateOrderWeight($order),
                'cash_on_delivery' => $order->payment_method === 'cod' ? 1 : 0,
                'items' => $this->formatOrderItems($order),
            ];

            // Call Pathao API
            $response = $this->makePathaoRequest('/aladdin/api/v2/orders/create/instant-pickup', $shipmentData);

            if (!isset($response['delivery_id'])) {
                throw new \Exception('Failed to create shipment');
            }

            $order->shipments()->create([
                'tracking_number' => $response['delivery_id'],
                'courier_name' => 'Pathao',
                'status' => 'pending',
                'carrier_response' => $response,
            ]);

            return ['success' => true, 'tracking_id' => $response['delivery_id']];
        } catch (\Exception $e) {
            Log::error('Pathao shipment creation failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Process Steadfast shipment
     */
    public function createSteadfastShipment(Order $order): array
    {
        try {
            $shipmentData = [
                'invoice' => $order->order_number,
                'recipient_name' => $order->deliveryAddress->fullName(),
                'recipient_phone' => $order->deliveryAddress->phone,
                'recipient_address' => $order->deliveryAddress->fullAddress(),
                'recipient_city' => $order->deliveryAddress->city,
                'recipient_zone' => $this->getZoneFromCity($order->deliveryAddress->city),
                'weight' => $this->calculateOrderWeight($order),
                'cod' => $order->payment_method === 'cod' ? 1 : 0,
                'note' => $order->notes,
            ];

            $response = $this->makeSteadfastRequest('create_order', $shipmentData);

            if ($response['status'] !== 200) {
                throw new \Exception($response['message'] ?? 'Failed to create shipment');
            }

            $order->shipments()->create([
                'tracking_number' => $response['tracking_code'],
                'courier_name' => 'Steadfast',
                'status' => 'pending',
                'carrier_response' => $response,
            ]);

            return ['success' => true, 'tracking_id' => $response['tracking_code']];
        } catch (\Exception $e) {
            Log::error('Steadfast shipment creation failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Track shipment
     */
    public function trackShipment(string $trackingNumber, string $courier): array
    {
        try {
            $response = match ($courier) {
                'pathao' => $this->trackPathaoShipment($trackingNumber),
                'steadfast' => $this->trackSteadfastShipment($trackingNumber),
                default => throw new \Exception('Unknown courier'),
            };

            return ['success' => true, 'data' => $response];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Calculate order weight
     */
    private function calculateOrderWeight(Order $order): float
    {
        return $order->items->sum(function ($item) {
            return ($item->product->weight ?? 0.5) * $item->quantity;
        });
    }

    /**
     * Format order items for shipment
     */
    private function formatOrderItems(Order $order): array
    {
        return $order->items->map(function ($item) {
            return [
                'item_type' => 1,
                'quantity' => $item->quantity,
                'weight' => $item->product->weight ?? 0.5,
            ];
        })->toArray();
    }

    /**
     * Get zone from city
     */
    private function getZoneFromCity(string $city): string
    {
        // This would map cities to delivery zones
        $zones = [
            'Dhaka' => 1,
            'Chittagong' => 2,
            'Sylhet' => 3,
        ];

        return $zones[$city] ?? 99;
    }

    /**
     * Make Pathao request
     */
    private function makePathaoRequest(string $endpoint, array $data): array
    {
        // Implementation would call Pathao API
        return [];
    }

    /**
     * Make Steadfast request
     */
    private function makeSteadfastRequest(string $endpoint, array $data): array
    {
        // Implementation would call Steadfast API
        return [];
    }

    /**
     * Track Pathao shipment
     */
    private function trackPathaoShipment(string $trackingNumber): array
    {
        // Implementation
        return [];
    }

    /**
     * Track Steadfast shipment
     */
    private function trackSteadfastShipment(string $trackingNumber): array
    {
        // Implementation
        return [];
    }

    /**
     * Calculate shipping cost
     */
    public function calculateShippingCost(string $destination, float $weight): float
    {
        // Simple shipping cost calculation
        $baseCost = 50; // Base shipping cost
        $perKgCost = 10; // Cost per kg

        return $baseCost + ($weight * $perKgCost);
    }
}
