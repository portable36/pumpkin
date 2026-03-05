<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\MailTemplateController;
use App\Http\Controllers\Api\PreferenceController;
use App\Http\Controllers\Api\EmailConfigurationController;
use App\Http\Controllers\Api\CompanySettingController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CountryController;

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
Route::group(['prefix' => 'api', 'middleware' => ['api']], function () {
    Route::middleware('auth:api')->get('/user', [UserController::class, 'getUser']);

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('password/reset-link', [AuthController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [AuthController::class, 'setPassword']);
    Route::get('user/verification/{otp}', [AuthController::class, 'verifyEmail']);
    Route::group(['middleware' => ['auth:api', 'locale', 'permission-api']], function () {
        // User
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::get('user/list', [UserController::class, 'index']);
        Route::post('user/store', [UserController::class, 'store']);
        Route::get('user/view/{id}', [UserController::class, 'detail']);
        Route::post('user/update/{id}', [UserController::class, 'update']);
        Route::post('user/update-password/{id}', [UserController::class, 'updatePassword']);
        Route::post('user/delete/{id}', [UserController::class, 'destroy']);

        // Role
        Route::get('role/list', [RoleController::class, 'index']);
        Route::post('role/store', [RoleController::class, 'store']);
        Route::get('role/view/{id}', [RoleController::class, 'detail']);
        Route::post('role/update/{id}', [RoleController::class, 'update']);
        Route::post('role/delete/{id}', [RoleController::class, 'destroy']);

        // Email Template
        Route::get('emailTemplate/list', [MailTemplateController::class, 'index']);
        Route::post('emailTemplate/store', [MailTemplateController::class, 'store']);
        Route::post('emailTemplate/view/{id}', [MailTemplateController::class, 'detail']);
        Route::post('emailTemplate/update/{id}', [MailTemplateController::class, 'update']);
        Route::post('emailTemplate/delete/{id}', [MailTemplateController::class, 'destroy']);

        // Preference
        Route::match(['GET', 'POST'], 'preference', [PreferenceController::class, 'index']);

        // Email Configuration
        Route::match(['GET', 'POST'], 'emailConfiguration', [EmailConfigurationController::class, 'index']);

        // Company Setting
        Route::match(['GET', 'POST'], 'companySetting', [CompanySettingController::class, 'index']);

        // Currency
        Route::get('currency/list', [CurrencyController::class, 'index']);
        Route::post('currency/store', [CurrencyController::class, 'store']);
        Route::post('currency/update/{id}', [CurrencyController::class, 'update']);
        Route::get('currency/view/{id}', [CurrencyController::class, 'detail']);
        Route::delete('currency/delete/{id}', [CurrencyController::class, 'destroy']);

        // Brand
        Route::get('brand/list', [BrandController::class, 'index']);
        Route::post('brand/store', [BrandController::class, 'store']);
        Route::post('brand/update/{id}', [BrandController::class, 'update']);
        Route::get('brand/view/{id}', [BrandController::class, 'detail']);
        Route::post('brand/delete/{id}', [BrandController::class, 'destroy']);

        // Vendor
        Route::get('vendor/list', [VendorController::class, 'index']);
        Route::post('vendor/store', [VendorController::class, 'store']);
        Route::post('vendor/update/{id}', [VendorController::class, 'update']);
        Route::get('vendor/view/{id}', [VendorController::class, 'detail']);
        Route::post('vendor/delete/{id}', [VendorController::class, 'destroy']);

        // Product
        Route::get('products', [ProductController::class, 'index']);
        Route::post('product/search/{type}', [ProductController::class, 'search']);
        Route::get('product/view/{id}', [ProductController::class, 'detail']);
        Route::post('product/delete/{id}', [ProductController::class, 'deleteProduct']);

        // Category
        Route::get('categories', [CategoryController::class, 'index']);
        Route::post('category/store', [CategoryController::class, 'store']);
        Route::post('category/update/{id}', [CategoryController::class, 'update']);
        Route::get('category/view/{id}', [CategoryController::class, 'detail']);
        Route::post('category/delete/{id}', [CategoryController::class, 'destroy']);

        // product update
        Route::post('product/update', [ProductController::class, 'update']);

        // user preference for POS
        Route::post('user/store-meta/{id}', [UserController::class, 'storeMeta']);
        Route::get('user/get-meta/{id}', [UserController::class, 'getMeta']);
    });

    // Country list
    Route::get('country', [CountryController::class, 'index']);

    // Preference for App
    Route::get('preferences', [PreferenceController::class, 'preference']);

    // Vendor specification
    Route::get('vendor/{id}', [VendorController::class, 'detail']);
});
