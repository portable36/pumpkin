<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Address;
use App\Services\OrderService;
use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    private OrderService $orderService;
    private CartService $cartService;

    public function __construct(OrderService $orderService, CartService $cartService)
    {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
    }

    /**
     * Display checkout page
     */
    public function index()
    {
        $cart = $this->cartService->getOrCreateCart(auth()->id(), session()->getId());

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('message', 'Your cart is empty');
        }

        $addresses = auth()->user()->addresses;
        $totals = $this->cartService->getCartTotals($cart);

        return view('checkout.index', compact('cart', 'addresses', 'totals'));
    }

    /**
     * Process checkout
     */
    public function process(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:card,bkash,sslcommerz,cod',
            'terms_accepted' => 'required|accepted',
        ]);

        // Verify addresses belong to user
        $shippingAddress = Address::findOrFail($request->shipping_address_id);
        $billingAddress = Address::findOrFail($request->billing_address_id);

        if ($shippingAddress->user_id !== auth()->id() || $billingAddress->user_id !== auth()->id()) {
            abort(403);
        }

        $cart = $this->cartService->getOrCreateCart(auth()->id(), session()->getId());

        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        // Create order
        $order = $this->orderService->createOrderFromCart(
            auth()->user(),
            $cart,
            $shippingAddress,
            $billingAddress,
            $request->payment_method
        );

        return response()->json([
            'message' => 'Order created successfully',
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'redirect_url' => route('orders.show', $order),
        ]);
    }

    /**
     * Display order confirmation
     */
    public function confirmation(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('checkout.confirmation', compact('order'));
    }
}
