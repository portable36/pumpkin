<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, Sluggable, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'vendor_id',
        'category_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'description',
        'cost_price',
        'selling_price',
        'thumbnail',
        'images',
        'videos',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'stock',
        'min_order_quantity',
        'max_order_quantity',
        'is_active',
        'views_count',
        'weight',
        'weight_unit',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'images' => 'array',
        'videos' => 'array',
        'views_count' => 'integer',
        'weight' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->sku) && config('ecommerce.sku.auto_generate', true)) {
                $product->sku = app(\App\Services\SKU\SKUGenerator::class)
                    ->generate($product->category_id, $product->vendor_id, $product->name);
            }
            
            if (empty($product->barcode) && config('ecommerce.barcode.auto_generate', true)) {
                $product->barcode = app(\App\Services\SKU\BarcodeGenerator::class)->generate();
            }
        });
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    public function getTotalReviewsAttribute(): int
    {
        return $this->reviews()->where('is_approved', true)->count();
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function getDiscountPercentageAttribute(): ?float
    {
        if (!$this->cost_price || $this->cost_price == 0) {
            return null;
        }
        
        return (($this->cost_price - $this->selling_price) / $this->cost_price) * 100;
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function hasVariants(): bool
    {
        return $this->variants()->exists();
    }
}
