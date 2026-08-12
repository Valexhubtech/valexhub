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

use App\Http\Controllers\AuthRelayController;
use App\Http\Controllers\Billing\PaystackWebhookController;
use App\Http\Controllers\DemoRequestController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\DeploymentLoginController;
use App\Http\Controllers\DomainCheckoutController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductCheckoutController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SupportTicketController;
use Illuminate\Support\Facades\Route;
use Wave\Facades\Wave;

// Wave routes
Wave::routes();

// Demo Request Routes
Route::post('/demo-request', [DemoRequestController::class, 'store'])->name('demo-request.store');

// Internship
Route::post('/internship/apply', [InternshipController::class, 'store'])->name('internship.apply');
Route::get('/internship/applications/{application}/cv', [InternshipController::class, 'downloadCv'])
    ->middleware('auth')
    ->name('internship.cv.download');

// Pay & Deploy (Pathway A) checkout
Route::middleware('auth')->group(function () {
    Route::post('/products/{product}/checkout', [ProductCheckoutController::class, 'initialize'])
        ->name('products.checkout');
    Route::get('/products/checkout/callback', [ProductCheckoutController::class, 'callback'])
        ->name('products.checkout.callback');

    Route::get('/deployments/{deployment}/one-click-login', [DeploymentLoginController::class, 'redirect'])
        ->middleware(['signed', 'authorize-deployment-access'])
        ->name('deployments.one-click-login');

    Route::get('/dashboard/invoices/{invoice}/download', [InvoiceController::class, 'download'])
        ->name('dashboard.invoices.download');
    Route::get('/dashboard/invoices/{invoice}/pay', [InvoiceController::class, 'pay'])
        ->name('dashboard.invoices.pay');
    Route::get('/dashboard/invoices/{invoice}/pay/callback', [InvoiceController::class, 'payCallback'])
        ->name('dashboard.invoices.pay.callback');

    Route::post('/dashboard/support/tickets', [SupportTicketController::class, 'store'])
        ->name('support.tickets.store');

    // Domain purchase callback
    Route::get('/dashboard/domain/callback', [DomainCheckoutController::class, 'callback'])
        ->name('dashboard.domain.callback');
});

// Paystack webhook (no auth - signature-verified instead)
Route::post('/webhook/paystack', [PaystackWebhookController::class, 'handler'])
    ->middleware('paystack-webhook-signature')
    ->name('webhook.paystack');

// Affiliate referral link capture
Route::get('/r/{referralCode}', [ReferralController::class, 'capture'])
    ->name('referral.capture');

// Google OAuth relay — no auth middleware, JWT-verified internally
Route::get('/auth-relay/start', [AuthRelayController::class, 'start'])->name('auth-relay.start');
Route::get('/auth-relay/callback', [AuthRelayController::class, 'callback'])->name('auth-relay.callback');
