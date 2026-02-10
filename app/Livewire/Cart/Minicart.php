<?php

namespace App\Livewire\Cart;

use App\Services\Cart\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class Minicart extends Component
{
    public $items = [];
    public $subtotal = 0;
    public $totalItems = 0;

    public function mount(CartService $cartService)
    {
        $this->refreshCart($cartService);
    }

    #[On('cart-updated')]
    public function refreshCart(CartService $cartService)
    {
        $cart = $cartService->getCart();
        if ($cart) {
            $this->items = $cart->items->load('product');
            $this->subtotal = $cart->subtotal;
            $this->totalItems = $cart->total_items;
        }
    }

    public function removeItem($itemId, CartService $cartService)
    {
        $cartService->removeItem($itemId);
        $this->dispatch('cart-updated');
        $this->dispatch('notify', ['message' => 'Item removed from cart', 'type' => 'success']);
    }

    public function updateQuantity($itemId, $quantity, CartService $cartService)
    {
        if ($quantity < 1) {
            return;
        }
        $cartService->updateQuantity($itemId, $quantity);
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.cart.minicart');
    }
}
