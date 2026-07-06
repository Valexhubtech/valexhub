<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Wave\Facades\Wave;

// Wave routes
Wave::routes();

// Demo Request Routes
Route::post('/demo-request', [\App\Http\Controllers\DemoRequestController::class, 'store'])->name('demo-request.store');

// Pay & Deploy (Pathway A) checkout
Route::middleware('auth')->group(function () {
    Route::post('/products/{product}/checkout', [\App\Http\Controllers\ProductCheckoutController::class, 'initialize'])
        ->name('products.checkout');
    Route::get('/products/checkout/callback', [\App\Http\Controllers\ProductCheckoutController::class, 'callback'])
        ->name('products.checkout.callback');

    Route::get('/deployments/{deployment}/one-click-login', [\App\Http\Controllers\DeploymentLoginController::class, 'redirect'])
        ->middleware(['signed', 'authorize-deployment-access'])
        ->name('deployments.one-click-login');

    Route::get('/dashboard/invoices/{invoice}/download', [\App\Http\Controllers\InvoiceController::class, 'download'])
        ->name('dashboard.invoices.download');
    Route::get('/dashboard/invoices/{invoice}/pay', [\App\Http\Controllers\InvoiceController::class, 'pay'])
        ->name('dashboard.invoices.pay');
    Route::get('/dashboard/invoices/{invoice}/pay/callback', [\App\Http\Controllers\InvoiceController::class, 'payCallback'])
        ->name('dashboard.invoices.pay.callback');

    Route::post('/dashboard/support/tickets', [\App\Http\Controllers\SupportTicketController::class, 'store'])
        ->name('support.tickets.store');

    // Domain purchase callback
    Route::get('/dashboard/domain/callback', [\App\Http\Controllers\DomainCheckoutController::class, 'callback'])
        ->name('dashboard.domain.callback');
});

// Paystack webhook (no auth - signature-verified instead)
Route::post('/webhook/paystack', [\App\Http\Controllers\Billing\PaystackWebhookController::class, 'handler'])
    ->middleware('paystack-webhook-signature')
    ->name('webhook.paystack');

// Affiliate referral link capture
Route::get('/r/{referralCode}', [\App\Http\Controllers\ReferralController::class, 'capture'])
    ->name('referral.capture');
