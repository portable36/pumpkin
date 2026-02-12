<?php

return [
    /**
     * Application Mode Settings
     */
    'mode' => env('ECOMMERCE_MODE', 'multivendor'), // 'singlevendor' or 'multivendor'
    
    /**
     * Commission Settings
     */
    'commission' => [
        'default_rate' => env('VENDOR_COMMISSION_RATE', 10), // percent
        'payment_terms' => 'monthly', // 'daily', 'weekly', 'monthly'
        'minimum_payout' => env('MINIMUM_PAYOUT', 1000), // in local currency
    ],

    /**
     * Tax Settings
     */
    'tax' => [
        'enabled' => env('TAX_ENABLED', true),
        'default_rate' => env('TAX_RATE', 15), // percent
        'tax_id_format' => 'BIN', // Bangladesh
    ],

    /**
     * Shipping Settings
     */
    'shipping' => [
        'default_cost' => env('DEFAULT_SHIPPING_COST', 50),
        'free_shipping_amount' => env('FREE_SHIPPING_AMOUNT', 5000),
        'weight_unit' => 'kg',
        'couriers' => ['pathao', 'steadfast'],
        'enable_international' => env('ENABLE_INTERNATIONAL_SHIPPING', false),
    ],

    /**
     * Payment Gateway Settings
     */
    'gateways' => [
        'bkash' => [
            'enabled' => env('BKASH_ENABLED', true),
            'app_key' => env('BKASH_APP_KEY'),
            'app_secret' => env('BKASH_APP_SECRET'),
            'username' => env('BKASH_USERNAME'),
            'password' => env('BKASH_PASSWORD'),
            'commission' => 1.5, // percent
        ],
        'sslcommerz' => [
            'enabled' => env('SSLCOMMERZ_ENABLED', true),
            'store_id' => env('SSLCOMMERZ_STORE_ID'),
            'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
            'is_sandbox' => env('SSLCOMMERZ_SANDBOX', true),
            'commission' => 2.5, // percent
        ],
        'cod' => [
            'enabled' => env('COD_ENABLED', true),
            'charge_percentage' => 0, // free
        ],
    ],

    /**
     * Search Settings
     */
    'search' => [
        'engine' => 'database', // 'database' or 'elasticsearch'
        'min_query_length' => 2,
        'max_suggestions' => 10,
        'facets' => ['category', 'brand', 'price_range', 'rating'],
    ],

    /**
     * Inventory Settings
     */
    'inventory' => [
        'track_stock' => true,
        'low_stock_threshold' => 10,
        'stock_reservation_time' => 15, // minutes
        'allow_backorder' => env('ALLOW_BACKORDER', false),
    ],

    /**
     * Newsletter Settings
     */
    'newsletter' => [
        'enabled' => env('NEWSLETTER_ENABLED', true),
        'provider' => 'database', // 'mailchimp', 'sendgrid', 'database'
    ],

    /**
     * Media Settings
     */
    'media' => [
        'disk' => env('MEDIA_DISK', 'public'),
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'generate_thumbnails' => true,
        'thumbnail_sizes' => [
            'small' => '150x150',
            'medium' => '300x300',
            'large' => '600x600',
        ],
        'convert_to_webp' => env('CONVERT_TO_WEBP', true),
    ],

    /**
     * Cache Settings
     */
    'cache' => [
        'ttl' => env('CACHE_TTL', 3600), // seconds
        'product_ttl' => 86400, // 24 hours
        'category_ttl' => 86400, // 24 hours
        'vendor_ttl' => 3600, // 1 hour
    ],

    /**
     * Email Settings
     */
    'email' => [
        'from_address' => env('MAIL_FROM_ADDRESS'),
        'from_name' => env('MAIL_FROM_NAME', 'Pumpkin'),
        'bcc_orders' => env('BCC_ORDERS_EMAIL'),
        'enable_transactional' => true,
    ],

    /**
     * SMS Settings
     */
    'sms' => [
        'enabled' => env('SMS_ENABLED', true),
        'provider' => 'twilio', // 'twilio', 'nexmo'
        'twilio_sid' => env('TWILIO_SID'),
        'twilio_token' => env('TWILIO_TOKEN'),
        'twilio_phone' => env('TWILIO_PHONE'),
        'otp_validity' => 600, // seconds
    ],

    /**
     * Social Login Settings
     */
    'social' => [
        'google' => [
            'enabled' => env('GOOGLE_LOGIN_ENABLED', true),
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        ],
        'facebook' => [
            'enabled' => env('FACEBOOK_LOGIN_ENABLED', true),
            'app_id' => env('FACEBOOK_APP_ID'),
            'app_secret' => env('FACEBOOK_APP_SECRET'),
        ],
    ],

    /**
     * Security Settings
     */
    'security' => [
        'password_min_length' => 8,
        'login_attempt_limit' => 5,
        'login_attempt_window' => 15, // minutes
        'session_timeout' => 1440, // minutes
        'require_email_verification' => env('REQUIRE_EMAIL_VERIFICATION', true),
    ],

    /**
     * Feature Flags
     */
    'features' => [
        'wishlist' => true,
        'reviews' => true,
        'ratings' => true,
        'vendor_storefronts' => true,
        'vendor_analytics' => true,
        'bulk_import' => true,
        'product_variants' => true,
        'product_bundles' => false,
        'subscription' => false,
        'loyalty_points' => false,
    ],

    /**
     * Analytics Settings
     */
    'analytics' => [
        'enabled' => env('ANALYTICS_ENABLED', true),
        'google_analytics_id' => env('GOOGLE_ANALYTICS_ID'),
        'track_events' => true,
        'track_conversions' => true,
    ],
];
