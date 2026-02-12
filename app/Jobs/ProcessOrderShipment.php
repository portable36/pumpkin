<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\ShippingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessOrderShipment implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public string $courier = 'pathao'
    ) {}

    public function handle(ShippingService $shippingService): void
    {
        try {
            if ($this->courier === 'pathao') {
                $result = $shippingService->createPathaoShipment($this->order);
            } else {
                $result = $shippingService->createSteadfastShipment($this->order);
            }

            if (!$result['success']) {
                $this->release(delay: 300); // Retry after 5 minutes
            }

            Log::info("Order {$this->order->id} shipment processed", $result);
        } catch (\Exception $e) {
            Log::error("Shipment processing failed for order {$this->order->id}: " . $e->getMessage());
            $this->release(delay: 300);
        }
    }
}
