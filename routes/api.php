<?php

use App\Http\Controllers\Api\CoolifyWebhookController;
use App\Http\Controllers\Api\InstanceWebhookController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return auth()->user();
});

// Webhook from deployed instances — verified by central_api_key + HMAC signature
Route::post('/instance-webhook/setup-complete', [InstanceWebhookController::class, 'setupComplete']);

// Webhook from Coolify — token embedded in path to avoid query-string validation issues
Route::post('/coolify-webhook/{token}', [CoolifyWebhookController::class, 'handle']);

Wave::api();
