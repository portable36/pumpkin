<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\SocialAuthController;

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

// Vendor Registration (Public)
Route::get('/vendor/register', [VendorController::class, 'showRegistrationForm'])->name('vendor.register.form')->middleware('guest');
Route::post('/vendor/register', [VendorController::class, 'register'])->name('vendor.register')->middleware('guest');

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

    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
    Route::get('/stats', [AdminSettingsController::class, 'stats'])->name('admin.stats');
    Route::get('/activity-log', [AdminSettingsController::class, 'activityLog'])->name('admin.activity-log');
});

// Customer Dashboard
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.customer.index');
    })->name('dashboard');
    
    Route::get('/dashboard/orders', function () {
        return view('dashboard.customer.orders', [
            'orders' => auth()->user()->orders()->latest()->paginate(10)
        ]);
    })->name('dashboard.orders');
    
    Route::get('/dashboard/wishlist', function () {
        return view('dashboard.customer.wishlist', [
            'wishlists' => auth()->user()->wishlists()->with('product')->get()
        ]);
    })->name('dashboard.wishlist');
    
    Route::get('/dashboard/reviews', function () {
        return view('dashboard.customer.reviews', [
            'reviews' => auth()->user()->reviews()->with('product')->latest()->get()
        ]);
    })->name('dashboard.reviews');
    
    Route::get('/dashboard/settings', function () {
        return view('dashboard.customer.settings');
    })->name('dashboard.settings');
    
    Route::get('/dashboard/addresses', function () {
        return view('dashboard.customer.addresses', [
            'addresses' => auth()->user()->addresses
        ]);
    })->name('dashboard.addresses');
    
    Route::post('/dashboard/settings/update', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:6|confirmed',
        ]);
        
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['phone'])) {
            $user->phone = $validated['phone'];
        }
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        $user->save();
        
        return redirect()->route('dashboard.settings')->with('success', 'Settings updated successfully!');
    })->name('dashboard.settings.update');
    
    // Address Management
    Route::post('/dashboard/addresses/create', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|in:shipping,billing,both',
            'recipient_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);
        
        // If this is set as default, unset other default addresses
        if ($request->is_default) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }
        
        auth()->user()->addresses()->create($validated);
        
        return redirect()->route('dashboard.addresses')->with('success', 'Address added successfully!');
    })->name('dashboard.addresses.create');
    
    Route::post('/dashboard/addresses/{address}/set-default', function (\App\Models\Address $address) {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }
        
        auth()->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        
        return redirect()->route('dashboard.addresses')->with('success', 'Default address updated!');
    })->name('dashboard.addresses.set-default');
    
    Route::delete('/dashboard/addresses/{address}/delete', function (\App\Models\Address $address) {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }
        
        if ($address->is_default) {
            return redirect()->route('dashboard.addresses')->with('error', 'Cannot delete default address. Set another address as default first.');
        }
        
        $address->delete();
        
        return redirect()->route('dashboard.addresses')->with('success', 'Address deleted successfully!');
    })->name('dashboard.addresses.delete');
});

// Search
Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search.index');
Route::get('/search/autocomplete', [\App\Http\Controllers\SearchController::class, 'autocomplete'])->name('search.autocomplete');
Route::get('/search/trending', [\App\Http\Controllers\SearchController::class, 'trending'])->name('search.trending');
Route::get('/search/filters', [\App\Http\Controllers\SearchController::class, 'filters'])->name('search.filters');

// Social auth
Route::get('/auth/redirect/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/callback/{provider}', [SocialAuthController::class, 'callback'])->name('social.callback');

// Payments
Route::middleware('auth')->group(function () {
    Route::post('/payments/initiate', [\App\Http\Controllers\PaymentController::class, 'initiate'])->name('payments.initiate');
    Route::get('/payments/{payment}/process', [\App\Http\Controllers\PaymentController::class, 'process'])->name('payments.process');
    Route::post('/payments/{payment}/verify', [\App\Http\Controllers\PaymentController::class, 'verify'])->name('payments.verify');
    Route::post('/payments/{payment}/refund', [\App\Http\Controllers\PaymentController::class, 'refund'])->name('payments.refund');
});

// Payment Webhooks (public)
Route::post('/webhooks/payment', [\App\Http\Controllers\PaymentController::class, 'webhook'])->name('webhooks.payment')->withoutMiddleware('web');

// Notifications
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'delete'])->name('notifications.delete');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/preferences', [\App\Http\Controllers\NotificationController::class, 'preferences'])->name('notifications.preferences');
    Route::post('/notifications/preferences', [\App\Http\Controllers\NotificationController::class, 'updatePreferences'])->name('notifications.update-preferences');
    Route::post('/notifications/subscribe-push', [\App\Http\Controllers\NotificationController::class, 'subscribeToPush'])->name('notifications.subscribe-push');
});


