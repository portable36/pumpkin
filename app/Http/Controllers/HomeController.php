<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->limit(8)
            ->get();
        
        $categories = Category::where('is_active', true)
            ->where('parent_id', null)
            ->limit(6)
            ->get();

        return view('home', compact('featuredProducts', 'categories'));
    }

    public function shop(): View
    {
        $products = Product::where('is_active', true)
            ->paginate(12);
        
        $categories = Category::where('is_active', true)
            ->where('parent_id', null)
            ->get();

        return view('shop', compact('products', 'categories'));
    }

    public function about(): View
    {
        return view('about');
    }

    public function contact(): View
    {
        return view('contact');
    }
}

