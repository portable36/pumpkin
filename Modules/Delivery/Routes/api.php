<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;
use Modules\Delivery\Http\Controllers\Api\V1\{
    DeliveryController,
    OrderController,
    WalletController,
    WithdrawalController
};

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


Route::prefix('carrier/v1')->group(function () {

    // auth
    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signUp']);

    Route::prefix('password')->group(function () {
        Route::post('reset-link', [AuthController::class, 'sendResetLinkEmail']);
        Route::post('reset', [AuthController::class, 'setPassword']);
    });

    Route::get('otp-validity/{otp}', [AuthController::class, 'checkOtp']);
    Route::get('verification/{otp}', [AuthController::class, 'verifyEmail']);
    Route::post('resend-otp', [AuthController::class, 'resendUserVerificationCode']);
    Route::get('addon-activity', [UserController::class, 'addonActivity']);
    Route::post('remove-image', [UserController::class, 'removeImage']);

    Route::group(['middleware' => ['auth:api', 'locale', 'checkedDeliveryLicense']], function () {

        Route::get('update_status', [DeliveryController::class, 'updateStatus'])->name('update_status');

        // order
        Route::prefix('order')->as('order.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('delivery.index');
            Route::get('show/{order_id}', [OrderController::class, 'show'])->name('show');
            Route::post('status_update', [OrderController::class, 'orderStatusUpdate'])->name('status_update');
        });

        // User profile
        Route::get('profile', [UserController::class, 'profile']);
        Route::post('profile/update', [UserController::class, 'update']);
        Route::get('logout', [AuthController::class, 'logout']);


        // withdrawal
        Route::prefix('withdrawal')->as('withdrawal.')->group(function () {
            Route::get('/', [WithdrawalController::class, 'index']);
            Route::post('setting/paypal', [WithdrawalController::class, 'paypalSetting'])->name('setting_paypal');
            Route::post('setting/bank', [WithdrawalController::class, 'bankSetting'])->name('setting_bank');
            Route::post('money', [WithdrawalController::class, 'withdraw'])->name('money');
            Route::get('method/{methodId}', [WithdrawalController::class, 'method'])->name('method');
            Route::get('payment_method', [WithdrawalController::class, 'paymentMethod'])->name('payment_method');
        });

        // Wallet
        Route::get('earning', [WalletController::class, 'earning'])->name('earning');
        Route::get('wallet', [WalletController::class, 'wallet'])->name('wallet');
    });
});
