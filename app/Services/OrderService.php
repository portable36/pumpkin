<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;

class OrderService
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Create order from cart
     */
    public function createOrderFromCart(
        User $user,
        Cart $cart,
        Address $shippingAddress,
        Address $billingAddress,
        string $paymentMethod,
        float $shippingCost = 0
    ): Order {
        $totals = $this->cartService->getCartTotals($cart, shippingCost: $shippingCost);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad(Order::max('id') + 1, 5, '0', STR_PAD_LEFT),
            'subtotal' => $totals['subtotal'],
            'discount_amount' => $totals['discount'],
            'tax_amount' => $totals['tax'],
            'shipping_cost' => $totals['shipping'],
            'total_amount' => $totals['total'],
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $paymentMethod,
            'delivery_address_id' => $shippingAddress->id,
            'billing_address_id' => $billingAddress->id,
            'coupon_code' => $cart->coupon_code,
            'ip_address' => request()->ip(),
        ]);

        // Create order items grouped by vendor
        foreach ($cart->items as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product->id,
                'product_variant_id' => $cartItem->product_variant_id,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->unit_price,
                'total_amount' => $cartItem->total_price,
                'status' => 'pending',
            ]);

            // Reserve stock
            $product = $cartItem->product;
            $product->decrement('stock_quantity', $cartItem->quantity);
        }

        // Clear cart
        $this->cartService->clearCart($cart);

        return $order;
    }

    /**
     * Get vendor orders from an order
     */
    public function getVendorOrders(Order $order): Collection
    {
        $vendorOrders = [];

        foreach ($order->items as $item) {
            $vendorId = $item->product->vendor_id;

            if (!isset($vendorOrders[$vendorId])) {
                $vendorOrders[$vendorId] = [
                    'vendor_id' => $vendorId,
                    'items' => [],
                    'subtotal' => 0,
                ];
            }

            $vendorOrders[$vendorId]['items'][] = $item;
            $vendorOrders[$vendorId]['subtotal'] += $item->total_amount;
        }

        // Create or update vendor specific orders
        foreach ($vendorOrders as $vendorId => $orderData) {
            Order::updateOrCreate(
                ['id' => $order->id, 'vendor_id' => $vendorId],
                [
                    'subtotal' => $orderData['subtotal'],
                    'total_amount' => $orderData['subtotal'],
                ]
            );
        }

        return Order::where('id', $order->id)->get();
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Order $order, string $status): Order
    {
        $updateData = ['status' => $status];

        match ($status) {
            'shipped' => $updateData['shipped_at'] = now(),
            'delivered' => $updateData['delivered_at'] = now(),
            'cancelled' => $updateData['cancelled_at'] = now(),
            default => null,
        };

        $order->update($updateData);

        return $order;
    }

    /**
     * Mark order as paid
     */
    public function markAsPaid(Order $order, string $transactionId): Order
    {
        $order->update([
            'payment_status' => 'paid',
            'transaction_id' => $transactionId,
            'status' => 'processing',
        ]);

        return $order;
    }

    /**
     * Cancel order
     */
    public function cancelOrder(Order $order, string $reason = null): Order
    {
        // Restore stock
        foreach ($order->items as $item) {
            $item->product->increment('stock_quantity', $item->quantity);
        }

        $order->update([
            'status' => 'cancelled',
            'admin_notes' => $reason,
            'cancelled_at' => now(),
        ]);

        return $order;
    }

    /**
     * Get order statistics
     */
    public function getOrderStats($vendorId = null): array
    {
        $query = Order::query();

        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }

        return [
            'total_orders' => $query->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'processing' => $query->where('status', 'processing')->count(),
            'shipped' => $query->where('status', 'shipped')->count(),
            'delivered' => $query->where('status', 'delivered')->count(),
            'cancelled' => $query->where('status', 'cancelled')->count(),
            'total_revenue' => $query->sum('total_amount'),
            'unpaid_orders' => $query->where('payment_status', 'pending')->sum('total_amount'),
        ];
    }
}
