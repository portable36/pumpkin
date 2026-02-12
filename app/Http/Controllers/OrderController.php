<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderReturn;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * List user orders
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['items', 'shipments'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display order details
     */
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items', 'shipments', 'returns', 'refunds', 'payments']);

        return view('orders.show', compact('order'));
    }

    /**
     * Track order
     */
    public function track(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $shipments = $order->shipments()->with('order')->latest()->get();

        return view('orders.track', compact('order', 'shipments'));
    }

    /**
     * Request return
     */
    public function requestReturn(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'item_id' => 'required|exists:orderd_items,id',
            'reason' => 'required|string|min:10',
            'description' => 'required|string|min:20',
        ]);

        $item = $order->items()->findOrFail($request->item_id);

        OrderReturn::create([
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'requested',
            'requested_at' => now(),
        ]);

        return response()->json(['message' => 'Return request submitted']);
    }

    /**
     * Download invoice
     */
    public function downloadInvoice(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Generate and serve invoice PDF
        return response()->download(
            storage_path("app/private/invoices/order-{$order->order_number}.pdf")
        );
    }

    /**
     * Show checkout form
     */
    public function checkoutForm()
    {
        $user = auth()->user();
        $cart = $user->cart ?? [];
        
        $subtotal = 0;
        $tax = 0;
        $total = 0;
        $cart_items = [];

        if ($user->cart) {
            $cart_items = $user->cart->items;
            $subtotal = $cart_items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });
            $tax = $subtotal * 0.1;
            $total = $subtotal + 9.99 + $tax;
        }

        return view('checkout.index', compact('cart_items', 'subtotal', 'tax', 'total'));
    }

    /**
     * Create order from checkout
     */
    public function createFromCheckout(Request $request)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $request->validate([
            'shipping_first_name' => 'required|string|max:255',
            'shipping_last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_zip' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:100',
            'shipping_method' => 'required|in:standard,express,overnight',
            'payment_method' => 'required|in:credit_card,paypal,bank_transfer',
        ]);

        $user = auth()->user();
        if (!$user->cart || $user->cart->items()->count() === 0) {
            return redirect('/shop')->with('error', 'Cart is empty');
        }

        $cart_items = $user->cart->items;
        $subtotal = $cart_items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $shipping_cost = match($request->shipping_method) {
            'express' => 24.99,
            'overnight' => 49.99,
            default => 9.99,
        };

        $tax = $subtotal * 0.1;
        $total = $subtotal + $shipping_cost + $tax;

        $order_number = 'ORD-' . strtoupper(uniqid());

        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => $order_number,
            'status' => 'pending',
            'payment_status' => 'pending',
            'subtotal_amount' => $subtotal,
            'shipping_cost' => $shipping_cost,
            'tax_amount' => $tax,
            'discount_amount' => 0,
            'total_amount' => $total,
            'shipping_first_name' => $request->shipping_first_name,
            'shipping_last_name' => $request->shipping_last_name,
            'shipping_address' => $request->shipping_address,
            'shipping_city' => $request->shipping_city,
            'shipping_state' => $request->shipping_state,
            'shipping_zip' => $request->shipping_zip,
            'shipping_country' => $request->shipping_country,
            'phone' => $request->phone,
            'email' => $request->email,
            'shipping_method' => $request->shipping_method,
            'payment_method' => $request->payment_method,
        ]);

        foreach ($cart_items as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);

            $item->product->decrement('stock', $item->quantity);
        }

        // Clear cart
        $user->cart->items()->delete();

        return redirect("/orders/{$order->id}/confirmation");
    }

    /**
     * Show order confirmation
     */
    public function showConfirmation($orderId)
    {
        $order = Order::with('items.product')->findOrFail($orderId);

        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('orders.confirmation', compact('order'));
    }
}
