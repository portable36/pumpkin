<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStockLow
{
    use Dispatchable, SerializesModels;

    public function __construct(public Product $product) {}

    public function broadcastOn(): Channel
    {
        return new Channel('admin');
    }
}
