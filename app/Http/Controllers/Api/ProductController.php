<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Services\SearchService;
use Illuminate\Http\Request;

class ProductController extends \App\Http\Controllers\Controller
{
    private SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Get products with filters
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
            'sort_by' => $request->get('sort_by', 'latest'),
        ];

        $products = Product::active()
            ->with(['vendor', 'category', 'brand'])
            ->when($filters['category_id'], fn($q) => $q->where('category_id', $filters['category_id']))
            ->when($filters['brand_id'], fn($q) => $q->where('brand_id', $filters['brand_id']))
            ->when($filters['vendor_id'], fn($q) => $q->where('vendor_id', $filters['vendor_id']))
            ->when($filters['min_price'], fn($q) => $q->where('selling_price', '>=', $filters['min_price']))
            ->when($filters['max_price'], fn($q) => $q->where('selling_price', '<=', $filters['max_price']))
            ->when($filters['in_stock'], fn($q) => $q->inStock())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($products);
    }

    /**
     * Get single product
     */
    public function show(Product $product)
    {
        if (!$product->is_active) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $this->searchService->trackView($product);

        $product->load([
            'vendor',
            'category',
            'brand',
            'variants',
            'attributes',
            'reviews' => function ($query) {
                $query->approved()->latest();
            }
        ]);

        return response()->json($product);
    }

    /**
     * Search products
     */
    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2|max:100']);

        $filters = [
            'min_price' => $request->get('min_price'),
            'max_price' => $request->get('max_price'),
            'sort_by' => $request->get('sort_by', 'latest'),
        ];

        $products = $this->searchService->searchProducts($request->get('q'), $filters);

        return response()->json($products);
    }

    /**
     * Get trending products
     */
    public function trending()
    {
        $products = $this->searchService->getTrendingProducts(12);
        return response()->json($products);
    }

    /**
     * Get featured products
     */
    public function featured()
    {
        $products = $this->searchService->getFeaturedProducts(12);
        return response()->json($products);
    }

    /**
     * Get related products
     */
    public function related(Product $product)
    {
        $relatedProducts = $this->searchService->getRelatedProducts($product, 5);
        return response()->json($relatedProducts);
    }
}
