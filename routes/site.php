<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Sakawat Hossain Rony <[sakawat.techvill@gmail.com]>
 * @contributor Al Mamun <[almamun.techvill@gmail.com]>
 *
 * @created 07-11-2021
 *
 * @modified 19-12-2021
 */

use App\Http\Controllers\LoginController;
use App\Http\Controllers\Site\AddressController;
use App\Http\Controllers\Site\CartController;
use App\Http\Controllers\Site\CompareController;
use App\Http\Controllers\Site\DashboardController;
use App\Http\Controllers\Site\DownloadController;
use App\Http\Controllers\Site\SiteController;
use App\Http\Controllers\Site\LoginController as SiteLoginController;
use App\Http\Controllers\Site\NotificationController;
use App\Http\Controllers\Site\OrderController;
use App\Http\Controllers\Site\ProductController;
use App\Http\Controllers\Site\RegisteredSellerController;
use App\Http\Controllers\Site\ResetDataController;
use App\Http\Controllers\Site\ReviewController;
use App\Http\Controllers\Site\SellerController;
use App\Http\Controllers\Site\ShippingProviderController;
use App\Http\Controllers\Site\UserController;
use App\Http\Controllers\Site\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Site Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['web']], function () {
    // homepage
    Route::group(['middleware' => ['locale']], function () {
        Route::get('/', [SiteController::class, 'index'])->name('site.index')->middleware('themeable');
        Route::post('review/pagination/fetch', [SiteController::class, 'fetch'])->name('fetch.review')->middleware('themeable');
        Route::post('change-language', [DashboardController::class, 'switchLanguage']);
        Route::post('change-currency', [DashboardController::class, 'switchCurrency']);

        Route::get('shop/{alias}', [SellerController::class, 'index'])->name('site.shop')->middleware('themeable');
        Route::get('shop/profile/{alias}', [SellerController::class, 'vendorProfile'])->name('site.shop.profile')->middleware('themeable');

        Route::get('auth', [LoginController::class, 'showLoginForm'])->middleware('themeable');
        Route::get('auth/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('themeable');
        Route::get('auth/registration', [LoginController::class, 'showRegisterForm'])->name('registration')->middleware('themeable');

        // login register
        Route::get('login', [SiteLoginController::class, 'login']);
        Route::get('user/login', [SiteLoginController::class, 'login'])->name('site.login');
        Route::post('authenticate', [SiteLoginController::class, 'authenticate'])->name('site.authenticate');
        Route::get('user-verify/{token}/{from?}', [SiteLoginController::class, 'verification'])->name('site.verify');
        Route::get('user-verification/{otp}', [SiteLoginController::class, 'verifyByOtp']);
        Route::post('sign-up-store', [SiteLoginController::class, 'signUp'])->name('site.signUpStore');
        Route::get('myaccount/logout', [SiteLoginController::class, 'logout'])->name('site.logout');
        Route::get('check-email-existence/{email}', [SiteLoginController::class, 'checkEmailExistence']);
        Route::post('resend-verification-code', [SiteLoginController::class, 'resendUserVerificationCode']);

        // Password reset
        Route::get('password/resets/{token}', [SiteLoginController::class, 'showResetForm'])->name('site.password.reset');
        Route::post('password/resets', [SiteLoginController::class, 'setPassword'])->name('site.password.resets');
        Route::post('password/email', [SiteLoginController::class, 'sendResetLinkEmail'])->name('site.login.sendResetLink');
        Route::get('password/reset-otp/{token}', [SiteLoginController::class, 'resetOtp'])->name('site.reset.otp');
        // Check valid mail
        Route::post('valid-mail/{mail}', [SiteLoginController::class, 'validMail'])->name('site.valid_mail');

        // Seller register
        Route::get('seller/sign-up', [RegisteredSellerController::class, 'showSignUpForm'])->name('site.seller.signUp')->middleware('themeable');
        Route::post('seller/sign-up-store', [RegisteredSellerController::class, 'signUp'])->name('site.seller.signUpStore');
        Route::get('seller/otp', [RegisteredSellerController::class, 'otpForm'])->name('site.seller.otp')->middleware('themeable');
        Route::get('seller/resend-otp/{email?}', [RegisteredSellerController::class, 'resendVerificationCode'])->name('site.seller.resend-otp');
        Route::get('seller-verify/{token}', [RegisteredSellerController::class, 'verification'])->name('site.seller.verify');
        Route::post('seller-verify/otp', [RegisteredSellerController::class, 'otpVerification'])->name('site.seller.otpVerify');

        // Review
        Route::post('site/review/filter', [SiteController::class, 'filterReview']);
        Route::post('site/review/search', [SellerController::class, 'searchReview']);

        // product
        Route::get('products/{slug}', [SiteController::class, 'productDetails'])->name('site.productDetails')->middleware('themeable');

        // Blog
        Route::get('blogs/{value?}', [SiteController::class, 'allBlogs'])->name('blog.all')->middleware('themeable');
        Route::get('blog/search', [SiteController::class, 'blogSearch'])->name('blog.search')->middleware('themeable');
        Route::get('blog/details/{slug}', [SiteController::class, 'blogDetails'])->name('blog.details')->middleware('themeable');
        Route::get('blog-category/{id}', [SiteController::class, 'blogCategory'])->name('blog.category')->middleware('themeable');

        // Brands
        Route::get('brand/{id}/products', [SiteController::class, 'brandProducts'])->name('site.brandProducts')->middleware('themeable');

        // cart
        Route::get('carts', [CartController::class, 'index'])->name('site.cart')->middleware('themeable');
        Route::post('cart-store', [CartController::class, 'store'])->name('site.addCart');
        Route::post('cart-reduce-qty', [CartController::class, 'reduceQuantity'])->name('site.cartReduceQuantity');
        Route::post('cart-delete', [CartController::class, 'destroy'])->name('site.delete');
        Route::post('cart-selected-delete', [CartController::class, 'destroySelected']);
        Route::post('cart-selected-store', [CartController::class, 'storeSelected']);
        Route::post('cart-all-delete', [CartController::class, 'destroyAll']);
        Route::post('cart-select-shipping', [CartController::class, 'selectShipping']);

        // Order
        Route::post('order', [OrderController::class, 'store'])->middleware(['checkGuest'])->name('site.orderStore');
        Route::get('order-confirm/{reference}', [OrderController::class, 'confirmation'])->name('site.orderConfirm')->middleware('themeable');
        Route::get('order-paid', [OrderController::class, 'orderPaid'])->name('site.orderpaid');
        Route::post('order-get-shipping-tax', [OrderController::class, 'getShippingTax'])->name('site.orderTaxShipping');

        // Check Out
        Route::get('checkout', [OrderController::class, 'checkOut'])->middleware(['checkGuest', 'themeable'])->name('site.checkOut');

        Route::get('buynow', [OrderController::class, 'buynow'])->middleware(['checkGuest', 'themeable'])->name('site.buynow');


        // check coupon
        Route::post('check-coupon', [CartController::class, 'checkCoupon'])->name('site.checkCoupon');
        Route::post('delete-coupon', [CartController::class, 'deleteCoupon'])->name('site.deleteCoupon');

        // search
        Route::get('search-products', [SiteController::class, 'search'])->name('site.productSearch')->middleware('themeable');

        // userSearch
        Route::post('get-search-data', [SiteController::class, 'getSearchData'])->name('site.searchData');

        // compare
        Route::get('compare', [CompareController::class, 'index'])->name('site.compare')->middleware('themeable');
        Route::post('/compare-store', [CompareController::class, 'store'])->name('site.addCompare');
        Route::post('/compare-delete', [CompareController::class, 'destroy'])->name('site.compareDestroy');

        // Track order
        Route::get('/track-order', [OrderController::class, 'track'])->name('site.trackOrder')->middleware('themeable');

        // Quick View
        Route::get('product/quick-view/{id}', [SiteController::class, 'quickView'])->name('quickView')->middleware('themeable');

        // coupon
        Route::get('/coupon', [SiteController::class, 'coupon'])->name('site.coupon')->middleware('themeable');

        // shipping
        Route::get('/get-shipping', [SiteController::class, 'getShipping'])->middleware('themeable');

        // downloadable link
        Route::get('/download', [SiteController::class, 'download'])->name('site.downloadProduct')->middleware('themeable');

        // Pages
        Route::get('page/{slug}', [SiteController::class, 'page'])->name('site.page')->middleware('themeable');

        Route::get('/get-component-product', [SiteController::class, 'getComponentProduct'])->name('ajax-product')->middleware('themeable');

        // all categories
        Route::get('/categories', [SiteController::class, 'allCategories'])->name('all.categories')->middleware('themeable');

        // payment link
        Route::get('/order/payment/{reference}', [SiteController::class, 'orderPayment'])->name('site.order.custom.payment');

        
    });

    // login or register by google
    Route::get('login/google', [SiteLoginController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('login/google/callback', [SiteLoginController::class, 'handelGoogleCallback'])->name('google');

    // login or register by facebook
    Route::get('login/facebook', [SiteLoginController::class, 'redirectToFacebook'])->name('login.facebook');
    Route::get('login/facebook/callback', [SiteLoginController::class, 'handelFacebookCallback'])->name('facebook');

    Route::group(['middleware' => ['site.auth', 'locale', 'permission']], function () {
        Route::post('/site/review/destroy', [SiteController::class, 'deleteReview']);
        Route::post('/site/review/update', [SiteController::class, 'updateReview']);
        // be a seller request
        Route::post('/seller/request-store', [RegisteredSellerController::class, 'sellerRequestStore'])->name('seller.store.request');
    });

    Route::get('/reset-data', [ResetDataController::class, 'reset']);

    Route::get('guest/payment/{reference}', [OrderController::class, 'payment'])->name('site.order.payment.guest');
    Route::get('guest/order-paid', [OrderController::class, 'orderPaid'])->name('site.orderpaid.guest');
    Route::get('guest/order-confirm/{reference}', [OrderController::class, 'confirmation'])->name('site.orderConfirm.guest')->middleware('themeable');
    Route::get('guest/invoice/print/{id}', [OrderController::class, 'invoicePrint'])->name('site.invoice.print.guest')->middleware('themeable');

    Route::get('shipping/provider/{id}', [ShippingProviderController::class, 'shippingProvider'])->name('shipping.provider');
    Route::get('find-shipping-providers', [ShippingProviderController::class, 'findShippingProviders'])->name('find.shipping.providers');
    Route::get('search-shipping-providers', [ShippingProviderController::class, 'searchShippingProviders'])->name('search.shipping.providers');
    Route::group(['prefix' => 'myaccount', 'as' => 'site.', 'middleware' => ['site.auth', 'locale', 'permission']], function () {
        Route::get('overview', [DashboardController::class, 'index'])->name('dashboard')->middleware('themeable');
        Route::get('wishlists', [WishlistController::class, 'index'])->name('wishlist')->middleware('themeable');
        Route::get('reviews', [ReviewController::class, 'index'])->name('review')->middleware('themeable');
        Route::get('profile', [UserController::class, 'edit'])->name('userProfile')->middleware('themeable');
        Route::get('setting', [UserController::class, 'setting'])->name('userSetting')->middleware('themeable');
        Route::get('activity', [UserController::class, 'activity'])->name('userActivity')->middleware('themeable');
        Route::get('downloads', [DownloadController::class, 'index'])->name('download')->middleware('themeable');
        Route::get('addresses', [AddressController::class, 'index'])->name('address')->middleware('themeable');
        Route::get('address/create', [AddressController::class, 'create'])->name('addressCreate')->middleware('themeable');
        Route::get('address/edit/{id}', [AddressController::class, 'edit'])->name('addressEdit')->middleware('themeable');
        Route::get('orders', [OrderController::class, 'index'])->name('order')->middleware('themeable');
        Route::get('orders/{reference}', [OrderController::class, 'orderDetails'])->name('orderDetails')->middleware('themeable');
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index')->middleware('themeable');

        // user
        Route::post('profile/update', [UserController::class, 'update'])->middleware(['checkForDemoMode'])->name('profile.update');
        Route::post('profile/update-password', [UserController::class, 'updatePassword'])->middleware(['checkForDemoMode'])->name('password.update');
        Route::post('delete', [UserController::class, 'destroy'])->name('user.delete')->middleware(['checkForDemoMode']);
        Route::get('profile/remove-image', [UserController::class, 'removeImage'])->name('profile.delete');
        Route::get('invoice/print/{id}', [OrderController::class, 'invoicePrint'])->name('invoice.print');

        // Wishlist
        Route::post('wishlist/store', [WishlistController::class, 'store'])->name('wishlist.store');

        // Address
        Route::post('address/store', [AddressController::class, 'store'])->name('address.store');
        Route::post('address/update/{id}', [AddressController::class, 'update'])->name('address.update');
        Route::post('address/delete/{id}', [AddressController::class, 'destroy'])->name('address.delete');
        Route::post('check-default-address', [AddressController::class, 'checkDefault']);
        Route::get('make-default-address/{id}', [AddressController::class, 'makeDefault'])->name('address.set.default');

        // review
        Route::post('review-store', [SiteController::class, 'reviewStore'])->name('review.store');
        Route::post('review/delete/{id}', [ReviewController::class, 'destroy'])->name('review.destroy');

        // Notifications
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::patch('notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark_read');
        Route::patch('notifications/mark-as-unread/{id}', [NotificationController::class, 'markAsUnread'])->name('notifications.mark_unread');
        Route::get('notifications/view/{id}', [NotificationController::class, 'view'])->name('notifications.view');
    });

    Route::group(['middleware' => ['locale']], function () {
        Route::get('products', [ProductController::class, 'search'])->name('site.product.search')->middleware('themeable');
    });

    Route::get('load-web-categories', [SiteController::class, 'loadWebCategories'])->middleware('locale');
    Route::get('load-mobile-categories', [SiteController::class, 'loadMobileCategories'])->middleware('locale');

    Route::get('load-login-modal', [SiteController::class, 'loadLoginModal'])->middleware('locale');

    Route::get('load-same-shop/{id}', [SiteController::class, 'loadSameShop'])->middleware('locale');
    Route::get('load-related-products/{id}', [SiteController::class, 'loadRelatedProducts'])->middleware('locale');
    Route::get('load-upsale-products/{id}', [SiteController::class, 'loadUpSaleProducts'])->middleware('locale');
});
