<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'vendor_id',
        'category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'barcode',
        'purchase_price',
        'selling_price',
        'discount_price',
        'weight',
        'dimensions',
        'stock_quantity',
        'low_stock_threshold',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views_count',
        'rating',
        'reviews_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'dimensions' => 'json',
        'selling_price' => 'float',
        'purchase_price' => 'float',
        'discount_price' => 'float',
        'weight' => 'float',
        'rating' => 'float',
    ];

    // Relationships
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(ProductAttribute::class, 'product_attributes')
            ->withPivot('value');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(ProductInventory::class);
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_product_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= low_stock_threshold');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->onlyKeepLatest(20);
        $this->addMediaCollection('thumbnail')
            ->singleFile();
    }

    // Mutators
    public function getDiscountPercentageAttribute(): float
    {
        if (!$this->selling_price || $this->selling_price == 0) return 0;
        return round((($this->selling_price - ($this->discount_price ?? $this->selling_price)) / $this->selling_price) * 100, 2);
    }

    public function getFinalPriceAttribute(): float
    {
        return $this->discount_price ?? $this->selling_price;
    }
}
