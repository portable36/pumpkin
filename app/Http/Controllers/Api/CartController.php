<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends \App\Http\Controllers\Controller
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Get cart
     */
    public function show(Request $request)
    {
        $cart = $this->cartService->getOrCreateCart(
            userId: auth()->id(),
            sessionId: session()->getId()
        );

        $totals = $this->cartService->getCartTotals($cart);

        return response()->json([
            'cart' => $cart->load('items.product'),
            'totals' => $totals,
        ]);
    }

    /**
     * Add item
     */
    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);

        if (!$product->is_active || $product->stock_quantity < $request->quantity) {
            return response()->json(['message' => 'Product not available'], 422);
        }

        $cart = $this->cartService->getOrCreateCart(auth()->id(), session()->getId());
        $this->cartService->addItem($cart, $product, $request->quantity, $request->variant_id);

        return response()->json([
            'message' => 'Item added to cart',
            'cart_count' => $cart->items()->count(),
        ]);
    }

    /**
     * Update item
     */
    public function updateItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:0|max:100',
        ]);

        $item = \App\Models\CartItem::findOrFail($request->item_id);
        $this->cartService->updateItemQuantity($item, $request->quantity);

        return response()->json(['message' => 'Cart updated']);
    }

    /**
     * Remove item
     */
    public function removeItem(Request $request)
    {
        $request->validate(['item_id' => 'required|exists:cart_items,id']);

        $item = \App\Models\CartItem::findOrFail($request->item_id);
        $this->cartService->removeItem($item);

        return response()->json(['message' => 'Item removed']);
    }

    /**
     * Apply coupon
     */
    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string|size:6']);

        $cart = $this->cartService->getOrCreateCart(auth()->id(), session()->getId());
        $result = $this->cartService->applyCoupon($cart, $request->coupon_code);

        return response()->json($result);
    }

    /**
     * Clear cart
     */
    public function clear(Request $request)
    {
        $cart = $this->cartService->getOrCreateCart(auth()->id(), session()->getId());
        $this->cartService->clearCart($cart);

        return response()->json(['message' => 'Cart cleared']);
    }
}
