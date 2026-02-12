<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\SearchService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Display products listing
     */
    public function index(Request $request)
    {
        $filters = [
            'category_id' => $request->get('category'),
            'brand_id' => $request->get('brand'),
            'vendor_id' => $request->get('vendor'),
            'min_price' => $request->get('min_price'),
            'max_price' => $request->get('max_price'),
            'in_stock' => $request->boolean('in_stock'),
            'min_rating' => $request->get('min_rating'),
            'sort_by' => $request->get('sort_by', 'latest'),
            'sort_order' => $request->get('sort_order', 'desc'),
        ];

        $products = $request->has('q')
            ? $this->searchService->searchProducts($request->get('q'), $filters)
            : Product::active()->with(['vendor', 'category'])->paginate(20);

        $categories = Category::active()->root()->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Display single product
     */
    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        // Track view
        $this->searchService->trackView($product);

        // Get related products
        $relatedProducts = $this->searchService->getRelatedProducts($product);

        // Get reviews
        $reviews = $product->reviews()
            ->approved()
            ->latest()
            ->paginate(10);

        // Calculate average rating
        $averageRating = $reviews->avg('rating');

        return view('products.show', compact('product', 'relatedProducts', 'reviews', 'averageRating'));
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $filters = [
            'min_price' => $request->get('min_price'),
            'max_price' => $request->get('max_price'),
            'sort_by' => $request->get('sort_by', 'latest'),
        ];

        $products = $this->searchService->searchProducts($request->get('q'), $filters);

        if ($request->wantsJson()) {
            return response()->json($products);
        }

        return view('products.search', compact('products'));
    }

    /**
     * Get auto-suggest results
     */
    public function autoSuggest(Request $request)
    {
        $suggestions = $this->searchService->autoSuggest($request->get('q', ''));
        return response()->json($suggestions);
    }

    /**
     * Get trending products
     */
    public function trending()
    {
        $products = $this->searchService->getTrendingProducts(12);
        return view('products.trending', compact('products'));
    }

    /**
     * Get featured products
     */
    public function featured()
    {
        $products = $this->searchService->getFeaturedProducts(12);
        return view('products.featured', compact('products'));
    }

    /**
     * Submit a product review
     */
    public function submitReview(Request $request, Product $product)
    {
        if (!auth()->check()) {
            return redirect('/login')->with('error', 'Please login to submit a review');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'review' => 'required|string|max:2000',
        ]);

        // Check if user already reviewed this product
        $existingReview = $product->reviews()
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'You have already reviewed this product');
        }

        // Check if user has purchased this product
        $hasPurchased = auth()->user()->orders()
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->where('status', 'completed')
            ->exists();

        $product->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'title' => $request->title,
            'review' => $request->review,
            'approved' => $hasPurchased, // Auto-approve if purchased, otherwise needs admin approval
        ]);

        // Update product rating
        $avgRating = $product->reviews()
            ->where('approved', true)
            ->avg('rating');
        
        $product->update(['rating' => $avgRating ?? 0]);

        return redirect()->back()->with('success', 'Review submitted successfully!');
    }
}
