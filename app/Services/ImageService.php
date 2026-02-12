<?php

namespace App\Services;

use App\Models\Product;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageService
{
    /**
     * Process and optimize product image
     */
    public function processProductImage(Product $product, $file, bool $isPrimary = false): Media
    {
        return $product->addMedia($file)
            ->sanitizingFileName(function($fileName) {
                return strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
            })
            ->toMediaCollection('images');
    }

    /**
     * Generate thumbnails for lazy loading
     */
    public function generateThumbnails(Product $product): void
    {
        foreach ($product->getMedia('images') as $media) {
            // Generate placeholder for lazy loading
            // This would integrate with image manipulation library
        }
    }

    /**
     * Convert image to WebP
     */
    public function convertToWebP(Media $media): void
    {
        // This would use an image manipulation library
        // For production, use intervention/image package
    }

    /**
     * Get optimized product image URL
     */
    public function getOptimizedImageUrl(Product $product, string $size = 'featured'): string
    {
        $media = $product->getFirstMedia('images');
        
        if (!$media) {
            return '/images/placeholder.webp';
        }

        // Return CDN-ready URL for cached assets
        return $media->getUrl();
    }

    /**
     * Delete product image
     */
    public function deleteImage(Product $product, $mediaId): bool
    {
        $media = $product->getMedia('images')->find($mediaId);
        
        if (!$media) {
            return false;
        }

        $media->delete();
        return true;
    }
}
