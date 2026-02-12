<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Auth;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Auth
Route::get('/login', [AuthController::class, 'loginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/{product}/review', [ProductController::class, 'submitReview'])->name('products.review')->middleware('auth');

// Cart
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'addItem'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'updateItem'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'removeItem'])->name('cart.remove');
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
});

// Orders & Checkout
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [OrderController::class, 'checkoutForm'])->name('checkout.index');
    Route::post('/orders/create', [OrderController::class, 'createFromCheckout'])->name('orders.create');
    Route::get('/orders/{order}/confirmation', [OrderController::class, 'showConfirmation'])->name('orders.confirmation');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/track', [OrderController::class, 'track'])->name('orders.track');
});

// Messages/Chat
Route::middleware('auth')->group(function () {
    Route::get('/messages', function () {
        return view('messages.index', [
            'conversations' => auth()->user()->conversations,
            'activeConversation' => \App\Models\Conversation::find(request()->query('conversation_id')),
        ]);
    })->name('messages.index');
    Route::post('/messages/send', function () {
        // Handle message sending
        return response()->json(['success' => true]);
    })->name('messages.send');
});

// Vendor Routes
Route::middleware(['auth', 'vendor'])->prefix('vendor')->group(function () {
    Route::get('/dashboard', function () {
        return view('vendor.dashboard', [
            'totalSales' => 0,
            'totalOrders' => 0,
            'pendingOrders' => 0,
            'activeProducts' => 0,
            'draftProducts' => 0,
            'avgRating' => 0,
            'totalReviews' => 0,
            'recentOrders' => [],
            'topProducts' => [],
        ]);
    })->name('vendor.dashboard');
    
    Route::get('/products', function () {
        return view('vendor.products.index', [
            'products' => auth()->user()->products()->paginate(15),
        ]);
    })->name('vendor.products.index');

    Route::get('/earnings', function () {
        return view('vendor.earnings', [
            'totalEarnings' => 0,
            'availableBalance' => 0,
            'monthlyEarnings' => 0,
            'pendingPayouts' => 0,
            'processingAmount' => 0,
            'transactions' => [],
        ]);
    })->name('vendor.earnings');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard', [
            'totalRevenue' => 0,
            'totalOrders' => 0,
            'pendingOrders' => 0,
            'totalUsers' => 0,
            'activeUsers' => 0,
            'totalVendors' => 0,
            'pendingVendors' => 0,
            'totalProducts' => 0,
            'lowStockProducts' => 0,
            'avgOrderValue' => 0,
            'recentOrders' => [],
            'pendingVendorApprovals' => [],
            'lowStockProductsList' => [],
        ]);
    })->name('admin.dashboard');
});

// Customer Dashboard
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.customer.index');
    })->name('dashboard');
});


