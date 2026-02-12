<?php

namespace App\Console\Commands;

use App\Services\CartService;
use Illuminate\Console\Command;

class MarkAbandonedCarts extends Command
{
    protected $signature = 'carts:mark-abandoned';
    protected $description = 'Mark inactive carts as abandoned';

    public function handle(CartService $cartService): int
    {
        $cartService->markAbandonedIfInactive();
        $this->info('Abandoned carts marked successfully');
        return 0;
    }
}
