<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Events\OrderPaid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderPaymentService
{
    /**
     * Handle successful payment
     */
    public function handlePaymentSuccess(Payment $payment)
    {
        return DB::transaction(function () use ($payment) {
            try {
                // Mark payment as successful
                $payment->markAsSuccessful($payment->external_id, $payment->gateway_response);

                // Get the order
                $order = $payment->order;
                if (!$order) {
                    throw new \Exception('Order not found for payment');
                }

                // Update order status
                $order->update([
                    'payment_status' => 'completed',
                    'status' => 'processing',
                    'paid_at' => now(),
                ]);

                // Perform multi-vendor order split if applicable
                if ($order->items->count() > 0) {
                    $multiVendorService = new MultiVendorOrderService();
                    $multiVendorService->splitOrderByVendor($order);
                }

                // Dispatch event for further processing (shipments, notifications, etc.)
                event(new OrderPaid($order, $payment));

                Log::info("Order {$order->id} payment processed successfully via {$payment->gateway}");

                return $order;
            } catch (\Exception $e) {
                Log::error("Error processing payment {$payment->id}: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Handle failed payment
     */
    public function handlePaymentFailure(Payment $payment, string $reason = null)
    {
        return DB::transaction(function () use ($payment, $reason) {
            try {
                // Mark payment as failed
                $payment->markAsFailed($reason, $payment->gateway_response);

                // Get the order
                $order = $payment->order;
                if ($order) {
                    $order->update([
                        'payment_status' => 'failed',
                    ]);
                }

                Log::warning("Payment {$payment->id} failed: {$reason}");

                return $order ?? null;
            } catch (\Exception $e) {
                Log::error("Error marking payment as failed: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Handle refund
     */
    public function handleRefund(Payment $payment, float $amount = null)
    {
        return DB::transaction(function () use ($payment, $amount) {
            try {
                $refundAmount = $amount ?? $payment->amount;

                // Mark payment as refunded
                $payment->markAsRefunded($refundAmount);

                // Get the order
                $order = $payment->order;
                if ($order) {
                    $order->update([
                        'payment_status' => 'refunded',
                        'refunded_amount' => $order->refunded_amount + $refundAmount,
                    ]);

                    // Record refund in vendor ledger if multi-vendor
                    if ($order->parent_id) {
                        $multiVendorService = new MultiVendorOrderService();
                        $multiVendorService->recordRefundInLedger($order, $refundAmount);
                    }
                }

                Log::info("Payment {$payment->id} refunded: {$refundAmount}");

                return $order ?? null;
            } catch (\Exception $e) {
                Log::error("Error processing refund: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Create child order for specific vendor from parent order
     */
    public function createVendorSubOrder(Order $parentOrder, int $vendorId, array $items, float $total)
    {
        return DB::transaction(function () use ($parentOrder, $vendorId, $items, $total) {
            // Create child order
            $childOrder = Order::create([
                'parent_id' => $parentOrder->id,
                'user_id' => $parentOrder->user_id,
                'vendor_id' => $vendorId,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'payment_status' => 'awaiting',
                'subtotal' => $total,
                'shipping_cost' => 0,
                'tax' => 0,
                'total' => $total,
                'currency' => $parentOrder->currency,
                'shipping_method' => null,
                'customer_name' => $parentOrder->customer_name,
                'customer_email' => $parentOrder->customer_email,
                'customer_phone' => $parentOrder->customer_phone,
                'shipping_address' => $parentOrder->shipping_address,
                'billing_address' => $parentOrder->billing_address,
                'notes' => $parentOrder->notes,
            ]);

            // Attach items to child order
            foreach ($items as $item) {
                $childOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'vendor_id' => $vendorId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // Create marker for vendor payout linkage
            if (class_exists(\App\Models\VendorLedger::class)) {
                \App\Models\VendorLedger::recordSale(
                    $vendorId,
                    $childOrder->id,
                    $total
                );
            }

            return $childOrder;
        });
    }
}
