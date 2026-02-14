<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderdItem;
use App\Models\VendorLedger;
use Illuminate\Support\Collection;

/**
 * Multi-vendor order splitting service
 * Splits a single order by vendor, creating separate shipments and ledger entries
 */
class MultiVendorOrderService
{
    /**
     * Split order items by vendor and create sub-orders
     */
    public function splitOrderByVendor(Order $order): Collection
    {
        $items = $order->items()->get();
        $groupedByVendor = $items->groupBy('vendor_id');

        $vendorOrders = collect();

        foreach ($groupedByVendor as $vendorId => $vendorItems) {
            $subOrder = $this->createVendorSubOrder($order, $vendorId, $vendorItems);
            $vendorOrders->push($subOrder);

            // Record ledger entry for vendor
            $amount = $vendorItems->sum('total_amount');
            VendorLedger::create([
                'vendor_id' => $vendorId,
                'order_id' => $subOrder->id,
                'type' => 'sale',
                'amount' => $amount,
            ]);

            // Record commission deduction
            $commission = $this->calculateCommission($vendorId, $amount);
            VendorLedger::create([
                'vendor_id' => $vendorId,
                'order_id' => $subOrder->id,
                'type' => 'commission',
                'amount' => $commission,
            ]);
        }

        return $vendorOrders;
    }

    /**
     * Create a sub-order for a specific vendor
     */
    protected function createVendorSubOrder(Order $parentOrder, int $vendorId, Collection $items): Order
    {
        $total = $items->sum('total_amount');
        
        $subOrder = Order::create([
            'user_id' => $parentOrder->user_id,
            'vendor_id' => $vendorId,
            'order_number' => $parentOrder->order_number . '-' . $vendorId,
            'status' => 'pending',
            'payment_status' => 'pending',
            'subtotal' => $total,
            'total_amount' => $total,
        ]);

        // Copy items to sub-order
        foreach ($items as $item) {
            $subOrder->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total_amount' => $item->total_amount,
            ]);
        }

        return $subOrder;
    }

    /**
     * Calculate commission for vendor
     */
    protected function calculateCommission(int $vendorId, float $amount): float
    {
        $vendor = \App\Models\Vendor::find($vendorId);
        if (!$vendor) {
            $rate = Setting::get('commission.default_rate', 0.10);
        } else {
            $rate = $vendor->commission_rate / 100;
        }

        return $amount * $rate;
    }

    /**
     * Check if order should be split by vendor
     */
    public function shouldSplitOrder(Order $order): bool
    {
        $vendorCount = $order->items()->distinct('vendor_id')->count('vendor_id');
        return $vendorCount > 1;
    }
}
