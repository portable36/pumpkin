<?php

namespace App\Livewire\Search;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductSearch extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $category = '';

    #[Url]
    public $minPrice = '';

    #[Url]
    public $maxPrice = '';

    #[Url]
    public $sort = 'latest';

    #[Url]
    public $inStock = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function updatedMinPrice()
    {
        $this->resetPage();
    }

    public function updatedMaxPrice()
    {
        $this->resetPage();
    }

    public function updatedSort()
    {
        $this->resetPage();
    }

    public function updatedInStock()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'category', 'minPrice', 'maxPrice', 'sort', 'inStock']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::with(['category', 'vendor', 'reviews'])
            ->active();

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($this->category) {
            $categoryModel = Category::where('slug', $this->category)->first();
            if ($categoryModel) {
                $categoryIds = [$categoryModel->id];
                if ($categoryModel->children->count() > 0) {
                    $categoryIds = array_merge($categoryIds, $categoryModel->children->pluck('id')->toArray());
                }
                $query->whereIn('category_id', $categoryIds);
            }
        }

        if ($this->minPrice !== '') {
            $query->where('selling_price', '>=', $this->minPrice);
        }

        if ($this->maxPrice !== '') {
            $query->where('selling_price', '<=', $this->maxPrice);
        }

        if ($this->inStock) {
            $query->where('stock', '>', 0);
        }

        match ($this->sort) {
            'price_low' => $query->orderBy('selling_price', 'asc'),
            'price_high' => $query->orderBy('selling_price', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            'popular' => $query->orderBy('views_count', 'desc'),
            default => $query->latest(),
        };

        $products = $query->paginate(12);

        $categories = Category::active()
            ->parent()
            ->ordered()
            ->withCount('products')
            ->get();

        return view('livewire.search.product-search', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
