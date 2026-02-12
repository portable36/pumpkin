<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display user profile
     */
    public function show()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'required|string|max:20',
            'bio' => 'nullable|string|max:500',
            'timezone' => 'required|timezone',
        ]);

        auth()->user()->update($request->only(['name', 'email', 'phone', 'bio', 'timezone']));

        return response()->json(['message' => 'Profile updated successfully']);
    }

    /**
     * Display addresses
     */
    public function addresses()
    {
        $addresses = auth()->user()->addresses()->latest()->get();
        return view('profile.addresses', compact('addresses'));
    }

    /**
     * Create address
     */
    public function createAddress(Request $request)
    {
        $request->validate([
            'type' => 'required|in:shipping,billing,both',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'street_address' => 'required|string',
            'apartment' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'nullable|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
            'is_default' => 'required|boolean',
        ]);

        auth()->user()->addresses()->create($request->all());

        return response()->json(['message' => 'Address added successfully']);
    }

    /**
     * Update address
     */
    public function updateAddress(Request $request, Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'type' => 'required|in:shipping,billing,both',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'street_address' => 'required|string',
            'apartment' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'nullable|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
            'is_default' => 'required|boolean',
        ]);

        $address->update($request->all());

        return response()->json(['message' => 'Address updated successfully']);
    }

    /**
     * Delete address
     */
    public function deleteAddress(Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $address->delete();

        return response()->json(['message' => 'Address deleted successfully']);
    }

    /**
     * Display wishlist
     */
    public function wishlist()
    {
        $wishlist = auth()->user()->wishlist()->paginate(12);
        return view('profile.wishlist', compact('wishlist'));
    }

    /**
     * Add to wishlist
     */
    public function addToWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        auth()->user()->wishlist()->attach($request->product_id);

        return response()->json(['message' => 'Added to wishlist']);
    }

    /**
     * Remove from wishlist
     */
    public function removeFromWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        auth()->user()->wishlist()->detach($request->product_id);

        return response()->json(['message' => 'Removed from wishlist']);
    }

    /**
     * Display reviews
     */
    public function reviews()
    {
        $reviews = auth()->user()->reviews()->latest()->paginate(10);
        return view('profile.reviews', compact('reviews'));
    }

    /**
     * Create review
     */
    public function createReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|between:1,5',
            'title' => 'required|string|max:100',
            'review' => 'required|string|min:20|max:2000',
        ]);

        // Check if user has purchased this product
        $hasPurchased = auth()->user()->orders()
            ->whereHas('items', function ($query) {
                $query->where('product_id', request('product_id'));
            })
            ->exists();

        ProductReview::create([
            'product_id' => $request->product_id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'title' => $request->title,
            'review' => $request->review,
            'is_verified_purchase' => $hasPurchased,
            'is_approved' => false,
        ]);

        return response()->json(['message' => 'Review submitted and awaiting approval']);
    }
}
