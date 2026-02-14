<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search products (primary search endpoint)
     */
    public function index(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:100',
            'category' => 'nullable|exists:categories,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'vendor' => 'nullable|exists:vendors,id',
            'sort' => 'nullable|in:latest,price_asc,price_desc,popular,rating',
        ]);

        $query = Product::where('is_active', true);

        // Text search
        if ($request->has('q') && $request->q) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('sku', 'like', "%$search%");
            });
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Price range filter
        if ($request->has('min_price') && $request->min_price) {
            $query->where('selling_price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('selling_price', '<=', $request->max_price);
        }

        // Vendor filter
        if ($request->has('vendor') && $request->vendor) {
            $query->where('vendor_id', $request->vendor);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('selling_price', 'desc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);

        if ($request->wantsJson()) {
            return response()->json($products);
        }

        return view('search.results', compact('products'));
    }

    /**
     * Autocomplete suggestions
     */
    public function autocomplete(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:50',
        ]);

        $suggestions = Product::where('is_active', true)
            ->where('name', 'like', $request->q . '%')
            ->select('id', 'name', 'slug')
            ->distinct()
            ->limit(10)
            ->get();

        return response()->json($suggestions);
    }

    /**
     * Get trending/popular searches
     */
    public function trending()
    {
        // Return top search terms (can be tracked from a searches table)
        $trending = [
            'Electronics',
            'Fashion',
            'Home & Living',
            'Beauty & Personal Care',
            'Sports & Outdoors',
        ];

        return response()->json($trending);
    }

    /**
     * Full-text search (MySQL full-text or Elasticsearch integration)
     */
    public function fullText(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        // MySQL full-text search
        $products = Product::where('is_active', true)
            ->whereRaw("MATCH(name, description) AGAINST(?)", [$request->q])
            ->paginate(12);

        if ($request->wantsJson()) {
            return response()->json($products);
        }

        return view('search.results', compact('products'));
    }

    /**
     * Advanced filters
     */
    public function filters(Request $request)
    {
        $filters = [
            'price_ranges' => [
                ['label' => 'Under $50', 'min' => 0, 'max' => 50],
                ['label' => '$50 - $100', 'min' => 50, 'max' => 100],
                ['label' => '$100 - $200', 'min' => 100, 'max' => 200],
                ['label' => 'Over $200', 'min' => 200, 'max' => 999999],
            ],
            'ratings' => [
                ['label' => '★★★★★ (5 Stars)', 'value' => 5],
                ['label' => '★★★★☆ (4+ Stars)', 'value' => 4],
                ['label' => '★★★☆☆ (3+ Stars)', 'value' => 3],
            ],
            'in_stock' => [
                ['label' => 'In Stock', 'value' => true],
                ['label' => 'Out of Stock', 'value' => false],
            ],
        ];

        return response()->json($filters);
    }

    /**
     * Recent searches for user
     */
    public function recentSearches()
    {
        $searches = session()->get('recent_searches', []);
        return response()->json($searches);
    }

    /**
     * Save search to session
     */
    public function saveSearch(Request $request)
    {
        $q = $request->get('q');
        if ($q) {
            $searches = session()->get('recent_searches', []);
            if (!in_array($q, $searches)) {
                array_unshift($searches, $q);
                session()->put('recent_searches', array_slice($searches, 0, 10));
            }
        }

        return response()->json(['success' => true]);
    }
}
