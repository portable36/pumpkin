<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Support\Facades\Cache;

/**
 * CacheService
 * Centralized cache management for Hostinger shared hosting optimization
 * 
 * Usage:
 *  - CacheService::getProductsForVendor($vendorId)
 *  - CacheService::invalidateVendorCache($vendorId)
 */
class CacheService
{
    // Cache durations (in seconds)
    const CACHE_1_MINUTE = 60;
    const CACHE_5_MINUTES = 300;
    const CACHE_15_MINUTES = 900;
    const CACHE_1_HOUR = 3600;
    const CACHE_6_HOURS = 21600;
    const CACHE_24_HOURS = 86400;

    /**
     * Get all active categories with product counts
     */
    public static function getCategories()
    {
        return Cache::remember('categories:active', self::CACHE_1_HOUR, function () {
            return Category::where('is_active', true)
                ->with('products_count')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get featured products (fast-moving items)
     */
    public static function getFeaturedProducts($limit = 12)
    {
        return Cache::remember("products:featured:limit_{$limit}", self::CACHE_1_HOUR, function () use ($limit) {
            return Product::where('status', 'active')
                ->where('is_featured', true)
                ->with('vendor:id,name', 'category:id,name')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get products by category with pagination
     */
    public static function getProductsByCategory($categoryId, $page = 1, $perPage = 20)
    {
        $cacheKey = "products:category_{$categoryId}:page_{$page}";
        
        return Cache::remember($cacheKey, self::CACHE_15_MINUTES, function () use ($categoryId, $perPage) {
            return Product::where('category_id', $categoryId)
                ->where('status', 'active')
                ->with(['vendor:id,name', 'images:product_id,url'])
                ->paginate($perPage);
        });
    }

    /**
     * Get vendor profile with stats
     */
    public static function getVendorProfile($vendorId)
    {
        return Cache::remember("vendor:{$vendorId}:profile", self::CACHE_6_HOURS, function () use ($vendorId) {
            return Vendor::with('stats')
                ->findOrFail($vendorId);
        });
    }

    /**
     * Get vendor's active products
     */
    public static function getVendorProducts($vendorId, $limit = 50)
    {
        return Cache::remember("vendor:{$vendorId}:products:limit_{$limit}", self::CACHE_1_HOUR, function () use ($vendorId, $limit) {
            return Product::where('vendor_id', $vendorId)
                ->where('status', 'active')
                ->select(['id', 'name', 'price', 'vendor_id', 'category_id'])
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get product details with all relationships
     */
    public static function getProductDetails($productId)
    {
        return Cache::remember("product:{$productId}:details", self::CACHE_1_HOUR, function () use ($productId) {
            return Product::with([
                'vendor:id,name,description',
                'category:id,name',
                'variants:id,product_id,name,price',
                'images:id,product_id,url,alt_text',
                'reviews' => fn($query) => $query->limit(5)->latest(),
                'attributes'
            ])->findOrFail($productId);
        });
    }

    /**
     * Get best-rated products
     */
    public static function getTopRatedProducts($limit = 10)
    {
        return Cache::remember("products:top_rated:limit_{$limit}", self::CACHE_6_HOURS, function () use ($limit) {
            return Product::where('status', 'active')
                ->withAvg('reviews', 'rating')
                ->orderByDesc('reviews_avg_rating')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get low stock alerts
     */
    public static function getLowStockProducts($threshold = 10)
    {
        return Cache::remember("products:low_stock:threshold_{$threshold}", self::CACHE_5_MINUTES, function () use ($threshold) {
            return Product::whereHas('inventory', function ($query) use ($threshold) {
                $query->where('quantity', '<', $threshold);
            })->get();
        });
    }

    /**
     * Search products (lightweight)
     */
    public static function searchProducts($query, $perPage = 20)
    {
        $cacheKey = "products:search:" . md5($query);
        
        return Cache::remember($cacheKey, self::CACHE_15_MINUTES, function () use ($query, $perPage) {
            return Product::where('status', 'active')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('sku', 'like', "%{$query}%");
                })
                ->with(['vendor:id,name', 'category:id,name'])
                ->paginate($perPage);
        });
    }

    /**
     * ============ CACHE INVALIDATION METHODS ============
     */

    /**
     * Invalidate all product-related caches
     */
    public static function invalidateProductCache($productId)
    {
        Cache::forget("product:{$productId}:details");
        Cache::forget('products:featured:*');
        Cache::forget('products:top_rated:*');
    }

    /**
     * Invalidate vendor-specific caches
     */
    public static function invalidateVendorCache($vendorId)
    {
        Cache::forget("vendor:{$vendorId}:profile");
        Cache::forget("vendor:{$vendorId}:products:*");
        Cache::forget("vendor:{$vendorId}:stats");
    }

    /**
     * Invalidate category caches
     */
    public static function invalidateCategoryCache()
    {
        Cache::forget('categories:active');
        Cache::forget('products:category_*');
    }

    /**
     * Full cache flush (use sparingly)
     */
    public static function flushAll()
    {
        Cache::flush();
    }

    /**
     * Invalidate search cache
     */
    public static function invalidateSearchCache($query = null)
    {
        if ($query) {
            Cache::forget("products:search:" . md5($query));
        } else {
            // Invalidate all search caches
            Cache::flush();
        }
    }

    /**
     * Invalidate inventory-related caches
     */
    public static function invalidateInventoryCache($productId = null)
    {
        if ($productId) {
            self::invalidateProductCache($productId);
        }
        
        Cache::forget('products:low_stock:*');
    }

    /**
     * Get cache statistics (for debugging)
     */
    public static function getCacheStats()
    {
        return [
            'driver' => config('cache.default'),
            'ttl' => [
                '1_minute' => self::CACHE_1_MINUTE,
                '5_minutes' => self::CACHE_5_MINUTES,
                '15_minutes' => self::CACHE_15_MINUTES,
                '1_hour' => self::CACHE_1_HOUR,
                '6_hours' => self::CACHE_6_HOURS,
                '24_hours' => self::CACHE_24_HOURS,
            ]
        ];
    }
}
