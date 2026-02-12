<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'price',
        'cost_price',
        'stock_quantity',
        'attributes',
        'image',
    ];

    protected $casts = [
        'price' => 'float',
        'cost_price' => 'float',
        'attributes' => 'json',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
