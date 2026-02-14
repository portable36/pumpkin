@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="container">
    <div class="dashboard">
        <div class="sidebar">
            <h3 style="margin-bottom: 1rem;">{{ auth()->user()->name }}</h3>
            <ul class="sidebar-menu">
                <li><a href="/dashboard">Overview</a></li>
                <li><a href="/dashboard/orders">Orders</a></li>
                <li><a href="/dashboard/wishlist" class="active">Wishlist</a></li>
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
            <h2>My Wishlist</h2>
            <p style="color: #666; margin-bottom: 2rem;">{{ $wishlists->count() }} item(s) saved</p>

            @forelse($wishlists as $wishlist)
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div style="display: grid; grid-template-columns: 120px 1fr auto; gap: 1.5rem; padding: 1.5rem;">
                        <div class="card-image" style="width: 120px; height: 120px; margin: 0;">
                            @if($wishlist->product && $wishlist->product->image_url)
                                <img src="{{ $wishlist->product->image_url }}" alt="{{ $wishlist->product->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                            @else
                                <div style="width: 100%; height: 100%; background: #e0e0e0; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                    <span style="font-size: 2rem;">📦</span>
                                </div>
                            @endif
                        </div>
                        
                        <div style="display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <a href="/products/{{ $wishlist->product_id }}" style="text-decoration: none; color: #333;">
                                    <h3 style="margin-bottom: 0.5rem; font-size: 1.2rem;">{{ $wishlist->product->name ?? 'Product Unavailable' }}</h3>
                                </a>
                                @if($wishlist->product)
                                    <p style="color: #666; font-size: 0.9rem; margin-bottom: 0.5rem;">{{ Str::limit($wishlist->product->description, 100) }}</p>
                                    @if($wishlist->product->stock > 0)
                                        <span style="color: #28a745; font-size: 0.9rem;">✓ In Stock ({{ $wishlist->product->stock }} available)</span>
                                    @else
                                        <span style="color: #dc3545; font-size: 0.9rem;">✗ Out of Stock</span>
                                    @endif
                                @endif
                            </div>
                            <p style="font-size: 0.85rem; color: #999; margin-top: 0.5rem;">Added {{ $wishlist->created_at->diffForHumans() }}</p>
                        </div>
                        
                        <div style="display: flex; flex-direction: column; justify-content: space-between; align-items: flex-end; text-align: right;">
                            @if($wishlist->product)
                                <div>
                                    <div class="card-price" style="margin: 0;">${{ number_format($wishlist->product->price, 2) }}</div>
                                    @if($wishlist->product->rating_avg)
                                        <div class="rating" style="margin-top: 0.5rem;">
                                            ⭐ {{ number_format($wishlist->product->rating_avg, 1) }} ({{ $wishlist->product->rating_count }} reviews)
                                        </div>
                                    @endif
                                </div>
                                
                                <div style="display: flex; gap: 0.5rem;">
                                    @if($wishlist->product->stock > 0)
                                        <form action="/cart/add" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $wishlist->product_id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn" style="font-size: 0.9rem; padding: 0.5rem 1rem;">Add to Cart</button>
                                        </form>
                                    @endif
                                    <form action="/wishlist/remove" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $wishlist->product_id }}">
                                        <button type="submit" style="background: none; border: 1px solid #dc3545; color: #dc3545; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer;" onclick="return confirm('Remove from wishlist?')">Remove</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 4rem 2rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">❤️</div>
                    <h3 style="margin-bottom: 1rem;">Your Wishlist is Empty</h3>
                    <p style="color: #666; margin-bottom: 2rem;">Save your favorite products here for easy access later!</p>
                    <a href="/shop" class="btn">Browse Products</a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
