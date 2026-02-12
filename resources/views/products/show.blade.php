@extends('layouts.app')

@section('title', 'Product Details - Pumpkin')

@section('content')
<div class="container">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin: 2rem 0;">
        <div>
            <div style="background: #f0f0f0; padding: 2rem; border-radius: 8px; text-align: center; font-size: 4rem;">📦</div>
            @if($product->images->count())
                <div style="margin-top: 1rem; display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem;">
                    @foreach($product->images as $img)
                        <img src="{{ $img->image_url }}" style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px; cursor: pointer;">
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <h1>{{ $product->name }}</h1>
            <p class="rating" style="font-size: 1.2rem; margin: 1rem 0;">⭐ {{ number_format($product->rating, 1) }} ({{ $product->reviews_count }} reviews)</p>
            
            <div style="margin: 2rem 0;">
                <h2 class="card-price">${{ number_format($product->price, 2) }}</h2>
                @if($product->discount_price)
                    <p style="color: #888;"><s>${{ number_format($product->discount_price, 2) }}</s> Save {{ round((1 - $product->price / $product->discount_price) * 100) }}%</p>
                @endif
            </div>

            <p><strong>Stock:</strong> <span class="badge {{ $product->stock > 0 ? 'badge-success' : 'badge-danger' }}">{{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}</span></p>
            <p><strong>SKU:</strong> {{ $product->sku }}</p>
            <p><strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</p>
            <p><strong>Brand:</strong> {{ $product->brand->name ?? 'N/A' }}</p>

            <form action="/cart/add" method="POST" style="margin: 2rem 0;">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="form-group" style="display: flex; gap: 1rem; align-items: end;">
                    <div style="flex: 0.3;">
                        <label>Quantity</label>
                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}">
                    </div>
                    <button type="submit" class="btn" style="flex: 1;">Add to Cart</button>
                </div>
            </form>

            <button class="btn btn-outline" style="width: 100%; padding: 1rem;">❤️ Add to Wishlist</button>

            <div style="margin-top: 2rem; padding: 1.5rem; background: #f9f9f9; border-radius: 8px;">
                <h3>Product Details</h3>
                <p>{{ $product->description }}</p>
            </div>
        </div>
    </div>

    <hr style="margin: 3rem 0;">

    <h2>Related Products</h2>
    <div class="grid">
        @forelse($relatedProducts as $related)
            <div class="card">
                <div class="card-image">📦</div>
                <div class="card-body">
                    <h3 class="card-title">{{ $related->name }}</h3>
                    <p class="card-price">${{ number_format($related->price, 2) }}</p>
                    <a href="/products/{{ $related->id }}" class="btn" style="width: 100%; text-align: center;">View</a>
                </div>
            </div>
        @empty
            <p>No related products</p>
        @endforelse
    </div>

    <hr style="margin: 3rem 0;">

    <h2>Customer Reviews</h2>
    @auth
        <form action="/products/{{ $product->id }}/review" method="POST" style="background: #f9f9f9; padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
            @csrf
            <div class="form-group">
                <label for="rating">Rating</label>
                <select id="rating" name="rating" required>
                    <option value="">Select rating</option>
                    <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                    <option value="4">⭐⭐⭐⭐ Good</option>
                    <option value="3">⭐⭐⭐ Average</option>
                    <option value="2">⭐⭐ Poor</option>
                    <option value="1">⭐ Terrible</option>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="review">Review</label>
                <textarea id="review" name="review" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn">Submit Review</button>
        </form>
    @endauth

    <div>
        @forelse($reviews as $review)
            <div style="padding: 1.5rem; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <strong>{{ $review->user->name }}</strong>
                        <p style="color: #666; font-size: 0.9rem;">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="rating">{{ str_repeat('⭐', $review->rating) }}</span>
                </div>
                <h4>{{ $review->title }}</h4>
                <p>{{ $review->review }}</p>
            </div>
        @empty
            <p>No reviews yet</p>
        @endforelse
    </div>
</div>
@endsection
