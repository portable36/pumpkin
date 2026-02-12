<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class SearchService
{
    /**
     * Full-text search products
     */
    public function searchProducts(string $query, array $filters = []): Collection
    {
        $search = Product::active()
            ->whereRaw("MATCH(name, description, short_description) AGAINST(? IN BOOLEAN MODE)", [$query]);

        // Apply filters
        if (isset($filters['category_id'])) {
            $search->where('category_id', $filters['category_id']);
        }

        if (isset($filters['brand_id'])) {
            $search->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['vendor_id'])) {
            $search->where('vendor_id', $filters['vendor_id']);
        }

        if (isset($filters['min_price'])) {
            $search->where('selling_price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $search->where('selling_price', '<=', $filters['max_price']);
        }

        if (isset($filters['in_stock']) && $filters['in_stock']) {
            $search->inStock();
        }

        if (isset($filters['min_rating'])) {
            $search->where('rating', '>=', $filters['min_rating']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        return match ($sortBy) {
            'price' => $search->orderBy('selling_price', $sortOrder),
            'rating' => $search->orderBy('rating', $sortOrder),
            'popularity' => $search->orderBy('views_count', $sortOrder),
            'newest' => $search->latest(),
            default => $search->latest(),
        }->with(['vendor', 'category', 'brand'])
            ->paginate(20);
    }

    /**
     * Auto-suggest search terms
     */
    public function autoSuggest(string $query): array
    {
        return Product::active()
            ->select('name')
            ->where('name', 'LIKE', "%{$query}%")
            ->distinct()
            ->limit(10)
            ->pluck('name')
            ->toArray();
    }

    /**
     * Get trending products
     */
    public function getTrendingProducts(int $limit = 10): Collection
    {
        return Product::active()
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get featured products
     */
    public function getFeaturedProducts(int $limit = 10): Collection
    {
        return Product::active()
            ->featured()
            ->limit($limit)
            ->get();
    }

    /**
     * Track product view
     */
    public function trackView(Product $product): void
    {
        $product->increment('views_count');
    }

    /**
     * Get related products
     */
    public function getRelatedProducts(Product $product, int $limit = 5): Collection
    {
        return Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->orderBy('rating', 'desc')
            ->limit($limit)
            ->get();
    }
}
