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

        'resend_api_key'        => env('DEPLOY_RESEND_API_KEY'),
        'plume_base_url'        => env('DEPLOY_PLUME_BASE_URL'),
        'plume_platform_api_key' => env('DEPLOY_PLUME_PLATFORM_API_KEY'),
        'platform_mail_domain'  => env('DEPLOY_PLATFORM_MAIL_DOMAIN'),

        'bunny_api_url' => env('DEPLOY_BUNNY_API_URL', 'https://api.bunny.net'),
        'bunny_api_key' => env('DEPLOY_BUNNY_API_KEY'),

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

    'plume' => [
        'base_url'    => env('PLUME_BASE_URL'),
        'master_key'  => env('PLUME_MASTER_KEY'),
        'mail_domain' => env('PLATFORM_MAIL_DOMAIN'),
    ],

    'mail_provider' => env('MAIL_PROVIDER', 'resend'),

    'go54' => [
        'endpoint' => env('GO54_ENDPOINT', 'https://api.go54.com'),
        'username' => env('GO54_USERNAME'),
        'token'    => env('GO54_TOKEN'),
        'enabled'  => env('GO54_ENABLED', true),
    ],

    'desec' => [
        'token'    => env('DESEC_TOKEN'),
        'base_url' => 'https://desec.io/api/v1',
    ],

];
