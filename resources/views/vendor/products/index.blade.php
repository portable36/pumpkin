@extends('layouts.app')

@section('title', 'My Products - Pumpkin Vendor')

@section('content')
<div style="display: flex; min-height: calc(100vh - 80px);">
    <!-- Sidebar -->
    <div style="width: 250px; background: #f8f9fa; border-right: 1px solid #ddd; padding: 2rem 0;">
        <nav style="display: flex; flex-direction: column;">
            <a href="/vendor/dashboard" style="padding: 1rem; color: #333; text-decoration: none;">📊 Dashboard</a>
            <a href="/vendor/products" style="padding: 1rem; border-left: 3px solid #667eea; color: #667eea; text-decoration: none;">📦 Products</a>
            <a href="/vendor/orders" style="padding: 1rem; color: #333; text-decoration: none;">🛒 Orders</a>
            <a href="/vendor/earnings" style="padding: 1rem; color: #333; text-decoration: none;">💰 Earnings</a>
            <a href="/vendor/reviews" style="padding: 1rem; color: #333; text-decoration: none;">⭐ Reviews</a>
            <a href="/vendor/settings" style="padding: 1rem; color: #333; text-decoration: none;">⚙️ Settings</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div style="flex: 1; padding: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>My Products</h1>
            <a href="/vendor/products/create" class="btn">+ Add Product</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Filters -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-body">
                <form action="/vendor/products" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label>Search</label>
                        <input type="text" name="search" placeholder="Product name" value="{{ request()->search }}">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="">All</option>
                            <option value="active" {{ request()->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request()->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="draft" {{ request()->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <select name="stock">
                            <option value="">All</option>
                            <option value="low" {{ request()->stock === 'low' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out" {{ request()->stock === 'out' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                    <div class="form-group" style="display: flex; align-items: end;">
                        <button type="submit" class="btn" style="flex: 1;">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card">
            <div class="card-body">
                @if($products->count() > 0)
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 2px solid #ddd;">
                                    <th style="padding: 1rem; text-align: left;">Product</th>
                                    <th style="padding: 1rem; text-align: center;">SKU</th>
                                    <th style="padding: 1rem; text-align: right;">Price</th>
                                    <th style="padding: 1rem; text-align: center;">Stock</th>
                                    <th style="padding: 1rem; text-align: center;">Rating</th>
                                    <th style="padding: 1rem; text-align: center;">Status</th>
                                    <th style="padding: 1rem; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 1rem;">
                                            <strong>{{ $product->name }}</strong>
                                            <p style="color: #666; font-size: 0.9rem; margin: 0.25rem 0;">{{ Str::limit($product->description, 50) }}</p>
                                        </td>
                                        <td style="padding: 1rem; text-align: center;">{{ $product->sku }}</td>
                                        <td style="padding: 1rem; text-align: right; font-weight: bold;">${{ number_format($product->price, 2) }}</td>
                                        <td style="padding: 1rem; text-align: center;">
                                            <span class="badge {{ $product->stock > 10 ? 'badge-success' : ($product->stock > 0 ? 'badge-warning' : 'badge-danger') }}">
                                                {{ $product->stock }}
                                            </span>
                                        </td>
                                        <td style="padding: 1rem; text-align: center;">⭐ {{ number_format($product->rating, 1) }}</td>
                                        <td style="padding: 1rem; text-align: center;">
                                            <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-secondary' }}">
                                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td style="padding: 1rem; text-align: center;">
                                            <a href="/vendor/products/{{ $product->id }}/edit" class="btn btn-small">Edit</a>
                                            <form action="/vendor/products/{{ $product->id }}/toggle" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-small btn-outline">{{ $product->is_active ? 'Disable' : 'Enable' }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div style="margin-top: 2rem; text-align: center;">
                        {{ $products->links() }}
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem;">
                        <p style="color: #999; font-size: 1.1rem;">No products found</p>
                        <a href="/vendor/products/create" class="btn" style="display: inline-block; margin-top: 1rem;">Create Your First Product</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.btn-small {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
    margin: 0 0.25rem;
}
</style>
@endsection
