<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Sabbir Al-Razi <[sabbir.techvill@gmail.com]>
 * @contributor Sakawat Hossain Rony <[sakawat.techvill@gmail.com]>
 * @contributor Al Mamun <[almamun.techvill@gmail.com]>
 *
 * @created 20-05-2021
 *
 * @modified 06-09-2021
 */

use App\Http\Controllers\AccountSettingController;
use App\Http\Controllers\AddonsMangerController;
use App\Http\Controllers\AddressSettingController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeGroupController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\CurrencySettingsController;
use App\Http\Controllers\CustomerAddressController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataTableController;
use App\Http\Controllers\EmailConfigurationController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InvoiceSettingController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MailTemplateController;
use App\Http\Controllers\MaintenanceModeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderSettingController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\OtpLogController;
use App\Http\Controllers\PermissionRoleController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSettingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SeederController;
use App\Http\Controllers\SmsConfigurationController;
use App\Http\Controllers\SmsTemplateController;
use App\Http\Controllers\SsoController;
use App\Http\Controllers\SystemInfoController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'admin', 'middleware' => ['web']], function () {
    Route::get('/', [LoginController::class, 'showLoginForm']);
    Route::get('/login', [LoginController::class, 'showLoginForm']);
    Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('login.post');

    // Password reset
    Route::get('password/resets/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/resets', [LoginController::class, 'setPassword'])->name('password.resets');
    Route::post('password/email', [LoginController::class, 'sendResetLinkEmail'])->name('login.sendResetLink');
    Route::post('password/resend-otp', [LoginController::class, 'resendResetOtp'])->middleware('throttle:3,5')->name('password.resendOtp');
    Route::get('password/reset', [LoginController::class, 'reset'])->name('login.reset');
    Route::get('password/reset-otp', [LoginController::class, 'resetOtp'])->name('reset.otp');

    Route::get('/impersonate/{impersonate}', [LoginController::class, 'impersonate'])->name('impersonator');

    Route::get('/cancel-impersonate', [LoginController::class, 'cancelImpersonate'])->name('impersonator-cancel');

    Route::get('/logout', [LoginController::class, 'logout'])->name('users.logout');

    Route::group(['middleware' => ['auth', 'locale', 'permission']], function () {
        Route::get('/clear-cache', [DashboardController::class, 'clearCache'])->name('clear-cache');
        Route::get('load-seed-data', [SeederController::class, 'loadSeedData']);
        Route::get('load-live-seed-data', [SeederController::class, 'loadLiveSeedData']);

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('dashboard/widget', [DashboardController::class, 'setWidgetData']);
        Route::post('dashboard/widget/option', [DashboardController::class, 'setWidgetOption']);
        Route::get('dashboard/widget/forget-cache', [DashboardController::class, 'forgetWidget'])->name('dashboard.forget_widget');

        // Role
        Route::get('role/list', [RoleController::class, 'index'])->name('roles.index');
        Route::get('role/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('role/store', [RoleController::class, 'store'])->middleware(['checkForDemoMode'])->name('roles.store');
        Route::get('role/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');
        Route::post('role/update/{id}', [RoleController::class, 'update'])->middleware(['checkForDemoMode'])->name('roles.update');
        Route::post('role/permissions/{id}', [RoleController::class, 'updatePermissions'])->middleware(['checkForDemoMode'])->name('roles.updatePermissions');
        Route::post('role/delete/{id}', [RoleController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('roles.destroy');

        // Role
        Route::get('generate-permission', [PermissionRoleController::class, 'generatePermission'])->middleware(['checkForDemoMode'])->name('generatePermission.index');

        // User
        Route::get('user/list', [UserController::class, 'index'])->name('users.index');
        Route::get('user/create', [UserController::class, 'create'])->name('users.create');
        Route::get('user/vendor', [UserController::class, 'vendorList'])->name('users.vendor.list');
        Route::get('user/vendor-role', [UserController::class, 'vendorRole'])->name('users.vendor.role');
        Route::post('user/store', [UserController::class, 'store'])->middleware(['checkForDemoMode'])->name('users.store');
        Route::get('user/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::post('user/updatePassword/{id}', [UserController::class, 'updatePassword'])->middleware(['checkForDemoMode'])->name('users.password');
        Route::post('user/update/{id}', [UserController::class, 'update'])->middleware(['checkForDemoMode'])->name('users.update');
        Route::post('user/delete/{id}', [UserController::class, 'destroy'])->name('users.destroy')->middleware(['checkForDemoMode']);
        Route::match(['GET', 'POST'], 'import/users', [UserController::class, 'import'])->name('epz.import.users');
        Route::get('user/pdf', [UserController::class, 'pdf'])->name('users.pdf');
        Route::get('user/csv', [UserController::class, 'csv'])->name('users.csv');
        Route::get('user/wallet/{id}', [UserController::class, 'wallet'])->name('user.wallet');
        Route::get('user/activity/', [UserController::class, 'allUserActivity'])->name('users.activity');
        Route::post('user/activity/delete/{id}', [UserController::class, 'deleteUserActivity'])->name('users.activity.delete');
        Route::get('user/customer', [UserController::class, 'index'])->name('users.customer');
        Route::get('user/{id}/ledger', [UserController::class, 'ledger'])->name('users.ledger');

        // OTP Logs
        Route::get('otp-logs', [OtpLogController::class, 'index'])->name('otp-logs.index');
        Route::get('otp-logs/detail', [OtpLogController::class, 'detail'])->name('otp-logs.detail');
        Route::post('otp-logs/summary/delete', [OtpLogController::class, 'deleteSummary'])->name('otp-logs.summary.delete');
        Route::delete('otp-logs/detail/{id}', [OtpLogController::class, 'deleteDetail'])->name('otp-logs.detail.delete');

        Route::post('user/update-profile/{id}', [UserController::class, 'updateProfile'])->name('users.updateProfile');
        Route::get('profile', [UserController::class, 'profile'])->name('users.profile');
        Route::post('user/update-profile-password/{id}', [UserController::class, 'updateProfilePassword'])->name('users.profilePassword');


        // admin vendor's customer
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('customers/store', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('customers/edit/{customer}', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::post('customers/update/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('customers/destroy/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

        // admin vendor's customer address
        Route::get('customer/{customer}/addresses', [CustomerAddressController::class, 'index'])->name('customer.addresses.index');
        Route::get('customer/{customer}/addresses/create', [CustomerAddressController::class, 'create'])->name('customer.addresses.create');
        Route::post('customer-addresses/store', [CustomerAddressController::class, 'store'])->name('customer.addresses.store');
        Route::get('customer-addresses/edit/{address}', [CustomerAddressController::class, 'edit'])->name('customer.addresses.edit');
        Route::post('customer-addresses/update/{address}', [CustomerAddressController::class, 'update'])->name('customer.addresses.update');
        Route::delete('customer-addresses/destroy/{address}', [CustomerAddressController::class, 'destroy'])->name('customer.addresses.destroy');

        Route::get('customer/{id}/ledger', [CustomerController::class, 'ledger'])->name('customer.ledger');
        Route::get('customer/{id}/payment', [CustomerController::class, 'payment'])->name('customer.payment');
        Route::post('customer/{id}/payment', [CustomerController::class, 'paymentStore'])->name('customer.paymentStore');


        // Product
        Route::get('products', [ProductController::class, 'index'])->name('product.index');
        Route::get('product/edit/{code}', [ProductController::class, 'edit'])->name('product.edit');
        Route::match(['get', 'post'], '/product/create', [ProductController::class, 'createProduct'])->name('product.create');
        Route::match(['get', 'post'], '/product/edit/{code}/action', [ProductController::class, 'editProductAction'])->name('products.edit-action');
        Route::get('products/view/{id}', [ProductController::class, 'view'])->name('product.view');
        Route::delete('product/trash/{code}/action', [ProductController::class, 'deleteProduct'])->middleware(['checkForDemoMode'])->name('product.destroy');
        Route::delete('product/delete/{code}/action', [ProductController::class, 'forceDeleteProduct'])->middleware(['checkForDemoMode'])->name('product.force-delete');
        Route::get('pending/products', [ProductController::class, 'index'])->name('product.pending');

        // duplicate product
        Route::get('product/duplicate/{code}', [ProductController::class, 'duplicate'])->name('product.duplicate');

        // Vendor Admin Routes
        Route::get('vendors', [VendorController::class, 'index'])->name('vendors.index');
        Route::get('vendors/create', [VendorController::class, 'create'])->name('vendors.create');
        Route::post('vendors/store', [VendorController::class, 'store'])->name('vendors.store');
        Route::get('vendors/edit/{id}', [VendorController::class, 'edit'])->name('vendors.edit');
        Route::post('vendors/update/{id}', [VendorController::class, 'update'])->name('vendors.update');
        Route::post('vendors/delete/{id}', [VendorController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('vendors.destroy');
        Route::match(['GET', 'POST'], 'vendors/import', [VendorController::class, 'import'])->name('vendors.import');
        Route::get('vendors/pdf', [VendorController::class, 'pdf'])->name('vendors.pdf');
        Route::get('vendors/csv', [VendorController::class, 'csv'])->name('vendors.csv');

        // Brand
        Route::get('brands', [BrandController::class, 'index'])->name('brands.index');
        Route::get('brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::post('brands/store', [BrandController::class, 'store'])->name('brands.store');
        Route::get('brands/edit/{id}', [BrandController::class, 'edit'])->name('brands.edit');
        Route::post('brands/update/{id}', [BrandController::class, 'update'])->name('brands.update');
        Route::post('brands/delete/{id}', [BrandController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('brands.destroy');
        Route::get('brands/pdf', [BrandController::class, 'pdf'])->name('brands.pdf');
        Route::get('brands/csv', [BrandController::class, 'csv'])->name('brands.csv');

        // Attribute
        Route::get('attributes', [AttributeController::class, 'index'])->name('attribute.index');
        Route::get('attributes/create', [AttributeController::class, 'create'])->name('attribute.create');
        Route::post('attributes/store', [AttributeController::class, 'store'])->name('attribute.store');
        Route::get('attributes/edit/{id}', [AttributeController::class, 'edit'])->name('attribute.edit');
        Route::post('attributes/get-attribute', [AttributeController::class, 'getAttribute']);
        Route::post('attributes/update/{id}', [AttributeController::class, 'update'])->name('attribute.update');
        Route::delete('attributes/delete/{id}', [AttributeController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('attribute.destroy');
        Route::get('attributes/pdf', [AttributeController::class, 'pdf'])->name('attribute.pdf');
        Route::get('attributes/csv', [AttributeController::class, 'csv'])->name('attribute.csv');

        // Attribute Group
        Route::get('attribute-groups', [AttributeGroupController::class, 'index'])->name('attributeGroup.index');
        Route::get('attribute-groups/create', [AttributeGroupController::class, 'create'])->name('attributeGroup.create');
        Route::post('attribute-groups/store', [AttributeGroupController::class, 'store'])->name('attributeGroup.store');
        Route::get('attribute-groups/edit/{id}', [AttributeGroupController::class, 'edit'])->name('attributeGroup.edit');
        Route::post('attribute-groups/update/{id}', [AttributeGroupController::class, 'update'])->name('attributeGroup.update');
        Route::delete('attribute-groups/delete/{id}', [AttributeGroupController::class, 'destroy'])->name('attributeGroup.destroy');
        Route::get('attribute-groups/pdf', [AttributeGroupController::class, 'pdf'])->name('attributeGroup.pdf');
        Route::get('attribute-groups/csv', [AttributeGroupController::class, 'csv'])->name('attributeGroup.csv');

        // Category
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('categories/store', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/get-data', [CategoryController::class, 'getData']);
        Route::post('categories/get-parent-data', [CategoryController::class, 'getParentData']);
        Route::post('categories/move-node', [CategoryController::class, 'moveNode']);
        Route::post('categories/edit', [CategoryController::class, 'edit']);
        Route::post('categories/update', [CategoryController::class, 'update'])->name('categories.update');
        Route::post('categories/delete', [CategoryController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('categories.destroy');

        // Email Template
        Route::get('emailTemplate/list', [MailTemplateController::class, 'index'])->name('emailTemplates.index');
        Route::get('emailTemplate/create', [MailTemplateController::class, 'create'])->name('emailTemplates.create');
        Route::post('emailTemplate/store', [MailTemplateController::class, 'store'])->middleware(['checkForDemoMode'])->name('emailTemplates.store');
        Route::get('emailTemplate/edit/{id}', [MailTemplateController::class, 'edit'])->name('emailTemplates.edit');
        Route::post('emailTemplate/update/{id}', [MailTemplateController::class, 'update'])->middleware(['checkForDemoMode'])->name('emailTemplates.update');
        Route::post('emailTemplate/delete/{id}', [MailTemplateController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('emailTemplates.destroy');

        // Preference
        Route::match(['GET', 'POST'], 'preference', [PreferenceController::class, 'index'])->name('preferences.index');
        Route::match(['GET', 'POST'], 'preference/password', [PreferenceController::class, 'password'])->name('preferences.password');

        // Email Configuration
        Route::match(['GET', 'POST'], 'email-setting', [EmailConfigurationController::class, 'index'])->name('emailConfigurations.index');

        // Company Settings
        Route::match(['GET', 'POST'], 'general-setting', [CompanySettingController::class, 'index'])->name('companyDetails.setting');

        Route::get('system-info', [SystemInfoController::class, 'index'])->name('systemInfo.index');

        // Language
        Route::get('languages/translation/{id}', [LanguageController::class, 'translation'])->name('language.translation');
        Route::get('languages', [LanguageController::class, 'index'])->name('language.index');
        Route::post('languages/save', [LanguageController::class, 'store'])->middleware(['checkForDemoMode'])->name('language.store');
        Route::post('languages/edit', [LanguageController::class, 'edit']);
        Route::post('languages/update', [LanguageController::class, 'update'])->middleware(['checkForDemoMode'])->name('language.update');
        Route::post('languages/delete/{id}', [LanguageController::class, 'delete'])->middleware(['checkForDemoMode'])->name('language.delete');
        Route::post('languages/translation/save', [LanguageController::class, 'translationStore'])->middleware(['checkForDemoMode'])->name('language.translationSave');
        Route::match(['get', 'post'], 'languages/{id}/import', [LanguageController::class, 'import'])->middleware(['checkForDemoMode'])->name('language.import');

        // Currency
        Route::get('currencies', [CurrencyController::class, 'index'])->name('currency.index');
        Route::post('currencies/save', [CurrencyController::class, 'store'])->name('currency.store');
        Route::post('currencies/edit', [CurrencyController::class, 'edit'])->name('currency.edit');
        Route::post('currencies/update', [CurrencyController::class, 'update'])->name('currency.update');
        Route::post('currencies/delete/{id}', [CurrencyController::class, 'destroy'])->name('currency.delete');
        Route::get('currencies/valid', [CurrencyController::class, 'validCurrencyName']);

        // Review
        Route::get('reviews', [ReviewController::class, 'index'])->name('review.index');
        Route::post('reviews/edit', [ReviewController::class, 'edit'])->name('review.edit');
        Route::get('reviews/view/{id}', [ReviewController::class, 'view'])->name('review.view');
        Route::post('reviews/update', [ReviewController::class, 'update'])->name('review.update');
        Route::post('reviews/delete/{id}', [ReviewController::class, 'destroy'])->name('review.destroy');
        Route::get('reviews/pdf', [ReviewController::class, 'pdf'])->name('review.pdf');
        Route::get('reviews/csv', [ReviewController::class, 'csv'])->name('review.csv');

        // SSO Service
        Route::match(['GET', 'POST'], 'sso-service', [SsoController::class, 'index'])->name('sso.index');

        // Maintenance mode
        Route::match(['GET', 'POST'], 'maintenance-mode', [MaintenanceModeController::class, 'enable'])->name('maintenance.enable');

        // Order status
        Route::get('order-statuses', [OrderStatusController::class, 'index'])->name('orderStatues.index');
        Route::post('order-statuses/save', [OrderStatusController::class, 'store'])->middleware(['checkForDemoMode'])->name('orderStatues.store');
        Route::post('order-statuses/edit', [OrderStatusController::class, 'edit']);
        Route::post('order-statuses/update', [OrderStatusController::class, 'update'])->middleware(['checkForDemoMode'])->name('orderStatues.update');
        Route::post('order-statuses/delete/{id}', [OrderStatusController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('orderStatues.delete');

        // Order
        Route::get('orders', [AdminOrderController::class, 'index'])->name('order.index');
        Route::get('orders/edit/{id?}', [AdminOrderController::class, 'edit'])->name('order.edit');
        Route::get('orders/view/{id?}', [AdminOrderController::class, 'view'])->name('order.view');
        Route::post('orders/change-status', [AdminOrderController::class, 'changeStatus'])->name('order.changeStatus');
        Route::post('orders/update', [AdminOrderController::class, 'update'])->name('order.update');
        Route::delete('orders/delete/{id}', [AdminOrderController::class, 'destroy'])->middleware(['checkForDemoMode'])->name('order.destroy');
        Route::get('orders/pdf', [AdminOrderController::class, 'pdf'])->name('order.pdf');
        Route::get('orders/csv', [AdminOrderController::class, 'csv'])->name('order.csv');
        Route::get('invoice/print/{id}', [AdminOrderController::class, 'invoicePrint'])->name('invoice.print');

        Route::post('orders/customize', [AdminOrderController::class, 'customize'])->name('order.customize');

        Route::post('orders/partial-payment/{id}', [AdminOrderController::class, 'partialPayment'])->name('order.partialPayment');

        Route::get('invoice/tax-shipping', [AdminOrderController::class, 'invoiceTaxShipping'])->name('invoice.addToCart');
        Route::post('invoice/save', [AdminOrderController::class, 'invoiceSave'])->name('invoice.save');

        // Transaction
        Route::get('transactions', [TransactionController::class, 'index'])->name('transaction.index');
        Route::get('transaction/edit/{id}', [TransactionController::class, 'edit'])->name('transaction.edit');
        Route::post('transaction/update/{id}', [TransactionController::class, 'update'])->name('transaction.update');
        Route::get('transactions/pdf', [TransactionController::class, 'pdf'])->name('transaction.pdf');
        Route::get('transactions/csv', [TransactionController::class, 'csv'])->name('transaction.csv');

        // Withdrawal
        Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('withdrawal.index');
        Route::get('withdrawal/edit/{id}', [WithdrawalController::class, 'edit'])->name('withdrawal.edit');
        Route::post('withdrawal/update/{id}', [WithdrawalController::class, 'update'])->name('withdrawal.update');
        Route::get('withdrawals/pdf', [WithdrawalController::class, 'pdf'])->name('withdrawal.pdf');
        Route::get('withdrawals/csv', [WithdrawalController::class, 'csv'])->name('withdrawal.csv');

        // Addons Manager
        Route::get('addons', [AddonsMangerController::class, 'index'])->name('addon.index');

        // Dashboard route
        Route::get('/user/{uid}/getinfo/{type}', [DashboardController::class, 'getUserData'])->name('users.user-data');
        Route::get('/product/{uid}/getinfo', [DashboardController::class, 'getProductData'])->name('products.product-data');
        Route::get('/get-most-sold-products', [DashboardController::class, 'mostSoldProducts'])->name('dashboard.most-sold-products');
        Route::get('/get-active-users', [DashboardController::class, 'mostActiveUsers'])->name('dashboard.most-active-users');
        Route::get('/vendor-stats', [DashboardController::class, 'vendorStats'])->name('dashboard.vendor-stats');
        Route::get('/vendor-stats/{type}', [DashboardController::class, 'vendorStatsType'])->name('dashboard.vendor-stats-type');
        Route::get('/vendor-req', [DashboardController::class, 'vendorReq'])->name('dashboard.vendor-req');
        Route::get('/sales-of-the-month', [DashboardController::class, 'salesOfTheMonth'])->name('dashboard.sales-of-this-month');
        Route::get('user/change-status/{status}/{id}', [DashboardController::class, 'changeStatus'])->name('dashboard.changeStatus');

        // Email
        Route::get('verify-email-setting', [EmailController::class, 'emailVerifySetting'])->name('emailVerifySetting');
        Route::post('verify-email-setting', [EmailController::class, 'emailVerifySetting'])->middleware(['checkForDemoMode'])->name('emailVerifySetting');

        // Product Setting
        Route::match(['GET', 'POST'], 'product-setting', [ProductSettingController::class, 'general'])->name('product.setting.general');
        Route::post('product-setting/inventory', [ProductSettingController::class, 'inventory'])->name('product.setting.inventory');
        Route::post('product-setting/vendor', [ProductSettingController::class, 'vendor'])->name('product.setting.vendor');
        Route::post('product-setting/filter', [ProductSettingController::class, 'filter'])->name('product.setting.filter');

        // Order Setting
        Route::match(['GET', 'POST'], 'order-setting', [OrderSettingController::class, 'index'])->name('order.setting.option');

        // Invoice Setting
        Route::match(['GET', 'POST'], 'invoice-setting', [InvoiceSettingController::class, 'index'])->name('invoice.setting.option');

        // Account Setting
        Route::match(['GET', 'POST'], 'account-setting', [AccountSettingController::class, 'index'])->name('account.setting.option');
        Route::match(['GET', 'POST'], 'account-setting/email-phone-change', [AccountSettingController::class, 'emailPhoneChange'])->name('account.setting.emailPhoneChange');
        // downloadable products
        Route::get('/find-downloadable-products-with-ajax', [ProductController::class, 'findDownloadProducts'])->name('findDownloadProducts');

        // grant access
        Route::post('/grant-access-with-ajax', [AdminOrderController::class, 'grantAccess'])->name('grantAccess');

        /**
         * Test routes
         */
        Route::get('user/verify/{token}', [UserController::class, 'verification'])->name('users.verify');

        // Get users by search key
        Route::get('find-users-with-ajax', [UserController::class, 'findUser'])->name('find.users.ajax');

        // Get vendors by search key
        Route::get('find-vendors-with-ajax', [VendorController::class, 'findVendor'])->name('find.vendors.ajax');

        // Find brands by search key
        Route::get('find-brands-with-ajax', [BrandController::class, 'findBrand'])->name('find.brands.ajax');

        // Find categories by search key
        Route::get('find-categories-with-ajax', [CategoryController::class, 'findCategory'])->name('find.categories.ajax');

        // Import/Exports
        Route::get('/imports', [ImportController::class, 'index'])->name('epz.imports');
        Route::match(['GET', 'POST'], '/import/products', [ImportController::class, 'productImport'])->name('epz.import.products');

        // Export
        Route::get('/exports', [ExportController::class, 'index'])->name('epz.exports');
        Route::match(['GET', 'POST'], '/export/products', [ExportController::class, 'productExport'])->name('epz.export.products');

        Route::get('/find-currency-in-ajax', [CurrencyController::class, 'findCurrencyAjaxQuery'])->name('findCurrencyAjax');

        Route::post('/batch/delete', [BatchController::class, 'destroy'])->name('admin.batch_delete');

        Route::get('sms/configs', [SmsConfigurationController::class, 'index'])->name('sms.config.index');

        Route::get('sms/configs/twilio', [SmsConfigurationController::class, 'twilio'])->name('sms.config.twilio.index');
        Route::post('sms/configs/twilio', [SmsConfigurationController::class, 'storeTwilio'])->name('sms.config.twilio.update');

        Route::get('sms/configs/nexmo', [SmsConfigurationController::class, 'nexmo'])->name('sms.config.nexmo.index');
        Route::post('sms/configs/nexmo', [SmsConfigurationController::class, 'storeNexmo'])->name('sms.config.nexmo.update');

        Route::get('sms/configs/fast2sms', [SmsConfigurationController::class, 'fast2Sms'])->name('sms.config.fast2sms.index');
        Route::post('sms/configs/fast2sms', [SmsConfigurationController::class, 'storeFast2Sms'])->name('sms.config.fast2sms.update');

        Route::get('sms/configs/sslwireless', [SmsConfigurationController::class, 'sslWireless'])->name('sms.config.sslwireless.index');
        Route::post('sms/configs/sslwireless', [SmsConfigurationController::class, 'storeSslWireless'])->name('sms.config.sslwireless.update');

        Route::get('sms/configs/mim-sms', [SmsConfigurationController::class, 'mimSms'])->name('sms.config.mim_sms.index');
        Route::post('sms/configs/mim-sms', [SmsConfigurationController::class, 'storeMimSms'])->name('sms.config.mim_sms.update');

        Route::get('sms/configs/msegat', [SmsConfigurationController::class, 'msegat'])->name('sms.config.msegat.index');
        Route::post('sms/configs/msegat', [SmsConfigurationController::class, 'storeMsegat'])->name('sms.config.msegat.update');

        Route::get('sms/configs/sparrow', [SmsConfigurationController::class, 'sparrow'])->name('sms.config.sparrow.index');
        Route::post('sms/configs/sparrow', [SmsConfigurationController::class, 'storeSparrow'])->name('sms.config.sparrow.update');

        Route::get('sms/configs/zender', [SmsConfigurationController::class, 'zender'])->name('sms.config.zender.index');
        Route::post('sms/configs/zender', [SmsConfigurationController::class, 'storeZender'])->name('sms.config.zender.update');

        Route::get('sms/templates', [SmsTemplateController::class, 'index'])->name('sms.template.index');
        Route::get('sms/templates/{slug}', [SmsTemplateController::class, 'edit'])->name('sms.template.edit');
        Route::put('sms/templates/{id}', [SmsTemplateController::class, 'update'])->name('sms.template.update');

        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::get('notifications/mark-as-read', [NotificationController::class, 'markAsReadAll'])->name('notifications.mark_read_all');
        Route::patch('notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark_read');
        Route::patch('notifications/mark-as-unread/{id}', [NotificationController::class, 'markAsUnread'])->name('notifications.mark_unread');
        Route::get('notifications/ajax-loading', [NotificationController::class, 'headerNotification'])->name('notifications.ajax-loading');
        Route::get('notifications/view/{id}', [NotificationController::class, 'view'])->name('notifications.view');

        Route::get('notifications/log', [NotificationController::class, 'log'])->name('notifications.log');
        Route::delete('notifications/log/{id}', [NotificationController::class, 'destroyLog'])->name('notifications.log.destroy');

        Route::get('notifications/setting', [NotificationController::class, 'setting'])->name('notifications.setting');
        Route::post('notifications/setting', [NotificationController::class, 'updateSetting'])->name('notifications.setting.update');

        // barcode
        Route::match(['get', 'post'], '/barcode/product', [BarcodeController::class, 'product'])->name('barcode.product');
        Route::match(['get', 'post'], '/barcode/settings', [BarcodeController::class, 'settings'])->name('barcode.settings');
        Route::match(['get', 'post'], '/barcode/product-search', [BarcodeController::class, 'search'])->name('barcode.product.search');

        Route::get('/search-product', [ProductController::class, 'search'])->name('search.product');

        Route::post('user-address', [AdminOrderController::class, 'userAddress'])->name('order.user.address');

        Route::group(['prefix' => 'custom-fields', 'as' => 'custom_fields.'], function () {
            Route::get('/', [CustomFieldController::class, 'index'])->name('index');
            Route::get('create', [CustomFieldController::class, 'create'])->name('create');
            Route::post('/', [CustomFieldController::class, 'store'])->name('store');
            Route::get('{id}/edit', [CustomFieldController::class, 'edit'])->name('edit');
            Route::put('{id}', [CustomFieldController::class, 'update'])->name('update');
            Route::delete('/{id}', [CustomFieldController::class, 'destroy'])->name('destroy');
        });

        Route::match(['get', 'post'], 'address/setting', [AddressSettingController::class, 'index'])->name('address.setting.index');

        Route::get('find-vendor-assign-users', [VendorController::class, 'findVendorAssignUsers']);

        Route::match(['get', 'post'], 'sso/clients', [SsoController::class, 'client'])->name('sso.client');
        Route::delete('sso/clients/{id}', [SsoController::class, 'deleteClient'])->name('sso.client.delete');
        Route::resource('api-keys', ApiKeyController::class)->except(['show', 'create', 'edit']);
        Route::match(['get', 'post'], 'api-settings', [ApiKeyController::class, 'settings'])->name('api-settings');

        Route::get('themes', [ThemeController::class, 'index'])->name('themes.index');
        Route::get('themes/{slug}', [ThemeController::class, 'active'])->name('themes.active');

        // Multi-Currency
        Route::group(['prefix' => 'settings/currency', 'as' => 'settings.currency.'], function () {
            Route::match(['get', 'post'], '/', [CurrencySettingsController::class, 'index'])->name('index');
            Route::post('store', [CurrencySettingsController::class, 'store'])->name('store');
            Route::get('{id}/edit', [CurrencySettingsController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [CurrencySettingsController::class, 'update'])->name('update');
            Route::delete('{id}', [CurrencySettingsController::class, 'destroy'])->name('destroy');
            Route::post('exchange-update', [CurrencySettingsController::class, 'exchangeUpdate'])->name('exchange-update');
        });

        Route::get('data-tables', [DataTableController::class, 'index'])->name('datatables.index');
        Route::post('data-tables', [DataTableController::class, 'store'])->name('datatables.store');
        Route::get('data-tables/status/{query}', [DataTableController::class, 'status'])->name('datatables.status');
        Route::resource('units', UnitController::class)->except(['show', 'create', 'edit']);
    });

    Route::group(['middleware' => ['isLoggedIn']], function () {
        Route::get('files/download/{id}', [FilesController::class, 'downloadAttachment']);
        Route::post('change-lang', [DashboardController::class, 'switchLanguage'])->middleware(['checkForDemoMode']);

        Route::get('is-valid-file-size', [FilesController::class, 'isValidFileSize']);
    });

    Route::get('/find-products-in-ajax', [ProductController::class, 'findProductAjaxQuery'])->middleware('locale')->name('findProductsAjax');
    Route::get('/find-tags-in-ajax', [ProductController::class, 'findTagsAjaxQuery'])->name('findTagsAjax');

    // Test Routes
    Route::get('/product/{code}/json', [ProductController::class, 'productJson']);
    Route::get('get-brand-attribute-by-vendor/{id}', [ProductController::class, 'loadBrandAttribute']);
});
