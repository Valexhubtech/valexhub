<?php

return [

    'mailers' => [
        'mailgun' => [
            'transport' => 'mailgun',
        ],
        'resend' => [
            'transport' => 'resend',
        ],
    ],

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];
