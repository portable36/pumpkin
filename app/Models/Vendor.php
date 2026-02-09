<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Vendor extends Model
{
   use Sluggable;

    protected $fillable = [
        'user_id',
        'store_name',
        'approved',
        'approved_at',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'store_name',
            ],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
