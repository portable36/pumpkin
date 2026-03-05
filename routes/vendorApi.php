<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Vendor\ApiVendorProductController;

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

Route::group(['prefix' => 'api/vendor', 'middleware' => ['api', 'auth:api', 'locale', 'permission-api']], function () {
    // Product
    Route::get('products', [ApiVendorProductController::class, 'index']);
    Route::post('product/store', [ApiVendorProductController::class, 'store']);
    Route::post('product/update/{id}', [ApiVendorProductController::class, 'update']);
    Route::post('product/search/{type}', [ApiVendorProductController::class, 'search']);
    Route::get('product/view/{id}', [ApiVendorProductController::class, 'detail']);
    Route::post('product/related/update/{type}', [ApiVendorProductController::class, 'updateRelatedProduct']);
    Route::post('product/delete/{id}', [ApiVendorProductController::class, 'destroy']);
});
