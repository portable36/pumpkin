<?php

namespace Database\Seeders\versions\v4_2_0;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionUpdateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permission mappings with groups and alias - Manual mapping
        $permissionMappings = [
            'App\\Http\\Controllers\\DashboardController@index' => [
                'groups' => 'admin_panel/web/dashboard',
                'alias' => 'Dashboard',
            ],
            'App\\Http\\Controllers\\RoleController@index' => [
                'groups' => 'admin_panel/web/role_management',
                'alias' => 'Role List',
            ],
            'App\\Http\\Controllers\\RoleController@create' => [
                'groups' => 'admin_panel/web/role_management',
                'alias' => 'Role Create',
            ],
            'App\\Http\\Controllers\\RoleController@store' => [
                'groups' => 'admin_panel/web/role_management',
                'alias' => 'Role Store',
            ],
            'App\\Http\\Controllers\\RoleController@edit' => [
                'groups' => 'admin_panel/web/role_management',
                'alias' => 'Role Edit',
            ],
            'App\\Http\\Controllers\\RoleController@update' => [
                'groups' => 'admin_panel/web/role_management',
                'alias' => 'Role Update',
            ],
            'App\\Http\\Controllers\\RoleController@destroy' => [
                'groups' => 'admin_panel/web/role_management',
                'alias' => 'Role Destroy',
            ],
            'App\\Http\\Controllers\\UserController@index' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'User List',
            ],
            'App\\Http\\Controllers\\UserController@create' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'User Create',
            ],
            'App\\Http\\Controllers\\UserController@store' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'User Store',
            ],
            'App\\Http\\Controllers\\UserController@edit' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'User Edit',
            ],
            'App\\Http\\Controllers\\UserController@updatePassword' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'User Update Password',
            ],
            'App\\Http\\Controllers\\UserController@update' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'User Update',
            ],
            'App\\Http\\Controllers\\UserController@destroy' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'User Destroy',
            ],
            'App\\Http\\Controllers\\UserController@import' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'User Import',
            ],
            'App\\Http\\Controllers\\UserController@pdf' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'User PDF Download',
            ],
            'App\\Http\\Controllers\\UserController@csv' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'User CSV Download',
            ],
            'App\\Http\\Controllers\\UserController@ledger' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'User Ledger',
            ],
            'App\\Http\\Controllers\\UserController@verification' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'User Verification',
            ],
            'App\\Http\\Controllers\\ProductController@index' => [
                'groups' => 'admin_panel/web/product_management',
                'alias' => 'Product List',
            ],
            'App\\Http\\Controllers\\ProductController@edit' => [
                'groups' => 'admin_panel/web/product_management',
                'alias' => 'Product Edit',
            ],
            'App\\Http\\Controllers\\ProductController@view' => [
                'groups' => 'admin_panel/web/product_management',
                'alias' => 'Product View',
            ],
            'App\\Http\\Controllers\\VendorController@index' => [
                'groups' => 'admin_panel/web/vendor_management',
                'alias' => 'Vendor List',
            ],
            'App\\Http\\Controllers\\VendorController@create' => [
                'groups' => 'admin_panel/web/vendor_management',
                'alias' => 'Vendor Create',
            ],
            'App\\Http\\Controllers\\VendorController@store' => [
                'groups' => 'admin_panel/web/vendor_management',
                'alias' => 'Vendor Store',
            ],
            'App\\Http\\Controllers\\VendorController@edit' => [
                'groups' => 'admin_panel/web/vendor_management',
                'alias' => 'Vendor Edit',
            ],
            'App\\Http\\Controllers\\VendorController@update' => [
                'groups' => 'admin_panel/web/vendor_management',
                'alias' => 'Vendor Update',
            ],
            'App\\Http\\Controllers\\VendorController@destroy' => [
                'groups' => 'admin_panel/web/vendor_management',
                'alias' => 'Vendor Destroy',
            ],
            'App\\Http\\Controllers\\VendorController@import' => [
                'groups' => 'admin_panel/web/vendor_management',
                'alias' => 'Vendor Import (Deprecated)',
            ],
            'App\\Http\\Controllers\\VendorController@pdf' => [
                'groups' => 'admin_panel/web/vendor_management',
                'alias' => 'Vendor PDF Download',
            ],
            'App\\Http\\Controllers\\VendorController@csv' => [
                'groups' => 'admin_panel/web/vendor_management',
                'alias' => 'Vendor CSV Download',
            ],
            'App\\Http\\Controllers\\BrandController@index' => [
                'groups' => 'admin_panel/web/brand_management',
                'alias' => 'Brand List',
            ],
            'App\\Http\\Controllers\\BrandController@create' => [
                'groups' => 'admin_panel/web/brand_management',
                'alias' => 'Brand Create',
            ],
            'App\\Http\\Controllers\\BrandController@store' => [
                'groups' => 'admin_panel/web/brand_management',
                'alias' => 'Brand Store',
            ],
            'App\\Http\\Controllers\\BrandController@edit' => [
                'groups' => 'admin_panel/web/brand_management',
                'alias' => 'Brand Edit',
            ],
            'App\\Http\\Controllers\\BrandController@update' => [
                'groups' => 'admin_panel/web/brand_management',
                'alias' => 'Brand Update',
            ],
            'App\\Http\\Controllers\\BrandController@destroy' => [
                'groups' => 'admin_panel/web/brand_management',
                'alias' => 'Brand Destroy',
            ],
            'App\\Http\\Controllers\\BrandController@pdf' => [
                'groups' => 'admin_panel/web/brand_management',
                'alias' => 'Brand PDF Download',
            ],
            'App\\Http\\Controllers\\BrandController@csv' => [
                'groups' => 'admin_panel/web/brand_management',
                'alias' => 'Brand CSV Download',
            ],
            'App\\Http\\Controllers\\AttributeController@index' => [
                'groups' => 'admin_panel/web/attribute_management',
                'alias' => 'Attribute List',
            ],
            'App\\Http\\Controllers\\AttributeController@create' => [
                'groups' => 'admin_panel/web/attribute_management',
                'alias' => 'Attribute Create',
            ],
            'App\\Http\\Controllers\\AttributeController@store' => [
                'groups' => 'admin_panel/web/attribute_management',
                'alias' => 'Attribute Store',
            ],
            'App\\Http\\Controllers\\AttributeController@edit' => [
                'groups' => 'admin_panel/web/attribute_management',
                'alias' => 'Attribute Edit',
            ],
            'App\\Http\\Controllers\\AttributeController@getAttribute' => [
                'groups' => 'admin_panel/web/attribute_management',
                'alias' => 'Get Attribute (Ajax)',
            ],
            'App\\Http\\Controllers\\AttributeController@update' => [
                'groups' => 'admin_panel/web/attribute_management',
                'alias' => 'Attribute Update',
            ],
            'App\\Http\\Controllers\\AttributeController@destroy' => [
                'groups' => 'admin_panel/web/attribute_management',
                'alias' => 'Attribute Destroy',
            ],
            'App\\Http\\Controllers\\AttributeController@pdf' => [
                'groups' => 'admin_panel/web/attribute_management',
                'alias' => 'Attribute PDF Download',
            ],
            'App\\Http\\Controllers\\AttributeController@csv' => [
                'groups' => 'admin_panel/web/attribute_management',
                'alias' => 'Attribute CSV Download',
            ],
            'App\\Http\\Controllers\\AttributeGroupController@index' => [
                'groups' => 'admin_panel/web/attribute_management_(Deprecated)',
                'alias' => 'Attribute Group List',
            ],
            'App\\Http\\Controllers\\AttributeGroupController@create' => [
                'groups' => 'admin_panel/web/attribute_management_(Deprecated)',
                'alias' => 'Attribute Group Create',
            ],
            'App\\Http\\Controllers\\AttributeGroupController@store' => [
                'groups' => 'admin_panel/web/attribute_management_(Deprecated)',
                'alias' => 'Attribute Group Store',
            ],
            'App\\Http\\Controllers\\AttributeGroupController@edit' => [
                'groups' => 'admin_panel/web/attribute_management_(Deprecated)',
                'alias' => 'Attribute Group Edit',
            ],
            'App\\Http\\Controllers\\AttributeGroupController@update' => [
                'groups' => 'admin_panel/web/attribute_management_(Deprecated)',
                'alias' => 'Attribute Group Update',
            ],
            'App\\Http\\Controllers\\AttributeGroupController@destroy' => [
                'groups' => 'admin_panel/web/attribute_management_(Deprecated)',
                'alias' => 'Attribute Group Destroy',
            ],
            'App\\Http\\Controllers\\AttributeGroupController@pdf' => [
                'groups' => 'admin_panel/web/attribute_management_(Deprecated)',
                'alias' => 'Attribute Group PDF Download',
            ],
            'App\\Http\\Controllers\\AttributeGroupController@csv' => [
                'groups' => 'admin_panel/web/attribute_management_(Deprecated)',
                'alias' => 'Attribute Group CSV Download',
            ],
            'App\\Http\\Controllers\\CategoryController@index' => [
                'groups' => 'admin_panel/web/category_management',
                'alias' => 'Category List',
            ],
            'App\\Http\\Controllers\\CategoryController@store' => [
                'groups' => 'admin_panel/web/category_management',
                'alias' => 'Category Store',
            ],
            'App\\Http\\Controllers\\CategoryController@getData' => [
                'groups' => 'admin_panel/web/category_management',
                'alias' => 'Retrieve Category Data',
            ],
            'App\\Http\\Controllers\\CategoryController@getParentData' => [
                'groups' => 'admin_panel/web/category_management',
                'alias' => 'Retrieve Parent Category Data',
            ],
            'App\\Http\\Controllers\\CategoryController@moveNode' => [
                'groups' => 'admin_panel/web/category_management',
                'alias' => 'Move Category Node',
            ],
            'App\\Http\\Controllers\\CategoryController@edit' => [
                'groups' => 'admin_panel/web/category_management',
                'alias' => 'Edit Category',
            ],
            'App\\Http\\Controllers\\CategoryController@update' => [
                'groups' => 'admin_panel/web/category_management',
                'alias' => 'Update Category',
            ],
            'App\\Http\\Controllers\\CategoryController@destroy' => [
                'groups' => 'admin_panel/web/category_management',
                'alias' => 'Delete Category',
            ],

            'App\\Http\\Controllers\\MailTemplateController@index' => [
                'groups' => 'admin_panel/web/email_management',
                'alias' => 'Email Template List',
            ],
            'App\\Http\\Controllers\\MailTemplateController@create' => [
                'groups' => 'admin_panel/web/email_management',
                'alias' => 'Email Template Create',
            ],
            'App\\Http\\Controllers\\MailTemplateController@store' => [
                'groups' => 'admin_panel/web/email_management',
                'alias' => 'Email Template Store',
            ],
            'App\\Http\\Controllers\\MailTemplateController@edit' => [
                'groups' => 'admin_panel/web/email_management',
                'alias' => 'Email Template Edit',
            ],
            'App\\Http\\Controllers\\MailTemplateController@update' => [
                'groups' => 'admin_panel/web/email_management',
                'alias' => 'Email Template Update',
            ],
            'App\\Http\\Controllers\\MailTemplateController@destroy' => [
                'groups' => 'admin_panel/web/email_management',
                'alias' => 'Email Template Destroy',
            ],

            'App\\Http\\Controllers\\LanguageController@translation' => [
                'groups' => 'admin_panel/web/language_management',
                'alias' => 'View Language Translations',
            ],
            'App\\Http\\Controllers\\LanguageController@index' => [
                'groups' => 'admin_panel/web/language_management',
                'alias' => 'List Languages',
            ],
            'App\\Http\\Controllers\\LanguageController@store' => [
                'groups' => 'admin_panel/web/language_management',
                'alias' => 'Create Language',
            ],
            'App\\Http\\Controllers\\LanguageController@edit' => [
                'groups' => 'admin_panel/web/language_management',
                'alias' => 'Edit Language',
            ],
            'App\\Http\\Controllers\\LanguageController@update' => [
                'groups' => 'admin_panel/web/language_management',
                'alias' => 'Update Language',
            ],
            'App\\Http\\Controllers\\LanguageController@delete' => [
                'groups' => 'admin_panel/web/language_management',
                'alias' => 'Delete Language',
            ],
            'App\\Http\\Controllers\\LanguageController@translationStore' => [
                'groups' => 'admin_panel/web/language_management',
                'alias' => 'Store Language Translations',
            ],

            'App\\Http\\Controllers\\CurrencyController@index' => [
                'groups' => 'admin_panel/web/currency_management_(Deprecated)',
                'alias' => 'List Currencies',
            ],
            'App\\Http\\Controllers\\CurrencyController@store' => [
                'groups' => 'admin_panel/web/currency_management_(Deprecated)',
                'alias' => 'Create Currency',
            ],
            'App\\Http\\Controllers\\CurrencyController@edit' => [
                'groups' => 'admin_panel/web/currency_management_(Deprecated)',
                'alias' => 'Edit Currency',
            ],
            'App\\Http\\Controllers\\CurrencyController@update' => [
                'groups' => 'admin_panel/web/currency_management_(Deprecated)',
                'alias' => 'Update Currency',
            ],
            'App\\Http\\Controllers\\CurrencyController@destroy' => [
                'groups' => 'admin_panel/web/currency_management_(Deprecated)',
                'alias' => 'Delete Currency',
            ],
            'App\\Http\\Controllers\\CurrencyController@validCurrencyName' => [
                'groups' => 'admin_panel/web/currency_management_(Deprecated)',
                'alias' => 'Validate Currency Name',
            ],
            'App\\Http\\Controllers\\CurrencyController@findCurrencyAjaxQuery' => [
                'groups' => 'admin_panel/web/currency_management_(Deprecated)',
                'alias' => 'Find Currency (Ajax)',
            ],
            'App\\Http\\Controllers\\ReviewController@index' => [
                'groups' => 'admin_panel/web/review_management',
                'alias' => 'List Reviews',
            ],
            'App\\Http\\Controllers\\ReviewController@edit' => [
                'groups' => 'admin_panel/web/review_management',
                'alias' => 'Edit Review',
            ],
            'App\\Http\\Controllers\\ReviewController@view' => [
                'groups' => 'admin_panel/web/review_management',
                'alias' => 'View Review',
            ],
            'App\\Http\\Controllers\\ReviewController@update' => [
                'groups' => 'admin_panel/web/review_management',
                'alias' => 'Update Review',
            ],
            'App\\Http\\Controllers\\ReviewController@destroy' => [
                'groups' => 'admin_panel/web/review_management',
                'alias' => 'Delete Review',
            ],
            'App\\Http\\Controllers\\ReviewController@pdf' => [
                'groups' => 'admin_panel/web/review_management',
                'alias' => 'Export Reviews (PDF)',
            ],
            'App\\Http\\Controllers\\ReviewController@csv' => [
                'groups' => 'admin_panel/web/review_management',
                'alias' => 'Export Reviews (CSV)',
            ],

            'Modules\\Coupon\\Http\\Controllers\\CouponController@index' => [
                'groups' => 'admin_panel/web/coupon_management',
                'alias' => 'List Coupons',
            ],
            'Modules\\Coupon\\Http\\Controllers\\CouponController@create' => [
                'groups' => 'admin_panel/web/coupon_management',
                'alias' => 'Create Coupon',
            ],
            'Modules\\Coupon\\Http\\Controllers\\CouponController@store' => [
                'groups' => 'admin_panel/web/coupon_management',
                'alias' => 'Store Coupon',
            ],
            'Modules\\Coupon\\Http\\Controllers\\CouponController@edit' => [
                'groups' => 'admin_panel/web/coupon_management',
                'alias' => 'Edit Coupon',
            ],
            'Modules\\Coupon\\Http\\Controllers\\CouponController@update' => [
                'groups' => 'admin_panel/web/coupon_management',
                'alias' => 'Update Coupon',
            ],
            'Modules\\Coupon\\Http\\Controllers\\CouponController@destroy' => [
                'groups' => 'admin_panel/web/coupon_management',
                'alias' => 'Delete Coupon',
            ],
            'Modules\\Coupon\\Http\\Controllers\\CouponController@downloadPdf' => [
                'groups' => 'admin_panel/web/coupon_management',
                'alias' => 'Export Coupons (PDF)',
            ],
            'Modules\\Coupon\\Http\\Controllers\\CouponController@downloadCsv' => [
                'groups' => 'admin_panel/web/coupon_management',
                'alias' => 'Export Coupons (CSV)',
            ],
            'Modules\\Coupon\\Http\\Controllers\\CouponController@getShopByVendor' => [
                'groups' => 'admin_panel/web/coupon_management',
                'alias' => 'Get Shops By Vendor (Ajax)',
            ],
            'Modules\\Coupon\\Http\\Controllers\\CouponController@getCouponProduct' => [
                'groups' => 'admin_+_vendor_panel/web/coupon_management',
                'alias' => 'Get Coupon Products (Deprecated)',
            ],
            'Modules\\Coupon\\Http\\Controllers\\CouponRedeemController@index' => [
                'groups' => 'admin_panel/web/coupon_management',
                'alias' => 'List Redeemed Coupons',
            ],
            'Modules\\Coupon\\Http\\Controllers\\CouponRedeemController@pdf' => [
                'groups' => 'admin_panel/web/coupon_management',
                'alias' => 'Export Redeemed Coupons (PDF)',
            ],
            'Modules\\Coupon\\Http\\Controllers\\CouponRedeemController@csv' => [
                'groups' => 'admin_panel/web/coupon_management',
                'alias' => 'Export Redeemed Coupons (CSV)',
            ],
            'Modules\\Coupon\\Http\\Controllers\\CouponController@getOldProducts' => [
                'groups' => 'admin_+_vendor_panel/web/coupon_management',
                'alias' => 'Get Old Products (Ajax)',
            ],
            'Modules\\Coupon\\Http\\Controllers\\CouponController@getOldVendor' => [
                'groups' => 'admin_panel/web/coupon_management',
                'alias' => 'Get Old Vendor (Ajax)',
            ],
            'Modules\\Blog\\Http\\Controllers\\BlogCategoryController@index' => [
                'groups' => 'admin_panel/web/blog_management',
                'alias' => 'List Blog Categories',
            ],
            'Modules\\Blog\\Http\\Controllers\\BlogCategoryController@delete' => [
                'groups' => 'admin_panel/web/blog_management',
                'alias' => 'Delete Blog Category',
            ],
            'Modules\\Blog\\Http\\Controllers\\BlogController@index' => [
                'groups' => 'admin_panel/web/blog_management',
                'alias' => 'List Blogs',
            ],
            'Modules\\Blog\\Http\\Controllers\\BlogController@create' => [
                'groups' => 'admin_panel/web/blog_management',
                'alias' => 'Create Blog',
            ],
            'Modules\\Blog\\Http\\Controllers\\BlogController@edit' => [
                'groups' => 'admin_panel/web/blog_management',
                'alias' => 'Edit Blog',
            ],
            'Modules\\Blog\\Http\\Controllers\\BlogController@delete' => [
                'groups' => 'admin_panel/web/blog_management',
                'alias' => 'Delete Blog',
            ],
            'Modules\\Blog\\Http\\Controllers\\BlogCategoryController@store' => [
                'groups' => 'admin_panel/web/blog_management',
                'alias' => 'Store Blog Category',
            ],
            'Modules\\Blog\\Http\\Controllers\\BlogCategoryController@update' => [
                'groups' => 'admin_panel/web/blog_management',
                'alias' => 'Update Blog Category',
            ],
            'Modules\\Blog\\Http\\Controllers\\BlogController@store' => [
                'groups' => 'admin_panel/web/blog_management',
                'alias' => 'Store Blog',
            ],
            'Modules\\Blog\\Http\\Controllers\\BlogController@update' => [
                'groups' => 'admin_panel/web/blog_management',
                'alias' => 'Update Blog',
            ],

            'Modules\\MenuBuilder\\Http\\Controllers\\MenuBuilderController@index' => [
                'groups' => 'admin_panel/web/menu_builder',
                'alias' => 'List Menus',
            ],
            'Modules\\MenuBuilder\\Http\\Controllers\\MenuController@createNewMenu' => [
                'groups' => 'admin_panel/web/menu_builder',
                'alias' => 'Create New Menu',
            ],
            'Modules\\MenuBuilder\\Http\\Controllers\\MenuController@delete' => [
                'groups' => 'admin_panel/web/menu_builder',
                'alias' => 'Delete Menu',
            ],
            'Modules\\MenuBuilder\\Http\\Controllers\\MenuController@addCustomMenu' => [
                'groups' => 'admin_panel/web/menu_builder',
                'alias' => 'Add Custom Menu',
            ],
            'Modules\\MenuBuilder\\Http\\Controllers\\MenuController@update' => [
                'groups' => 'admin_panel/web/menu_builder',
                'alias' => 'Update Menu',
            ],
            'Modules\\MenuBuilder\\Http\\Controllers\\MenuController@generateMenuControl' => [
                'groups' => 'admin_panel/web/menu_builder',
                'alias' => 'Generate Menu Control (Ajax)',
            ],
            'Modules\\MenuBuilder\\Http\\Controllers\\MenuController@deleteMenu' => [
                'groups' => 'admin_panel/web/menu_builder',
                'alias' => 'Delete Menu Item',
            ],

            'App\\Http\\Controllers\\OrderStatusController@index' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'List Order Statuses',
            ],
            'App\\Http\\Controllers\\OrderStatusController@store' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Store Order Status',
            ],
            'App\\Http\\Controllers\\OrderStatusController@edit' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Edit Order Status',
            ],
            'App\\Http\\Controllers\\OrderStatusController@update' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Update Order Status',
            ],
            'App\\Http\\Controllers\\OrderStatusController@destroy' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Delete Order Status',
            ],

            'App\\Http\\Controllers\\AdminOrderController@index' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'List Orders',
            ],
            'App\\Http\\Controllers\\AdminOrderController@view' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'View Order',
            ],
            'App\\Http\\Controllers\\AdminOrderController@changeStatus' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Change Order Status',
            ],
            'App\\Http\\Controllers\\AdminOrderController@destroy' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Delete Order',
            ],
            'App\\Http\\Controllers\\AdminOrderController@pdf' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Order PDF Export',
            ],
            'App\\Http\\Controllers\\AdminOrderController@csv' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Order CSV Export',
            ],
            'App\\Http\\Controllers\\AdminOrderController@update' => [
                'groups' => 'admin_+_vendor_panel/web/order_management',
                'alias' => 'Update Order',
            ],
            'App\\Http\\Controllers\\AdminOrderController@grantAccess' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Grant Access to Downloadable Products',
            ],
            'App\\Http\\Controllers\\AdminOrderController@userAddress' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Order User Address',
            ],
            'App\\Http\\Controllers\\UserController@wallet' => [
                'groups' => 'admin_panel/web/user_management_(Deprecated)',
                'alias' => 'User Wallet',
            ],
            'Modules\\CMS\\Http\\Controllers\\SlideController@create' => [
                'groups' => 'admin_panel/web/slider_management',
                'alias' => 'Create Slide',
            ],
            'Modules\\CMS\\Http\\Controllers\\SlideController@store' => [
                'groups' => 'admin_panel/web/slider_management',
                'alias' => 'Store Slide',
            ],
            'Modules\\CMS\\Http\\Controllers\\SlideController@edit' => [
                'groups' => 'admin_panel/web/slider_management',
                'alias' => 'Edit Slide',
            ],
            'Modules\\CMS\\Http\\Controllers\\SlideController@update' => [
                'groups' => 'admin_panel/web/slider_management',
                'alias' => 'Update Slide',
            ],
            'Modules\\CMS\\Http\\Controllers\\SlideController@delete' => [
                'groups' => 'admin_panel/web/slider_management',
                'alias' => 'Delete Slide',
            ],
            'Modules\\CMS\\Http\\Controllers\\SliderController@index' => [
                'groups' => 'admin_panel/web/slider_management',
                'alias' => 'List Sliders',
            ],
            'Modules\\CMS\\Http\\Controllers\\SliderController@store' => [
                'groups' => 'admin_panel/web/slider_management',
                'alias' => 'Store Slider',
            ],
            'Modules\\CMS\\Http\\Controllers\\SliderController@update' => [
                'groups' => 'admin_panel/web/slider_management',
                'alias' => 'Update Slider',
            ],
            'Modules\\CMS\\Http\\Controllers\\SliderController@delete' => [
                'groups' => 'admin_panel/web/slider_management',
                'alias' => 'Delete Slider',
            ],
            'Modules\\Tax\\Http\\Controllers\\TaxClassController@index' => [
                'groups' => 'admin_panel/web/tax_management',
                'alias' => 'Tax Settings',
            ],
            'Modules\\Tax\\Http\\Controllers\\TaxClassController@store' => [
                'groups' => 'admin_panel/web/tax_management',
                'alias' => 'Create Tax Class',
            ],
            'Modules\\Tax\\Http\\Controllers\\TaxClassController@update' => [
                'groups' => 'admin_panel/web/tax_management',
                'alias' => 'Update Tax Class',
            ],
            'Modules\\Tax\\Http\\Controllers\\TaxClassController@destroy' => [
                'groups' => 'admin_panel/web/tax_management',
                'alias' => 'Delete Tax Class',
            ],
            'Modules\\Tax\\Http\\Controllers\\TaxClassController@setting' => [
                'groups' => 'admin_panel/web/tax_management',
                'alias' => 'Tax Settings',
            ],
            'Modules\\Tax\\Http\\Controllers\\TaxRateController@update' => [
                'groups' => 'admin_panel/web/tax_management',
                'alias' => 'Update Tax Rate',
            ],
            'Modules\\Shipping\\Http\\Controllers\\ShippingController@storeClass' => [
                'groups' => 'admin_panel/web/shipping_management',
                'alias' => 'Create Shipping Class',
            ],
            'Modules\\Shipping\\Http\\Controllers\\ShippingController@storeSetting' => [
                'groups' => 'admin_panel/web/shipping_management',
                'alias' => 'Store Shipping Setting',
            ],
            'Modules\\Shipping\\Http\\Controllers\\ShippingController@updateProvider' => [
                'groups' => 'admin_panel/web/shipping_management',
                'alias' => 'Update Shipping Provider',
            ],
            'Modules\\Shipping\\Http\\Controllers\\ShippingController@removeProvider' => [
                'groups' => 'admin_panel/web/shipping_management',
                'alias' => 'Remove Shipping Provider',
            ],
            'Modules\\Shipping\\Http\\Controllers\\ShippingController@storeProvider' => [
                'groups' => 'admin_panel/web/shipping_management',
                'alias' => 'Store Shipping Provider',
            ],
            'Modules\\Shipping\\Http\\Controllers\\ShippingController@index' => [
                'groups' => 'admin_panel/web/shipping_management',
                'alias' => 'Shipping Settings',
            ],
            'Modules\\Shipping\\Http\\Controllers\\ShippingController@store' => [
                'groups' => 'admin_panel/web/shipping_management',
                'alias' => 'Save Shipping Settings',
            ],
            'Modules\\CMS\\Http\\Controllers\\CMSController@index' => [
                'groups' => 'admin_panel/web/cms',
                'alias' => 'Page List',
            ],
            'Modules\\CMS\\Http\\Controllers\\CMSController@create' => [
                'groups' => 'admin_panel/web/cms',
                'alias' => 'Create Page',
            ],
            'Modules\\CMS\\Http\\Controllers\\CMSController@edit' => [
                'groups' => 'admin_panel/web/cms',
                'alias' => 'Edit Page',
            ],
            'Modules\\CMS\\Http\\Controllers\\CMSController@delete' => [
                'groups' => 'admin_panel/web/cms',
                'alias' => 'Delete Page',
            ],
            'Modules\\CMS\\Http\\Controllers\\CMSController@store' => [
                'groups' => 'admin_panel/web/cms',
                'alias' => 'Store Page',
            ],
            'Modules\\CMS\\Http\\Controllers\\CMSController@update' => [
                'groups' => 'admin_panel/web/cms',
                'alias' => 'Update Page',
            ],
            'Modules\\Gateway\\Http\\Controllers\\GatewayController@enableModule' => [
                'groups' => 'admin_panel/web/payment_gateway',
                'alias' => 'Enable Module (Deprecated)',
            ],
            'Modules\\Gateway\\Http\\Controllers\\GatewayController@disableModule' => [
                'groups' => 'admin_panel/web/payment_gateway',
                'alias' => 'Disable Module (Deprecated)',
            ],
            'App\\Http\\Controllers\\TransactionController@index' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Transaction List',
            ],
            'App\\Http\\Controllers\\TransactionController@edit' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Edit Transaction',
            ],
            'App\\Http\\Controllers\\TransactionController@update' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Update Transaction',
            ],
            'App\\Http\\Controllers\\TransactionController@pdf' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Transaction PDF',
            ],
            'App\\Http\\Controllers\\TransactionController@csv' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Transaction CSV',
            ],
            'Modules\\Refund\\Http\\Controllers\\RefundController@index' => [
                'groups' => 'admin_panel/web/refund',
                'alias' => 'Refund List',
            ],
            'Modules\\Refund\\Http\\Controllers\\RefundController@edit' => [
                'groups' => 'admin_panel/web/refund',
                'alias' => 'View Refund',
            ],
            'Modules\\Refund\\Http\\Controllers\\RefundController@update' => [
                'groups' => 'admin_panel/web/refund',
                'alias' => 'Update Refund',
            ],
            'Modules\\Refund\\Http\\Controllers\\RefundController@pdf' => [
                'groups' => 'admin_panel/web/refund',
                'alias' => 'Refund PDF',
            ],
            'Modules\\Refund\\Http\\Controllers\\RefundController@csv' => [
                'groups' => 'admin_panel/web/refund',
                'alias' => 'Refund CSV',
            ],

            'App\\Http\\Controllers\\WithdrawalController@index' => [
                'groups' => 'admin_panel/web/withdrawal',
                'alias' => 'Withdrawal List',
            ],
            'App\\Http\\Controllers\\WithdrawalController@update' => [
                'groups' => 'admin_panel/web/withdrawal',
                'alias' => 'Update Withdrawal',
            ],
            'App\\Http\\Controllers\\WithdrawalController@edit' => [
                'groups' => 'admin_panel/web/withdrawal',
                'alias' => 'Edit Withdrawal',
            ],
            'App\\Http\\Controllers\\WithdrawalController@pdf' => [
                'groups' => 'admin_panel/web/withdrawal',
                'alias' => 'Withdrawal PDF',
            ],
            'App\\Http\\Controllers\\WithdrawalController@csv' => [
                'groups' => 'admin_panel/web/withdrawal',
                'alias' => 'Withdrawal CSV',
            ],

            'Modules\\Popup\\Http\\Controllers\\PopupController@index' => [
                'groups' => 'admin_panel/web/popup_management',
                'alias' => 'Popup List',
            ],
            'Modules\\Popup\\Http\\Controllers\\PopupController@create' => [
                'groups' => 'admin_panel/web/popup_management',
                'alias' => 'Create Popup',
            ],
            'Modules\\Popup\\Http\\Controllers\\PopupController@store' => [
                'groups' => 'admin_panel/web/popup_management',
                'alias' => 'Store Popup',
            ],
            'Modules\\Popup\\Http\\Controllers\\PopupController@show' => [
                'groups' => 'admin_panel/web/popup_management',
                'alias' => 'Show Popup',
            ],
            'Modules\\Popup\\Http\\Controllers\\PopupController@edit' => [
                'groups' => 'admin_panel/web/popup_management',
                'alias' => 'Edit Popup',
            ],
            'Modules\\Popup\\Http\\Controllers\\PopupController@update' => [
                'groups' => 'admin_panel/web/popup_management',
                'alias' => 'Update Popup',
            ],
            'Modules\\Popup\\Http\\Controllers\\PopupController@destroy' => [
                'groups' => 'admin_panel/web/popup_management',
                'alias' => 'Delete Popup',
            ],
            'Modules\\Popup\\Http\\Controllers\\PopupController@pdf' => [
                'groups' => 'admin_panel/web/popup_management',
                'alias' => 'Popup PDF',
            ],
            'Modules\\Popup\\Http\\Controllers\\PopupController@csv' => [
                'groups' => 'admin_panel/web/popup_management',
                'alias' => 'Popup CSV',
            ],

            'Modules\\MediaManager\\Http\\Controllers\\MediaManagerController@create' => [
                'groups' => 'admin_+_vendor_panel/web/media_manager',
                'alias' => 'Create Media File',
            ],
            'Modules\\MediaManager\\Http\\Controllers\\MediaManagerController@store' => [
                'groups' => 'admin_+_vendor_panel/web/media_manager',
                'alias' => 'Store Media File',
            ],
            'Modules\\MediaManager\\Http\\Controllers\\MediaManagerController@upload' => [
                'groups' => 'admin_+_vendor_panel/web/media_manager',
                'alias' => 'Upload Media File',
            ],
            'Modules\\MediaManager\\Http\\Controllers\\MediaManagerController@uploadedFiles' => [
                'groups' => 'admin_+_vendor_panel/web/media_manager',
                'alias' => 'List Uploaded Files',
            ],
            'Modules\\MediaManager\\Http\\Controllers\\MediaManagerController@sortFiles' => [
                'groups' => 'admin_+_vendor_panel/web/media_manager',
                'alias' => 'Sort Files',
            ],
            'Modules\\MediaManager\\Http\\Controllers\\MediaManagerController@paginateFiles' => [
                'groups' => 'admin_+_vendor_panel/web/media_manager',
                'alias' => 'Paginate Files',
            ],
            'Modules\\MediaManager\\Http\\Controllers\\MediaManagerController@download' => [
                'groups' => 'admin_+_vendor_panel/web/media_manager',
                'alias' => 'Download File',
            ],
            'Modules\\MediaManager\\Http\\Controllers\\MediaManagerController@paginateData' => [
                'groups' => 'admin_+_vendor_panel/web/media_manager',
                'alias' => 'Paginate Data (Ajax)',
            ],
            'Modules\\MediaManager\\Http\\Controllers\\MediaManagerController@deleteImage' => [
                'groups' => 'admin_+_vendor_panel/web/media_manager',
                'alias' => 'Delete Image',
            ],
            'App\\Http\\Controllers\\DashboardController@getUserData' => [
                'groups' => 'admin_panel/web/dashboard',
                'alias' => 'Get User Data (Ajax)',
            ],
            'App\\Http\\Controllers\\DashboardController@getProductData' => [
                'groups' => 'admin_+_vendor_panel/web/dashboard',
                'alias' => 'Get Product Data (Ajax)',
            ],
            'App\\Http\\Controllers\\DashboardController@mostSoldProducts' => [
                'groups' => 'admin_panel/web/dashboard',
                'alias' => 'Most Sold Products (Ajax)',
            ],
            'App\\Http\\Controllers\\DashboardController@mostActiveUsers' => [
                'groups' => 'admin_panel/web/dashboard',
                'alias' => 'Most Active Users (Ajax)',
            ],
            'App\\Http\\Controllers\\DashboardController@vendorStats' => [
                'groups' => 'admin_panel/web/dashboard',
                'alias' => 'Vendor Stats (Ajax)',
            ],
            'App\\Http\\Controllers\\DashboardController@salesOfTheMonth' => [
                'groups' => 'admin_panel/web/dashboard',
                'alias' => 'Sales Of The Month (Ajax)',
            ],
            'App\\Http\\Controllers\\DashboardController@vendorStatsType' => [
                'groups' => 'admin_panel/web/dashboard',
                'alias' => 'Vendor Stats Type (Ajax)',
            ],
            'App\\Http\\Controllers\\DashboardController@vendorReq' => [
                'groups' => 'admin_panel/web/dashboard',
                'alias' => 'Vendor Requests (Ajax)',
            ],
            'App\\Http\\Controllers\\DashboardController@changeStatus' => [
                'groups' => 'admin_panel/web/dashboard',
                'alias' => 'Change Vendor Status',
            ],
            'Modules\\Coupon\\Http\\Controllers\\Vendor\\CouponController@index' => [
                'groups' => 'vendor_panel/web/coupon_management',
                'alias' => 'List Coupons',
            ],
            'Modules\\Coupon\\Http\\Controllers\\Vendor\\CouponController@create' => [
                'groups' => 'vendor_panel/web/coupon_management',
                'alias' => 'Create Coupon',
            ],
            'Modules\\Coupon\\Http\\Controllers\\Vendor\\CouponController@store' => [
                'groups' => 'vendor_panel/web/coupon_management',
                'alias' => 'Store Coupon',
            ],
            'Modules\\Coupon\\Http\\Controllers\\Vendor\\CouponController@edit' => [
                'groups' => 'vendor_panel/web/coupon_management',
                'alias' => 'Edit Coupon',
            ],
            'Modules\\Coupon\\Http\\Controllers\\Vendor\\CouponController@update' => [
                'groups' => 'vendor_panel/web/coupon_management',
                'alias' => 'Update Coupon',
            ],
            'Modules\\Coupon\\Http\\Controllers\\Vendor\\CouponController@destroy' => [
                'groups' => 'vendor_panel/web/coupon_management',
                'alias' => 'Delete Coupon',
            ],
            'Modules\\Coupon\\Http\\Controllers\\Vendor\\CouponController@pdf' => [
                'groups' => 'vendor_panel/web/coupon_management',
                'alias' => 'Export Coupons (PDF)',
            ],
            'Modules\\Coupon\\Http\\Controllers\\Vendor\\CouponController@csv' => [
                'groups' => 'vendor_panel/web/coupon_management',
                'alias' => 'Export Coupons (CSV)',
            ],
            'Modules\\Coupon\\Http\\Controllers\\Vendor\\CouponController@item' => [
                'groups' => 'vendor_panel/web/coupon_management',
                'alias' => 'Coupon Shop Item (Deprecated)',
            ],

            'Modules\\FormBuilder\\Http\\Controllers\\KycController@index' => [
                'groups' => 'admin_panel/web/kyc_management',
                'alias' => 'KYC List',
            ],
            'Modules\\FormBuilder\\Http\\Controllers\\KycController@edit' => [
                'groups' => 'admin_panel/web/kyc_management',
                'alias' => 'KYC Edit',
            ],
            'Modules\\FormBuilder\\Http\\Controllers\\KycController@update' => [
                'groups' => 'admin_panel/web/kyc_management',
                'alias' => 'KYC Update',
            ],
            'Modules\\FormBuilder\\Http\\Controllers\\KycController@editSubmission' => [
                'groups' => 'admin_panel/web/kyc_management',
                'alias' => 'KYC Submission Edit',
            ],
            'Modules\\FormBuilder\\Http\\Controllers\\KycController@viewSubmission' => [
                'groups' => 'admin_panel/web/kyc_management',
                'alias' => 'KYC Submission View',
            ],
            'Modules\\FormBuilder\\Http\\Controllers\\KycController@submissionDelete' => [
                'groups' => 'admin_panel/web/kyc_management',
                'alias' => 'KYC Submission Delete',
            ],

            'Modules\\Ticket\\Http\\Controllers\\ChatController@getConversations' => [
                'groups' => 'common/web/ticket_chat',
                'alias' => 'Get Conversations',
            ],
            'Modules\\Ticket\\Http\\Controllers\\ChatController@sendProductDetails' => [
                'groups' => 'common/web/ticket_chat',
                'alias' => 'Send Product Details in Chat',
            ],
            'Modules\\Ticket\\Http\\Controllers\\ChatController@contact-list' => [
                'groups' => 'common/web/ticket_chat',
                'alias' => 'Chat Contact List',
            ],
            'Modules\\Ticket\\Http\\Controllers\\ChatController@storeMessage' => [
                'groups' => 'common/web/ticket_chat',
                'alias' => 'Store Chat Message',
            ],
            'Modules\\Ticket\\Http\\Controllers\\ChatController@createChat' => [
                'groups' => 'common/web/ticket_chat',
                'alias' => 'Create New Chat',
            ],
            'Modules\\Ticket\\Http\\Controllers\\ChatController@inboxRefresh' => [
                'groups' => 'common/web/ticket_chat',
                'alias' => 'Inbox Refresh',
            ],
            'Modules\\Ticket\\Http\\Controllers\\ChatController@initiateChatWithVendor' => [
                'groups' => 'common/web/ticket_chat',
                'alias' => 'Initiate Chat with Vendor',
            ],

            'App\\Http\\Controllers\\BatchController@destroy' => [
                'groups' => 'admin_+_vendor_panel/web/batch_management',
                'alias' => 'Delete Batch Data',
            ],
            'App\\Http\\Controllers\\DataTableController@status' => [
                'groups' => 'admin_+_vendor_panel/web/data_table',
                'alias' => 'Show Status Counts (Ajax)',
            ],
            'App\\Http\\Controllers\\ProductController@search' => [
                'groups' => 'admin_+_vendor_panel/web/order_management',
                'alias' => 'Search Products (Ajax)',
            ],
            'App\\Http\\Controllers\\UserController@findUser' => [
                'groups' => 'admin_+_vendor_panel/web/user_management',
                'alias' => 'Find Users (Ajax)',
            ],
            'App\\Http\\Controllers\\VendorController@findVendor' => [
                'groups' => 'admin_panel/web/vendor_management',
                'alias' => 'Find Vendors (Ajax)',
            ],
            'App\\Http\\Controllers\\VendorController@findVendorAssignUsers' => [
                'groups' => 'admin_panel/web/vendor_management',
                'alias' => 'Find Vendor Assign Users (Ajax)',
            ],
            'App\\Http\\Controllers\\PermissionRoleController@index' => [
                'groups' => 'admin_panel/web/permission_management',
                'alias' => 'Permission List (Deprecated)',
            ],
            'App\\Http\\Controllers\\PermissionRoleController@generatePermission' => [
                'groups' => 'admin_panel/web/permission_management',
                'alias' => 'Generate Permissions',
            ],
            'App\\Http\\Controllers\\PermissionRoleController@assignPermission' => [
                'groups' => 'admin_panel/web/permission_management',
                'alias' => 'Assign Permissions (Deprecated)',
            ],
            'App\\Http\\Controllers\\PreferenceController@index' => [
                'groups' => 'admin_panel/web/configuration_management',
                'alias' => 'Preference Update',
            ],
            'App\\Http\\Controllers\\EmailConfigurationController@index' => [
                'groups' => 'admin_panel/web/email_management',
                'alias' => 'Email Configuration Update',
            ],
            'App\\Http\\Controllers\\CompanySettingController@index' => [
                'groups' => 'admin_panel/web/configuration_management',
                'alias' => 'Company Setting Update',
            ],
            'App\\Http\\Controllers\\AddressSettingController@index' => [
                'groups' => 'admin_panel/web/configuration_management',
                'alias' => 'Address Setting Update',
            ],
            'App\\Http\\Controllers\\InvoiceSettingController@index' => [
                'groups' => 'admin_panel/web/configuration_management',
                'alias' => 'PDF Invoice Setting',
            ],
            'App\\Http\\Controllers\\EmailController@emailVerifySetting' => [
                'groups' => 'admin_panel/web/configuration_management',
                'alias' => 'User Verification Setting',
            ],

            'Modules\\Ticket\\Http\\Controllers\\TicketController@index' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Ticket List',
            ],
            'Modules\\Ticket\\Http\\Controllers\\TicketController@store' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Store Ticket',
            ],
            'Modules\\Ticket\\Http\\Controllers\\TicketController@view' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'View Ticket',
            ],
            'Modules\\Ticket\\Http\\Controllers\\TicketController@replyStore' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Store Ticket Reply',
            ],
            'Modules\\Ticket\\Http\\Controllers\\TicketController@edit' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Edit Ticket',
            ],
            'Modules\\Ticket\\Http\\Controllers\\TicketController@update' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Update Ticket',
            ],
            'Modules\\Ticket\\Http\\Controllers\\TicketController@pdf' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Download Ticket PDF',
            ],
            'Modules\\Ticket\\Http\\Controllers\\TicketController@delete' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Delete Ticket',
            ],
            'Modules\\Ticket\\Http\\Controllers\\TicketController@add' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Add Ticket',
            ],
            'Modules\\Ticket\\Http\\Controllers\\TicketController@changePriority' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Change Ticket Priority',
            ],
            'Modules\\Ticket\\Http\\Controllers\\TicketController@changeAssignee' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Change Ticket Assignee',
            ],
            'Modules\\Ticket\\Http\\Controllers\\TicketController@updateReply' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Update Ticket Reply',
            ],

            'Modules\\Ticket\\Http\\Controllers\\CannedController@messages' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Canned Messages List',
            ],
            'Modules\\Ticket\\Http\\Controllers\\CannedController@storeMessage' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Store Canned Message',
            ],
            'Modules\\Ticket\\Http\\Controllers\\CannedController@search' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Search Canned Messages',
            ],
            'Modules\\Ticket\\Http\\Controllers\\CannedController@editMessage' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Edit Canned Message',
            ],
            'Modules\\Ticket\\Http\\Controllers\\CannedController@updateMessage' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Update Canned Message',
            ],
            'Modules\\Ticket\\Http\\Controllers\\CannedController@destroyMessage' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Delete Canned Message',
            ],
            'Modules\\Ticket\\Http\\Controllers\\CannedController@links' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Canned Links List',
            ],
            'Modules\\Ticket\\Http\\Controllers\\CannedController@storeLink' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Store Canned Link',
            ],
            'Modules\\Ticket\\Http\\Controllers\\CannedController@editLink' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Edit Canned Link',
            ],
            'Modules\\Ticket\\Http\\Controllers\\CannedController@updateLink' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Update Canned Link',
            ],
            'Modules\\Ticket\\Http\\Controllers\\CannedController@destroyLink' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Delete Canned Link',
            ],

            'Modules\\Ticket\\Http\\Controllers\\Vendor\\TicketController@index' => [
                'groups' => 'vendor_panel/web/ticket_management',
                'alias' => 'Ticket List',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Vendor\\TicketController@create' => [
                'groups' => 'vendor_panel/web/ticket_management',
                'alias' => 'Ticket Create',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Vendor\\TicketController@store' => [
                'groups' => 'vendor_panel/web/ticket_management',
                'alias' => 'Ticket Store',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Vendor\\TicketController@view' => [
                'groups' => 'vendor_panel/web/ticket_management',
                'alias' => 'Ticket View',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Vendor\\TicketController@update' => [
                'groups' => 'vendor_panel/web/ticket_management',
                'alias' => 'Ticket Update',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Vendor\\TicketController@replyStore' => [
                'groups' => 'vendor_panel/web/ticket_management',
                'alias' => 'Ticket Reply Store',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Vendor\\TicketController@changeStatus' => [
                'groups' => 'vendor_panel/web/ticket_management',
                'alias' => 'Ticket Change Status',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Vendor\\TicketController@pdf' => [
                'groups' => 'vendor_panel/web/ticket_management',
                'alias' => 'Ticket PDF Download',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Vendor\\FilesController@downloadAttachment' => [
                'groups' => 'vendor_panel/web/ticket_management',
                'alias' => 'Download Ticket Attachment',
            ],

            'App\\Http\\Controllers\\ProductController@createProduct' => [
                'groups' => 'admin_panel/web/product_management',
                'alias' => 'Product Create',
            ],
            'App\\Http\\Controllers\\ProductController@editProductAction' => [
                'groups' => 'admin_panel/web/product_management',
                'alias' => 'Product Edit Action',
            ],
            'App\\Http\\Controllers\\ProductController@deleteProduct' => [
                'groups' => 'admin_panel/web/product_management',
                'alias' => 'Product Delete',
            ],
            'App\\Http\\Controllers\\ProductController@forceDeleteProduct' => [
                'groups' => 'admin_panel/web/product_management',
                'alias' => 'Force Delete (Deprecated)',
            ],
            'App\\Http\\Controllers\\Vendor\\ProductController@createProduct' => [
                'groups' => 'vendor_panel/web/product_management',
                'alias' => 'Product Create',
            ],
            'App\\Http\\Controllers\\Vendor\\ProductController@editProductAction' => [
                'groups' => 'vendor_panel/web/product_management',
                'alias' => 'Product Edit Action',
            ],
            'App\\Http\\Controllers\\Vendor\\ProductController@deleteProduct' => [
                'groups' => 'vendor_panel/web/product_management',
                'alias' => 'Product Delete',
            ],
            'App\\Http\\Controllers\\Vendor\\ProductController@forceDeleteProduct' => [
                'groups' => 'vendor_panel/web/product_management',
                'alias' => 'Force Delete (Deprecated)',
            ],
            'Modules\\Ticket\\Http\\Controllers\\TicketController@csv' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Ticket CSV Download',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Vendor\\TicketController@csv' => [
                'groups' => 'vendor_panel/web/ticket_management',
                'alias' => 'Ticket CSV Download',
            ],
            'Modules\\Ticket\\Http\\Controllers\\TicketController@changeStatus' => [
                'groups' => 'admin_panel/web/ticket_management',
                'alias' => 'Ticket Change Status',
            ],
            'Modules\\MediaManager\\Http\\Controllers\\Vendor\\MediaManagerController@upload' => [
                'groups' => 'vendor_panel/web/media_manager',
                'alias' => 'Media Upload',
            ],
            'Modules\\MediaManager\\Http\\Controllers\\Vendor\\MediaManagerController@sortFiles' => [
                'groups' => 'vendor_panel/web/media_manager',
                'alias' => 'Sort Media Files',
            ],
            'Modules\\MediaManager\\Http\\Controllers\\Vendor\\MediaManagerController@paginateData' => [
                'groups' => 'vendor_panel/web/media_manager',
                'alias' => 'Paginate Media Data',
            ],

            'App\\Http\\Controllers\\Vendor\\ProductController@findProductAjaxQuery' => [
                'groups' => 'vendor_panel/web/product_management',
                'alias' => 'Find Product (Ajax)',
            ],
            'App\\Http\\Controllers\\Vendor\\ProductController@findTagsAjaxQuery' => [
                'groups' => 'vendor_panel/web/product_management',
                'alias' => 'Find Tags (Ajax)',
            ],
            'App\\Http\\Controllers\\OrderSettingController@index' => [
                'groups' => 'admin_panel/web/order_settings',
                'alias' => 'Order Setting Update',
            ],
            'App\\Http\\Controllers\\AccountSettingController@index' => [
                'groups' => 'admin_panel/web/account_settings',
                'alias' => 'Account Setting Update',
            ],
            'App\\Http\\Controllers\\UserController@allUserActivity' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'User Activity List',
            ],
            'App\\Http\\Controllers\\UserController@deleteUserActivity' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'Delete User Activity',
            ],
            'Modules\\Upgrader\\Http\\Controllers\\SystemUpdateController@checkVersion' => [
                'groups' => 'admin_panel/web/system_update',
                'alias' => 'Check System Version',
            ],
            'Modules\\Upgrader\\Http\\Controllers\\SystemUpdateController@downloadVersion' => [
                'groups' => 'admin_panel/web/system_update',
                'alias' => 'Download System Version',
            ],

            'Modules\\Inventory\\Http\\Controllers\\SupplierController@ledger' => [
                'groups' => 'admin_+_vendor_panel/web/inventory_management',
                'alias' => 'Supplier Ledger',
            ],
            'Modules\\Inventory\\Http\\Controllers\\SupplierController@payment' => [
                'groups' => 'admin_panel/web/inventory_management',
                'alias' => 'Supplier Payment',
            ],
            'Modules\\Inventory\\Http\\Controllers\\SupplierController@paymentStore' => [
                'groups' => 'admin_panel/web/inventory_management',
                'alias' => 'Store Supplier Payment',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@view' => [
                'groups' => 'admin_panel/web/inventory_management',
                'alias' => 'View Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@print' => [
                'groups' => 'admin_panel/web/inventory_management',
                'alias' => 'Print Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@payment' => [
                'groups' => 'admin_panel/web/inventory_management',
                'alias' => 'Purchase Payment',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@findVendorLocation' => [
                'groups' => 'admin_panel/web/inventory_management',
                'alias' => 'Find Vendor Location (Ajax)',
            ],
            'Modules\\AdvanceReport\\Http\\Controllers\\AdvanceReportController@index' => [
                'groups' => 'admin_panel/web/advanced_reports',
                'alias' => 'Advance Report List',
            ],
            'Modules\\AdvanceReport\\Http\\Controllers\\AdvanceReportController@show' => [
                'groups' => 'admin_panel/web/advanced_reports',
                'alias' => 'View Advance Report',
            ],
            'Modules\\AdvanceReport\\Http\\Controllers\\AdvanceReportController@export' => [
                'groups' => 'admin_panel/web/advanced_reports',
                'alias' => 'Export Advance Report',
            ],
            'Modules\\AdvanceReport\\Http\\Controllers\\Vendor\\AdvanceReportController@index' => [
                'groups' => 'vendor_panel/web/advanced_reports',
                'alias' => 'Advance Report List',
            ],
            'Modules\\AdvanceReport\\Http\\Controllers\\Vendor\\AdvanceReportController@show' => [
                'groups' => 'vendor_panel/web/advanced_reports',
                'alias' => 'View Advance Report',
            ],
            'Modules\\AdvanceReport\\Http\\Controllers\\Vendor\\AdvanceReportController@export' => [
                'groups' => 'vendor_panel/web/advanced_reports',
                'alias' => 'Export Advance Report',
            ],
            'App\\Http\\Controllers\\DashboardController@setWidgetData' => [
                'groups' => 'admin_panel/web/dashboard',
                'alias' => 'Set Widget Data',
            ],
            'App\\Http\\Controllers\\DashboardController@setWidgetOption' => [
                'groups' => 'admin_panel/web/dashboard',
                'alias' => 'Set Widget Option',
            ],
            'App\\Http\\Controllers\\DashboardController@forgetWidget' => [
                'groups' => 'admin_panel/web/dashboard',
                'alias' => 'Forget Widget',
            ],
            'App\\Http\\Controllers\\DashboardController@clearCache' => [
                'groups' => 'admin_panel/web/dashboard',
                'alias' => 'Clear System Cache',
            ],
            'App\\Http\\Controllers\\SeederController@loadSeedData' => [
                'groups' => 'admin_panel/web/seeder_management',
                'alias' => 'Load Seed Data (Deprecated)',
            ],
            'App\\Http\\Controllers\\SeederController@loadLiveSeedData' => [
                'groups' => 'admin_panel/web/seeder_management',
                'alias' => 'Load Live Seed Data (Deprecated)',
            ],

            'App\\Http\\Controllers\\CustomerController@index' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer List',
            ],
            'App\\Http\\Controllers\\CustomerController@create' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer Create',
            ],
            'App\\Http\\Controllers\\CustomerController@store' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer Store',
            ],
            'App\\Http\\Controllers\\CustomerController@edit' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer Edit',
            ],
            'App\\Http\\Controllers\\CustomerController@update' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer Update',
            ],
            'App\\Http\\Controllers\\CustomerController@destroy' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer Delete',
            ],
            'App\\Http\\Controllers\\CustomerAddressController@index' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer Address List',
            ],
            'App\\Http\\Controllers\\CustomerAddressController@create' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer Address Create',
            ],
            'App\\Http\\Controllers\\CustomerAddressController@store' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer Address Store',
            ],
            'App\\Http\\Controllers\\CustomerAddressController@edit' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer Address Edit',
            ],
            'App\\Http\\Controllers\\CustomerAddressController@update' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer Address Update',
            ],
            'App\\Http\\Controllers\\CustomerAddressController@destroy' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer Address Delete',
            ],
            'App\\Http\\Controllers\\CustomerController@ledger' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer Ledger',
            ],
            'App\\Http\\Controllers\\CustomerController@payment' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer Payment View',
            ],
            'App\\Http\\Controllers\\CustomerController@paymentStore' => [
                'groups' => 'admin_panel/web/customer_management',
                'alias' => 'Vendor Customer Payment Store',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@twilio' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Show Twilio Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@storeTwilio' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Update Twilio Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@nexmo' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Show Nexmo Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@storeNexmo' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Update Nexmo Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@fast2Sms' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Show Fast2SMS Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@storeFast2Sms' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Update Fast2SMS Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@sslWireless' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Show SSL Wireless Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@storeSslWireless' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Update SSL Wireless Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@mimSms' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Show MIM Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@storeMimSms' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Update MIM Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@msegat' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Show MSEGAT Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@storeMsegat' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Update MSEGAT Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@sparrow' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Show Sparrow Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@storeSparrow' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Update Sparrow Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@zender' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Show Zender Configuration',
            ],
            'App\\Http\\Controllers\\SmsConfigurationController@storeZender' => [
                'groups' => 'admin_panel/web/sms_configuration',
                'alias' => 'Update Zender Configuration',
            ],
            'App\\Http\\Controllers\\BarcodeController@product' => [
                'groups' => 'admin_panel/web/barcode_management',
                'alias' => 'Show Product Barcode',
            ],
            'App\\Http\\Controllers\\BarcodeController@settings' => [
                'groups' => 'admin_panel/web/barcode_management',
                'alias' => 'Barcode Settings',
            ],
            'App\\Http\\Controllers\\BarcodeController@search' => [
                'groups' => 'admin_panel/web/barcode_management',
                'alias' => 'Search Barcode',
            ],
            'App\\Http\\Controllers\\NotificationController@headerNotification' => [
                'groups' => 'admin_panel/web/notification_management',
                'alias' => 'Header Notifications (Ajax Load)',
            ],
            'App\\Http\\Controllers\\NotificationController@view' => [
                'groups' => 'admin_panel/web/notification_management',
                'alias' => 'View Notification',
            ],
            'App\\Http\\Controllers\\NotificationController@updateSetting' => [
                'groups' => 'admin_panel/web/notification_management',
                'alias' => 'Update Notification Settings',
            ],
            'App\\Http\\Controllers\\ThemeController@index' => [
                'groups' => 'admin_panel/web/theme_management',
                'alias' => 'Theme List (Deprecated)',
            ],
            'App\\Http\\Controllers\\ThemeController@active' => [
                'groups' => 'admin_panel/web/theme_management',
                'alias' => 'Activate Theme (Deprecated)',
            ],
            'App\\Http\\Controllers\\CurrencySettingsController@index' => [
                'groups' => 'admin_panel/web/currency_settings',
                'alias' => 'Show Currency List',
            ],
            'App\\Http\\Controllers\\CurrencySettingsController@store' => [
                'groups' => 'admin_panel/web/currency_settings',
                'alias' => 'Store Currency',
            ],
            'App\\Http\\Controllers\\CurrencySettingsController@edit' => [
                'groups' => 'admin_panel/web/currency_settings',
                'alias' => 'Edit Currency',
            ],
            'App\\Http\\Controllers\\CurrencySettingsController@update' => [
                'groups' => 'admin_panel/web/currency_settings',
                'alias' => 'Update Currency',
            ],
            'App\\Http\\Controllers\\CurrencySettingsController@destroy' => [
                'groups' => 'admin_panel/web/currency_settings',
                'alias' => 'Delete Currency',
            ],
            'App\\Http\\Controllers\\CurrencySettingsController@exchangeUpdate' => [
                'groups' => 'admin_panel/web/currency_settings',
                'alias' => 'Exchange Rate Update',
            ],

            'App\\Http\\Controllers\\Vendor\\AttributeController@getAttribute' => [
                'groups' => 'vendor_panel/web/attribute_management',
                'alias'  => 'Get Vendor Attribute',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeController@pdf' => [
                'groups' => 'vendor_panel/web/attribute_management',
                'alias'  => 'Export Vendor Attribute PDF',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeController@csv' => [
                'groups' => 'vendor_panel/web/attribute_management',
                'alias'  => 'Export Vendor Attribute CSV',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeController@suggestion' => [
                'groups' => 'vendor_panel/web/attribute_management',
                'alias'  => 'Vendor Attribute Suggestions',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeController@assignAttribute' => [
                'groups' => 'vendor_panel/web/attribute_management',
                'alias'  => 'Assign Vendor Attribute',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeGroupController@index' => [
                'groups' => 'vendor_panel/web/attribute_group_management_(Deprecated)',
                'alias'  => 'Vendor Attribute Group List',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeGroupController@create' => [
                'groups' => 'vendor_panel/web/attribute_group_management_(Deprecated)',
                'alias'  => 'Create Vendor Attribute Group',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeGroupController@store' => [
                'groups' => 'vendor_panel/web/attribute_group_management_(Deprecated)',
                'alias'  => 'Store Vendor Attribute Group',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeGroupController@edit' => [
                'groups' => 'vendor_panel/web/attribute_group_management_(Deprecated)',
                'alias'  => 'Edit Vendor Attribute Group',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeGroupController@update' => [
                'groups' => 'vendor_panel/web/attribute_group_management_(Deprecated)',
                'alias'  => 'Update Vendor Attribute Group',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeGroupController@destroy' => [
                'groups' => 'vendor_panel/web/attribute_group_management_(Deprecated)',
                'alias'  => 'Delete Vendor Attribute Group',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeGroupController@pdf' => [
                'groups' => 'vendor_panel/web/attribute_group_management_(Deprecated)',
                'alias'  => 'Export Vendor Attribute Group PDF',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeGroupController@csv' => [
                'groups' => 'vendor_panel/web/attribute_group_management_(Deprecated)',
                'alias'  => 'Export Vendor Attribute Group CSV',
            ],

            'App\\Http\\Controllers\\UnitController@index' => [
                'groups' => 'admin_panel/web/units',
                'alias'  => 'Unit List',
            ],
            'App\\Http\\Controllers\\UnitController@store' => [
                'groups' => 'admin_panel/web/units',
                'alias'  => 'Create/Store Unit',
            ],
            'App\\Http\\Controllers\\UnitController@update' => [
                'groups' => 'admin_panel/web/units',
                'alias'  => 'Update Unit',
            ],
            'App\\Http\\Controllers\\UnitController@destroy' => [
                'groups' => 'admin_panel/web/units',
                'alias'  => 'Delete Unit',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerController@index' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Customer List',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerController@create' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Create Customer',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerController@store' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Store Customer',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerController@edit' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Edit Customer',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerController@update' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Update Customer',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerController@destroy' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Delete Customer',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerController@findCustomerByVendor' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Find Customer By Vendor',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerController@ledger' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Customer Ledger',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerController@findUser' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Find User',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerController@payment' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Customer Payment',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerController@paymentStore' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Store Customer Payment',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerAddressController@index' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Customer Address List',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerAddressController@create' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Create Customer Address',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerAddressController@store' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Store Customer Address',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerAddressController@edit' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Edit Customer Address',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerAddressController@update' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Update Customer Address',
            ],
            'App\\Http\\Controllers\\Vendor\\CustomerAddressController@destroy' => [
                'groups' => 'vendor_panel/web/customer_management',
                'alias'  => 'Delete Customer Address',
            ],

            'Modules\\Inventory\\Http\\Controllers\\InventoryController@index' => [
                'groups' => 'admin_panel/web/inventory_management',
                'alias'  => 'Stock List',
            ],
            'Modules\\Inventory\\Http\\Controllers\\InventoryController@adjust' => [
                'groups' => 'admin_panel/web/inventory_management',
                'alias'  => 'Stock Adjust',
            ],
            'Modules\\Inventory\\Http\\Controllers\\InventoryController@settings' => [
                'groups' => 'admin_panel/web/inventory_management',
                'alias'  => 'Inventory Settings',
            ],
            'Modules\\Inventory\\Http\\Controllers\\InventoryController@transaction' => [
                'groups' => 'admin_panel/web/inventory_management',
                'alias'  => 'Inventory Transaction',
            ],
            'Modules\\Inventory\\Http\\Controllers\\LocationController@index' => [
                'groups' => 'admin_panel/web/location_management',
                'alias'  => 'Location List',
            ],
            'Modules\\Inventory\\Http\\Controllers\\LocationController@create' => [
                'groups' => 'admin_panel/web/location_management',
                'alias'  => 'Create Location',
            ],
            'Modules\\Inventory\\Http\\Controllers\\LocationController@store' => [
                'groups' => 'admin_panel/web/location_management',
                'alias'  => 'Store Location',
            ],
            'Modules\\Inventory\\Http\\Controllers\\LocationController@edit' => [
                'groups' => 'admin_panel/web/location_management',
                'alias'  => 'Edit Location',
            ],
            'Modules\\Inventory\\Http\\Controllers\\LocationController@update' => [
                'groups' => 'admin_panel/web/location_management',
                'alias'  => 'Update Location',
            ],
            'Modules\\Inventory\\Http\\Controllers\\LocationController@destroy' => [
                'groups' => 'admin_panel/web/location_management',
                'alias'  => 'Delete Location',
            ],
            'Modules\\Inventory\\Http\\Controllers\\LocationController@vendorLocation' => [
                'groups' => 'admin_panel/web/location_management',
                'alias'  => 'Get Vendor Location',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@index' => [
                'groups' => 'admin_panel/web/purchase_management',
                'alias'  => 'Purchase List',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@create' => [
                'groups' => 'admin_panel/web/purchase_management',
                'alias'  => 'Create Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@store' => [
                'groups' => 'admin_panel/web/purchase_management',
                'alias'  => 'Store Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@edit' => [
                'groups' => 'admin_panel/web/purchase_management',
                'alias'  => 'Edit Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@update' => [
                'groups' => 'admin_panel/web/purchase_management',
                'alias'  => 'Update Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@destroy' => [
                'groups' => 'admin_panel/web/purchase_management',
                'alias'  => 'Delete Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@search' => [
                'groups' => 'admin_panel/web/purchase_management',
                'alias'  => 'Search Product in Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@findSupplier' => [
                'groups' => 'admin_panel/web/purchase_management',
                'alias'  => 'Find Supplier',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@findLocation' => [
                'groups' => 'admin_panel/web/purchase_management',
                'alias'  => 'Find Location',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@findVendor' => [
                'groups' => 'admin_panel/web/purchase_management',
                'alias'  => 'Find Vendor',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@receive' => [
                'groups' => 'admin_panel/web/purchase_management',
                'alias'  => 'Receive Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\PurchaseController@receiveStore' => [
                'groups' => 'admin_panel/web/purchase_management',
                'alias'  => 'Receive Purchase Store',
            ],
            'Modules\\Inventory\\Http\\Controllers\\SupplierController@index' => [
                'groups' => 'admin_panel/web/supplier_management',
                'alias'  => 'Supplier List',
            ],
            'Modules\\Inventory\\Http\\Controllers\\SupplierController@create' => [
                'groups' => 'admin_panel/web/supplier_management',
                'alias'  => 'Create Supplier',
            ],
            'Modules\\Inventory\\Http\\Controllers\\SupplierController@store' => [
                'groups' => 'admin_panel/web/supplier_management',
                'alias'  => 'Store Supplier',
            ],
            'Modules\\Inventory\\Http\\Controllers\\SupplierController@edit' => [
                'groups' => 'admin_panel/web/supplier_management',
                'alias'  => 'Edit Supplier',
            ],
            'Modules\\Inventory\\Http\\Controllers\\SupplierController@update' => [
                'groups' => 'admin_panel/web/supplier_management',
                'alias'  => 'Update Supplier',
            ],
            'Modules\\Inventory\\Http\\Controllers\\SupplierController@destroy' => [
                'groups' => 'admin_panel/web/supplier_management',
                'alias'  => 'Delete Supplier',
            ],
            'Modules\\Inventory\\Http\\Controllers\\TransferController@index' => [
                'groups' => 'admin_panel/web/transfer_management',
                'alias'  => 'Transfer List',
            ],
            'Modules\\Inventory\\Http\\Controllers\\TransferController@create' => [
                'groups' => 'admin_panel/web/transfer_management',
                'alias'  => 'Create Transfer',
            ],
            'Modules\\Inventory\\Http\\Controllers\\TransferController@store' => [
                'groups' => 'admin_panel/web/transfer_management',
                'alias'  => 'Store Transfer',
            ],
            'Modules\\Inventory\\Http\\Controllers\\TransferController@search' => [
                'groups' => 'admin_panel/web/transfer_management',
                'alias'  => 'Search Product in Transfer',
            ],
            'Modules\\Inventory\\Http\\Controllers\\TransferController@edit' => [
                'groups' => 'admin_panel/web/transfer_management',
                'alias'  => 'Edit Transfer',
            ],
            'Modules\\Inventory\\Http\\Controllers\\TransferController@update' => [
                'groups' => 'admin_panel/web/transfer_management',
                'alias'  => 'Update Transfer',
            ],
            'Modules\\Inventory\\Http\\Controllers\\TransferController@destroy' => [
                'groups' => 'admin_panel/web/transfer_management',
                'alias'  => 'Delete Transfer',
            ],
            'Modules\\Inventory\\Http\\Controllers\\TransferController@receive' => [
                'groups' => 'admin_panel/web/transfer_management',
                'alias'  => 'Receive Transfer',
            ],
            'Modules\\Inventory\\Http\\Controllers\\TransferController@receiveStore' => [
                'groups' => 'admin_panel/web/transfer_management',
                'alias'  => 'Receive Transfer Store',
            ],

            'Modules\\Inventory\\Http\\Controllers\\Vendor\\InventoryController@index' => [
                'groups' => 'vendor_panel/web/inventory_management',
                'alias'  => 'Stock List',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\InventoryController@adjust' => [
                'groups' => 'vendor_panel/web/inventory_management',
                'alias'  => 'Stock Adjust',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\InventoryController@transaction' => [
                'groups' => 'vendor_panel/web/inventory_management',
                'alias'  => 'Inventory Transactions',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\LocationController@index' => [
                'groups' => 'vendor_panel/web/location_management',
                'alias'  => 'Location List',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\LocationController@create' => [
                'groups' => 'vendor_panel/web/location_management',
                'alias'  => 'Create Location',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\LocationController@store' => [
                'groups' => 'vendor_panel/web/location_management',
                'alias'  => 'Store Location',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\LocationController@edit' => [
                'groups' => 'vendor_panel/web/location_management',
                'alias'  => 'Edit Location',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\LocationController@update' => [
                'groups' => 'vendor_panel/web/location_management',
                'alias'  => 'Update Location',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\LocationController@destroy' => [
                'groups' => 'vendor_panel/web/location_management',
                'alias'  => 'Delete Location',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\LocationController@vendorLocation' => [
                'groups' => 'vendor_panel/web/location_management',
                'alias'  => 'Vendor Location',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@index' => [
                'groups' => 'vendor_panel/web/purchase_management',
                'alias'  => 'Purchase List',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@create' => [
                'groups' => 'vendor_panel/web/purchase_management',
                'alias'  => 'Create Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@store' => [
                'groups' => 'vendor_panel/web/purchase_management',
                'alias'  => 'Store Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@edit' => [
                'groups' => 'vendor_panel/web/purchase_management',
                'alias'  => 'Edit Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@update' => [
                'groups' => 'vendor_panel/web/purchase_management',
                'alias'  => 'Update Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@destroy' => [
                'groups' => 'vendor_panel/web/purchase_management',
                'alias'  => 'Delete Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@search' => [
                'groups' => 'vendor_panel/web/purchase_management',
                'alias'  => 'Search Product in Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@findSupplier' => [
                'groups' => 'vendor_panel/web/purchase_management',
                'alias'  => 'Find Supplier (Ajax) in Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@findLocation' => [
                'groups' => 'vendor_panel/web/purchase_management',
                'alias'  => 'Find Location (Ajax) in Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@receive' => [
                'groups' => 'vendor_panel/web/purchase_management',
                'alias'  => 'Receive Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@receiveStore' => [
                'groups' => 'vendor_panel/web/purchase_management',
                'alias'  => 'Store Receive Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@index' => [
                'groups' => 'vendor_panel/web/supplier_management',
                'alias'  => 'Supplier List',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@create' => [
                'groups' => 'vendor_panel/web/supplier_management',
                'alias'  => 'Create Supplier',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@store' => [
                'groups' => 'vendor_panel/web/supplier_management',
                'alias'  => 'Store Supplier',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@edit' => [
                'groups' => 'vendor_panel/web/supplier_management',
                'alias'  => 'Edit Supplier',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@update' => [
                'groups' => 'vendor_panel/web/supplier_management',
                'alias'  => 'Update Supplier',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@destroy' => [
                'groups' => 'vendor_panel/web/supplier_management',
                'alias'  => 'Delete Supplier',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\TransferController@index' => [
                'groups' => 'vendor_panel/web/transfer_management',
                'alias'  => 'Transfer List',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\TransferController@create' => [
                'groups' => 'vendor_panel/web/transfer_management',
                'alias'  => 'Create Transfer',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\TransferController@store' => [
                'groups' => 'vendor_panel/web/transfer_management',
                'alias'  => 'Store Transfer',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\TransferController@search' => [
                'groups' => 'vendor_panel/web/transfer_management',
                'alias'  => 'Search Product in Transfer',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\TransferController@edit' => [
                'groups' => 'vendor_panel/web/transfer_management',
                'alias'  => 'Edit Transfer',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\TransferController@update' => [
                'groups' => 'vendor_panel/web/transfer_management',
                'alias'  => 'Update Transfer',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\TransferController@destroy' => [
                'groups' => 'vendor_panel/web/transfer_management',
                'alias'  => 'Delete Transfer',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\TransferController@receive' => [
                'groups' => 'vendor_panel/web/transfer_management',
                'alias'  => 'Receive Transfer',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\TransferController@receiveStore' => [
                'groups' => 'vendor_panel/web/transfer_management',
                'alias'  => 'Store Receive Transfer',
            ],

            'Modules\\Delivery\\Http\\Controllers\\Admin\\DeliveryController@settings' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias'  => 'Delivery Settings',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Admin\\DeliveryController@settingStore' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias'  => 'Store Delivery Settings',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController@index' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias'  => 'Withdrawal List',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController@edit' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias'  => 'Edit Withdrawal',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController@update' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias'  => 'Update Withdrawal',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController@pdf' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias'  => 'Withdrawal PDF',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@index' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias'  => 'Carrier List',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@create' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias'  => 'Create Carrier',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@store' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias'  => 'Store Carrier',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@show' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias'  => 'Show Carrier',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@edit' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias'  => 'Edit Carrier',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@update' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias'  => 'Update Carrier',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Admin\\CarrierController@destroy' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias'  => 'Delete Carrier',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Admin\\DeliveryController@updatePassword' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias'  => 'Update Delivery Password',
            ],

            'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@logout' => [
                'groups' => 'delivery_panel/web/profile_management',
                'alias'  => 'Carrier Logout',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\DashboardController@index' => [
                'groups' => 'delivery_panel/web/dashboard',
                'alias'  => 'Dashboard Overview',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\DashboardController@status' => [
                'groups' => 'delivery_panel/web/dashboard',
                'alias'  => 'Change Status',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@earning' => [
                'groups' => 'delivery_panel/web/profile_management',
                'alias'  => 'View Earnings',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@profile' => [
                'groups' => 'delivery_panel/web/profile_management',
                'alias'  => 'Show Profile',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@updateProfile' => [
                'groups' => 'delivery_panel/web/profile_management',
                'alias'  => 'Update Profile',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@updatePassword' => [
                'groups' => 'delivery_panel/web/profile_management',
                'alias'  => 'Update Password',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\ProfileController@activity' => [
                'groups' => 'delivery_panel/web/profile_management',
                'alias'  => 'Profile Activity',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\WithdrawalController@index' => [
                'groups' => 'delivery_panel/web/withdrawal_management',
                'alias'  => 'Withdrawal List',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\WithdrawalController@setting' => [
                'groups' => 'delivery_panel/web/withdrawal_management',
                'alias'  => 'Withdrawal Setting',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\WithdrawalController@withdraw' => [
                'groups' => 'delivery_panel/web/withdrawal_management',
                'alias'  => 'Withdraw Request',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@assign' => [
                'groups' => 'delivery_panel/web/order_management',
                'alias'  => 'Assign Order',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@pickup' => [
                'groups' => 'delivery_panel/web/order_management',
                'alias'  => 'Pickup Order',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@delivered' => [
                'groups' => 'delivery_panel/web/order_management',
                'alias'  => 'Order Delivered',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@completed' => [
                'groups' => 'delivery_panel/web/order_management',
                'alias'  => 'Order Completed',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@show' => [
                'groups' => 'delivery_panel/web/order_management',
                'alias'  => 'Show Order',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@changeStatus' => [
                'groups' => 'delivery_panel/web/order_management',
                'alias'  => 'Change Order Status',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\OrderController@print' => [
                'groups' => 'delivery_panel/web/order_management',
                'alias'  => 'Print Order',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\MediaManagerController@upload' => [
                'groups' => 'delivery_panel/web/media_management',
                'alias'  => 'Upload Media',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\MediaManagerController@paginateData' => [
                'groups' => 'delivery_panel/web/media_management',
                'alias'  => 'Paginate Media',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Carrier\\MediaManagerController@sortFiles' => [
                'groups' => 'delivery_panel/web/media_management',
                'alias'  => 'Sort Media',
            ],

            'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@view' => [
                'groups' => 'vendor_panel/web/inventory_management',
                'alias'  => 'View Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@print' => [
                'groups' => 'vendor_panel/web/inventory_management',
                'alias'  => 'Print Purchase',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\PurchaseController@payment' => [
                'groups' => 'vendor_panel/web/inventory_management',
                'alias'  => 'Purchase Payment',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@ledger' => [
                'groups' => 'vendor_panel/web/inventory_management',
                'alias'  => 'Supplier Ledger',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@payment' => [
                'groups' => 'vendor_panel/web/inventory_management',
                'alias'  => 'Supplier Payment',
            ],
            'Modules\\Inventory\\Http\\Controllers\\Vendor\\SupplierController@paymentStore' => [
                'groups' => 'vendor_panel/web/inventory_management',
                'alias'  => 'Store Supplier Payment',
            ],

            'App\\Http\\Controllers\\DataTableController@index' => [
                'groups' => 'admin_panel/web/data_table_management',
                'alias'  => 'Data Table settings',
            ],
            'App\\Http\\Controllers\\DataTableController@store' => [
                'groups' => 'admin_panel/web/data_table_management',
                'alias'  => 'Update Data Table settings',
            ],

            'Modules\\Dummy\\Http\\Controllers\\DummyController@index' => [
                'groups' => 'admin_panel/web/dummy_management',
                'alias'  => 'Dummy Confirmation',
            ],
            'Modules\\Dummy\\Http\\Controllers\\DummyController@store' => [
                'groups' => 'admin_panel/web/dummy_management',
                'alias'  => 'Dummy Import',
            ],

            'App\\Http\\Controllers\\Api\\RoleController@index' => [
                'groups' => 'admin_panel/api/role_management_(Deprecated)',
                'alias'  => 'Role List',
            ],
            'App\\Http\\Controllers\\Api\\RoleController@store' => [
                'groups' => 'admin_panel/api/role_management_(Deprecated)',
                'alias'  => 'Create Role',
            ],
            'App\\Http\\Controllers\\Api\\RoleController@detail' => [
                'groups' => 'admin_panel/api/role_management_(Deprecated)',
                'alias'  => 'View Role',
            ],
            'App\\Http\\Controllers\\Api\\RoleController@update' => [
                'groups' => 'admin_panel/api/role_management_(Deprecated)',
                'alias'  => 'Update Role',
            ],
            'App\\Http\\Controllers\\Api\\RoleController@destroy' => [
                'groups' => 'admin_panel/api/role_management_(Deprecated)',
                'alias'  => 'Delete Role',
            ],
            'App\\Http\\Controllers\\Api\\MailTemplateController@index' => [
                'groups' => 'admin_panel/api/email_management_(Deprecated)',
                'alias'  => 'Mail Template List',
            ],
            'App\\Http\\Controllers\\Api\\MailTemplateController@store' => [
                'groups' => 'admin_panel/api/email_management_(Deprecated)',
                'alias'  => 'Create Mail Template',
            ],
            'App\\Http\\Controllers\\Api\\MailTemplateController@detail' => [
                'groups' => 'admin_panel/api/email_management_(Deprecated)',
                'alias'  => 'View Mail Template Detail',
            ],
            'App\\Http\\Controllers\\Api\\MailTemplateController@update' => [
                'groups' => 'admin_panel/api/email_management_(Deprecated)',
                'alias'  => 'Update Mail Template',
            ],
            'App\\Http\\Controllers\\Api\\MailTemplateController@destroy' => [
                'groups' => 'admin_panel/api/email_management_(Deprecated)',
                'alias'  => 'Delete Mail Template',
            ],
            'App\\Http\\Controllers\\Api\\EmailConfigurationController@index' => [
                'groups' => 'admin_panel/api/email_management_(Deprecated)',
                'alias'  => 'Email Configuration',
            ],
            'App\\Http\\Controllers\\Api\\CurrencyController@index' => [
                'groups' => 'admin_panel/api/currency_management_(Deprecated)',
                'alias'  => 'Currency List',
            ],
            'App\\Http\\Controllers\\Api\\CurrencyController@store' => [
                'groups' => 'admin_panel/api/currency_management_(Deprecated)',
                'alias'  => 'Add Currency',
            ],
            'App\\Http\\Controllers\\Api\\CurrencyController@update' => [
                'groups' => 'admin_panel/api/currency_management_(Deprecated)',
                'alias'  => 'Update Currency',
            ],
            'App\\Http\\Controllers\\Api\\CurrencyController@detail' => [
                'groups' => 'admin_panel/api/currency_management_(Deprecated)',
                'alias'  => 'View Currency Detail',
            ],
            'App\\Http\\Controllers\\Api\\CurrencyController@destroy' => [
                'groups' => 'admin_panel/api/currency_management_(Deprecated)',
                'alias'  => 'Delete Currency',
            ],

            'App\\Http\\Controllers\\Api\\VendorController@index' => [
                'groups' => 'admin_panel/api/vendor_management_(Deprecated)',
                'alias'  => 'Vendor List',
            ],
            'App\\Http\\Controllers\\Api\\VendorController@store' => [
                'groups' => 'admin_panel/api/vendor_management_(Deprecated)',
                'alias'  => 'Add Vendor',
            ],
            'App\\Http\\Controllers\\Api\\VendorController@update' => [
                'groups' => 'admin_panel/api/vendor_management_(Deprecated)',
                'alias'  => 'Update Vendor',
            ],
            'App\\Http\\Controllers\\Api\\VendorController@detail' => [
                'groups' => 'admin_panel/api/vendor_management_(Deprecated)',
                'alias'  => 'View Vendor Detail',
            ],
            'App\\Http\\Controllers\\Api\\VendorController@destroy' => [
                'groups' => 'admin_panel/api/vendor_management_(Deprecated)',
                'alias'  => 'Delete Vendor',
            ],

            'App\\Http\\Controllers\\Site\\UserController@edit' => [
                'groups' => 'customer_panel/web/user_management',
                'alias'  => 'Edit User Profile',
            ],
            'App\\Http\\Controllers\\Site\\UserController@removeImage' => [
                'groups' => 'customer_panel/web/user_management',
                'alias' => 'Remove Profile Image',
            ],
            'App\\Http\\Controllers\\Site\\UserController@update' => [
                'groups' => 'customer_panel/web/user_management',
                'alias'  => 'Update User Profile',
            ],
            'App\\Http\\Controllers\\Site\\UserController@updatePassword' => [
                'groups' => 'customer_panel/web/user_management',
                'alias'  => 'Update User Password',
            ],
            'App\\Http\\Controllers\\Site\\UserController@setting' => [
                'groups' => 'customer_panel/web/user_management',
                'alias'  => 'User Account Settings',
            ],
            'App\\Http\\Controllers\\Site\\UserController@destroy' => [
                'groups' => 'customer_panel/web/user_management',
                'alias'  => 'Delete User Account',
            ],
            'App\\Http\\Controllers\\Site\\UserController@activity' => [
                'groups' => 'customer_panel/web/user_management',
                'alias' => 'User Activity List',
            ],
            'App\\Http\\Controllers\\Site\\DownloadController@index' => [
                'groups' => 'customer_panel/web/order_management',
                'alias' => 'Download List',
            ],
            'App\\Http\\Controllers\\Site\\WishlistController@index' => [
                'groups' => 'customer_panel/web/wishlist_management',
                'alias'  => 'View Wishlist',
            ],
            'App\\Http\\Controllers\\Site\\WishlistController@store' => [
                'groups' => 'customer_panel/web/wishlist_management',
                'alias'  => 'Add To Wishlist',
            ],
            'App\\Http\\Controllers\\Site\\AddressController@index' => [
                'groups' => 'customer_panel/web/address_management',
                'alias'  => 'View Addresses',
            ],
            'App\\Http\\Controllers\\Site\\AddressController@create' => [
                'groups' => 'customer_panel/web/address_management',
                'alias'  => 'Add Address (Form)',
            ],
            'App\\Http\\Controllers\\Site\\AddressController@store' => [
                'groups' => 'customer_panel/web/address_management',
                'alias'  => 'Save New Address',
            ],
            'App\\Http\\Controllers\\Site\\AddressController@edit' => [
                'groups' => 'customer_panel/web/address_management',
                'alias'  => 'Edit Address',
            ],
            'App\\Http\\Controllers\\Site\\AddressController@update' => [
                'groups' => 'customer_panel/web/address_management',
                'alias'  => 'Update Address',
            ],
            'App\\Http\\Controllers\\Site\\AddressController@destroy' => [
                'groups' => 'customer_panel/web/address_management',
                'alias'  => 'Delete Address',
            ],
            'App\\Http\\Controllers\\Site\\AddressController@checkDefault' => [
                'groups' => 'customer_panel/web/address_management',
                'alias'  => 'Check Default Address',
            ],
            'App\\Http\\Controllers\\Site\\AddressController@makeDefault' => [
                'groups' => 'customer_panel/web/address_management',
                'alias'  => 'Make Default Address',
            ],
            'App\\Http\\Controllers\\Site\\ReviewController@index' => [
                'groups' => 'customer_panel/web/review_management',
                'alias'  => 'View Reviews',
            ],
            'App\\Http\\Controllers\\Site\\ReviewController@destroy' => [
                'groups' => 'customer_panel/web/review_management',
                'alias' => 'Delete Review',
            ],
            'App\\Http\\Controllers\\Api\\User\\ReviewController@destroy' => [
                'groups' => 'customer_panel/api/review_management',
                'alias' => 'Delete Review',
            ],
            'App\\Http\\Controllers\\Api\\User\\ReviewController@store' => [
                'groups' => 'customer_panel/api/review_management',
                'alias'  => 'Save Review',
            ],
            'App\\Http\\Controllers\\Api\\User\\ReviewController@update' => [
                'groups' => 'customer_panel/api/review_management',
                'alias'  => 'Update Review',
            ],
            'App\\Http\\Controllers\\Api\\User\\ReviewController@deleteFile' => [
                'groups' => 'customer_panel/api/review_management',
                'alias'  => 'Delete Review File',
            ],
            'App\\Http\\Controllers\\Site\\OrderController@index' => [
                'groups' => 'customer_panel/web/order_management',
                'alias'  => 'Order List',
            ],
            'App\\Http\\Controllers\\Site\\OrderController@orderDetails' => [
                'groups' => 'customer_panel/web/order_management',
                'alias'  => 'Order Details',
            ],
            'App\\Http\\Controllers\\Site\\OrderController@invoicePrint' => [
                'groups' => 'customer_panel/web/order_management',
                'alias' => 'Order Invoice Print',
            ],

            'App\\Http\\Controllers\\Vendor\\ReviewController@index' => [
                'groups' => 'vendor_panel/web/review_management',
                'alias'  => 'Review List',
            ],
            'App\\Http\\Controllers\\Vendor\\ReviewController@edit' => [
                'groups' => 'vendor_panel/web/review_management',
                'alias'  => 'Edit Review',
            ],
            'App\\Http\\Controllers\\Vendor\\ReviewController@view' => [
                'groups' => 'vendor_panel/web/review_management',
                'alias'  => 'View Single Review',
            ],
            'App\\Http\\Controllers\\Vendor\\ReviewController@update' => [
                'groups' => 'vendor_panel/web/review_management',
                'alias'  => 'Update Review',
            ],
            'App\\Http\\Controllers\\Vendor\\ReviewController@destroy' => [
                'groups' => 'vendor_panel/web/review_management',
                'alias'  => 'Delete Review',
            ],
            'App\\Http\\Controllers\\Vendor\\ReviewController@pdf' => [
                'groups' => 'vendor_panel/web/review_management',
                'alias'  => 'Export Reviews to PDF',
            ],
            'App\\Http\\Controllers\\Vendor\\ReviewController@csv' => [
                'groups' => 'vendor_panel/web/review_management',
                'alias'  => 'Export Reviews to CSV',
            ],

            'App\\Http\\Controllers\\Vendor\\WithdrawalController@index' => [
                'groups' => 'vendor_panel/web/withdrawal_management',
                'alias'  => 'Withdrawal List',
            ],
            'App\\Http\\Controllers\\Vendor\\WithdrawalController@setting' => [
                'groups' => 'vendor_panel/web/withdrawal_management',
                'alias'  => 'Withdrawal Settings',
            ],
            'App\\Http\\Controllers\\Vendor\\WithdrawalController@withdraw' => [
                'groups' => 'vendor_panel/web/withdrawal_management',
                'alias'  => 'Withdraw Money',
            ],
            'App\\Http\\Controllers\\Vendor\\WithdrawalController@pdf' => [
                'groups' => 'vendor_panel/web/withdrawal_management',
                'alias'  => 'Export Withdrawals to PDF',
            ],
            'App\\Http\\Controllers\\Vendor\\WithdrawalController@csv' => [
                'groups' => 'vendor_panel/web/withdrawal_management',
                'alias'  => 'Export Withdrawals to CSV',
            ],
            'App\\Http\\Controllers\\Vendor\\DashboardController@getUserData' => [
                'groups' => 'vendor_panel/web/dashboard',
                'alias'  => 'Get User Data (Ajax)',
            ],
            'App\\Http\\Controllers\\Vendor\\DashboardController@getProductData' => [
                'groups' => 'vendor_panel/web/dashboard',
                'alias'  => 'Get Product Data (Ajax)',
            ],
            'App\\Http\\Controllers\\Vendor\\DashboardController@mostSoldProducts' => [
                'groups' => 'vendor_panel/web/dashboard',
                'alias'  => 'Most Sold Products (Ajax)',
            ],
            'App\\Http\\Controllers\\Vendor\\DashboardController@mostActiveUsers' => [
                'groups' => 'vendor_panel/web/dashboard',
                'alias'  => 'Most Active Users (Ajax)',
            ],
            'App\\Http\\Controllers\\Vendor\\DashboardController@vendorStats' => [
                'groups' => 'vendor_panel/web/dashboard',
                'alias'  => 'Vendor Statistics (Ajax)',
            ],
            'App\\Http\\Controllers\\Vendor\\DashboardController@salesOfTheMonth' => [
                'groups' => 'vendor_panel/web/dashboard',
                'alias'  => 'Sales of the Month (Ajax)',
            ],

            'Modules\\Refund\\Http\\Controllers\\Site\\RefundController@index' => [
                'groups' => 'customer_panel/web/refund_management',
                'alias'  => 'Refund List',
            ],
            'Modules\\Refund\\Http\\Controllers\\Site\\RefundController@createRequest' => [
                'groups' => 'customer_panel/web/refund_management',
                'alias'  => 'Create Refund Request',
            ],
            'Modules\\Refund\\Http\\Controllers\\Site\\RefundController@refund' => [
                'groups' => 'customer_panel/web/refund_management',
                'alias'  => 'Send Refund Request',
            ],
            'Modules\\Refund\\Http\\Controllers\\Site\\RefundController@refundDetails' => [
                'groups' => 'customer_panel/web/refund_management',
                'alias'  => 'Refund Details',
            ],
            'Modules\\Refund\\Http\\Controllers\\Site\\RefundController@getProducts' => [
                'groups' => 'customer_panel/web/refund_management',
                'alias'  => 'Get Refund Products',
            ],
            'Modules\\Refund\\Http\\Controllers\\Site\\RefundProcessController@process' => [
                'groups' => 'customer_panel/web/refund_management',
                'alias'  => 'Process Refund',
            ],

            'Modules\\Refund\\Http\\Controllers\\Vendor\\RefundController@index' => [
                'groups' => 'vendor_panel/web/refund_management',
                'alias'  => 'Refund List',
            ],
            'Modules\\Refund\\Http\\Controllers\\Vendor\\RefundController@edit' => [
                'groups' => 'vendor_panel/web/refund_management',
                'alias'  => 'Edit Refund',
            ],
            'Modules\\Refund\\Http\\Controllers\\Vendor\\RefundController@update' => [
                'groups' => 'vendor_panel/web/refund_management',
                'alias'  => 'Update Refund',
            ],
            'Modules\\Refund\\Http\\Controllers\\Vendor\\RefundProcessController@process' => [
                'groups' => 'vendor_panel/web/refund_management',
                'alias'  => 'Process Refund',
            ],
            'Modules\\Refund\\Http\\Controllers\\Vendor\\RefundController@pdf' => [
                'groups' => 'vendor_panel/web/refund_management',
                'alias'  => 'Export Refunds to PDF',
            ],
            'Modules\\Refund\\Http\\Controllers\\Vendor\\RefundController@csv' => [
                'groups' => 'vendor_panel/web/refund_management',
                'alias'  => 'Export Refunds to CSV',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorTransactionController@index' => [
                'groups' => 'vendor_panel/web/vendor_transaction',
                'alias'  => 'Transaction List',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorTransactionController@pdf' => [
                'groups' => 'vendor_panel/web/vendor_transaction',
                'alias'  => 'Export Transactions to PDF',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorTransactionController@csv' => [
                'groups' => 'vendor_panel/web/vendor_transaction',
                'alias'  => 'Export Transactions to CSV',
            ],
            'App\\Http\\Controllers\\ProductSettingController@general' => [
                'groups' => 'admin_panel/web/settings/product_settings',
                'alias'  => 'General Product Settings',
            ],
            'App\\Http\\Controllers\\ProductSettingController@inventory' => [
                'groups' => 'admin_panel/web/settings/product_settings',
                'alias'  => 'Product Inventory Settings',
            ],
            'App\\Http\\Controllers\\ProductSettingController@vendor' => [
                'groups' => 'admin_panel/web/settings/product_settings',
                'alias'  => 'Vendor Product Settings',
            ],

            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneController@index' => [
                'groups' => 'admin_panel/api/shipping_zone_(Deprecated)',
                'alias'  => 'Shipping Zone List',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneController@store' => [
                'groups' => 'admin_panel/api/shipping_zone_(Deprecated)',
                'alias'  => 'Create Shipping Zone',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneController@detail' => [
                'groups' => 'admin_panel/api/shipping_zone_(Deprecated)',
                'alias'  => 'View Shipping Zone',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneController@update' => [
                'groups' => 'admin_panel/api/shipping_zone_(Deprecated)',
                'alias'  => 'Update Shipping Zone',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneController@destroy' => [
                'groups' => 'admin_panel/api/shipping_zone_(Deprecated)',
                'alias'  => 'Delete Shipping Zone',
            ],

            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingClassController@index' => [
                'groups' => 'admin_panel/api/shipping_class_(Deprecated)',
                'alias'  => 'Shipping Class List',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingClassController@store' => [
                'groups' => 'admin_panel/api/shipping_class_(Deprecated)',
                'alias'  => 'Create Shipping Class',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingClassController@detail' => [
                'groups' => 'admin_panel/api/shipping_class_(Deprecated)',
                'alias'  => 'View Shipping Class',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingClassController@update' => [
                'groups' => 'admin_panel/api/shipping_class_(Deprecated)',
                'alias'  => 'Update Shipping Class',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingClassController@destroy' => [
                'groups' => 'admin_panel/api/shipping_class_(Deprecated)',
                'alias'  => 'Delete Shipping Class',
            ],

            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingMethodController@index' => [
                'groups' => 'admin_panel/api/shipping_method_(Deprecated)',
                'alias'  => 'Shipping Method List',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingMethodController@detail' => [
                'groups' => 'admin_panel/api/shipping_method_(Deprecated)',
                'alias'  => 'View Shipping Method',
            ],

            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneGeolocaleController@index' => [
                'groups' => 'admin_panel/api/shipping_zone_geolocale_(Deprecated)',
                'alias'  => 'Shipping Zone Geolocale List',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneGeolocaleController@store' => [
                'groups' => 'admin_panel/api/shipping_zone_geolocale_(Deprecated)',
                'alias'  => 'Create Shipping Zone Geolocale',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneGeolocaleController@detail' => [
                'groups' => 'admin_panel/api/shipping_zone_geolocale_(Deprecated)',
                'alias'  => 'View Shipping Zone Geolocale',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneGeolocaleController@update' => [
                'groups' => 'admin_panel/api/shipping_zone_geolocale_(Deprecated)',
                'alias'  => 'Update Shipping Zone Geolocale',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneGeolocaleController@destroy' => [
                'groups' => 'admin_panel/api/shipping_zone_geolocale_(Deprecated)',
                'alias'  => 'Delete Shipping Zone Geolocale',
            ],

            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneClassController@index' => [
                'groups' => 'admin_panel/api/shipping_zone_class_(Deprecated)',
                'alias'  => 'Shipping Zone Class List',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneClassController@store' => [
                'groups' => 'admin_panel/api/shipping_zone_class_(Deprecated)',
                'alias'  => 'Create Shipping Zone Class',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneClassController@detail' => [
                'groups' => 'admin_panel/api/shipping_zone_class_(Deprecated)',
                'alias'  => 'View Shipping Zone Class',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneClassController@update' => [
                'groups' => 'admin_panel/api/shipping_zone_class_(Deprecated)',
                'alias'  => 'Update Shipping Zone Class',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneClassController@destroy' => [
                'groups' => 'admin_panel/api/shipping_zone_class_(Deprecated)',
                'alias'  => 'Delete Shipping Zone Class',
            ],

            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneMethodController@index' => [
                'groups' => 'admin_panel/api/shipping_zone_method_(Deprecated)',
                'alias'  => 'Shipping Zone Method List',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneMethodController@store' => [
                'groups' => 'admin_panel/api/shipping_zone_method_(Deprecated)',
                'alias'  => 'Create Shipping Zone Method',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneMethodController@detail' => [
                'groups' => 'admin_panel/api/shipping_zone_method_(Deprecated)',
                'alias'  => 'View Shipping Zone Method',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneMethodController@update' => [
                'groups' => 'admin_panel/api/shipping_zone_method_(Deprecated)',
                'alias'  => 'Update Shipping Zone Method',
            ],
            'Modules\\Shipping\\Http\\Controllers\\Api\\ShippingZoneMethodController@destroy' => [
                'groups' => 'admin_panel/api/shipping_zone_method_(Deprecated)',
                'alias'  => 'Delete Shipping Zone Method',
            ],

            'Modules\\Tax\\Http\\Controllers\\Api\\TaxClassController@index' => [
                'groups' => 'admin_panel/api/tax_class_(Deprecated)',
                'alias'  => 'Tax Class List',
            ],
            'Modules\\Tax\\Http\\Controllers\\Api\\TaxClassController@store' => [
                'groups' => 'admin_panel/api/tax_class_(Deprecated)',
                'alias'  => 'Create Tax Class',
            ],
            'Modules\\Tax\\Http\\Controllers\\Api\\TaxClassController@destroy' => [
                'groups' => 'admin_panel/api/tax_class_(Deprecated)',
                'alias'  => 'Delete Tax Class',
            ],

            'Modules\\Tax\\Http\\Controllers\\Api\\TaxRateController@index' => [
                'groups' => 'admin_panel/api/tax_rate_(Deprecated)',
                'alias'  => 'Tax Rate List',
            ],
            'Modules\\Tax\\Http\\Controllers\\Api\\TaxRateController@store' => [
                'groups' => 'admin_panel/api/tax_rate_(Deprecated)',
                'alias'  => 'Create Tax Rate',
            ],
            'Modules\\Tax\\Http\\Controllers\\Api\\TaxRateController@detail' => [
                'groups' => 'admin_panel/api/tax_rate_(Deprecated)',
                'alias'  => 'View Tax Rate',
            ],
            'Modules\\Tax\\Http\\Controllers\\Api\\TaxRateController@update' => [
                'groups' => 'admin_panel/api/tax_rate_(Deprecated)',
                'alias'  => 'Update Tax Rate',
            ],
            'Modules\\Tax\\Http\\Controllers\\Api\\TaxRateController@destroy' => [
                'groups' => 'admin_panel/api/tax_rate_(Deprecated)',
                'alias'  => 'Delete Tax Rate',
            ],

            'App\\Http\\Controllers\\SmsConfigurationController@index' => [
                'groups' => 'admin_panel/web/sms_management',
                'alias'  => 'View SMS Configuration',
            ],
            'App\\Http\\Controllers\\SmsTemplateController@index' => [
                'groups' => 'admin_panel/web/sms_management',
                'alias'  => 'SMS Template List',
            ],
            'App\\Http\\Controllers\\SmsTemplateController@edit' => [
                'groups' => 'admin_panel/web/sms_management',
                'alias'  => 'Edit SMS Template',
            ],
            'App\\Http\\Controllers\\SmsTemplateController@update' => [
                'groups' => 'admin_panel/web/sms_management',
                'alias'  => 'Update SMS Template',
            ],
            'App\\Http\\Controllers\\NotificationController@index' => [
                'groups' => 'admin_panel/web/notification_management',
                'alias'  => 'View Notifications',
            ],
            'App\\Http\\Controllers\\NotificationController@destroy' => [
                'groups' => 'admin_panel/web/notification_management',
                'alias'  => 'Delete Notification',
            ],
            'App\\Http\\Controllers\\NotificationController@markAsRead' => [
                'groups' => 'admin_panel/web/notification_management',
                'alias'  => 'Mark as Read',
            ],
            'App\\Http\\Controllers\\NotificationController@markAsUnread' => [
                'groups' => 'admin_panel/web/notification_management',
                'alias'  => 'Mark as Unread',
            ],
            'App\\Http\\Controllers\\NotificationController@markAsReadAll' => [
                'groups' => 'admin_panel/web/notification_management',
                'alias'  => 'Mark All as Read',
            ],

            'App\\Http\\Controllers\\Vendor\\BarcodeController@product' => [
                'groups' => 'vendor_panel/web/barcode_management',
                'alias'  => 'Product Barcode',
            ],
            'App\\Http\\Controllers\\Vendor\\BarcodeController@search' => [
                'groups' => 'vendor_panel/web/barcode_management',
                'alias'  => 'Search Barcode',
            ],
            'App\\Http\\Controllers\\Vendor\\CategoryController@index' => [
                'groups' => 'vendor_panel/web/category_management',
                'alias'  => 'Category List',
            ],
            'App\\Http\\Controllers\\Vendor\\CategoryController@getData' => [
                'groups' => 'vendor_panel/web/category_management',
                'alias'  => 'Get Category Data',
            ],
            'App\\Http\\Controllers\\Vendor\\CategoryController@store' => [
                'groups' => 'vendor_panel/web/category_management',
                'alias'  => 'Create Category',
            ],
            'App\\Http\\Controllers\\Vendor\\CategoryController@edit' => [
                'groups' => 'vendor_panel/web/category_management',
                'alias'  => 'Edit Category',
            ],
            'App\\Http\\Controllers\\Vendor\\CategoryController@update' => [
                'groups' => 'vendor_panel/web/category_management',
                'alias'  => 'Update Category',
            ],
            'App\\Http\\Controllers\\Vendor\\CategoryController@destroy' => [
                'groups' => 'vendor_panel/web/category_management',
                'alias'  => 'Delete Category',
            ],
            'App\\Http\\Controllers\\Vendor\\CategoryController@getParentData' => [
                'groups' => 'vendor_panel/web/category_management',
                'alias'  => 'Get Parent Category Data',
            ],
            'App\\Http\\Controllers\\Vendor\\CategoryController@moveNode' => [
                'groups' => 'vendor_panel/web/category_management',
                'alias'  => 'Move Category',
            ],
            'App\\Http\\Controllers\\Vendor\\CategoryController@suggestion' => [
                'groups' => 'vendor_panel/web/category_management',
                'alias'  => 'Category Suggestion',
            ],
            'App\\Http\\Controllers\\Vendor\\CategoryController@assignCategory' => [
                'groups' => 'vendor_panel/web/category_management',
                'alias'  => 'Assign Category',
            ],
            'App\\Http\\Controllers\\Vendor\\NotificationController@index' => [
                'groups' => 'vendor_panel/web/notification_management',
                'alias'  => 'View Vendor Notifications',
            ],
            'App\\Http\\Controllers\\Vendor\\NotificationController@destroy' => [
                'groups' => 'vendor_panel/web/notification_management',
                'alias'  => 'Delete Vendor Notification',
            ],
            'App\\Http\\Controllers\\Vendor\\NotificationController@markAsRead' => [
                'groups' => 'vendor_panel/web/notification_management',
                'alias'  => 'Mark Vendor Notification as Read',
            ],
            'App\\Http\\Controllers\\Vendor\\NotificationController@markAsUnread' => [
                'groups' => 'vendor_panel/web/notification_management',
                'alias'  => 'Mark Vendor Notification as Unread',
            ],
            'App\\Http\\Controllers\\Vendor\\NotificationController@markAsReadAll' => [
                'groups' => 'vendor_panel/web/notification_management',
                'alias'  => 'Mark All Vendor Notifications as Read',
            ],
            'App\\Http\\Controllers\\Vendor\\NotificationController@headerNotification' => [
                'groups' => 'vendor_panel/web/notification_management',
                'alias'  => 'Vendor Header Notification',
            ],

            'App\\Http\\Controllers\\Site\\NotificationController@index' => [
                'groups' => 'customer_panel/web/notification_management',
                'alias'  => 'View Site Notifications',
            ],
            'App\\Http\\Controllers\\Site\\NotificationController@destroy' => [
                'groups' => 'customer_panel/web/notification_management',
                'alias'  => 'Delete Site Notification',
            ],
            'App\\Http\\Controllers\\Site\\NotificationController@markAsRead' => [
                'groups' => 'customer_panel/web/notification_management',
                'alias'  => 'Mark Site Notification as Read',
            ],
            'App\\Http\\Controllers\\Site\\NotificationController@markAsUnread' => [
                'groups' => 'customer_panel/web/notification_management',
                'alias'  => 'Mark Site Notification as Unread',
            ],
            'App\\Http\\Controllers\\Site\\NotificationController@view' => [
                'groups' => 'customer_panel/web/notification_management',
                'alias'  => 'View Site Notification',
            ],

            'App\\Http\\Controllers\\Vendor\\BrandController@index' => [
                'groups' => 'vendor_panel/web/brand_management',
                'alias'  => 'Brand List',
            ],
            'App\\Http\\Controllers\\Vendor\\BrandController@create' => [
                'groups' => 'vendor_panel/web/brand_management',
                'alias'  => 'Create Brand',
            ],
            'App\\Http\\Controllers\\Vendor\\BrandController@store' => [
                'groups' => 'vendor_panel/web/brand_management',
                'alias'  => 'Store Brand',
            ],
            'App\\Http\\Controllers\\Vendor\\BrandController@edit' => [
                'groups' => 'vendor_panel/web/brand_management',
                'alias'  => 'Edit Brand',
            ],
            'App\\Http\\Controllers\\Vendor\\BrandController@update' => [
                'groups' => 'vendor_panel/web/brand_management',
                'alias'  => 'Update Brand',
            ],
            'App\\Http\\Controllers\\Vendor\\BrandController@destroy' => [
                'groups' => 'vendor_panel/web/brand_management',
                'alias'  => 'Delete Brand',
            ],
            'App\\Http\\Controllers\\Vendor\\BrandController@suggestion' => [
                'groups' => 'vendor_panel/web/brand_management',
                'alias'  => 'Brand Suggestions',
            ],
            'App\\Http\\Controllers\\Vendor\\BrandController@assignBrand' => [
                'groups' => 'vendor_panel/web/brand_management',
                'alias'  => 'Assign Brand',
            ],
            'App\\Http\\Controllers\\Vendor\\BrandController@pdf' => [
                'groups' => 'vendor_panel/web/brand_management',
                'alias'  => 'Export Brands to PDF',
            ],
            'App\\Http\\Controllers\\Vendor\\BrandController@csv' => [
                'groups' => 'vendor_panel/web/brand_management',
                'alias'  => 'Export Brands to CSV',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeController@index' => [
                'groups' => 'vendor_panel/web/attribute_management',
                'alias'  => 'Attribute List',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeController@create' => [
                'groups' => 'vendor_panel/web/attribute_management',
                'alias'  => 'Create Attribute',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeController@store' => [
                'groups' => 'vendor_panel/web/attribute_management',
                'alias'  => 'Store Attribute',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeController@edit' => [
                'groups' => 'vendor_panel/web/attribute_management',
                'alias'  => 'Edit Attribute',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeController@update' => [
                'groups' => 'vendor_panel/web/attribute_management',
                'alias'  => 'Update Attribute',
            ],
            'App\\Http\\Controllers\\Vendor\\AttributeController@destroy' => [
                'groups' => 'vendor_panel/web/attribute_management',
                'alias'  => 'Delete Attribute',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\POSController@index' => [
                'groups' => 'admin_+_vendor_panel/web/pos_management',
                'alias'  => 'POS Landing Page',
            ],


            'Modules\\Pos\\Http\\Controllers\\Vendor\\POSController@setup' => [
                'groups' => 'vendor_panel/web/pos_management',
                'alias'  => 'POS Setup',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\POSController@receipt' => [
                'groups' => 'vendor_panel/web/pos_management',
                'alias'  => 'POS Receipt Setting',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\POSController@paymentMethod' => [
                'groups' => 'vendor_panel/web/pos_management',
                'alias'  => 'POS Payment Method Setting',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\POSController@discount' => [
                'groups' => 'vendor_panel/web/pos_management',
                'alias'  => 'POS Discount Setting',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\TerminalController@index' => [
                'groups' => 'vendor_panel/web/pos_terminal_management',
                'alias'  => 'Terminal List',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\TerminalController@create' => [
                'groups' => 'vendor_panel/web/pos_terminal_management',
                'alias'  => 'Terminal Create',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\TerminalController@store' => [
                'groups' => 'vendor_panel/web/pos_terminal_management',
                'alias'  => 'Terminal Store',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\TerminalController@edit' => [
                'groups' => 'vendor_panel/web/pos_terminal_management',
                'alias'  => 'Terminal Edit',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\TerminalController@update' => [
                'groups' => 'vendor_panel/web/pos_terminal_management',
                'alias'  => 'Terminal Update',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\TerminalController@destroy' => [
                'groups' => 'vendor_panel/web/pos_terminal_management',
                'alias'  => 'Terminal Delete',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\ActivityController@index' => [
                'groups' => 'vendor_panel/web/pos_activity_management',
                'alias'  => 'Activity List',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\TerminalController@history' => [
                'groups' => 'vendor_panel/web/pos_terminal_management',
                'alias'  => 'Terminal History',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\OrderController@index' => [
                'groups' => 'vendor_panel/web/pos_order_management',
                'alias'  => 'POS Order List',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\SettingController@adminSettings' => [
                'groups' => 'pos_panel/api/settings',
                'alias'  => 'POS Admin Settings',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\SettingController@vendorSettings' => [
                'groups' => 'pos_panel/api/settings',
                'alias'  => 'POS Vendor Settings',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\BrandController@index' => [
                'groups' => 'pos_panel/api/brand',
                'alias'  => 'POS Brand List',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\TerminalController@canBypassTerminal' => [
                'groups' => 'pos_panel/api/terminal',
                'alias'  => 'Can Bypass Terminal',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\TerminalController@terminals' => [
                'groups' => 'pos_panel/api/terminal',
                'alias'  => 'POS Terminal List',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\TerminalController@login' => [
                'groups' => 'pos_panel/api/terminal',
                'alias'  => 'POS Terminal Login',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\TerminalController@logout' => [
                'groups' => 'pos_panel/api/terminal',
                'alias'  => 'POS Terminal Logout',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\TerminalController@currentSessionBalance' => [
                'groups' => 'pos_panel/api/terminal',
                'alias'  => 'POS Terminal Session Balance',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\TerminalController@isSessionActive' => [
                'groups' => 'pos_panel/api/terminal',
                'alias'  => 'POS Terminal Session Active Check',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\OrderController@index' => [
                'groups' => 'pos_panel/api/order',
                'alias'  => 'POS Order List',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\OrderController@update' => [
                'groups' => 'pos_panel/api/order',
                'alias'  => 'POS Order Update',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\AuthController@signup' => [
                'groups' => 'pos_panel/api/auth',
                'alias'  => 'POS Signup',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\AuthController@activate' => [
                'groups' => 'pos_panel/api/auth',
                'alias'  => 'POS Activate',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\RefundController@store' => [
                'groups' => 'pos_panel/api/refund',
                'alias'  => 'POS Refund Store',
            ],
            'Modules\\Refund\\Http\\Controllers\\Api\\User\\RefundController@index' => [
                'groups' => 'customer_panel/api/refund_management',
                'alias'  => 'Get Refund List',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\AuthController@switchLanguage' => [
                'groups' => 'pos_panel/api/auth',
                'alias'  => 'POS Switch Language',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\ProductController@index' => [
                'groups' => 'pos_panel/api/product',
                'alias'  => 'POS Product List',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\LocationController@index' => [
                'groups' => 'pos_panel/api/location',
                'alias'  => 'POS Location List',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\AddressController@store' => [
                'groups' => 'pos_panel/api/address',
                'alias'  => 'POS Address Store',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\AddressController@update' => [
                'groups' => 'pos_panel/api/address',
                'alias'  => 'POS Address Update',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\CartController@store' => [
                'groups' => 'pos_panel/api/cart',
                'alias'  => 'POS Cart Store',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\POSController@customer' => [
                'groups' => 'vendor_panel/web/pos_management',
                'alias'  => 'POS Customer',
            ],
            'Modules\\Pos\\Http\\Controllers\\Api\\CustomerController@index' => [
                'groups' => 'pos_panel/api/customer',
                'alias'  => 'POS Customer List',
            ],

            'Modules\\Pos\\Http\\Controllers\\PosVerifyController@verify' => [
                'groups' => 'pos_panel/web/verification',
                'alias'  => 'POS Verify',
            ],
            'Modules\\Pos\\Http\\Controllers\\POSController@setup' => [
                'groups' => 'admin_panel/web/setup',
                'alias'  => 'POS Setup',
            ],
            'Modules\\Pos\\Http\\Controllers\\POSController@receipt' => [
                'groups' => 'admin_panel/web/receipt',
                'alias'  => 'POS Receipt',
            ],
            'Modules\\Pos\\Http\\Controllers\\POSController@customer' => [
                'groups' => 'admin_panel/web/customer',
                'alias'  => 'POS Customer',
            ],
            'Modules\\Pos\\Http\\Controllers\\POSController@paymentMethod' => [
                'groups' => 'admin_panel/web/payment',
                'alias'  => 'POS Payment Method',
            ],
            'Modules\\Pos\\Http\\Controllers\\Vendor\\TerminalController@resetPin' => [
                'groups' => 'vendor_panel/web/terminal_management',
                'alias'  => 'Terminal Reset PIN',
            ],

            'Modules\\Subscription\\Http\\Controllers\\PackageController@getInfo' => [
                'groups' => 'admin_panel/web/plan_management',
                'alias'  => 'Get Plan (Ajax)',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@paid' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias'  => 'Paid Subscription',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@deletePayment' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias'  => 'Delete Subscription Payment',
            ],
            'Modules\\Subscription\\Http\\Controllers\\SubscriptionVerifyController@verify' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias'  => 'Verify Subscription',
            ],
            'Modules\\Subscription\\Http\\Controllers\\Vendor\\SubscriptionController@makePayment' => [
                'groups' => 'vendor_panel/web/subscription_management',
                'alias'  => 'Subscription Payment',
            ],

            'Modules\\Subscription\\Http\\Controllers\\PackageController@index' => [
                'groups' => 'admin_panel/web/plan_management',
                'alias'  => 'Plan List',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageController@create' => [
                'groups' => 'admin_panel/web/plan_management',
                'alias'  => 'Create Plan',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageController@store' => [
                'groups' => 'admin_panel/web/plan_management',
                'alias'  => 'Store Plan',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageController@show' => [
                'groups' => 'admin_panel/web/plan_management',
                'alias'  => 'Show Plan',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageController@edit' => [
                'groups' => 'admin_panel/web/plan_management',
                'alias'  => 'Edit Plan',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageController@update' => [
                'groups' => 'admin_panel/web/plan_management',
                'alias'  => 'Update Plan',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageController@destroy' => [
                'groups' => 'admin_panel/web/plan_management',
                'alias'  => 'Delete Plan',
            ],

            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@index' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Subscription List',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@create' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Create Subscription',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@store' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Store Subscription',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@show' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Show Subscription',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@edit' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Edit Subscription',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@update' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Update Subscription',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@destroy' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Delete Subscription',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@setting' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Subscription Settings',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@payment' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Subscription Payment',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@invoice' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Subscription Invoice',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@invoicePdf' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Subscription Invoice PDF',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@invoiceEmail' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Subscription Invoice Email',
            ],

            'Modules\\Subscription\\Http\\Controllers\\Vendor\\SubscriptionController@index' => [
                'groups' => 'vendor_panel/web/subscription_management',
                'alias' => 'Vendor Subscription List',
            ],
            'Modules\\Subscription\\Http\\Controllers\\Vendor\\SubscriptionController@store' => [
                'groups' => 'vendor_panel/web/subscription_management',
                'alias' => 'Buy Plan',
            ],
            'Modules\\Subscription\\Http\\Controllers\\Vendor\\SubscriptionController@paid' => [
                'groups' => 'vendor_panel/web/subscription_management',
                'alias' => 'Subscription Payment',
            ],
            'Modules\\Subscription\\Http\\Controllers\\Vendor\\SubscriptionController@history' => [
                'groups' => 'vendor_panel/web/subscription_management',
                'alias' => 'Subscription History',
            ],
            'Modules\\Subscription\\Http\\Controllers\\Vendor\\SubscriptionController@invoice' => [
                'groups' => 'vendor_panel/web/subscription_management',
                'alias' => 'Subscription Invoice',
            ],
            'Modules\\Subscription\\Http\\Controllers\\Vendor\\SubscriptionController@pdfInvoice' => [
                'groups' => 'vendor_panel/web/subscription_management',
                'alias' => 'Subscription Invoice PDF',
            ],
            'Modules\\Subscription\\Http\\Controllers\\Vendor\\SubscriptionController@cancel' => [
                'groups' => 'vendor_panel/web/subscription_management',
                'alias' => 'Cancel Subscription',
            ],

            'Modules\\Subscription\\Http\\Controllers\\PackageSubscriptionController@notification' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Send Subscription Notification',
            ],

            'Modules\\Subscription\\Http\\Controllers\\PackageController@generateLink' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Generate Private Plan Link',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageController@getGenerateLink' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Get Private Plan Link',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageController@generateLinkIndex' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Show Generate Private Plan Links',
            ],
            'Modules\\Subscription\\Http\\Controllers\\PackageController@destroyLink' => [
                'groups' => 'admin_panel/web/subscription_management',
                'alias' => 'Delete Private Plan Link',
            ],

            'Modules\\Subscription\\Http\\Controllers\\Vendor\\SubscriptionController@privatePlan' => [
                'groups' => 'vendor_panel/web/subscription_management',
                'alias' => 'View Private Plan',
            ],
            'Modules\\Subscription\\Http\\Controllers\\Vendor\\SubscriptionController@update' => [
                'groups' => 'vendor_panel/web/subscription_management',
                'alias' => 'Change Subscription Plan',
            ],
            'Modules\\Subscription\\Http\\Controllers\\Vendor\\SubscriptionController@updatePaid' => [
                'groups' => 'vendor_panel/web/subscription_management',
                'alias' => 'Payment Unpaid Invoice',
            ],



            'Modules\\Affiliate\\Http\\Controllers\\AffiliateController@dashboard' => [
                'groups' => 'affiliate_panel/web/affiliate_management',
                'alias' => 'Affiliate Dashboard',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateController@users' => [
                'groups' => 'affiliate_panel/web/affiliate_management',
                'alias' => 'Affiliate Users',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateController@profile' => [
                'groups' => 'affiliate_panel/web/affiliate_management',
                'alias' => 'Affiliate Profile',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateController@userProfileUpdate' => [
                'groups' => 'affiliate_panel/web/affiliate_management',
                'alias' => 'Update Affiliate User Profile',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateController@userDestroy' => [
                'groups' => 'affiliate_panel/web/affiliate_management',
                'alias' => 'Delete Affiliate User',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateController@referrals' => [
                'groups' => 'affiliate_panel/web/affiliate_management',
                'alias' => 'Affiliate Referrals',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateController@topProducts' => [
                'groups' => 'affiliate_panel/web/affiliate_management',
                'alias' => 'Top Affiliate Products',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateController@payouts' => [
                'groups' => 'affiliate_panel/web/affiliate_management',
                'alias' => 'Affiliate Payouts',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateController@multiTier' => [
                'groups' => 'affiliate_panel/web/affiliate_management',
                'alias' => 'Affiliate MultiTier',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateController@settings' => [
                'groups' => 'affiliate_panel/web/affiliate_management',
                'alias' => 'Affiliate Settings',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateTagController@index' => [
                'groups' => 'affiliate_panel/web/affiliate_tag_management',
                'alias' => 'Affiliate Tags',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateTagController@store' => [
                'groups' => 'affiliate_panel/web/affiliate_tag_management',
                'alias' => 'Create Affiliate Tag',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateTagController@edit' => [
                'groups' => 'affiliate_panel/web/affiliate_tag_management',
                'alias' => 'Edit Affiliate Tag',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateTagController@update' => [
                'groups' => 'affiliate_panel/web/affiliate_tag_management',
                'alias' => 'Update Affiliate Tag',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateTagController@destroy' => [
                'groups' => 'affiliate_panel/web/affiliate_tag_management',
                'alias' => 'Delete Affiliate Tag',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\CampaignController@index' => [
                'groups' => 'affiliate_panel/web/affiliate_campaign_management',
                'alias' => 'View Affiliate Campaigns',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\CampaignController@store' => [
                'groups' => 'affiliate_panel/web/affiliate_campaign_management',
                'alias' => 'Create Affiliate Campaign',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\CampaignController@edit' => [
                'groups' => 'affiliate_panel/web/affiliate_campaign_management',
                'alias' => 'Edit Affiliate Campaign',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\CampaignController@update' => [
                'groups' => 'affiliate_panel/web/affiliate_campaign_management',
                'alias' => 'Update Affiliate Campaign',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\CampaignController@destroy' => [
                'groups' => 'affiliate_panel/web/affiliate_campaign_management',
                'alias' => 'Delete Affiliate Campaign',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\CommissionPlanController@index' => [
                'groups' => 'affiliate_panel/web/affiliate_commission_plan_management',
                'alias' => 'View Commission Plans',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\CommissionPlanController@create' => [
                'groups' => 'affiliate_panel/web/affiliate_commission_plan_management',
                'alias' => 'Create Commission Plan',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\CommissionPlanController@store' => [
                'groups' => 'affiliate_panel/web/affiliate_commission_plan_management',
                'alias' => 'Store Commission Plan',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\CommissionPlanController@edit' => [
                'groups' => 'affiliate_panel/web/affiliate_commission_plan_management',
                'alias' => 'Edit Commission Plan',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\CommissionPlanController@update' => [
                'groups' => 'affiliate_panel/web/affiliate_commission_plan_management',
                'alias' => 'Update Commission Plan',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\CommissionPlanController@destroy' => [
                'groups' => 'affiliate_panel/web/affiliate_commission_plan_management',
                'alias' => 'Delete Commission Plan',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\WithdrawalsController@index' => [
                'groups' => 'affiliate_panel/web/affiliate_withdrawals_management',
                'alias' => 'View Withdrawals',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\WithdrawalsController@view' => [
                'groups' => 'affiliate_panel/web/affiliate_withdrawals_management',
                'alias' => 'View Single Withdrawal',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateController@findAffiliateUserAjaxQuery' => [
                'groups' => 'affiliate_panel/web/affiliate_management',
                'alias' => 'Find Affiliate User (Ajax)',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateController@findAffiliateTagAjaxQuery' => [
                'groups' => 'affiliate_panel/web/affiliate_management',
                'alias' => 'Find Affiliate Tag (Ajax)',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateController@findCategoryAjaxQuery' => [
                'groups' => 'affiliate_panel/web/affiliate_management',
                'alias' => 'Find Category (Ajax)',
            ],
            'Modules\\Affiliate\\Http\\Controllers\\AffiliateController@findProductAjaxQuery' => [
                'groups' => 'affiliate_panel/web/affiliate_management',
                'alias' => 'Find Product (Ajax)',
            ],
            'App\\Http\\Controllers\\Api\\AuthController@logout' => [
                'groups' => 'customer_+_pos_panel/api/auth_management',
                'alias' => 'Logout',
            ],

            'Modules\\Coupon\\Http\\Controllers\\Api\\CouponController@index' => [
                'groups' => 'admin_panel/api/coupon_management_(Deprecated)',
                'alias'  => 'Coupon List',
            ],
            'Modules\\Coupon\\Http\\Controllers\\Api\\CouponController@store' => [
                'groups' => 'admin_panel/api/coupon_management_(Deprecated)',
                'alias'  => 'Store Coupon',
            ],
            'Modules\\Coupon\\Http\\Controllers\\Api\\CouponController@update' => [
                'groups' => 'admin_panel/api/coupon_management_(Deprecated)',
                'alias'  => 'Update Coupon',
            ],
            'Modules\\Coupon\\Http\\Controllers\\Api\\CouponController@detail' => [
                'groups' => 'admin_panel/api/coupon_management_(Deprecated)',
                'alias'  => 'Coupon Detail',
            ],
            'Modules\\Coupon\\Http\\Controllers\\Api\\CouponController@destroy' => [
                'groups' => 'admin_panel/api/coupon_management_(Deprecated)',
                'alias'  => 'Delete Coupon',
            ],

            'App\\Http\\Controllers\\Api\\CategoryController@index' => [
                'groups' => 'admin_panel/api/category_management_(Deprecated)',
                'alias'  => 'Category List',
            ],
            'App\\Http\\Controllers\\Api\\CategoryController@store' => [
                'groups' => 'admin_panel/api/category_management_(Deprecated)',
                'alias'  => 'Create Category',
            ],
            'App\\Http\\Controllers\\Api\\CategoryController@update' => [
                'groups' => 'admin_panel/api/category_management_(Deprecated)',
                'alias'  => 'Update Category',
            ],
            'App\\Http\\Controllers\\Api\\CategoryController@detail' => [
                'groups' => 'admin_panel/api/category_management_(Deprecated)',
                'alias'  => 'Category Detail',
            ],
            'App\\Http\\Controllers\\Api\\CategoryController@destroy' => [
                'groups' => 'admin_panel/api/category_management_(Deprecated)',
                'alias'  => 'Delete Category',
            ],

            'App\\Http\\Controllers\\Api\\Vendor\\ApiVendorProductController@index' => [
                'groups' => 'vendor_panel/api/product_management_(Deprecated)',
                'alias'  => 'Vendor Product List',
            ],
            'App\\Http\\Controllers\\Api\\Vendor\\ApiVendorProductController@store' => [
                'groups' => 'vendor_panel/api/product_management_(Deprecated)',
                'alias'  => 'Store Vendor Product',
            ],
            'App\\Http\\Controllers\\Api\\Vendor\\ApiVendorProductController@update' => [
                'groups' => 'vendor_panel/api/product_management_(Deprecated)',
                'alias'  => 'Update Vendor Product',
            ],
            'App\\Http\\Controllers\\Api\\Vendor\\ApiVendorProductController@search' => [
                'groups' => 'vendor_panel/api/product_management_(Deprecated)',
                'alias'  => 'Search Vendor Product',
            ],
            'App\\Http\\Controllers\\Api\\Vendor\\ApiVendorProductController@detail' => [
                'groups' => 'vendor_panel/api/product_management_(Deprecated)',
                'alias'  => 'Vendor Product Detail',
            ],
            'App\\Http\\Controllers\\Api\\Vendor\\ApiVendorProductController@updateRelatedProduct' => [
                'groups' => 'vendor_panel/api/product_management_(Deprecated)',
                'alias'  => 'Update Related Vendor Product',
            ],
            'App\\Http\\Controllers\\Api\\Vendor\\ApiVendorProductController@destroy' => [
                'groups' => 'vendor_panel/api/product_management_(Deprecated)',
                'alias'  => 'Delete Vendor Product',
            ],

            'Modules\\Ticket\\Http\\Controllers\\Api\\User\\ChatController@getConversations' => [
                'groups' => 'customer_panel/api/chat_management',
                'alias'  => 'Get User Conversations',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Api\\User\\ChatController@sendProductDetails' => [
                'groups' => 'customer_panel/api/chat_management',
                'alias'  => 'Send Product Details',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Api\\User\\ChatController@initiateChatWithVendor' => [
                'groups' => 'customer_panel/api/chat_management',
                'alias'  => 'Initiate Chat With Vendor',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Api\\User\\ChatController@contact-list' => [
                'groups' => 'customer_panel/api/chat_management',
                'alias'  => 'User Chat Contact List',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Api\\User\\ChatController@storeMessage' => [
                'groups' => 'customer_panel/api/chat_management',
                'alias'  => 'Store Chat Message',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Api\\User\\ChatController@createChat' => [
                'groups' => 'customer_panel/api/chat_management',
                'alias'  => 'Create Chat',
            ],
            'Modules\\Ticket\\Http\\Controllers\\Api\\User\\ChatController@inboxRefresh' => [
                'groups' => 'customer_panel/api/chat_management',
                'alias'  => 'Refresh Chat Inbox',
            ],

            'App\\Http\\Controllers\\Vendor\\DashboardController@setWidgetData' => [
                'groups' => 'vendor_panel/web/dashboard',
                'alias'  => 'Set Dashboard Widget Data',
            ],
            'App\\Http\\Controllers\\Vendor\\DashboardController@setWidgetOption' => [
                'groups' => 'vendor_panel/web/dashboard',
                'alias'  => 'Set Dashboard Widget Option',
            ],
            'App\\Http\\Controllers\\Vendor\\DashboardController@forgetWidget' => [
                'groups' => 'vendor_panel/web/dashboard',
                'alias'  => 'Forget Dashboard Widget',
            ],
            'Modules\\CMS\\Http\\Controllers\\Vendor\\HomeController@index' => [
                'groups' => 'vendor_panel/web/homepage_management',
                'alias'  => 'Homepage List',
            ],
            'Modules\\CMS\\Http\\Controllers\\Vendor\\HomeController@create' => [
                'groups' => 'vendor_panel/web/homepage_management',
                'alias'  => 'Create Homepage',
            ],
            'Modules\\CMS\\Http\\Controllers\\Vendor\\HomeController@store' => [
                'groups' => 'vendor_panel/web/homepage_management',
                'alias'  => 'Store Homepage',
            ],
            'Modules\\CMS\\Http\\Controllers\\Vendor\\HomeController@edit' => [
                'groups' => 'vendor_panel/web/homepage_management',
                'alias'  => 'Edit Homepage',
            ],
            'Modules\\CMS\\Http\\Controllers\\Vendor\\HomeController@update' => [
                'groups' => 'vendor_panel/web/homepage_management',
                'alias'  => 'Update Homepage',
            ],
            'Modules\\CMS\\Http\\Controllers\\Vendor\\HomeController@delete' => [
                'groups' => 'vendor_panel/web/homepage_management',
                'alias'  => 'Delete Homepage',
            ],
            'Modules\\CMS\\Http\\Controllers\\Vendor\\BuilderController@edit' => [
                'groups' => 'vendor_panel/web/home_builder_management',
                'alias'  => 'Edit Page Builder',
            ],
            'Modules\\CMS\\Http\\Controllers\\Vendor\\BuilderController@editElement' => [
                'groups' => 'vendor_panel/web/home_builder_management',
                'alias'  => 'Edit Page Builder Element',
            ],
            'Modules\\CMS\\Http\\Controllers\\Vendor\\BuilderController@updateComponent' => [
                'groups' => 'vendor_panel/web/home_builder_management',
                'alias'  => 'Update Page Builder Component',
            ],
            'Modules\\CMS\\Http\\Controllers\\Vendor\\BuilderController@deleteComponent' => [
                'groups' => 'vendor_panel/web/home_builder_management',
                'alias'  => 'Delete Page Builder Component',
            ],
            'Modules\\CMS\\Http\\Controllers\\Vendor\\BuilderController@updateAllComponents' => [
                'groups' => 'vendor_panel/web/home_builder_management',
                'alias'  => 'Update All Page Builder Components',
            ],
            'Modules\\CMS\\Http\\Controllers\\Vendor\\BuilderController@ajaxResourceFetch' => [
                'groups' => 'vendor_panel/web/home_builder_management',
                'alias'  => 'Ajax Fetch Page Builder Resource',
            ],
            'Modules\\CMS\\Http\\Controllers\\BuilderController@edit' => [
                'groups' => 'admin_panel/web/home_builder_management',
                'alias' => 'Edit Page Builder',
            ],
            'Modules\\CMS\\Http\\Controllers\\BuilderController@editElement' => [
                'groups' => 'admin_panel/web/home_builder_management',
                'alias' => 'Edit Page Builder Element',
            ],
            'Modules\\CMS\\Http\\Controllers\\BuilderController@updateComponent' => [
                'groups' => 'admin_panel/web/home_builder_management',
                'alias' => 'Update Page Builder Component',
            ],
            'Modules\\CMS\\Http\\Controllers\\BuilderController@deleteComponent' => [
                'groups' => 'admin_panel/web/home_builder_management',
                'alias' => 'Delete Page Builder Component',
            ],

            'App\\Http\\Controllers\\Vendor\\StaffController@index' => [
                'groups' => 'vendor_panel/web/staff_management',
                'alias' => 'Staff List',
            ],
            'App\\Http\\Controllers\\Vendor\\StaffController@create' => [
                'groups' => 'vendor_panel/web/staff_management',
                'alias' => 'Create Staff',
            ],
            'App\\Http\\Controllers\\Vendor\\StaffController@store' => [
                'groups' => 'vendor_panel/web/staff_management',
                'alias' => 'Store Staff',
            ],
            'App\\Http\\Controllers\\Vendor\\StaffController@edit' => [
                'groups' => 'vendor_panel/web/staff_management',
                'alias' => 'Edit Staff',
            ],
            'App\\Http\\Controllers\\Vendor\\StaffController@update' => [
                'groups' => 'vendor_panel/web/staff_management',
                'alias' => 'Update Staff',
            ],
            'App\\Http\\Controllers\\Vendor\\StaffController@destroy' => [
                'groups' => 'vendor_panel/web/staff_management',
                'alias' => 'Delete Staff',
            ],
            'App\\Http\\Controllers\\Vendor\\RoleController@index' => [
                'groups' => 'vendor_panel/web/role_management',
                'alias' => 'Role List',
            ],
            'App\\Http\\Controllers\\Vendor\\RoleController@create' => [
                'groups' => 'vendor_panel/web/role_management',
                'alias' => 'Create Role',
            ],
            'App\\Http\\Controllers\\Vendor\\RoleController@store' => [
                'groups' => 'vendor_panel/web/role_management',
                'alias' => 'Store Role',
            ],
            'App\\Http\\Controllers\\Vendor\\RoleController@edit' => [
                'groups' => 'vendor_panel/web/role_management',
                'alias' => 'Edit Role',
            ],
            'App\\Http\\Controllers\\Vendor\\RoleController@update' => [
                'groups' => 'vendor_panel/web/role_management',
                'alias' => 'Update Role',
            ],
            'App\\Http\\Controllers\\Vendor\\RoleController@destroy' => [
                'groups' => 'vendor_panel/web/role_management',
                'alias' => 'Delete Role',
            ],
            'App\\Http\\Controllers\\Vendor\\PermissionController@index' => [
                'groups' => 'vendor_panel/web/permission_management',
                'alias' => 'View Permissions',
            ],
            'App\\Http\\Controllers\\Vendor\\PermissionController@assign' => [
                'groups' => 'vendor_panel/web/permission_management',
                'alias' => 'Assign Permissions',
            ],

            'App\\Http\\Controllers\\SsoController@client' => [
                'groups' => 'admin_panel/web/sso_management',
                'alias'  => 'View SSO Clients',
            ],
            'App\\Http\\Controllers\\SsoController@deleteClient' => [
                'groups' => 'admin_panel/web/sso_management',
                'alias'  => 'Delete SSO Client',
            ],
            'App\\Http\\Controllers\\ApiKeyController@index' => [
                'groups' => 'admin_panel/web/api_key_management',
                'alias'  => 'API Key List',
            ],
            'App\\Http\\Controllers\\ApiKeyController@store' => [
                'groups' => 'admin_panel/web/api_key_management',
                'alias'  => 'Create API Key',
            ],
            'App\\Http\\Controllers\\ApiKeyController@update' => [
                'groups' => 'admin_panel/web/api_key_management',
                'alias'  => 'Update API Key',
            ],
            'App\\Http\\Controllers\\ApiKeyController@destroy' => [
                'groups' => 'admin_panel/web/api_key_management',
                'alias'  => 'Delete API Key',
            ],
            'App\\Http\\Controllers\\ApiKeyController@settings' => [
                'groups' => 'admin_panel/web/api_key_management',
                'alias'  => 'Manage API Key Settings',
            ],

            'App\\Http\\Controllers\\NotificationController@log' => [
                'groups' => 'admin_panel/web/notification_management',
                'alias' => 'View Notification Log',
            ],
            'App\\Http\\Controllers\\NotificationController@destroyLog' => [
                'groups' => 'admin_panel/web/notification_management',
                'alias' => 'Delete Notification Log',
            ],
            'App\\Http\\Controllers\\NotificationController@setting' => [
                'groups' => 'admin_panel/web/notification_management',
                'alias' => 'Notification Settings',
            ],

            'App\\Http\\Controllers\\CustomFieldController@index' => [
                'groups' => 'admin_panel/web/custom_field_management',
                'alias' => 'Custom Field List',
            ],
            'App\\Http\\Controllers\\CustomFieldController@create' => [
                'groups' => 'admin_panel/web/custom_field_management',
                'alias' => 'Create Custom Field',
            ],
            'App\\Http\\Controllers\\CustomFieldController@store' => [
                'groups' => 'admin_panel/web/custom_field_management',
                'alias' => 'Store Custom Field',
            ],
            'App\\Http\\Controllers\\CustomFieldController@edit' => [
                'groups' => 'admin_panel/web/custom_field_management',
                'alias' => 'Edit Custom Field',
            ],
            'App\\Http\\Controllers\\CustomFieldController@update' => [
                'groups' => 'admin_panel/web/custom_field_management',
                'alias' => 'Update Custom Field',
            ],
            'App\\Http\\Controllers\\CustomFieldController@destroy' => [
                'groups' => 'admin_panel/web/custom_field_management',
                'alias' => 'Delete Custom Field',
            ],

            'Modules\\FormBuilder\\Http\\Controllers\\Vendor\\KycController@userKycForm' => [
                'groups' => 'vendor_panel/web/kyc_management',
                'alias' => 'View KYC Form',
            ],
            'Modules\\FormBuilder\\Http\\Controllers\\Vendor\\KycController@userKycSubmit' => [
                'groups' => 'vendor_panel/web/kyc_management',
                'alias' => 'Submit KYC Form',
            ],
            'Modules\\FormBuilder\\Http\\Controllers\\Vendor\\KycController@userKycUpdateSubmission' => [
                'groups' => 'vendor_panel/web/kyc_management',
                'alias' => 'Update KYC Submission',
            ],

            'Modules\\CMS\\Http\\Controllers\\ThemeOptionController@layoutStore' => [
                'groups' => 'admin_panel/web/appearance_management',
                'alias' => 'Store Layout',
            ],
            'Modules\\CMS\\Http\\Controllers\\ThemeOptionController@storePrimaryColor' => [
                'groups' => 'admin_panel/web/appearance_management',
                'alias' => 'Store Primary Color',
            ],
            'Modules\\CMS\\Http\\Controllers\\ThemeOptionController@layoutUpdate' => [
                'groups' => 'admin_panel/web/appearance_management',
                'alias' => 'Update Layout',
            ],
            'Modules\\CMS\\Http\\Controllers\\ThemeOptionController@layoutDelete' => [
                'groups' => 'admin_panel/web/appearance_management',
                'alias' => 'Delete Layout',
            ],
            'Modules\\CMS\\Http\\Controllers\\ThemeOptionController@changeLanguage' => [
                'groups' => 'admin_panel/web/appearance_management',
                'alias' => 'Change Language',
            ],

            'Modules\\CMS\\Http\\Controllers\\AuthLayoutController@index' => [
                'groups' => 'admin_panel/web/appearance_management',
                'alias' => 'Auth Layout List',
            ],
            'Modules\\CMS\\Http\\Controllers\\AuthLayoutController@store' => [
                'groups' => 'admin_panel/web/appearance_management',
                'alias' => 'Store Auth Layout',
            ],

            'App\\Http\\Controllers\\Vendor\\ExportController@productExport' => [
                'groups' => 'vendor_panel/web/export_management',
                'alias' => 'Vendor Product Export',
            ],
            'App\\Http\\Controllers\\ExportController@index' => [
                'groups' => 'admin_panel/web/export_management',
                'alias' => 'Export Zone',
            ],
            'App\\Http\\Controllers\\ExportController@productExport' => [
                'groups' => 'admin_panel/web/export_management',
                'alias' => 'Product Export',
            ],

            'App\\Http\\Controllers\\LanguageController@import' => [
                'groups' => 'admin_panel/web/language_management',
                'alias' => 'Import Languages',
            ],
            'App\\Http\\Controllers\\AdminOrderController@edit' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Edit Order',
            ],
            'App\\Http\\Controllers\\AdminOrderController@customize' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Customize Order',
            ],
            'App\\Http\\Controllers\\ProductSettingController@filter' => [
                'groups' => 'admin_panel/web/product_settings',
                'alias' => 'Filter Product Settings',
            ],
            'App\\Http\\Controllers\\BrandController@findBrand' => [
                'groups' => 'admin_panel/web/brand_management',
                'alias' => 'Find Brand',
            ],
            'App\\Http\\Controllers\\CategoryController@findCategory' => [
                'groups' => 'admin_panel/web/category_management',
                'alias' => 'Find Category',
            ],

            'Modules\\CMS\\Http\\Controllers\\CMSController@home' => [
                'groups' => 'admin_panel/web/cms',
                'alias' => 'Homepage List',
            ],
            'Modules\\CMS\\Http\\Controllers\\CMSController@createHomepage' => [
                'groups' => 'admin_panel/web/cms',
                'alias' => 'Create Homepage',
            ],
            'Modules\\CMS\\Http\\Controllers\\CMSController@editHome' => [
                'groups' => 'admin_panel/web/cms',
                'alias' => 'Edit Homepage',
            ],
            'Modules\\CMS\\Http\\Controllers\\CMSController@exportHome' => [
                'groups' => 'admin_panel/web/cms',
                'alias' => 'Export Homepage',
            ],
            'Modules\\CMS\\Http\\Controllers\\CMSController@importHome' => [
                'groups' => 'admin_panel/web/cms',
                'alias' => 'Import Homepage',
            ],
            'Modules\\CMS\\Http\\Controllers\\CMSController@quickUpdate' => [
                'groups' => 'admin_panel/web/cms',
                'alias' => 'Quick Update Page & Homepage',
            ],
            'Modules\\CMS\\Http\\Controllers\\BuilderController@updateAllComponents' => [
                'groups' => 'admin_panel/web/cms',
                'alias' => 'Update All Builder Components',
            ],
            'Modules\\CMS\\Http\\Controllers\\BuilderController@ajaxResourceFetch' => [
                'groups' => 'admin_panel/web/cms',
                'alias' => 'Get Builder Resource (Ajax)',
            ],

            'Modules\\BulkPayment\\Http\\Controllers\\BulkPaymentController@order' => [
                'groups' => 'admin_panel/web/bulk_payment',
                'alias' => 'Bulk Payment Order',
            ],
            'Modules\\BulkPayment\\Http\\Controllers\\Vendor\\BulkPaymentController@order' => [
                'groups' => 'vendor_panel/web/bulk_payment',
                'alias' => 'Bulk Payment Order',
            ],
            'Modules\\Delivery\\Http\\Controllers\\Admin\\WithdrawalController@csv' => [
                'groups' => 'admin_panel/web/withdrawal',
                'alias' => 'Export Withdrawals (CSV)',
            ],

            'App\\Http\\Controllers\\Api\\BrandController@index' => [
                'groups' => 'admin_panel/api/brand_management_(Deprecated)',
                'alias' => 'Brand List',
            ],
            'App\\Http\\Controllers\\Api\\BrandController@store' => [
                'groups' => 'admin_panel/api/brand_management_(Deprecated)',
                'alias' => 'Create Brand',
            ],
            'App\\Http\\Controllers\\Api\\BrandController@update' => [
                'groups' => 'admin_panel/api/brand_management_(Deprecated)',
                'alias' => 'Update Brand',
            ],
            'App\\Http\\Controllers\\Api\\BrandController@detail' => [
                'groups' => 'admin_panel/api/brand_management_(Deprecated)',
                'alias' => 'Brand Details',
            ],
            'App\\Http\\Controllers\\Api\\BrandController@destroy' => [
                'groups' => 'admin_panel/api/brand_management_(Deprecated)',
                'alias' => 'Delete Brand',
            ],

            'App\\Http\\Controllers\\Api\\UserController@index' => [
                'groups' => 'pos_panel/api/user_management',
                'alias' => 'User List',
            ],
            'App\\Http\\Controllers\\Api\\UserController@store' => [
                'groups' => 'admin_panel/api/user_management_(Deprecated)',
                'alias' => 'Create User',
            ],
            'App\\Http\\Controllers\\Api\\UserController@detail' => [
                'groups' => 'admin_panel/api/user_management_(Deprecated)',
                'alias' => 'User Details',
            ],
            'App\\Http\\Controllers\\Api\\UserController@update' => [
                'groups' => 'admin_panel/api/user_management_(Deprecated)',
                'alias' => 'Update User',
            ],
            'App\\Http\\Controllers\\Api\\UserController@updatePassword' => [
                'groups' => 'admin_panel/api/user_management_(Deprecated)',
                'alias' => 'Update User Password',
            ],
            'App\\Http\\Controllers\\Api\\UserController@destroy' => [
                'groups' => 'admin_panel/api/user_management_(Deprecated)',
                'alias' => 'Delete User',
            ],

            'App\\Http\\Controllers\\Api\\User\\UserController@profile' => [
                'groups' => 'customer_+_pos_panel/api/user_management',
                'alias' => 'User Profile',
            ],
            'App\\Http\\Controllers\\Api\\User\\UserController@update' => [
                'groups' => 'customer_panel/api/user_management',
                'alias' => 'Update User Profile',
            ],
            'App\\Http\\Controllers\\Api\\User\\UserController@updatePassword' => [
                'groups' => 'customer_panel/api/user_management',
                'alias' => 'Update User Password',
            ],
            'App\\Http\\Controllers\\Api\\User\\UserController@destroy' => [
                'groups' => 'customer_panel/api/user_management',
                'alias' => 'Delete User Account',
            ],
            'App\\Http\\Controllers\\Api\\User\\WishlistController@index' => [
                'groups' => 'customer_panel/api/wishlist_management',
                'alias' => 'Wishlist List',
            ],
            'App\\Http\\Controllers\\Api\\User\\WishlistController@destroy' => [
                'groups' => 'customer_panel/api/wishlist_management',
                'alias' => 'Remove From Wishlist',
            ],
            'App\\Http\\Controllers\\Api\\User\\ReviewController@index' => [
                'groups' => 'customer_panel/api/review_management',
                'alias' => 'User Review List',
            ],
            'App\\Http\\Controllers\\Api\\User\\AddressController@addresses' => [
                'groups' => 'customer_panel/api/address_management',
                'alias' => 'User Addresses',
            ],
            'App\\Http\\Controllers\\Api\\User\\AddressController@storeAddress' => [
                'groups' => 'customer_panel/api/address_management',
                'alias' => 'Store User Address',
            ],
            'App\\Http\\Controllers\\Api\\User\\AddressController@updateAddress' => [
                'groups' => 'customer_panel/api/address_management',
                'alias' => 'Update User Address',
            ],
            'App\\Http\\Controllers\\Api\\User\\AddressController@destroyAddress' => [
                'groups' => 'customer_panel/api/address_management',
                'alias' => 'Delete User Address',
            ],
            'App\\Http\\Controllers\\Api\\User\\OrderController@index' => [
                'groups' => 'customer_panel/api/order_management',
                'alias' => 'User Order List',
            ],

            'App\\Http\\Controllers\\Vendor\\VendorOrderController@index' => [
                'groups' => 'vendor_panel/web/order_management',
                'alias' => 'Vendor Order List',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorOrderController@edit' => [
                'groups' => 'vendor_panel/web/order_management',
                'alias' => 'Edit Vendor Order',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorOrderController@view' => [
                'groups' => 'vendor_panel/web/order_management',
                'alias' => 'View Vendor Order',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorOrderController@changeStatus' => [
                'groups' => 'vendor_panel/web/order_management',
                'alias' => 'Change Vendor Order Status',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorOrderController@pdf' => [
                'groups' => 'vendor_panel/web/order_management',
                'alias' => 'Vendor Order PDF',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorOrderController@csv' => [
                'groups' => 'vendor_panel/web/order_management',
                'alias' => 'Vendor Order CSV',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorOrderController@invoicePrint' => [
                'groups' => 'vendor_panel/web/order_management',
                'alias' => 'Vendor Order Invoice Print',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorOrderController@customize' => [
                'groups' => 'vendor_panel/web/order_management',
                'alias' => 'Customize Vendor Order',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorOrderController@userAddress' => [
                'groups' => 'vendor_panel/web/order_management',
                'alias' => 'Vendor Order User Address',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorOrderController@storeNote' => [
                'groups' => 'vendor_panel/web/order_management',
                'alias' => 'Store Vendor Order Note',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorOrderController@orderAction' => [
                'groups' => 'vendor_panel/web/order_management',
                'alias' => 'Vendor Order Action',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorOrderController@grantAccess' => [
                'groups' => 'vendor_panel/web/order_management',
                'alias' => 'Grant Vendor Order Access',
            ],

            'App\\Http\\Controllers\\AdminOrderController@invoiceTaxShipping' => [
                'groups' => 'admin_+_vendor_panel/web/order_management',
                'alias' => 'Invoice Tax & Shipping',
            ],
            'App\\Http\\Controllers\\AdminOrderController@invoiceSave' => [
                'groups' => 'admin_+_vendor_panel/web/order_management',
                'alias' => 'Save Invoice',
            ],
            'App\\Http\\Controllers\\AdminOrderController@partialPayment' => [
                'groups' => 'admin_+_vendor_panel/web/order_management',
                'alias' => 'Partial Payment',
            ],

            'Modules\\MediaManager\\Http\\Controllers\\MediaManagerController@storeExtension' => [
                'groups' => 'admin_panel/web/media_manager',
                'alias' => 'Store Media Extension',
            ],
            'Modules\\MediaManager\\Http\\Controllers\\MediaManagerController@getExtensionAjaxQuery' => [
                'groups' => 'admin_panel/web/media_manager',
                'alias' => 'Get Media Extension Ajax Query',
            ],

            'App\\Http\\Controllers\\Api\\ProductController@index' => [
                'groups' => 'admin_panel/api/product_management_(Deprecated)',
                'alias' => 'Product List',
            ],
            'App\\Http\\Controllers\\Api\\ProductController@search' => [
                'groups' => 'admin_panel/api/product_management_(Deprecated)',
                'alias' => 'Product Search',
            ],
            'App\\Http\\Controllers\\Api\\ProductController@detail' => [
                'groups' => 'admin_panel/api/product_management_(Deprecated)',
                'alias' => 'Product Detail',
            ],
            'App\\Http\\Controllers\\Api\\ProductController@deleteProduct' => [
                'groups' => 'admin_panel/api/product_management_(Deprecated)',
                'alias' => 'Delete Product',
            ],
            'App\\Http\\Controllers\\Site\\SiteController@reviewStore' => [
                'groups' => 'customer_panel/web/review_management',
                'alias' => 'Store Review',
            ],
            'App\\Http\\Controllers\\Site\\SiteController@updateReview' => [
                'groups' => 'customer_panel/web/review_management',
                'alias' => 'Update Review',
            ],
            'App\\Http\\Controllers\\Site\\SiteController@deleteReview' => [
                'groups' => 'customer_panel/web/review_management',
                'alias' => 'Delete Review',
            ],
            'App\\Http\\Controllers\\Vendor\\DashboardController@index' => [
                'groups' => 'vendor_panel/web/dashboard',
                'alias' => 'Vendor Dashboard',
            ],
            'App\\Http\\Controllers\\Site\\DashboardController@index' => [
                'groups' => 'customer_panel/web/dashboard',
                'alias' => 'Customer Dashboard',
            ],

            'App\\Http\\Controllers\\Api\\PreferenceController@index' => [
                'groups' => 'admin_panel/api/configuration_management_(Deprecated)',
                'alias' => 'Preference List',
            ],
            'App\\Http\\Controllers\\Api\\CompanySettingController@index' => [
                'groups' => 'admin_panel/api/configuration_management_(Deprecated)',
                'alias' => 'Company Setting List',
            ],
            'App\\Http\\Controllers\\PreferenceController@password' => [
                'groups' => 'admin_panel/web/configuration_management',
                'alias' => 'Password Settings',
            ],
            'Modules\\Commission\\Http\\Controllers\\CommissionController@index' => [
                'groups' => 'admin_panel/web/commission_management_(Deprecated)',
                'alias' => 'Commission List',
            ],
            'Modules\\Commission\\Http\\Controllers\\CommissionController@store' => [
                'groups' => 'admin_panel/web/commission_management_(Deprecated)',
                'alias' => 'Store Commission',
            ],

            'App\\Http\\Controllers\\Vendor\\VendorController@profile' => [
                'groups' => 'vendor_panel/web/vendor_account_management',
                'alias'  => 'Vendor Profile',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorController@update' => [
                'groups' => 'vendor_panel/web/vendor_account_management',
                'alias'  => 'Update Vendor Profile',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorController@updatePassword' => [
                'groups' => 'vendor_panel/web/vendor_account_management',
                'alias'  => 'Update Vendor Password',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorController@logout' => [
                'groups' => 'vendor_panel/web/vendor_account_management',
                'alias'  => 'Vendor Logout',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorController@updateVendor' => [
                'groups' => 'vendor_panel/web/vendor_account_management',
                'alias' => 'Update Vendor Information',
            ],
            'App\\Http\\Controllers\\Vendor\\VendorController@loginActivity' => [
                'groups' => 'vendor_panel/web/vendor_account_management',
                'alias' => 'Vendor Login Activity',
            ],
            'App\\Http\\Controllers\\UserController@updateProfile' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'Update User Profile',
            ],
            'App\\Http\\Controllers\\UserController@profile' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'View User Profile',
            ],
            'App\\Http\\Controllers\\UserController@updateProfilePassword' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'Update User Password',
            ],
            'App\\Http\\Controllers\\UserController@vendorList' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'Vendor List (Ajax)',
            ],
            'App\\Http\\Controllers\\UserController@vendorRole' => [
                'groups' => 'admin_panel/web/user_management',
                'alias' => 'Vendor Role (Ajax)',
            ],
            'Modules\\CMS\\Http\\Controllers\\ThemeOptionController@list' => [
                'groups' => 'admin_panel/web/appearance_management',
                'alias' => 'Show Appearance',
            ],
            'Modules\\CMS\\Http\\Controllers\\ThemeOptionController@store' => [
                'groups' => 'admin_panel/web/appearance_management',
                'alias' => 'Save Appearance',
            ],

            'Modules\\Refund\\Http\\Controllers\\Api\\User\\RefundController@getReason' => [
                'groups' => 'customer_panel/api/refund_management',
                'alias'  => 'Get Refund Reason',
            ],
            'Modules\\Refund\\Http\\Controllers\\Api\\User\\RefundController@store' => [
                'groups' => 'customer_panel/api/refund_management',
                'alias'  => 'Store Refund Request',
            ],
            'Modules\\Refund\\Http\\Controllers\\Api\\User\\RefundController@details' => [
                'groups' => 'customer_panel/api/refund_management',
                'alias'  => 'Get Refund Details',
            ],
            'Modules\\Refund\\Http\\Controllers\\Api\\User\\RefundController@storeMessage' => [
                'groups' => 'customer_panel/api/refund_management',
                'alias'  => 'Send Refund Message',
            ],
            'Modules\\Refund\\Http\\Controllers\\Api\\User\\RefundController@getMessage' => [
                'groups' => 'customer_panel/api/refund_management',
                'alias'  => 'Get Refund Message',
            ],

            'App\\Http\\Controllers\\Api\\UserController@storeMeta' => [
                'groups' => 'admin_panel/api/user_management_(Deprecated)',
                'alias'  => 'Store User Meta',
            ],
            'App\\Http\\Controllers\\Api\\UserController@getMeta' => [
                'groups' => 'admin_panel/api/user_management_(Deprecated)',
                'alias'  => 'Get User Meta',
            ],
            'App\\Http\\Controllers\\Api\\ProductController@update' => [
                'groups' => 'admin_panel/api/product_management_(Deprecated)',
                'alias'  => 'Update Product',
            ],

            'App\\Http\\Controllers\\SystemInfoController@index' => [
                'groups' => 'admin_panel/web/system_tools',
                'alias' => 'View System Info',
            ],
            'Modules\\Upgrader\\Http\\Controllers\\SystemUpdateController@upgrade' => [
                'groups' => 'admin_panel/web/system_tools',
                'alias' => 'Perform System Upgrade',
            ],

            'App\\Http\\Controllers\\Api\\User\\UserController@removeImage' => [
                'groups' => 'admin_panel/api/user_management',
                'alias'  => 'Remove User Image',
            ],
            'App\\Http\\Controllers\\ProductController@duplicate' => [
                'groups' => 'admin_panel/web/product_management',
                'alias'  => 'Duplicate Product',
            ],
            'App\\Http\\Controllers\\Vendor\\ProductController@duplicate' => [
                'groups' => 'vendor_panel/web/product_management',
                'alias'  => 'Duplicate Product',
            ],
            'App\\Http\\Controllers\\ImportController@index' => [
                'groups' => 'admin_panel/web/import_management',
                'alias' => 'Importable Items List',
            ],
            'App\\Http\\Controllers\\ImportController@productImport' => [
                'groups' => 'admin_panel/web/import_management',
                'alias' => 'Product Import',
            ],
            'App\\Http\\Controllers\\Vendor\\ImportController@productImport' => [
                'groups' => 'vendor_panel/web/import_management',
                'alias' => 'Product Import',
            ],
            'App\\Http\\Controllers\\RoleController@updatePermissions' => [
                'groups' => 'admin_panel/web/role_management',
                'alias' => 'Permission Update',
            ],
            'App\\Http\\Controllers\\Vendor\\RoleController@updatePermissions' => [
                'groups' => 'vendor_panel/web/role_management',
                'alias' => 'Permission Update',
            ],
            'App\\Http\\Controllers\\MaintenanceModeController@enable' => [
                'groups' => 'admin_panel/web/configuration_management',
                'alias' => 'Enable Maintenance Mode',
            ],
            'App\\Http\\Controllers\\Vendor\\ProductController@index' => [
                'groups' => 'vendor_panel/web/product_management',
                'alias' => 'Product List',
            ],
            'App\\Http\\Controllers\\Vendor\\ProductController@edit' => [
                'groups' => 'vendor_panel/web/product_management',
                'alias' => 'Edit Product',
            ],
            'App\\Http\\Controllers\\Vendor\\ProductController@search' => [
                'groups' => 'vendor_panel/web/product_management',
                'alias' => 'Product Search (Deprecated)',
            ],
            'App\\Http\\Controllers\\SsoController@index' => [
                'groups' => 'admin_panel/web/configuration_management',
                'alias' => 'SSO Service',
            ],
            'App\\Http\\Controllers\\AdminOrderController@invoicePrint' => [
                'groups' => 'admin_panel/web/order_management',
                'alias' => 'Invoice Print',
            ],
            'App\\Http\\Controllers\\AddonsMangerController@index' => [
                'groups' => 'admin_panel/web/addons_management',
                'alias' => 'Addons Manager',
            ],
            'Modules\\Stripe\\Http\\Controllers\\StripeController@edit' => [
                'groups' => 'admin_panel/web/gateway_management',
                'alias' => 'Edit Stripe',
            ],
            'Modules\\Stripe\\Http\\Controllers\\StripeController@store' => [
                'groups' => 'admin_panel/web/gateway_management',
                'alias' => 'Update Stripe',
            ],
            'Modules\\Report\\Http\\Controllers\\ReportController@index' => [
                'groups' => 'admin_panel/web/report_management',
                'alias' => 'Show Reports',
            ],
            'Modules\\Report\\Http\\Controllers\\Vendor\\ReportController@index' => [
                'groups' => 'vendor_panel/web/report_management',
                'alias' => 'Show Reports',
            ],
            'App\\Http\\Controllers\\Api\\User\\WishlistController@store' => [
                'groups' => 'customer_panel/api/wishlist_management',
                'alias'  => 'Add to Wishlist',
            ],
            'App\\Http\\Controllers\\Api\\User\\UserController@wallet' => [
                'groups' => 'customer_panel/api/wallet_management',
                'alias'  => 'Show Wallet Info',
            ],
            'App\\Http\\Controllers\\Site\\RegisteredSellerController@sellerRequestStore' => [
                'groups' => 'customer_panel/web/user_management',
                'alias' => 'Send Seller Request',
            ],
            'App\\Http\\Controllers\\Vendor\\ProductController@findDownloadProducts' => [
                'groups' => 'vendor_panel/web/product_management',
                'alias' => 'Find Download Products (Ajax)',
            ],
            'App\\Http\\Controllers\\ProductController@findDownloadProducts' => [
                'groups' => 'admin_panel/web/product_management',
                'alias' => 'Find Download Products (Ajax)',
            ],
            'Modules\\Delivery\\Http\\Controllers\\DeliveryOrderController@assignCarrierView' => [
                'groups' => 'admin_panel/web/delivery_management',
                'alias' => 'Assign Delivery Man',
            ],

        ];

        // Update permissions
        foreach ($permissionMappings as $permissionName => $mapping) {
            $permission = Permission::where('name', $permissionName)->first();

            if ($permission) {
                $permission->update([
                    'groups' => $mapping['groups'],
                    'alias' => $mapping['alias'],
                ]);
            }
        }

        // Delete permissions
        $permissionsToDelete = [
            '\\App\\Http\\Controllers\\Api\\AuthController@logout',
        ];
        foreach ($permissionsToDelete as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $permission->delete();
            }
        }
    }
}
