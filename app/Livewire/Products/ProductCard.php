<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;

class ProductCard extends Component
{
    public $product;
    public $showAddToCart = true;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function render()
    {
        return view('livewire.products.product-card');
    }
}
