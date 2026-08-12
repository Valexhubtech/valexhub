<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'coolify' => [
        'use_real_service' => env('COOLIFY_USE_REAL_SERVICE', false),
        'mock_failure_rate' => env('COOLIFY_MOCK_FAILURE_RATE', 0),
        'webhook_secret' => env('COOLIFY_WEBHOOK_SECRET'),
    ],

    'support' => [
        'email' => env('SUPPORT_EMAIL'),
    ],

    // Vars injected into every customer deployment via the Coolify API.
    // These replace Coolify shared project vars (4.1.2 does not auto-apply them).
    'deploy' => [
        'db_host' => env('DEPLOY_DB_HOST'),
        'db_port' => env('DEPLOY_DB_PORT', '5432'),
        'db_user' => env('DEPLOY_DB_USER'),
        'db_password' => env('DEPLOY_DB_PASSWORD'),
        'redis_url' => env('DEPLOY_REDIS_URL'),

        'storage_type' => env('DEPLOY_STORAGE_TYPE', 'b2'),
        'b2_key_id' => env('DEPLOY_B2_APPLICATION_KEY_ID'),
        'b2_key' => env('DEPLOY_B2_APPLICATION_KEY'),
        'b2_bucket_id' => env('DEPLOY_B2_BUCKET_ID'),
        'b2_bucket_name' => env('DEPLOY_B2_BUCKET_NAME'),
        'b2_cdn_base_url' => env('DEPLOY_B2_CDN_BASE_URL'),

        'resend_api_key' => env('DEPLOY_RESEND_API_KEY'),

        'bunny_api_key' => env('DEPLOY_BUNNY_API_KEY'),
        'bunny_stream_token' => env('DEPLOY_BUNNY_STREAM_TOKEN_KEY'),
        'bunny_library_id' => env('DEPLOY_BUNNY_STREAM_LIBRARY_ID'),
        'bunny_cdn_hostname' => env('DEPLOY_BUNNY_STREAM_CDN_HOSTNAME'),
        'bunny_webhook_secret' => env('DEPLOY_BUNNY_WEBHOOK_SECRET'),

        'central_api_url' => env('DEPLOY_CENTRAL_API_URL'),
        'auth_relay_url' => env('DEPLOY_AUTH_RELAY_URL'),
    ],

    'hostinger' => [
        'api_token' => env('HOSTINGER_API_TOKEN'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    ],

];
