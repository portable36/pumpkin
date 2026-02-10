<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'vendor', 'reviews'])
            ->active();

        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->firstOrFail();
            $query->where(function ($q) use ($category) {
                $q->where('category_id', $category->id)
                    ->orWhereIn('category_id', $category->children()->pluck('id'));
            });
        }

        if ($request->filled('vendor')) {
            $query->whereHas('vendor', function ($q) use ($request) {
                $q->where('slug', $request->vendor);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('selling_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('selling_price', '<=', $request->max_price);
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_low' => $query->orderBy('selling_price', 'asc'),
            'price_high' => $query->orderBy('selling_price', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            'popular' => $query->orderBy('views_count', 'desc'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->parent()->ordered()->get();
        $vendors = Vendor::where('approved', true)->get();

        return view('products.index', compact('products', 'categories', 'vendors'));
    }

    public function show($slug)
    {
        $product = Product::with(['category', 'vendor', 'variants', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $product->incrementViews();

        $relatedProducts = Product::with(['category', 'vendor'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->inStock()
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
