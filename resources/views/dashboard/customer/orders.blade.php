@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container">
    <div class="dashboard">
        <div class="sidebar">
            <h3 style="margin-bottom: 1rem;">{{ auth()->user()->name }}</h3>
            <ul class="sidebar-menu">
                <li><a href="/dashboard">Overview</a></li>
                <li><a href="/dashboard/orders" class="active">Orders</a></li>
                <li><a href="/dashboard/wishlist">Wishlist</a></li>
                <li><a href="/dashboard/reviews">Reviews</a></li>
                <li><a href="/dashboard/settings">Settings</a></li>
                <li><a href="/dashboard/addresses">Addresses</a></li>
            </ul>
            <hr style="margin: 1rem 0;">
            <p style="font-size: 0.9rem; color: #666;">
                <strong>Email:</strong> {{ auth()->user()->email }}<br>
                <strong>Phone:</strong> {{ auth()->user()->phone }}<br>
                <strong>Joined:</strong> {{ auth()->user()->created_at->format('M d, Y') }}
            </p>
        </div>

        <div class="main-content">
            <h2>My Orders</h2>
            <p style="color: #666; margin-bottom: 2rem;">View and track all your orders</p>

            @forelse($orders as $order)
                <div style="background: #f9f9f9; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #ff6b35;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; align-items: start;">
                        <div>
                            <p style="font-size: 0.85rem; color: #999; margin-bottom: 0.25rem;">Order Number</p>
                            <strong style="font-size: 1.1rem;">#{{ $order->order_number }}</strong>
                        </div>
                        <div>
                            <p style="font-size: 0.85rem; color: #999; margin-bottom: 0.25rem;">Date</p>
                            <strong>{{ $order->created_at->format('M d, Y') }}</strong>
                            <p style="font-size: 0.85rem; color: #666;">{{ $order->created_at->format('h:i A') }}</p>
                        </div>
                        <div>
                            <p style="font-size: 0.85rem; color: #999; margin-bottom: 0.25rem;">Status</p>
                            <span class="badge 
                                @if($order->status === 'completed') badge-success
                                @elseif($order->status === 'pending') badge-warning
                                @else badge-danger
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 0.85rem; color: #999; margin-bottom: 0.25rem;">Total</p>
                            <strong style="font-size: 1.5rem; color: #ff6b35;">${{ number_format($order->total_amount, 2) }}</strong>
                        </div>
                    </div>

                    @if($order->items && $order->items->count() > 0)
                        <hr style="margin: 1rem 0; border: none; border-top: 1px solid #ddd;">
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            @foreach($order->items as $item)
                                <div style="display: flex; gap: 1rem; align-items: center;">
                                    <div style="width: 60px; height: 60px; background: #e0e0e0; border-radius: 4px; flex-shrink: 0;">
                                        @if($item->product && $item->product->image_url)
                                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                                        @endif
                                    </div>
                                    <div style="flex: 1;">
                                        <strong>{{ $item->product_name }}</strong>
                                        <p style="font-size: 0.9rem; color: #666;">Qty: {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}</p>
                                    </div>
                                    <div style="text-align: right;">
                                        <strong>${{ number_format($item->quantity * $item->unit_price, 2) }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                        <a href="/orders/{{ $order->id }}" class="btn" style="font-size: 0.9rem; padding: 0.5rem 1rem;">View Details</a>
                        @if($order->status !== 'cancelled')
                            <a href="/orders/{{ $order->id }}/track" class="btn-outline" style="font-size: 0.9rem; padding: 0.5rem 1rem; border: 2px solid #ff6b35; color: #ff6b35; text-decoration: none; border-radius: 4px; display: inline-block;">Track Order</a>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 4rem 2rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📦</div>
                    <h3 style="margin-bottom: 1rem;">No Orders Yet</h3>
                    <p style="color: #666; margin-bottom: 2rem;">You haven't placed any orders yet. Start shopping to see your orders here!</p>
                    <a href="/shop" class="btn">Browse Products</a>
                </div>
            @endforelse

            @if($orders->hasPages())
                <div style="margin-top: 2rem;">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
