<?php

return [

    'mailers' => [
        'mailgun' => [
            'transport' => 'mailgun',
        ],
        'resend' => [
            'transport' => 'resend',
        ],
        'plume' => [
            'transport' => 'plume',
        ],
    ],

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];
