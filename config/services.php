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
        'token' => env('POSTMARK_TOKEN'),
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

    /*
    |--------------------------------------------------------------------------
    | n8n Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for n8n automation webhooks
    |
    */

    'n8n' => [
        'webhook_url' => env('N8N_WEBHOOK_URL', 'http://localhost:5678/webhook/quote-created'),
        'enabled' => env('N8N_ENABLED', true),
    ],

    'tap' => [
        'base_url' => env('TAP_BASE_URL', 'https://api.tap.company/v2'),
        'secret_key' => env('TAP_SECRET_KEY'),
        'public_key' => env('TAP_PUBLIC_KEY'),
        'webhook_secret' => env('TAP_WEBHOOK_SECRET'),
    ],

    'recaptcha' => [
        'site_key'   => env('RECAPTCHA_V3_SITE_KEY', ''),
        'secret_key' => env('RECAPTCHA_V3_SECRET_KEY', ''),
        'enabled'    => env('RECAPTCHA_ENABLED', true),
        'threshold'  => env('RECAPTCHA_THRESHOLD', 0.5),
    ],

    'faalwa' => [
        'base_url'      => env('FAALWA_BASE_URL', 'https://chat.faal-wa.sa/api'),
        'token'         => env('FAALWA_API_TOKEN'),
        'ssl_verify'    => env('FAALWA_SSL_VERIFY', true),
        'webhook_token' => env('FAALWA_WEBHOOK_TOKEN'),
    ],

];
