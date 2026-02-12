@extends('layouts.app')

@section('title', 'Admin Dashboard - Pumpkin')

@section('content')
<div style="display: flex; min-height: calc(100vh - 80px);">
    <!-- Sidebar -->
    <div style="width: 250px; background: #f8f9fa; border-right: 1px solid #ddd; padding: 2rem 0;">
        <nav style="display: flex; flex-direction: column;">
            <a href="/admin/dashboard" style="padding: 1rem; border-left: 3px solid #667eea; color: #667eea; text-decoration: none;">📊 Dashboard</a>
            <a href="/admin/users" style="padding: 1rem; color: #333; text-decoration: none;">👥 Users</a>
            <a href="/admin/vendors" style="padding: 1rem; color: #333; text-decoration: none;">🏪 Vendors</a>
            <a href="/admin/products" style="padding: 1rem; color: #333; text-decoration: none;">📦 Products</a>
            <a href="/admin/orders" style="padding: 1rem; color: #333; text-decoration: none;">🛒 Orders</a>
            <a href="/admin/categories" style="padding: 1rem; color: #333; text-decoration: none;">📂 Categories</a>
            <a href="/admin/coupons" style="padding: 1rem; color: #333; text-decoration: none;">🎟️ Coupons</a>
            <a href="/admin/reports" style="padding: 1rem; color: #333; text-decoration: none;">📋 Reports</a>
            <a href="/admin/settings" style="padding: 1rem; color: #333; text-decoration: none;">⚙️ Settings</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div style="flex: 1; padding: 2rem;">
        <h1>Admin Dashboard</h1>
        <p style="color: #666;">Platform statistics and overview</p>

        <!-- Key Metrics -->
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); margin: 2rem 0;">
            <div class="card">
                <div class="card-body">
                    <p style="color: #666; margin: 0;">Total Revenue</p>
                    <p style="font-size: 2rem; font-weight: bold; margin: 0.5rem 0; color: #28a745;">${{ number_format($totalRevenue, 2) }}</p>
                    <p style="color: #28a745; font-size: 0.9rem;">+15% from last month</p>
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
                    <p style="color: #666; margin: 0;">Total Users</p>
                    <p style="font-size: 2rem; font-weight: bold; margin: 0.5rem 0;">{{ $totalUsers }}</p>
                    <p style="color: #667eea; font-size: 0.9rem;">Active: {{ $activeUsers }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <p style="color: #666; margin: 0;">Total Vendors</p>
                    <p style="font-size: 2rem; font-weight: bold; margin: 0.5rem 0;">{{ $totalVendors }}</p>
                    <p style="color: #ffc107; font-size: 0.9rem;">Pending: {{ $pendingVendors }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <p style="color: #666; margin: 0;">Total Products</p>
                    <p style="font-size: 2rem; font-weight: bold; margin: 0.5rem 0;">{{ $totalProducts }}</p>
                    <p style="color: #dc3545; font-size: 0.9rem;">Low Stock: {{ $lowStockProducts }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <p style="color: #666; margin: 0;">Average Order Value</p>
                    <p style="font-size: 2rem; font-weight: bold; margin: 0.5rem 0;">${{ number_format($avgOrderValue, 2) }}</p>
                    <p style="color: #667eea; font-size: 0.9rem;">Per transaction</p>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 2rem; margin: 2rem 0;">
            <div class="card">
                <div class="card-body">
                    <h3>Revenue Trend</h3>
                    <p style="color: #999; text-align: center; padding: 3rem;">📊 Chart visualization</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>Order Status Distribution</h3>
                    <p style="color: #999; text-align: center; padding: 3rem;">📈 Chart visualization</p>
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
                                <th style="padding: 1rem; text-align: left;">Vendor</th>
                                <th style="padding: 1rem; text-align: right;">Amount</th>
                                <th style="padding: 1rem; text-align: center;">Status</th>
                                <th style="padding: 1rem; text-align: center;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 1rem;"><a href="/admin/orders/{{ $order->id }}" style="color: #667eea; text-decoration: none;">{{ $order->order_number }}</a></td>
                                    <td style="padding: 1rem;">{{ $order->user->name }}</td>
                                    <td style="padding: 1rem;">{{ $order->items->first()?->product->vendor->name ?? 'N/A' }}</td>
                                    <td style="padding: 1rem; text-align: right; font-weight: bold;">${{ number_format($order->total_amount, 2) }}</td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <span class="badge badge-{{ $order->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">{{ $order->created_at->format('M d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 2rem; text-align: center; color: #999;">No orders</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="grid" style="grid-template-columns: 1fr 1fr;">
            <div class="card">
                <div class="card-body">
                    <h3>Pending Vendor Approvals</h3>
                    @forelse($pendingVendorApprovals as $vendor)
                        <div style="padding: 1rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                            <strong>{{ $vendor->name }}</strong>
                            <a href="/admin/vendors/{{ $vendor->id }}/approve" class="btn btn-small">Approve</a>
                        </div>
                    @empty
                        <p style="padding: 1rem; text-align: center; color: #999;">No pending approvals</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>Low Stock Products</h3>
                    @forelse($lowStockProductsList as $product)
                        <div style="padding: 1rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>{{ $product->name }}</strong>
                                <p style="color: #dc3545; font-size: 0.9rem; margin: 0;">Stock: {{ $product->stock }}</p>
                            </div>
                            <a href="/admin/products/{{ $product->id }}" class="btn btn-small">View</a>
                        </div>
                    @empty
                        <p style="padding: 1rem; text-align: center; color: #999;">All products in stock</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-small {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
}
</style>
@endsection
