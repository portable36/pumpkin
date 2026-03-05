<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Al Mamun <[almamun.techvill@gmail.com]>
 * @contributor Sakawat Hossain Rony <[sakawat.techvill@gmail.com]>
 *
 * @created 07-10-2021
 */

use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\RoleController;
use App\Http\Controllers\Vendor\StaffController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\Vendor\ReviewController;
use App\Http\Controllers\Vendor\VendorOrderController;
use App\Http\Controllers\Vendor\VendorTransactionController;
use App\Http\Controllers\Vendor\CategoryController;
use App\Http\Controllers\Vendor\BrandController;
use App\Http\Controllers\Vendor\AttributeController;
use App\Http\Controllers\Vendor\AttributeGroupController;
use App\Http\Controllers\Vendor\CustomerController;
use App\Http\Controllers\Vendor\CustomerAddressController;
use App\Http\Controllers\Vendor\WithdrawalController;
use App\Http\Controllers\Vendor\ImportController;
use App\Http\Controllers\Vendor\ExportController;
use App\Http\Controllers\Vendor\BarcodeController;
use App\Http\Controllers\Vendor\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Vendor Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'vendor', 'middleware' => ['web', 'auth', 'locale', 'permission']], function () {
    // Vendor Dashboard Routes
    Route::get('dashboard', [DashboardController::class, 'index'])->name('vendor-dashboard');
    Route::post('dashboard/widget', [DashboardController::class, 'setWidgetData']);
    Route::post('dashboard/widget/option', [DashboardController::class, 'setWidgetOption']);
    Route::get('dashboard/widget/forget-cache', [DashboardController::class, 'forgetWidget'])->name('vendor.dashboard.forget_widget');

    // Vendor
    Route::get('profile', [VendorController::class, 'profile'])->name('vendor.profile');
    Route::post('profile-update/{id}', [VendorController::class, 'update'])->name('user.update');
    Route::post('vendor-update/{id}', [VendorController::class, 'updateVendor'])->name('vendor.update');
    Route::post('update-password/{id}', [VendorController::class, 'updatePassword'])->name('vendor.password');
    Route::get('logout', [VendorController::class, 'logout'])->name('vendor.logout');

    // Email/Phone Change OTP
    Route::post('send-change-otp', [VendorController::class, 'sendChangeOtp'])->name('vendor.sendChangeOtp');
    Route::post('verify-change-otp', [VendorController::class, 'verifyChangeOtp'])->name('vendor.verifyChangeOtp');
    Route::post('resend-change-otp', [VendorController::class, 'resendChangeOtp'])->name('vendor.resendChangeOtp');

    // Product
    Route::get('products', [ProductController::class, 'index'])->name('vendor.products');
    Route::get('product/edit/{code}', [ProductController::class, 'edit'])->name('vendor.product.edit');
    Route::match(['get', 'post'], '/product/create', [ProductController::class, 'createProduct'])->name('vendor.product.create');
    Route::match(['get', 'post'], '/product/edit/{code}/action', [ProductController::class, 'editProductAction'])->name('vendor.product.edit-action');
    Route::delete('product/trash/{code}/action', [ProductController::class, 'deleteProduct'])->name('vendor.product.destroy');
    Route::delete('product/delete/{code}/action', [ProductController::class, 'forceDeleteProduct'])->name('vendor.product.force-delete');
    Route::get('/find-products-in-ajax', [ProductController::class, 'findProductAjaxQuery'])->name('vendor.findProductsAjax');
    Route::get('/find-tags-in-ajax', [ProductController::class, 'findTagsAjaxQuery'])->name('vendor.findTagsAjax');
    Route::post('products/search/{type}', [ProductController::class, 'search'])->name('vendor.product.search');

    // duplicate product
    Route::get('product/duplicate/{code}', [ProductController::class, 'duplicate'])->name('vendor.product.duplicate');

    // Review
    Route::get('reviews', [ReviewController::class, 'index'])->name('vendor.reviews');
    Route::post('reviews/edit', [ReviewController::class, 'edit'])->name('vendor.reviewEdit');
    Route::get('reviews/view/{id}', [ReviewController::class, 'view'])->name('vendor.reviewView');
    Route::post('reviews/update', [ReviewController::class, 'update'])->name('vendor.reviewUpdate');
    Route::post('reviews/delete/{id}', [ReviewController::class, 'destroy'])->name('vendor.reviewDestroy');
    Route::get('reviews/pdf', [ReviewController::class, 'pdf'])->name('vendor.reviewPdf');
    Route::get('reviews/csv', [ReviewController::class, 'csv'])->name('vendor.reviewCsv');

    // Order
    Route::get('orders', [VendorOrderController::class, 'index'])->name('vendorOrder.index');
    Route::get('orders/edit/{id}', [VendorOrderController::class, 'edit'])->name('vendorOrder.edit');
    Route::get('orders/view/{id?}', [VendorOrderController::class, 'view'])->name('vendorOrder.view');
    Route::post('orders/change-status', [VendorOrderController::class, 'changeStatus'])->name('vendorOrder.update');
    Route::get('orders/pdf', [VendorOrderController::class, 'pdf'])->name('vendorOrder.pdf');
    Route::get('orders/csv', [VendorOrderController::class, 'csv'])->name('vendorOrder.csv');
    Route::get('invoice/print/{id}', [VendorOrderController::class, 'invoicePrint'])->name('vendorInvoice.print');
    Route::post('store-note', [VendorOrderController::class, 'storeNote']);
    Route::post('order/actions', [VendorOrderController::class, 'orderAction']);
    Route::post('orders/customize', [VendorOrderController::class, 'customize'])->name('vendor.order.customize');
    Route::post('user-address', [VendorOrderController::class, 'userAddress'])->name('vendor.order.user.address');

    // Withdrawal
    Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('vendorWithdrawal.index');
    Route::match(['get', 'post'], 'withdrawal/setting', [WithdrawalController::class, 'setting'])->name('vendorWithdrawal.setting');
    Route::match(['get', 'post'], 'withdraw/money', [WithdrawalController::class, 'withdraw'])->name('vendorWithdrawal.money');
    Route::get('withdrawals/pdf', [WithdrawalController::class, 'pdf'])->name('vendorWithdrawal.pdf');
    Route::get('withdrawals/csv', [WithdrawalController::class, 'csv'])->name('vendorWithdrawal.csv');

    // Transactions
    Route::get('transactions', [VendorTransactionController::class, 'index'])->name('vendorTransaction.index');
    Route::get('transactions/pdf', [VendorTransactionController::class, 'pdf'])->name('vendorTransaction.pdf');
    Route::get('transactions/csv', [VendorTransactionController::class, 'csv'])->name('vendorTransaction.csv');

    // downloadable products
    Route::get('/find-downloadable-products-with-ajax', [ProductController::class, 'findDownloadProducts']);

    // grant access
    Route::post('/grant-access-with-ajax', [VendorOrderController::class, 'grantAccess'])->name('vendor.grantAccess');

    Route::name('vendor.')->group(function () {
        Route::get('/user/{uid}/getinfo', [DashboardController::class, 'getUserData'])->name('users.user-data');
        Route::get('/product/{uid}/getinfo', [DashboardController::class, 'getProductData'])->name('products.product-data');
        Route::get('/get-most-sold-products', [DashboardController::class, 'mostSoldProducts'])->name('dashboard.most-sold-products');
        Route::get('/get-active-users', [DashboardController::class, 'mostActiveUsers'])->name('dashboard.most-active-users');
        Route::get('/vendor-stats', [DashboardController::class, 'vendorStats'])->name('dashboard.vendor-stats');
        Route::get('/sales-of-the-month', [DashboardController::class, 'salesOfTheMonth'])->name('dashboard.sales-of-this-month');

        // Import routes
        Route::match(['GET', 'POST'], '/import/products', [ImportController::class, 'productImport'])->name('epz.import.products');

        // Export products
        Route::match(['GET', 'POST'], '/export/products', [ExportController::class, 'productExport'])->name('epz.export.products');

        // barcode
        Route::match(['get', 'post'], '/barcode/product', [BarcodeController::class, 'product'])->name('barcode.product');
        Route::match(['get', 'post'], '/barcode/product-search', [BarcodeController::class, 'search'])->name('barcode.product.search');
    });

    // activity
    Route::get('/activity', [VendorController::class, 'loginActivity'])->name('vendor.loginActivity');

    Route::name('vendor.')->group(function () {
        Route::get('all-categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('categories/store', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/get-data', [CategoryController::class, 'getData']);
        Route::post('categories/get-parent-data', [CategoryController::class, 'getParentData']);
        Route::post('categories/move-node', [CategoryController::class, 'moveNode']);
        Route::post('categories/edit', [CategoryController::class, 'edit']);
        Route::post('categories/update', [CategoryController::class, 'update'])->name('categories.update');
        Route::post('categories/delete', [CategoryController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('categories.destroy');
        Route::get('categories/suggestion', [CategoryController::class, 'suggestion']);
        Route::post('categories/assign-vendor', [CategoryController::class, 'assignCategory']);
    });


    // Brand
    Route::name('vendor.')->group(function () {
        Route::get('all-brands', [BrandController::class, 'index'])->name('brands.index');
        Route::get('brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::post('brands/store', [BrandController::class, 'store'])->name('brands.store');
        Route::get('brands/edit/{id}', [BrandController::class, 'edit'])->name('brands.edit');
        Route::post('brands/update/{id}', [BrandController::class, 'update'])->name('brands.update');
        Route::post('brands/delete/{id}', [BrandController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('brands.destroy');
        Route::get('brands/pdf', [BrandController::class, 'pdf'])->name('brands.pdf');
        Route::get('brands/csv', [BrandController::class, 'csv'])->name('brands.csv');
        Route::get('brands/suggestion', [BrandController::class, 'suggestion']);
        Route::post('brands/assign-vendor', [BrandController::class, 'assignBrand']);
    });


    // Attribute
    Route::name('vendor.')->group(function () {
        Route::get('all-attributes', [AttributeController::class, 'index'])->name('attribute.index');
        Route::get('attributes/create', [AttributeController::class, 'create'])->name('attribute.create');
        Route::post('attributes/store', [AttributeController::class, 'store'])->name('attribute.store');
        Route::get('attributes/edit/{id}', [AttributeController::class, 'edit'])->name('attribute.edit');
        Route::post('attributes/get-attribute', [AttributeController::class, 'getAttribute']);
        Route::post('attributes/update/{id}', [AttributeController::class, 'update'])->name('attribute.update');
        Route::delete('attributes/delete/{id}', [AttributeController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('attribute.destroy');
        Route::get('attributes/pdf', [AttributeController::class, 'pdf'])->name('attribute.pdf');
        Route::get('attributes/csv', [AttributeController::class, 'csv'])->name('attribute.csv');
        Route::get('attributes/suggestion', [AttributeController::class, 'suggestion']);
        Route::post('attributes/assign-vendor', [AttributeController::class, 'assignAttribute']);
    });

    // Attribute Group
    Route::name('vendor.')->group(function () {
        Route::get('attribute-groups', [AttributeGroupController::class, 'index'])->name('attributeGroup.index');
        Route::get('attribute-groups/create', [AttributeGroupController::class, 'create'])->name('attributeGroup.create');
        Route::post('attribute-groups/store', [AttributeGroupController::class, 'store'])->name('attributeGroup.store');
        Route::get('attribute-groups/edit/{id}', [AttributeGroupController::class, 'edit'])->name('attributeGroup.edit');
        Route::post('attribute-groups/update/{id}', [AttributeGroupController::class, 'update'])->name('attributeGroup.update');
        Route::delete('attribute-groups/delete/{id}', [AttributeGroupController::class, 'destroy'])->name('attributeGroup.destroy');
        Route::get('attribute-groups/pdf', [AttributeGroupController::class, 'pdf'])->name('attributeGroup.pdf');
        Route::get('attribute-groups/csv', [AttributeGroupController::class, 'csv'])->name('attributeGroup.csv');
    });


    // customer
    Route::get('customer', [CustomerController::class, 'index'])->name('vendor.customer');
    Route::get('customer/create', [CustomerController::class, 'create'])->name('vendor.customer.create');
    Route::post('customer/store', [CustomerController::class, 'store'])->name('vendor.customer.store');
    Route::get('customer/edit/{customer}', [CustomerController::class, 'edit'])->name('vendor.customer.edit');
    Route::post('customer/update/{customer}', [CustomerController::class, 'update'])->name('vendor.customer.update');
    Route::delete('customer/delete/{customer}', [CustomerController::class, 'destroy'])->name('vendor.customer.destroy');
    Route::get('find-customer-by-vendor-ajax', [CustomerController::class, 'findCustomerByVendor'])->name('vendor.findCustomerByVendor');

    // customer address
    Route::get('customer/{customer}/addresses', [CustomerAddressController::class, 'index'])->name('vendor.customer.addresses');
    Route::get('customer/{customer}/addresses/create', [CustomerAddressController::class, 'create'])->name('vendor.customer.addresses.create');
    Route::post('customer-addresses/store', [CustomerAddressController::class, 'store'])->name('vendor.customer.addresses.store');
    Route::get('customer-addresses/edit/{address}', [CustomerAddressController::class, 'edit'])->name('vendor.customer.addresses.edit');
    Route::post('customer-addresses/update/{address}', [CustomerAddressController::class, 'update'])->name('vendor.customer.addresses.update');
    Route::delete('customer-addresses/delete/{address}', [CustomerAddressController::class, 'destroy'])->name('vendor.customer.addresses.destroy');

    // Get users by search key
    Route::get('find-users-with-ajax', [CustomerController::class, 'findUser']);

    Route::get('customer/{id}/ledger', [CustomerController::class, 'ledger'])->name('vendor.customer.ledger');
    Route::get('customer/{id}/payment', [CustomerController::class, 'payment'])->name('vendor.customer.payment');
    Route::post('customer/{id}/payment', [CustomerController::class, 'paymentStore'])->name('vendor.customer.paymentStore');

    Route::name('vendor.')->group(function () {
        Route::resource('roles', RoleController::class)->except('show');
        Route::post('role/permissions/{id}', [RoleController::class, 'updatePermissions'])->middleware(['checkForDemoMode'])->name('roles.updatePermissions');
        Route::resource('staffs', StaffController::class)->except('show');

        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::get('notifications/mark-as-read', [NotificationController::class, 'markAsReadAll'])->name('notifications.mark_read_all');
        Route::patch('notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark_read');
        Route::patch('notifications/mark-as-unread/{id}', [NotificationController::class, 'markAsUnread'])->name('notifications.mark_unread');
        Route::get('notifications/ajax-loading', [NotificationController::class, 'headerNotification'])->name('notifications.ajax-loading');
    });

});
