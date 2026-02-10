<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->parent()
            ->ordered()
            ->with(['children' => function ($query) {
                $query->active()->ordered();
            }])
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->with(['children' => function ($query) {
                $query->active()->ordered();
            }])
            ->firstOrFail();

        $categoryIds = [$category->id];
        if ($category->children->count() > 0) {
            $categoryIds = array_merge($categoryIds, $category->children->pluck('id')->toArray());
        }

        $products = $category->products()
            ->with(['vendor', 'reviews'])
            ->active()
            ->latest()
            ->paginate(12);

        return view('categories.show', compact('category', 'products'));
    }
}
