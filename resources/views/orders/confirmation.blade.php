@extends('layouts.app')

@section('title', 'Order Confirmation - Pumpkin')

@section('content')
<div class="container" style="margin: 2rem 0;">
    <div style="text-align: center; padding: 3rem 2rem;">
        <div style="font-size: 4rem; margin-bottom: 1rem; color: #28a745;">✅</div>
        <h1>Order Confirmed!</h1>
        <p style="color: #666; font-size: 1.1rem; margin: 1rem 0;">Thank you for your purchase</p>
        <p style="color: #666; margin-bottom: 2rem;">Order confirmation has been sent to your email address.</p>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin: 3rem 0;">
        <div>
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <h3>Order Details</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin: 1.5rem 0;">
                        <div>
                            <p style="color: #666; font-size: 0.9rem;">Order Number</p>
                            <p style="font-size: 1.3rem; font-weight: bold;">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p style="color: #666; font-size: 0.9rem;">Order Date</p>
                            <p style="font-size: 1.3rem; font-weight: bold;">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p style="color: #666; font-size: 0.9rem;">Total Amount</p>
                            <p style="font-size: 1.3rem; font-weight: bold; color: #667eea;">${{ number_format($order->total_amount, 2) }}</p>
                        </div>
                        <div>
                            <p style="color: #666; font-size: 0.9rem;">Order Status</p>
                            <p style="font-size: 1.1rem;"><span class="badge badge-success">{{ ucfirst($order->status) }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <h3>Shipping Address</h3>
                    <p>
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}<br>
                        {{ $order->shipping_country }}<br>
                        {{ $order->phone }}
                    </p>
                </div>
            </div>

            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <h3>Items Ordered</h3>
                    <table style="width: 100%;">
                        <thead>
                            <tr style="border-bottom: 2px solid #ddd;">
                                <th style="padding: 0.5rem; text-align: left;">Product</th>
                                <th style="padding: 0.5rem; text-align: center;">Qty</th>
                                <th style="padding: 0.5rem; text-align: right;">Price</th>
                                <th style="padding: 0.5rem; text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 0.5rem;">{{ $item->product->name }}</td>
                                    <td style="padding: 0.5rem; text-align: center;">{{ $item->quantity }}</td>
                                    <td style="padding: 0.5rem; text-align: right;">${{ number_format($item->price, 2) }}</td>
                                    <td style="padding: 0.5rem; text-align: right; font-weight: bold;">${{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            <div class="card" style="position: sticky; top: 100px; margin-bottom: 2rem;">
                <div class="card-body">
                    <h3>Order Summary</h3>
                    <div style="margin: 1.5rem 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>Subtotal:</span>
                            <strong>${{ number_format($order->subtotal_amount, 2) }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>Shipping:</span>
                            <strong>${{ number_format($order->shipping_cost, 2) }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>Tax:</span>
                            <strong>${{ number_format($order->tax_amount, 2) }}</strong>
                        </div>
                        @if($order->discount_amount > 0)
                            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: #28a745;">
                                <span>Discount:</span>
                                <strong>-${{ number_format($order->discount_amount, 2) }}</strong>
                            </div>
                        @endif
                    </div>
                    <hr>
                    <div style="display: flex; justify-content: space-between; font-size: 1.3rem; font-weight: bold;">
                        <span>Total:</span>
                        <span>${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <h3>Next Steps</h3>
                    <ul style="margin: 1rem 0; padding-left: 1.5rem;">
                        <li style="margin-bottom: 0.5rem;">We'll send you a shipping confirmation when your order ships</li>
                        <li style="margin-bottom: 0.5rem;">Track your order in your dashboard</li>
                        <li style="margin-bottom: 0.5rem;">Contact us if you have any questions</li>
                    </ul>
                </div>
            </div>

            <a href="/dashboard" class="btn" style="width: 100%; text-align: center; display: block; padding: 1rem;">View My Orders</a>
            <a href="/shop" class="btn btn-outline" style="width: 100%; text-align: center; display: block; padding: 1rem; margin-top: 0.5rem;">Continue Shopping</a>
        </div>
    </div>
</div>
@endsection
