@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="container">
    <div class="dashboard">
        <div class="sidebar">
            <h3 style="margin-bottom: 1rem;">{{ auth()->user()->name }}</h3>
            <ul class="sidebar-menu">
                <li><a href="/dashboard" class="active">Overview</a></li>
                <li><a href="/dashboard/orders">Orders</a></li>
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
            <h2>Welcome Back, {{ auth()->user()->name }}!</h2>

            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin: 2rem 0;">
                <div class="stat-card">
                    <div class="number">{{ auth()->user()->orders->count() }}</div>
                    <div class="label">Total Orders</div>
                </div>
                <div class="stat-card">
                    <div class="number">{{ auth()->user()->reviews->count() }}</div>
                    <div class="label">Reviews</div>
                </div>
                <div class="stat-card">
                    <div class="number">${{ number_format(auth()->user()->orders->sum('total_amount'), 2) }}</div>
                    <div class="label">Total Spent</div>
                </div>
                <div class="stat-card">
                    <div class="number">{{ auth()->user()->addresses->count() }}</div>
                    <div class="label">Addresses</div>
                </div>
            </div>

            <h3 style="margin: 2rem 0 1rem;">Recent Orders</h3>
            @forelse(auth()->user()->orders->take(5) as $order)
                <div style="padding: 1rem; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>Order #{{ $order->order_number }}</strong>
                            <p style="color: #666; font-size: 0.9rem;">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div style="text-align: right;">
                            <span class="badge badge-success">{{ ucfirst($order->status) }}</span>
                            <p style="margin-top: 0.5rem; font-weight: bold;">${{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <p>No orders yet. <a href="/shop">Start shopping</a></p>
            @endforelse
        </div>
    </div>
</div>
@endsection
