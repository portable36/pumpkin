<?php

namespace App\Livewire\Cart;

use App\Models\Product;
use App\Services\Cart\CartService;
use Livewire\Component;

class AddToCart extends Component
{
    public $productId;
    public $quantity = 1;
    public $variantId = null;
    public $maxQuantity = 99;
    public $minQuantity = 1;

    public function mount($productId)
    {
        $this->productId = $productId;
        $product = Product::find($productId);
        if ($product) {
            $this->maxQuantity = min($product->stock, $product->max_order_quantity ?? 99);
            $this->minQuantity = $product->min_order_quantity ?? 1;
        }
    }

    public function increment()
    {
        if ($this->quantity < $this->maxQuantity) {
            $this->quantity++;
        }
    }

    public function decrement()
    {
        if ($this->quantity > $this->minQuantity) {
            $this->quantity--;
        }
    }

    public function addToCart(CartService $cartService)
    {
        $product = Product::findOrFail($this->productId);

        if (!$product->isInStock()) {
            $this->dispatch('notify', ['message' => 'Product is out of stock', 'type' => 'error']);
            return;
        }

        if ($this->quantity > $product->stock) {
            $this->dispatch('notify', ['message' => 'Not enough stock available', 'type' => 'error']);
            return;
        }

        $cartService->addItem($product, $this->quantity, $this->variantId);

        $this->dispatch('cart-updated');
        $this->dispatch('notify', ['message' => 'Product added to cart!', 'type' => 'success']);

        $this->quantity = $this->minQuantity;
    }

    public function render()
    {
        return view('livewire.cart.add-to-cart');
    }
}
