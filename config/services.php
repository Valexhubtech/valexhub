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
        'use_real_service'  => env('COOLIFY_USE_REAL_SERVICE', false),
        'mock_failure_rate' => env('COOLIFY_MOCK_FAILURE_RATE', 0),
    ],

    'hostinger' => [
        'api_token' => env('HOSTINGER_API_TOKEN'),
    ],

];
