<?php

use Illuminate\Support\Facades\Route;
use Modules\Delivery\Http\Controllers\Admin\{
    CarrierController,
    DeliveryController,
    WithdrawalController as AdminWithdrawalController
};
use Modules\Delivery\Http\Controllers\Carrier\{
    DashboardController,
    MediaManagerController,
    OrderController,
    ProfileController,
    RegisterController,
    WithdrawalController
};
use Modules\Delivery\Http\Controllers\DeliveryOrderController;
use Modules\Delivery\Http\Controllers\shared\AjaxRequestController;
use Modules\Delivery\Http\Controllers\vendor\DeliveryOrderController as VendorDeliveryOrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('admin/delivery')->name('admin.delivery.')->middleware(['locale', 'auth', 'permission', 'web'])->group(function () {
    Route::get('settings', [DeliveryController::class, 'settings'])->name('settings');
    Route::post('settings/store', [DeliveryController::class, 'settingStore'])->name('setting_store');

    // Withdrawal
    Route::prefix('withdrawal')->name('withdrawal.')->group(function () {
        Route::get('/', [AdminWithdrawalController::class, 'index'])->name('index');
        Route::get('edit/{id}', [AdminWithdrawalController::class, 'edit'])->name('edit');
        Route::post('update/{id}', [AdminWithdrawalController::class, 'update'])->name('update');
        Route::get('pdf', [AdminWithdrawalController::class, 'pdf'])->name('pdf');
        Route::get('csv', [AdminWithdrawalController::class, 'csv'])->name('csv');
    });

    Route::resource('carrier', CarrierController::class);
    Route::post('password/update/{id}', [DeliveryController::class, 'updatePassword'])->middleware(['checkForDemoMode'])->name('password');

    Route::get('/carrier/assign/view', [DeliveryOrderController::class, 'assignCarrierView'])->name('carrier.assign.view');
});

Route::prefix('vendor/delivery')->name('vendor.delivery.')->middleware(['locale', 'web'])->group(function () {
    Route::get('/carrier/assign/view', [VendorDeliveryOrderController::class, 'assignCarrierView'])->name('carrier.assign.view');
});

/* Shared Routes */
Route::middleware(['locale', 'web'])->get('delivery/ajax/carrier/search/available', [AjaxRequestController::class, 'searchAvailableCarrier'])->name('delivery.ajax.carrier.search.available');
Route::middleware(['locale', 'web'])->post('delivery/ajax/carrier/assign', [AjaxRequestController::class, 'assignCarrier'])->name('delivery.ajax.carrier.assign');

Route::prefix('carrier')->name('carrier.')->middleware(['locale', 'auth', 'permission', 'web', 'checkedDeliveryLicense'])->group(function () {
    Route::get('logout', [ProfileController::class, 'logout'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('status', [DashboardController::class, 'status'])->name('status');
    Route::get('earning', [ProfileController::class, 'earning'])->name('earning');

    // profile
    Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
    Route::post('profile/update/{id}', [ProfileController::class, 'updateProfile'])->name('profile_update');
    Route::post('password/update/{id}', [ProfileController::class, 'updatePassword'])->name('password_update');

    // activity
    Route::get('activity', [ProfileController::class, 'activity'])->name('activity');

    // Withdrawal
    Route::get('withdrawal', [WithdrawalController::class, 'index'])->name('withdrawal');
    Route::match(['get', 'post'], 'withdrawal/setting', [WithdrawalController::class, 'setting'])->name('withdrawal_setting');
    Route::match(['get', 'post'], 'withdraw/money', [WithdrawalController::class, 'withdraw'])->name('withdrawal_money');

    // orders
    Route::prefix('order')->group(function () {
        Route::get('assign', [OrderController::class, 'assign'])->name('assign');
        Route::get('pickup', [OrderController::class, 'pickup'])->name('pickup');
        Route::get('delivered', [OrderController::class, 'delivered'])->name('delivered');
        Route::get('completed', [OrderController::class, 'completed'])->name('completed');
        Route::get('show/{order_id}', [OrderController::class, 'show'])->name('show');
        Route::post('change-status', [OrderController::class, 'changeStatus'])->name('change_status');
        Route::get('print/{order_id}', [OrderController::class, 'print'])->name('order_print');
    });
});

Route::middleware(['locale', 'auth', 'permission', 'web'])->group(function () {
    Route::prefix('carrier')->name('carrier.')->group(function () {
        Route::post('media-manager/files/upload', [MediaManagerController::class, 'upload'])->name('mediaManager.upload');
        Route::post('paginate-data', [MediaManagerController::class, 'paginateData'])->name('mediaManager.paginateData');
        Route::get('sort-files', [MediaManagerController::class, 'sortFiles'])->name('mediaManager.sortFiles');
    });
});

Route::prefix('carrier')->middleware(['web'])->name('carrier.')->group(function () {

    Route::prefix('sign-up')->group(function () {
        Route::get('/', [RegisterController::class, 'showSignUpForm'])->name('sign_up');
        Route::post('store', [RegisterController::class, 'signUp'])->name('sign_up.store');
    });

    Route::get('otp', [RegisterController::class, 'otpForm'])->name('otp');
    Route::get('resend-otp/{email?}', [RegisterController::class, 'resendVerificationCode'])->name('resend_otp');
    Route::get('verify/{token}', [RegisterController::class, 'verification'])->name('verify');
    Route::post('verify/otp', [RegisterController::class, 'otpVerification'])->name('verify_otp');
});
