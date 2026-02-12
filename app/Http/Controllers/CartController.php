<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display cart
     */
    public function index()
    {
        $cart = $this->cartService->getOrCreateCart(
            userId: auth()->id(),
            sessionId: session()->getId()
        );

        $totals = $this->cartService->getCartTotals($cart);

        return view('cart.index', compact('cart', 'totals'));
    }

    /**
     * Add item to cart
     */
    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_active || $product->stock_quantity < $request->quantity) {
            return response()->json(['message' => 'Product not available'], 422);
        }

        $cart = $this->cartService->getOrCreateCart(
            userId: auth()->id(),
            sessionId: session()->getId()
        );

        $this->cartService->addItem($cart, $product, $request->quantity, $request->variant_id);

        return response()->json([
            'message' => 'Product added to cart',
            'cart_count' => $cart->items()->count(),
        ]);
    }

    /**
     * Update item quantity
     */
    public function updateItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:0|max:100',
        ]);

        $item = \App\Models\CartItem::findOrFail($request->item_id);

        if ($item->cart->user_id !== auth()->id() && $item->cart->session_id !== session()->getId()) {
            abort(403);
        }

        $this->cartService->updateItemQuantity($item, $request->quantity);

        $totals = $this->cartService->getCartTotals($item->cart);

        return response()->json([
            'message' => 'Cart updated',
            'totals' => $totals,
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
        ]);

        $item = \App\Models\CartItem::findOrFail($request->item_id);

        if ($item->cart->user_id !== auth()->id() && $item->cart->session_id !== session()->getId()) {
            abort(403);
        }

        $this->cartService->removeItem($item);

        return response()->json(['message' => 'Item removed from cart']);
    }

    /**
     * Apply coupon
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|size:6',
        ]);

        $cart = $this->cartService->getOrCreateCart(auth()->id(), session()->getId());

        $result = $this->cartService->applyCoupon($cart, $request->coupon_code);

        return response()->json($result);
    }

    /**
     * Remove coupon
     */
    public function removeCoupon()
    {
        $cart = $this->cartService->getOrCreateCart(auth()->id(), session()->getId());
        $this->cartService->removeCoupon($cart);

        return response()->json(['message' => 'Coupon removed']);
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        $cart = $this->cartService->getOrCreateCart(auth()->id(), session()->getId());
        $this->cartService->clearCart($cart);

        return response()->json(['message' => 'Cart cleared']);
    }
}
