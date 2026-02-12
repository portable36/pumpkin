@extends('layouts.app')

@section('title', 'Home - Pumpkin Marketplace')

@section('content')
<div class="hero">
    <div class="container">
        <h1>Welcome to Pumpkin Marketplace</h1>
        <p>Discover thousands of products from trusted vendors</p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="/shop" class="btn">Start Shopping</a>
            <a href="/vendor/register" class="btn btn-outline">Become a Vendor</a>
        </div>
    </div>
</div>

<div class="container">
    <h2 style="margin: 2rem 0 1rem;">Featured Products</h2>
    <div class="grid">
        @forelse($featuredProducts as $product)
            <div class="card">
                <div class="card-image">📦</div>
                <div class="card-body">
                    <h3 class="card-title">{{ $product->name }}</h3>
                    <p class="rating">⭐ {{ number_format($product->rating, 1) }} ({{ $product->reviews_count }} reviews)</p>
                    <p class="card-price">${{ number_format($product->price, 2) }}</p>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="/products/{{ $product->id }}" class="btn" style="flex: 1; text-align: center;">View</a>
                        <form action="/cart/add" method="POST" style="flex: 1;">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn" style="width: 100%;">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p>No featured products available</p>
        @endforelse
    </div>

    <h2 style="margin: 3rem 0 1rem;">Categories</h2>
    <div class="grid">
        @forelse($categories as $category)
            <div class="card">
                <div class="card-image">📂</div>
                <div class="card-body">
                    <h3 class="card-title">{{ $category->name }}</h3>
                    <p>{{ $category->products_count ?? 0 }} products</p>
                    <a href="/shop?category={{ $category->id }}" class="btn" style="width: 100%; text-align: center;">Browse</a>
                </div>
            </div>
        @empty
            <p>No categories available</p>
        @endforelse
    </div>

    <div style="margin: 4rem 0; padding: 2rem; background: #f9f9f9; border-radius: 8px; text-align: center;">
        <h2>Why Choose Pumpkin?</h2>
        <div class="grid" style="margin-top: 2rem;">
            <div>
                <h3>✓ Secure Payments</h3>
                <p>Multiple payment methods for your convenience</p>
            </div>
            <div>
                <h3>✓ Fast Shipping</h3>
                <p>Reliable delivery to your doorstep</p>
            </div>
            <div>
                <h3>✓ Trusted Vendors</h3>
                <p>Verified sellers with excellent ratings</p>
            </div>
            <div>
                <h3>✓ Customer Support</h3>
                <p>24/7 support for all your needs</p>
            </div>
        </div>
    </div>
</div>
@endsection
