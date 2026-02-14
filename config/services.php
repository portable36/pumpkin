<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'shipping' => [
        'default_gateway' => env('SHIPPING_DEFAULT_GATEWAY', 'steadfast'),

        'steadfast' => [
            'api_key' => env('STEADFAST_API_KEY'),
            'api_secret' => env('STEADFAST_SECRET_KEY'),
            'sandbox' => env('STEADFAST_SANDBOX', true),
            'base_url_sandbox' => env('STEADFAST_SANDBOX_BASE_URL', 'https://api-staging.steadfast.com.bd/api/v1'),
            'base_url_live' => env('STEADFAST_LIVE_BASE_URL', 'https://api.steadfast.com.bd/api/v1'),
        ],

        'pathao' => [
            'client_id' => env('PATHAO_CLIENT_ID'),
            'client_secret' => env('PATHAO_CLIENT_SECRET'),
            'sandbox' => env('PATHAO_SANDBOX', true),
            'base_url_sandbox' => env('PATHAO_SANDBOX_BASE_URL', 'https://courier-api-sandbox.pathao.com'),
            'base_url_live' => env('PATHAO_LIVE_BASE_URL', 'https://api-hermes.pathao.com'),
            'username' => env('PATHAO_SANDBOX_USERNAME'),
            'password' => env('PATHAO_SANDBOX_PASSWORD'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT'),
    ],

];
