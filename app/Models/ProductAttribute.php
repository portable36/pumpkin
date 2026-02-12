<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttribute extends Model
{
    protected $table = 'product_attributes';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_filterable',
    ];

    protected $casts = [
        'is_filterable' => 'boolean',
    ];
}
