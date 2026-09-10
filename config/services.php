<?php

return [

    'cashup' => [
        'base_url' => env('CASHUP_BASE_URL', 'https://api-link.cashup.id'),
        'username' => env('CASHUP_USERNAME'),
        'password' => env('CASHUP_PASSWORD'),
        'device_id' => env('CASHUP_DEVICE_ID'),
        'auth_scheme' => env('CASHUP_AUTH_SCHEME', ''),
        'callback_url' => env('CASHUP_CALLBACK_URL'),
        'redirect_url' => env('CASHUP_REDIRECT_URL'),
    ],

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

];
