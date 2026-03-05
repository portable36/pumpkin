<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\AddressController;
use App\Http\Controllers\Api\User\WishlistController;
use App\Http\Controllers\Api\User\ReviewController;
use App\Http\Controllers\Api\User\OrderController;
use App\Http\Controllers\Api\User\CartController;
use App\Http\Controllers\Api\User\CategoryController;
use App\Http\Controllers\Api\User\BrandController;
use App\Http\Controllers\Api\User\ProductController;
use App\Http\Controllers\Api\User\OrderStatusController;
use App\Http\Controllers\Api\User\RegisteredSellerController;
use App\Http\Controllers\Api\User\SellerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['prefix' => 'api/user', 'middleware' => ['api']], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/registration', [AuthController::class, 'signUp']);

    Route::post('password/reset-link', [AuthController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [AuthController::class, 'setPassword']);
    Route::get('otp-validity/{otp}', [AuthController::class, 'checkOtp']);
    Route::post('resend-verification-code', [AuthController::class, 'resendUserVerificationCode']);

    Route::group(['middleware' => ['auth:api', 'locale', 'permission-api']], function () {
        // User profile
        Route::get('/profile', [UserController::class, 'profile']);
        Route::post('/profile/update', [UserController::class, 'update']);
        Route::get('/logout', [AuthController::class, 'logout']);

        // User address
        Route::get('/addresses', [AddressController::class, 'addresses']);
        Route::post('/address/store', [AddressController::class, 'storeAddress']);
        Route::post('/address/update', [AddressController::class, 'updateAddress']);
        Route::delete('/address/delete/{id}', [AddressController::class, 'destroyAddress']);

        // User setting
        Route::post('/password/update', [UserController::class, 'updatePassword']);
        Route::delete('/account/delete', [UserController::class, 'destroy']);

        // User wishlist
        Route::get('/wishlists', [WishlistController::class, 'index']);
        Route::delete('/wishlist/delete/{id?}', [WishlistController::class, 'destroy']);
        Route::post('wishlist', [WishlistController::class, 'store']);

        // User review
        Route::get('/reviews', [ReviewController::class, 'index']);
        Route::post('/review/store', [ReviewController::class, 'store']);
        Route::post('/review/update/{id}', [ReviewController::class, 'update'])->whereNumber('id');
        Route::post('/review/delete-file', [ReviewController::class, 'deleteFile']);
        Route::post('/review/delete', [ReviewController::class, 'destroy']);

        // User order
        Route::get('orders', [OrderController::class, 'index']);

        // User Wallet
        Route::get('/wallet/{id?}', [UserController::class, 'wallet']);

        // remove user image
        Route::post('/remove-image', [UserController::class, 'removeImage']);

    });

    Route::group(['middleware' => ['locale']], function () {

        // Category
        Route::get('/categories/{param}', [CategoryController::class, 'index']);
        Route::get('/categories/sub-category/{parentId}', [CategoryController::class, 'subCategory']);

        // Product
        Route::get('products', [ProductController::class, 'search'])->name('site.productSearchApi');

        Route::get('/product/{slug}', [ProductController::class, 'productDetails']);

        // related products
        Route::post('/related-products', [ProductController::class, 'relatedProducts']);

        Route::get('/product-categorized/{type}', [ProductController::class, 'categorizedProduct']);

        // Top brand
        Route::get('/brand/{param}', [BrandController::class, 'index']);

        // cart
        Route::get('carts', [CartController::class, 'index']);
        Route::post('cart/store', [CartController::class, 'store']);
        Route::post('cart/reduce-qty', [CartController::class, 'reduceQuantity']);
        Route::post('cart/delete', [CartController::class, 'destroy']);
        Route::post('cart/selected-delete', [CartController::class, 'destroySelected']);
        Route::post('cart/selected-store', [CartController::class, 'storeSelected']);
        Route::post('cart/get-select-data', [CartController::class, 'getSelected']);
        Route::post('cart/all-delete', [CartController::class, 'destroyAll']);
        Route::post('cart/select-shipping', [CartController::class, 'selectShipping']);
        Route::post('cart/add-all', [CartController::class, 'addAll']);

        // check coupon
        Route::post('check-coupon', [CartController::class, 'checkCoupon']);
        Route::post('delete-coupon', [CartController::class, 'deleteCoupon']);

        // Get Stock
        Route::post('get-stock', [CartController::class, 'getStock']);

        // Seller Recommendation
        Route::get('top-seller', [UserController::class, 'topSeller']);

        // Order tracking
        Route::get('track-order/{code}', [OrderController::class, 'trackOrder']);

        // login or register by google or facebook
        Route::post('login/sso', [AuthController::class, 'registerOrLoginUser']);

        // Recent search
        Route::get('/recent-search', [ProductController::class, 'recentSearch']);

        // Product Reviews
        Route::get('reviews/{product_id}', [ReviewController::class, 'productReview']);

        // Get all order statuses

        Route::get('orders/statuses', [OrderStatusController::class, 'index']);

        // get-shipping in product details page
        Route::get('/get-shipping', [ProductController::class, 'getShipping']);

        // addon activity
        Route::get('/addon-activity', [UserController::class, 'addonActivity']);

        // Seller sign-up
        Route::post('seller/sign-up-store', [RegisteredSellerController::class, 'signUp']);
        Route::get('seller/resend-otp/{email?}', [RegisteredSellerController::class, 'resendVerificationCode']);
        Route::post('seller-verify/otp', [RegisteredSellerController::class, 'otpVerification']);

        // Shop
        Route::get('shop/{alias}', [SellerController::class, 'index'])->name('site.shop');
        Route::get('shop/profile/{alias}', [SellerController::class, 'vendorProfile'])->name('site.shop.profile');

        // Order
        Route::post('orders', [OrderController::class, 'store'])->middleware(['checkGuest']);
        Route::get('order-paid', [OrderController::class, 'checkoutPayment'])->middleware(['checkGuest']);
        Route::post('order-get-shipping-tax', [OrderController::class, 'getShippingTax'])->middleware(['checkGuest']);
        Route::get('/orders/{id}', [OrderController::class, 'details'])->whereNumber('id')->middleware(['checkGuest']);

        // Check Out
        Route::get('checkout', [OrderController::class, 'checkOut'])->middleware(['checkGuest'])->name('siteApi.orderpaid');
    });
});
