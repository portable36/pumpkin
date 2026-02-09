<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected ?Cart $cart = null;

    public function getCart(): Cart
    {
        if ($this->cart) {
            return $this->cart;
        }

        if (auth()->check()) {
            $this->cart = Cart::firstOrCreate(
                ['user_id' => auth()->id()],
                ['expires_at' => now()->addDays(30)]
            );
            
            // Merge session cart if exists
            $this->mergeSessionCart();
        } else {
            $sessionId = Session::getId();
            $this->cart = Cart::firstOrCreate(
                ['session_id' => $sessionId],
                ['expires_at' => now()->addDays(7)]
            );
        }

        // Clean expired items
        $this->cart->items()->where('created_at', '<', now()->subDays(30))->delete();

        return $this->cart;
    }

    public function addItem(int $productId, int $quantity = 1, ?int $variantId = null): CartItem
    {
        $cart = $this->getCart();
        
        $product = Product::with('variants')->findOrFail($productId);
        $variant = $variantId ? ProductVariant::findOrFail($variantId) : null;

        // Validate stock
        $availableStock = $variant ? $variant->stock : $product->stock;
        if ($availableStock < $quantity) {
            throw new \Exception('Insufficient stock available');
        }

        // Get price
        $price = $variant ? $variant->price : $product->selling_price;

        // Check if item already exists
        $cartItem = $cart->items()
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            
            if ($newQuantity > $availableStock) {
                throw new \Exception('Cannot add more than available stock');
            }
            
            $cartItem->update([
                'quantity' => $newQuantity,
                'price' => $price
            ]);
        } else {
            $cartItem = $cart->items()->create([
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price
            ]);
        }

        return $cartItem;
    }

    public function updateQuantity(int $cartItemId, int $quantity): CartItem
    {
        $cart = $this->getCart();
        $cartItem = $cart->items()->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $cartItem->delete();
            throw new \Exception('Item removed from cart');
        }

        // Validate stock
        $variant = $cartItem->product_variant;
        $product = $cartItem->product;
        $availableStock = $variant ? $variant->stock : $product->stock;

        if ($quantity > $availableStock) {
            throw new \Exception('Requested quantity exceeds available stock');
        }

        $cartItem->update(['quantity' => $quantity]);

        return $cartItem;
    }

    public function removeItem(int $cartItemId): void
    {
        $cart = $this->getCart();
        $cart->items()->findOrFail($cartItemId)->delete();
    }

    public function clear(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
    }

    public function getItemCount(): int
    {
        return $this->getCart()->items()->sum('quantity');
    }

    public function getSubtotal(): float
    {
        return $this->getCart()->items()
            ->get()
            ->sum(fn($item) => $item->price * $item->quantity);
    }

    public function applyCoupon(string $couponCode): array
    {
        $coupon = \App\Models\Coupon::where('code', $couponCode)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->first();

        if (!$coupon) {
            throw new \Exception('Invalid or expired coupon code');
        }

        $subtotal = $this->getSubtotal();

        if ($coupon->min_purchase_amount && $subtotal < $coupon->min_purchase_amount) {
            throw new \Exception("Minimum purchase amount of {$coupon->min_purchase_amount} required");
        }

        // Check usage limits
        if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
            throw new \Exception('Coupon usage limit exceeded');
        }

        if (auth()->check() && $coupon->usage_limit_per_user) {
            $userUsageCount = $coupon->usages()
                ->where('user_id', auth()->id())
                ->count();
            
            if ($userUsageCount >= $coupon->usage_limit_per_user) {
                throw new \Exception('You have reached the usage limit for this coupon');
            }
        }

        // Calculate discount
        $discount = $coupon->type === 'fixed' 
            ? $coupon->value 
            : ($subtotal * $coupon->value / 100);

        if ($coupon->max_discount_amount) {
            $discount = min($discount, $coupon->max_discount_amount);
        }

        return [
            'coupon' => $coupon,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount)
        ];
    }

    protected function mergeSessionCart(): void
    {
        $sessionId = Session::getId();
        $sessionCart = Cart::where('session_id', $sessionId)->first();

        if ($sessionCart && $sessionCart->items()->exists()) {
            foreach ($sessionCart->items as $item) {
                try {
                    $this->addItem($item->product_id, $item->quantity, $item->product_variant_id);
                } catch (\Exception $e) {
                    // Skip items that can't be added
                }
            }
            
            $sessionCart->delete();
        }
    }

    public function recalculatePrices(): void
    {
        $cart = $this->getCart();
        
        foreach ($cart->items as $item) {
            $product = $item->product;
            $variant = $item->product_variant;
            
            $currentPrice = $variant ? $variant->price : $product->selling_price;
            
            if ($item->price != $currentPrice) {
                $item->update(['price' => $currentPrice]);
            }
        }
    }
}
