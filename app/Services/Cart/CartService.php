<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected $sessionKey = 'cart_session_id';
    protected $cookieDuration = 43200; // 30 days in minutes

    public function getCart(): ?Cart
    {
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->with('items.product')
                ->first();

            if (!$cart) {
                $cart = $this->createCart();
            }

            return $cart;
        }

        $sessionId = Session::get($this->sessionKey);

        if ($sessionId) {
            $cart = Cart::where('session_id', $sessionId)
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->with('items.product')
                ->first();

            if ($cart) {
                return $cart;
            }
        }

        return $this->createCart();
    }

    protected function createCart(): Cart
    {
        $sessionId = Session::get($this->sessionKey);

        if (!$sessionId) {
            $sessionId = uniqid('cart_', true);
            Session::put($this->sessionKey, $sessionId);
        }

        $data = [
            'session_id' => $sessionId,
            'expires_at' => now()->addDays(30),
        ];

        if (Auth::check()) {
            $data['user_id'] = Auth::id();
            unset($data['session_id']);
        }

        return Cart::create($data);
    }

    public function addItem(Product $product, int $quantity = 1, ?int $variantId = null): CartItem
    {
        $cart = $this->getCart();

        $existingItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;

            if ($newQuantity > $product->stock) {
                throw new \Exception('Not enough stock available');
            }

            $existingItem->update([
                'quantity' => $newQuantity,
            ]);

            return $existingItem->fresh();
        }

        return $cart->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
            'price' => $product->selling_price,
        ]);
    }

    public function updateQuantity(int $itemId, int $quantity): bool
    {
        $cart = $this->getCart();
        $item = $cart->items()->findOrFail($itemId);

        if ($quantity > $item->product->stock) {
            throw new \Exception('Not enough stock available');
        }

        if ($quantity < 1) {
            return $this->removeItem($itemId);
        }

        return $item->update(['quantity' => $quantity]);
    }

    public function removeItem(int $itemId): bool
    {
        $cart = $this->getCart();
        return $cart->items()->where('id', $itemId)->delete();
    }

    public function clearCart(): bool
    {
        $cart = $this->getCart();
        return $cart->items()->delete();
    }

    public function mergeCarts(int $userId, string $sessionId): void
    {
        $guestCart = Cart::where('session_id', $sessionId)->first();
        $userCart = Cart::where('user_id', $userId)->first();

        if (!$guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        if (!$userCart) {
            $guestCart->update([
                'user_id' => $userId,
                'session_id' => null,
            ]);
            return;
        }

        foreach ($guestCart->items as $item) {
            $existingItem = $userCart->items()
                ->where('product_id', $item->product_id)
                ->where('product_variant_id', $item->product_variant_id)
                ->first();

            if ($existingItem) {
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $item->quantity,
                ]);
            } else {
                $userCart->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
            }
        }

        $guestCart->delete();
    }

    public function getCartCount(): int
    {
        $cart = $this->getCart();
        return $cart ? $cart->total_items : 0;
    }
}
