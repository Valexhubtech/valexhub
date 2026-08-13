<?php

use Illuminate\Support\Facades\Facade;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

return [

    'support_email' => env('SUPPORT_EMAIL', ''),

    'aliases' => Facade::defaultAliases()->merge([
        'JWTAuth' => JWTAuth::class,
        'JWTFactory' => JWTFactory::class,
    ])->toArray(),

];
