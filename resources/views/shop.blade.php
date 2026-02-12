@extends('layouts.app')

@section('title', 'Shop - Pumpkin')

@section('content')
<div class="container">
    <h2 style="margin: 2rem 0;">Products</h2>
    
    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div style="background: white; padding: 1.5rem; border-radius: 8px; height: fit-content;">
            <h4>Filters</h4>
            <form action="/shop" method="GET">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="">All Categories</option>
                        @forelse($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                <div class="form-group">
                    <label>Price Range</label>
                    <input type="number" name="min_price" placeholder="Min">
                    <input type="number" name="max_price" placeholder="Max">
                </div>
                <button type="submit" class="btn" style="width: 100%;">Apply Filter</button>
            </form>
        </div>

        <div>
            <div class="grid">
                @forelse($products as $product)
                    <div class="card">
                        <div class="card-image">📦</div>
                        <div class="card-body">
                            <h3 class="card-title">{{ $product->name }}</h3>
                            <p class="rating">⭐ {{ number_format($product->rating, 1) }}</p>
                            <p class="card-price">${{ number_format($product->price, 2) }}</p>
                            <span class="badge" style="background: {{ $product->stock > 0 ? '#d4edda' : '#f8d7da' }}; color: {{ $product->stock > 0 ? '#155724' : '#721c24' }};">
                                {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                            </span>
                            <a href="/products/{{ $product->id }}" class="btn" style="width: 100%; text-align: center; margin-top: 1rem;">View</a>
                        </div>
                    </div>
                @empty
                    <p>No products found</p>
                @endforelse
            </div>

            <div style="margin-top: 2rem;">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
