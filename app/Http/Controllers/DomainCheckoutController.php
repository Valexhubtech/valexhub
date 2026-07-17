<?php

namespace App\Http\Controllers;

use App\Jobs\RegisterDomainJob;
use App\Services\Paystack\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Wave\DomainPurchase;
use Wave\Invoice;

class DomainCheckoutController extends Controller
{
    public function callback(Request $request, PaystackService $paystack): RedirectResponse
    {
        $reference = $request->query('reference');

        if (! $reference) {
            return redirect()->route('dashboard')->with('error', 'Invalid payment reference.');
        }

        // Find the pending purchase
        $purchase = DomainPurchase::where('paystack_reference', $reference)
            ->where('user_id', Auth::id())
            ->first();

        if (! $purchase) {
            return redirect()->route('dashboard')->with('error', 'Domain purchase record not found.');
        }

        if ($purchase->payment_status === 'paid') {
            return $this->successRedirect($purchase, 'Your domain is already being set up.');
        }

        // Verify payment with Paystack
        try {
            $result = $paystack->verifyTransaction($reference);

            // Paystack wraps the transaction status under ['data']['status']
            // Top-level ['status'] is a boolean (true/false), not the payment outcome
            $paymentStatus = $result['data']['status'] ?? '';

            Log::info('DomainCheckout: Paystack verify response', [
                'reference' => $reference,
                'payment_status' => $paymentStatus,
                'top_status' => $result['status'] ?? null,
            ]);

            if ($paymentStatus !== 'success') {
                $purchase->update(['payment_status' => 'failed']);

                return redirect()->route('dashboard.deployments.show', ['deployment' => $purchase->deployment_id])
                    ->with('error', 'Payment was not completed on Paystack. Please try again.');
            }

            $purchase->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);

            // Issue invoice so the user has a receipt
            Invoice::create([
                'user_id' => $purchase->user_id,
                'deployment_id' => $purchase->deployment_id,
                'amount' => $purchase->total_kobo / 100,
                'currency' => 'NGN',
                'status' => 'paid',
                'paystack_reference' => $reference,
                'paid_at' => now(),
                'line_items' => [
                    [
                        'description' => 'Domain registration: '.$purchase->domain.' (1 year)',
                        'amount' => $purchase->domain_price_kobo / 100,
                    ],
                    [
                        'description' => 'Setup & DNS configuration fee',
                        'amount' => $purchase->setup_fee_kobo / 100,
                    ],
                ],
            ]);

            Log::info('Domain payment confirmed', [
                'purchase_id' => $purchase->id,
                'domain' => $purchase->domain,
            ]);

            // On sync queue (shared hosting) this runs immediately in the same request.
            // On a real queue (VPS) it runs in the background via a worker.
            // Either way, payment is already confirmed above — a registration failure
            // only sets registration_status=failed, it never fails the payment.
            try {
                RegisterDomainJob::dispatch($purchase->id);
            } catch (\Throwable $e) {
                Log::error('DomainCheckout: registration job failed', [
                    'purchase_id' => $purchase->id,
                    'error' => $e->getMessage(),
                ]);
                // Don't rethrow — payment is confirmed, registration can be retried from admin
            }

            return $this->successRedirect($purchase, "Payment received! Your domain {$purchase->domain} is being registered. DNS will be active within 30 minutes.");

        } catch (\Throwable $e) {
            Log::error('DomainCheckout: Paystack verify failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')->with('error', 'Could not verify payment. Contact support if charged.');
        }
    }

    private function successRedirect(DomainPurchase $purchase, string $message): RedirectResponse
    {
        if ($purchase->deployment_id) {
            return redirect()->route('dashboard.deployments.show', ['deployment' => $purchase->deployment_id])
                ->with('status', $message);
        }

        return redirect()->route('dashboard')->with('status', $message);
    }
}
