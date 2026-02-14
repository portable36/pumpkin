<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Helpers\Settings;

class VendorController extends Controller
{
    /**
     * Show vendor registration form
     */
    public function showRegistrationForm()
    {
        // Respect admin toggle for vendor registration
        $enabled = Settings::get('vendor_registration_enabled', '1');

        if (!in_array($enabled, ['1', 1, true, 'true', 'yes'], true)) {
            abort(404);
        }

        return view('vendor.register');
    }

    /**
     * Register a new vendor
     */
    public function register(Request $request)
    {
        $enabled = Settings::get('vendor_registration_enabled', '1');

        if (!in_array($enabled, ['1', 1, true, 'true', 'yes'], true)) {
            return redirect('/')->withErrors(['vendor' => 'Vendor registration is currently disabled.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'shop_name' => 'required|string|max:255|unique:vendors,shop_name',
            'shop_description' => 'required|string|max:1000',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_vendor' => true,
        ]);

        // Create vendor profile (use owner_id field expected by the Vendor model)
        Vendor::create([
            'owner_id' => $user->id,
            'shop_name' => $request->shop_name,
            'slug' => Str::slug($request->shop_name),
            'description' => $request->shop_description,
            'status' => 'pending', // Requires admin approval
            'commission_rate' => 15,
        ]);

        return redirect('/login')->with('success', 'Vendor registration submitted! Please wait for admin approval.');
    }

    /**
     * Get vendor dashboard data
     */
    public function getDashboardData()
    {
        $user = auth()->user();
        $vendor = $user->vendor;

        if (!$vendor || !$user->is_vendor) {
            abort(403);
        }

        $totalSales = $vendor->products()
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->sum('order_items.price');

        $totalOrders = $vendor->products()
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->count('DISTINCT orders.id');

        $pendingOrders = $vendor->products()
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'pending')
            ->count('DISTINCT orders.id');

        $activeProducts = $vendor->products()->where('is_active', true)->count();
        $draftProducts = $vendor->products()->where('is_active', false)->count();

        $avgRating = $vendor->products()
            ->join('reviews', 'products.id', '=', 'reviews.product_id')
            ->avg('reviews.rating') ?? 0;

        $totalReviews = $vendor->products()
            ->join('reviews', 'products.id', '=', 'reviews.product_id')
            ->count();

        $recentOrders = $vendor->products()
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->with('user')
            ->orderBy('orders.created_at', 'desc')
            ->limit(5)
            ->get();

        $topProducts = $vendor->products()
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->select('products.*')
            ->selectRaw('COUNT(order_items.id) as sold_count')
            ->groupBy('products.id')
            ->orderBy('sold_count', 'desc')
            ->limit(5)
            ->get();

        return compact('totalSales', 'totalOrders', 'pendingOrders', 'activeProducts', 
                      'draftProducts', 'avgRating', 'totalReviews', 'recentOrders', 'topProducts');
    }
}
