<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display category products
     */
    public function show(Category $category)
    {
        if (!$category->is_active) {
            abort(404);
        }

        $products = $category->products()
            ->active()
            ->with(['vendor', 'brand'])
            ->paginate(20);

        $subcategories = $category->children()->active()->get();

        return view('categories.show', compact('category', 'products', 'subcategories'));
    }
}
