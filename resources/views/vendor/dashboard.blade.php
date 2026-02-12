@extends('layouts.app')

@section('title', 'Vendor Dashboard - Pumpkin')

@section('content')
<div style="display: flex; min-height: calc(100vh - 80px);">
    <!-- Sidebar -->
    <div style="width: 250px; background: #f8f9fa; border-right: 1px solid #ddd; padding: 2rem 0;">
        <div style="padding: 0 1rem 1rem;">
            <h3 style="margin: 0;">Vendor Menu</h3>
        </div>
        <nav style="display: flex; flex-direction: column;">
            <a href="/vendor/dashboard" style="padding: 1rem; border-left: 3px solid transparent; border-left-color: {{ request()->route()->getName() === 'vendor.dashboard' ? '#667eea' : 'transparent' }}; color: {{ request()->route()->getName() === 'vendor.dashboard' ? '#667eea' : '#333' }}; text-decoration: none;">📊 Dashboard</a>
            <a href="/vendor/products" style="padding: 1rem; border-left: 3px solid transparent; border-left-color: {{ Str::startsWith(request()->route()->getName(), 'vendor.products') ? '#667eea' : 'transparent' }}; color: {{ Str::startsWith(request()->route()->getName(), 'vendor.products') ? '#667eea' : '#333' }}; text-decoration: none;">📦 Products</a>
            <a href="/vendor/orders" style="padding: 1rem; border-left: 3px solid transparent; border-left-color: {{ Str::startsWith(request()->route()->getName(), 'vendor.orders') ? '#667eea' : 'transparent' }}; color: {{ Str::startsWith(request()->route()->getName(), 'vendor.orders') ? '#667eea' : '#333' }}; text-decoration: none;">🛒 Orders</a>
            <a href="/vendor/earnings" style="padding: 1rem; border-left: 3px solid transparent; border-left-color: {{ request()->route()->getName() === 'vendor.earnings' ? '#667eea' : 'transparent' }}; color: {{ request()->route()->getName() === 'vendor.earnings' ? '#667eea' : '#333' }}; text-decoration: none;">💰 Earnings</a>
            <a href="/vendor/reviews" style="padding: 1rem; border-left: 3px solid transparent; border-left-color: {{ request()->route()->getName() === 'vendor.reviews' ? '#667eea' : 'transparent' }}; color: {{ request()->route()->getName() === 'vendor.reviews' ? '#667eea' : '#333' }}; text-decoration: none;">⭐ Reviews</a>
            <a href="/vendor/settings" style="padding: 1rem; border-left: 3px solid transparent; border-left-color: {{ request()->route()->getName() === 'vendor.settings' ? '#667eea' : 'transparent' }}; color: {{ request()->route()->getName() === 'vendor.settings' ? '#667eea' : '#333' }}; text-decoration: none;">⚙️ Settings</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div style="flex: 1; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="margin: 0;">Vendor Dashboard</h1>
                <p style="color: #666; margin: 0.5rem 0;">Welcome, {{ auth()->user()->name }}!</p>
            </div>
            <a href="/vendor/products/create" class="btn">+ Add Product</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); margin-bottom: 2rem;">
            <div class="card">
                <div class="card-body">
                    <p style="color: #666; margin: 0;">Total Sales</p>
                    <p style="font-size: 2rem; font-weight: bold; margin: 0.5rem 0;">${{ number_format($totalSales, 2) }}</p>
                    <p style="color: #28a745; font-size: 0.9rem;">This month</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <p style="color: #666; margin: 0;">Total Orders</p>
                    <p style="font-size: 2rem; font-weight: bold; margin: 0.5rem 0;">{{ $totalOrders }}</p>
                    <p style="color: #667eea; font-size: 0.9rem;">Pending: {{ $pendingOrders }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <p style="color: #666; margin: 0;">Active Products</p>
                    <p style="font-size: 2rem; font-weight: bold; margin: 0.5rem 0;">{{ $activeProducts }}</p>
                    <p style="color: #ffc107; font-size: 0.9rem;">Draft: {{ $draftProducts }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <p style="color: #666; margin: 0;">Average Rating</p>
                    <p style="font-size: 2rem; font-weight: bold; margin: 0.5rem 0;">{{ number_format($avgRating, 1) }} ⭐</p>
                    <p style="color: #666; font-size: 0.9rem;">Based on {{ $totalReviews }} reviews</p>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-body">
                <h3>Recent Orders</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #ddd;">
                                <th style="padding: 1rem; text-align: left;">Order ID</th>
                                <th style="padding: 1rem; text-align: left;">Customer</th>
                                <th style="padding: 1rem; text-align: center;">Items</th>
                                <th style="padding: 1rem; text-align: right;">Amount</th>
                                <th style="padding: 1rem; text-align: center;">Status</th>
                                <th style="padding: 1rem; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 1rem;">{{ $order->order_number }}</td>
                                    <td style="padding: 1rem;">{{ $order->user->name }}</td>
                                    <td style="padding: 1rem; text-align: center;">{{ $order->items->count() }}</td>
                                    <td style="padding: 1rem; text-align: right; font-weight: bold;">${{ number_format($order->total_amount, 2) }}</td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <span class="badge {{ $order->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <a href="/vendor/orders/{{ $order->id }}" class="btn btn-small">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 2rem; text-align: center; color: #999;">No orders yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="grid" style="grid-template-columns: 1fr 1fr;">
            <div class="card">
                <div class="card-body">
                    <h3>Top Products</h3>
                    @forelse($topProducts as $product)
                        <div style="padding: 1rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>{{ $product->name }}</strong>
                                <p style="color: #666; font-size: 0.9rem; margin: 0.25rem 0;">Sales: {{ $product->sold_count ?? 0 }}</p>
                            </div>
                            <strong>${{ number_format($product->price, 2) }}</strong>
                        </div>
                    @empty
                        <p style="padding: 1rem; text-align: center; color: #999;">No products yet</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>Sales Trend</h3>
                    <p style="color: #666; text-align: center; padding: 2rem;">
                        📈 Chart placeholder
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-small {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}
</style>
@endsection
