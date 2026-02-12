<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Coupon;

class CartService
{
    /**
     * Get or create cart
     */
    public function getOrCreateCart($userId = null, string $sessionId = null): Cart
    {
        if ($userId) {
            return Cart::firstOrCreate(
                ['user_id' => $userId],
                ['session_id' => $sessionId ?? session()->getId(), 'expires_at' => now()->addDays(30)]
            );
        }

        return Cart::firstOrCreate(
            ['session_id' => $sessionId ?? session()->getId()],
            ['expires_at' => now()->addDays(30)]
        );
    }

    /**
     * Add item to cart
     */
    public function addItem(Cart $cart, Product $product, int $quantity, $variantId = null): CartItem
    {
        $variant = $variantId ? ProductVariant::find($variantId) : null;
        $unitPrice = $variant ? $variant->price : $product->selling_price;

        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $quantity,
                'total_price' => ($cartItem->quantity + $quantity) * $unitPrice,
            ]);
        } else {
            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $quantity * $unitPrice,
            ]);
        }

        return $cartItem;
    }

    /**
     * Update cart item quantity
     */
    public function updateItemQuantity(CartItem $item, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            $item->delete();
            return $item;
        }

        $item->update([
            'quantity' => $quantity,
            'total_price' => $quantity * $item->unit_price,
        ]);

        return $item;
    }

    /**
     * Remove item from cart
     */
    public function removeItem(CartItem $item): bool
    {
        return $item->delete();
    }

    /**
     * Apply coupon
     */
    public function applyCoupon(Cart $cart, string $code): array
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || !$coupon->isValid()) {
            return ['success' => false, 'message' => 'Invalid or expired coupon'];
        }

        $subtotal = $cart->items->sum('total_price');

        if ($coupon->minimum_amount && $subtotal < $coupon->minimum_amount) {
            return ['success' => false, 'message' => "Minimum order amount is {$coupon->minimum_amount}"];
        }

        $discount = $coupon->type === 'percentage'
            ? ($subtotal * $coupon->value) / 100
            : $coupon->value;

        if ($coupon->maximum_discount) {
            $discount = min($discount, $coupon->maximum_discount);
        }

        $cart->update(['coupon_code' => $code]);

        return [
            'success' => true,
            'discount' => $discount,
            'message' => 'Coupon applied successfully',
        ];
    }

    /**
     * Remove coupon
     */
    public function removeCoupon(Cart $cart): bool
    {
        return $cart->update(['coupon_code' => null]);
    }

    /**
     * Clear cart
     */
    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->update(['coupon_code' => null]);
    }

    /**
     * Convert cart to abandoned if inactive
     */
    public function markAbandonedIfInactive(): void
    {
        Cart::where('is_abandoned', false)
            ->where('updated_at', '<', now()->subDays(7))
            ->update(['is_abandoned' => true, 'abandoned_at' => now()]);
    }

    /**
     * Get cart subtotal
     */
    public function getSubtotal(Cart $cart): float
    {
        return $cart->items()->sum('total_price');
    }

    /**
     * Get cart totals
     */
    public function getCartTotals(Cart $cart, float $taxRate = 0.10, float $shippingCost = 0): array
    {
        $subtotal = $this->getSubtotal($cart);
        $coupon = Coupon::where('code', $cart->coupon_code)->first();
        
        $discount = 0;
        if ($coupon && $coupon->isValid()) {
            $discount = $coupon->type === 'percentage'
                ? ($subtotal * $coupon->value) / 100
                : $coupon->value;
            $discount = min($discount, $coupon->maximum_discount ?? $discount);
        }

        $taxable = max($subtotal - $discount, 0);
        $tax = $taxable * $taxRate;
        $total = $subtotal - $discount + $tax + $shippingCost;

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'tax' => round($tax, 2),
            'shipping' => round($shippingCost, 2),
            'total' => round($total, 2),
        ];
    }
}
