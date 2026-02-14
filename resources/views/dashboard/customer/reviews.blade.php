@extends('layouts.app')

@section('title', 'My Reviews')

@section('content')
<div class="container">
    <div class="dashboard">
        <div class="sidebar">
            <h3 style="margin-bottom: 1rem;">{{ auth()->user()->name }}</h3>
            <ul class="sidebar-menu">
                <li><a href="/dashboard">Overview</a></li>
                <li><a href="/dashboard/orders">Orders</a></li>
                <li><a href="/dashboard/wishlist">Wishlist</a></li>
                <li><a href="/dashboard/reviews" class="active">Reviews</a></li>
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
            <h2>My Reviews</h2>
            <p style="color: #666; margin-bottom: 2rem;">You have written {{ $reviews->count() }} review(s)</p>

            @forelse($reviews as $review)
                <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; gap: 1.5rem;">
                        <div style="width: 80px; height: 80px; background: #e0e0e0; border-radius: 4px; flex-shrink: 0;">
                            @if($review->product && $review->product->image_url)
                                <img src="{{ $review->product->image_url }}" alt="{{ $review->product->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                            @else
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                    <span style="font-size: 2rem;">📦</span>
                                </div>
                            @endif
                        </div>
                        
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                                <div>
                                    <a href="/products/{{ $review->product_id }}" style="text-decoration: none; color: #333;">
                                        <h3 style="margin-bottom: 0.25rem; font-size: 1.1rem;">{{ $review->product->name ?? 'Product Unavailable' }}</h3>
                                    </a>
                                    <div class="rating" style="margin-bottom: 0.5rem;">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                ⭐
                                            @else
                                                ☆
                                            @endif
                                        @endfor
                                        <span style="color: #666; font-size: 0.9rem; margin-left: 0.5rem;">({{ $review->rating }}/5)</span>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <p style="font-size: 0.85rem; color: #999;">{{ $review->created_at->format('M d, Y') }}</p>
                                    <p style="font-size: 0.85rem; color: #999;">{{ $review->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            
                            @if($review->comment)
                                <div style="background: #f9f9f9; padding: 1rem; border-radius: 4px; margin-bottom: 0.75rem;">
                                    <p style="color: #333; line-height: 1.6;">{{ $review->comment }}</p>
                                </div>
                            @endif
                            
                            @if($review->is_verified_purchase)
                                <span class="badge badge-success" style="font-size: 0.85rem;">✓ Verified Purchase</span>
                            @endif
                            
                            @if($review->helpful_count > 0)
                                <span style="color: #666; font-size: 0.85rem; margin-left: 1rem;">
                                    👍 {{ $review->helpful_count }} {{ Str::plural('person', $review->helpful_count) }} found this helpful
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 4rem 2rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">⭐</div>
                    <h3 style="margin-bottom: 1rem;">No Reviews Yet</h3>
                    <p style="color: #666; margin-bottom: 2rem;">You haven't written any reviews yet. Share your experience with products you've purchased!</p>
                    <a href="/orders" class="btn">View Your Orders</a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
