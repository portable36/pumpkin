<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
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
});
