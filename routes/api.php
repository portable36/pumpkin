<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);
Route::post('/request-otp', [AuthController::class, 'requestOTP']);
Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);
Route::post('/social-login', [AuthController::class, 'socialLogin']);

// Public API routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/trending', [ProductController::class, 'trending']);
    Route::get('/featured', [ProductController::class, 'featured']);
    Route::get('/{product}', [ProductController::class, 'show']);
    Route::get('/{product}/related', [ProductController::class, 'related']);
});

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'show']);
        Route::post('/add', [CartController::class, 'addItem']);
        Route::post('/update', [CartController::class, 'updateItem']);
        Route::post('/remove', [CartController::class, 'removeItem']);
        Route::post('/apply-coupon', [CartController::class, 'applyCoupon']);
        Route::post('/clear', [CartController::class, 'clear']);
    });

    // Orders API
    Route::prefix('orders')->group(function () {
        Route::get('/', [\App\Http\Controllers\OrderController::class, 'index']);
        Route::get('/{order}', [\App\Http\Controllers\OrderController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\OrderController::class, 'store']);
        Route::post('/{order}/cancel', [\App\Http\Controllers\OrderController::class, 'cancel']);
    });

    // Payments API - New Gateway System
    Route::prefix('payments')->group(function () {
        Route::post('/initiate', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'initiate']);
        Route::get('/success', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'success'])->name('payments.success');
        Route::get('/fail', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'fail'])->name('payments.fail');
        Route::get('/cancel', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'cancel'])->name('payments.cancel');
    });

    // Legacy Payments API (backward compatibility)
    Route::prefix('payments-legacy')->group(function () {
        Route::post('/initiate', [\App\Http\Controllers\PaymentController::class, 'initiate']);
        Route::post('/{payment}/verify', [\App\Http\Controllers\PaymentController::class, 'verify']);
    });

    // Notifications API
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index']);
        Route::get('/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount']);
        Route::post('/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [\App\Http\Controllers\NotificationController::class, 'delete']);
    });
});

// Shipping gateway webhook endpoints (public)
Route::post('/webhook/shipping/{gateway}', [\App\Http\Controllers\ShippingWebhookController::class, 'handle']);

// Payment gateway webhook endpoints (public - unauthenticated)
Route::post('/webhook/payments/{gateway}', [\App\Http\Controllers\Api\PaymentGatewayController::class, 'webhook'])->name('webhooks.payment');
