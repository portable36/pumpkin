<?php

namespace App\Jobs;

use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class MarkAbandonedCarts implements ShouldQueue
{
    use Queueable;

    public function handle(CartService $cartService): void
    {
        $cartService->markAbandonedIfInactive();
    }
}
