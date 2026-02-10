<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $featuredProducts = Product::with(['category', 'vendor'])
            ->active()
            ->inStock()
            ->orderBy('views_count', 'desc')
            ->take(8)
            ->get();

        $newArrivals = Product::with(['category', 'vendor'])
            ->active()
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::active()
            ->parent()
            ->ordered()
            ->with(['children' => function ($query) {
                $query->active()->ordered();
            }])
            ->take(6)
            ->get();

        return view('welcome', compact('featuredProducts', 'newArrivals', 'categories'));
    }
}
