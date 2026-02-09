<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SKU Configuration
    |--------------------------------------------------------------------------
    */
    'sku' => [
        'auto_generate' => env('SKU_AUTO_GENERATE', true),
        'prefix' => env('SKU_PREFIX', 'PRD'),
        'suffix' => env('SKU_SUFFIX', null),
        'length' => env('SKU_LENGTH', 8),
        'include_category_code' => env('SKU_INCLUDE_CATEGORY', false),
        'include_vendor_code' => env('SKU_INCLUDE_VENDOR', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Barcode Configuration
    |--------------------------------------------------------------------------
    */
    'barcode' => [
        'auto_generate' => env('BARCODE_AUTO_GENERATE', true),
        'type' => env('BARCODE_TYPE', 'CODE128'), // CODE128, EAN13, QR
        'prefix' => env('BARCODE_PREFIX', null),
        'length' => env('BARCODE_LENGTH', 12),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cart Configuration
    |--------------------------------------------------------------------------
    */
    'cart' => [
        'session_lifetime' => env('CART_SESSION_LIFETIME', 7), // days
        'user_lifetime' => env('CART_USER_LIFETIME', 30), // days
    ],

    /*
    |--------------------------------------------------------------------------
    | Order Configuration
    |--------------------------------------------------------------------------
    */
    'order' => [
        'prefix' => env('ORDER_PREFIX', 'ORD'),
        'auto_cancel_pending' => env('ORDER_AUTO_CANCEL_PENDING', false),
        'auto_cancel_days' => env('ORDER_AUTO_CANCEL_DAYS', 7),
    ],

    /*
    |--------------------------------------------------------------------------
    | Commission Configuration
    |--------------------------------------------------------------------------
    */
    'commission' => [
        'default_rate' => env('DEFAULT_COMMISSION_RATE', 10.00), // percentage
        'calculate_on' => env('COMMISSION_CALCULATE_ON', 'subtotal'), // subtotal, total
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    */
    'payment' => [
        'currency' => env('PAYMENT_CURRENCY', 'USD'),
        'bkash' => [
            'app_key' => env('BKASH_APP_KEY'),
            'app_secret' => env('BKASH_APP_SECRET'),
            'username' => env('BKASH_USERNAME'),
            'password' => env('BKASH_PASSWORD'),
            'sandbox' => env('BKASH_SANDBOX', true),
            'base_url' => env('BKASH_SANDBOX', true) 
                ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
                : 'https://tokenized.pay.bka.sh/v1.2.0-beta',
        ],
        'sslcommerz' => [
            'store_id' => env('SSLCOMMERZ_STORE_ID'),
            'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
            'sandbox' => env('SSLCOMMERZ_SANDBOX', true),
        ],
        'stripe' => [
            'key' => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
        ],
        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
            'mode' => env('PAYPAL_MODE', 'sandbox'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Courier Service Configuration
    |--------------------------------------------------------------------------
    */
    'courier' => [
        'pathao' => [
            'client_id' => env('PATHAO_CLIENT_ID'),
            'client_secret' => env('PATHAO_CLIENT_SECRET'),
            'store_id' => env('PATHAO_STORE_ID'),
            'sandbox' => env('PATHAO_SANDBOX', true),
            'base_url' => env('PATHAO_SANDBOX', true)
                ? 'https://courier-api-sandbox.pathao.com/aladdin/api/v1'
                : 'https://api-hermes.pathao.com/aladdin/api/v1',
        ],
        'steadfast' => [
            'api_key' => env('STEADFAST_API_KEY'),
            'secret_key' => env('STEADFAST_SECRET_KEY'),
            'sandbox' => env('STEADFAST_SANDBOX', true),
            'base_url' => env('STEADFAST_SANDBOX', true)
                ? 'https://portal.sandbox.steadfast.com.bd/api/v1'
                : 'https://portal.steadfast.com.bd/api/v1',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Gateway Configuration
    |--------------------------------------------------------------------------
    */
    'sms' => [
        'gateway' => env('SMS_GATEWAY', 'default'),
        'api_key' => env('SMS_API_KEY'),
        'sender_id' => env('SMS_SENDER_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Inventory Configuration
    |--------------------------------------------------------------------------
    */
    'inventory' => [
        'prevent_overselling' => env('INVENTORY_PREVENT_OVERSELLING', true),
        'low_stock_threshold' => env('INVENTORY_LOW_STOCK_THRESHOLD', 10),
        'reserve_stock_on_checkout' => env('INVENTORY_RESERVE_ON_CHECKOUT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Configuration
    |--------------------------------------------------------------------------
    */
    'media' => [
        'disk' => env('MEDIA_DISK', 'public'),
        'max_file_size' => env('MEDIA_MAX_FILE_SIZE', 5120), // KB
        'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'allowed_video_types' => ['mp4', 'webm', 'ogg'],
        'image_quality' => env('MEDIA_IMAGE_QUALITY', 85),
        'thumbnail_width' => env('MEDIA_THUMBNAIL_WIDTH', 300),
        'thumbnail_height' => env('MEDIA_THUMBNAIL_HEIGHT', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Review Configuration
    |--------------------------------------------------------------------------
    */
    'review' => [
        'require_purchase' => env('REVIEW_REQUIRE_PURCHASE', true),
        'auto_approve' => env('REVIEW_AUTO_APPROVE', false),
        'allow_anonymous' => env('REVIEW_ALLOW_ANONYMOUS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Configuration
    |--------------------------------------------------------------------------
    */
    'search' => [
        'min_query_length' => 2,
        'results_per_page' => 24,
        'enable_suggestions' => true,
    ],
];
