<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Jobs\FulfillProductDeployment;
use App\Mail\AffiliateCommissionEarnedMail;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Wave\AffiliateCommission;
use Wave\Deployment;
use Wave\Invoice;
use Wave\PaymentTransaction;
use Wave\Plan;
use Wave\Subscription;
use Wave\UserProduct;

/**
 * Paystack fires `charge.success` for every successful charge — the initial
 * checkout of a subscription, every recurring renewal charge, and one-time
 * product purchases alike. That single event is treated as the universal
 * "payment confirmed" signal driving subscription activation, deployment
 * fulfillment, and affiliate commission accrual.
 */
class PaystackWebhookController extends Controller
{
    public function handler(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];
        $event = $payload['event'] ?? null;
        $data = $payload['data'] ?? [];

        match ($event) {
            'charge.success' => $this->handleChargeSuccess($data),
            'charge.failed' => $this->handleChargeFailed($data),
            'invoice.payment_failed' => $this->handleInvoicePaymentFailed($data),
            'subscription.disable', 'subscription.not_renew' => $this->handleSubscriptionDisabled($data),
            default => null,
        };

        return response()->json(['status' => 'ok']);
    }

    protected function handleChargeSuccess(array $data): void
    {
        $reference = $data['reference'] ?? null;
        if (! $reference) {
            return;
        }

        // Primary idempotency guard: Paystack retries webhook deliveries on any
        // non-2xx response, and the same charge can legitimately be re-delivered.
        if (PaymentTransaction::where('paystack_reference', $reference)->exists()) {
            return;
        }

        $metadata = $data['metadata'] ?? [];
        $type = $metadata['type'] ?? null;

        try {
            DB::transaction(function () use ($data, $reference, $metadata, $type) {
                match ($type) {
                    'subscription_initial' => $this->processSubscriptionInitial($data, $reference, $metadata),
                    'product_purchase' => $this->processProductPurchase($data, $reference, $metadata),
                    'invoice_payment' => $this->processInvoicePayment($reference, $metadata),
                    default => $this->processSubscriptionRenewal($data, $reference),
                };
            });
        } catch (QueryException $e) {
            if (! $this->isDuplicateKeyError($e)) {
                throw $e;
            }
            // Two concurrent webhook deliveries for the same reference raced each
            // other; the loser hits the unique constraint and is safely ignored.
        }
    }

    protected function processSubscriptionInitial(array $data, string $reference, array $metadata): void
    {
        $user = User::find($metadata['billable_id'] ?? null);
        $plan = Plan::find($metadata['plan_id'] ?? null);

        if (! $user || ! $plan) {
            Log::warning('Paystack subscription_initial charge.success missing user/plan', ['reference' => $reference]);

            return;
        }

        $subscription = Subscription::create([
            'billable_type' => $metadata['billable_type'] ?? 'user',
            'billable_id' => $user->id,
            'plan_id' => $plan->id,
            'vendor_slug' => 'paystack',
            'vendor_customer_id' => $data['customer']['customer_code'] ?? null,
            'vendor_transaction_id' => $reference,
            'cycle' => $metadata['billing_cycle'] ?? 'month',
            'status' => 'active',
            'seats' => 1,
        ]);

        $user->syncRoles([]);
        $user->assignRole($plan->role->name);
        $user->clearUserCache();

        $transaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'paystack_reference' => $reference,
            'amount' => ($data['amount'] ?? 0) / 100,
            'currency' => $data['currency'] ?? 'NGN',
            'type' => 'subscription_initial',
            'status' => 'success',
            'raw_payload' => $data,
            'processed_at' => now(),
        ]);

        $this->accrueCommissionIfReferred($subscription, $transaction);
    }

    protected function processSubscriptionRenewal(array $data, string $reference): void
    {
        $customerCode = $data['customer']['customer_code'] ?? null;
        if (! $customerCode) {
            return;
        }

        $subscription = Subscription::where('vendor_slug', 'paystack')
            ->where('vendor_customer_id', $customerCode)
            ->where('status', 'active')
            ->first();

        if (! $subscription) {
            // Not a subscription we track locally (e.g. an unrelated Paystack charge) - ignore.
            return;
        }

        $transaction = PaymentTransaction::create([
            'user_id' => $subscription->billable_id,
            'subscription_id' => $subscription->id,
            'paystack_reference' => $reference,
            'amount' => ($data['amount'] ?? 0) / 100,
            'currency' => $data['currency'] ?? 'NGN',
            'type' => 'subscription_renewal',
            'status' => 'success',
            'raw_payload' => $data,
            'processed_at' => now(),
        ]);

        $this->accrueCommissionIfReferred($subscription, $transaction);
    }

    protected function processProductPurchase(array $data, string $reference, array $metadata): void
    {
        $userProduct = UserProduct::find($metadata['user_product_id'] ?? null);
        if (! $userProduct) {
            Log::warning('Paystack product_purchase charge.success missing user_product', ['reference' => $reference]);

            return;
        }

        $userProduct->markAsActive();

        $transaction = PaymentTransaction::create([
            'user_id' => $userProduct->user_id,
            'user_product_id' => $userProduct->id,
            'paystack_reference' => $reference,
            'amount' => ($data['amount'] ?? 0) / 100,
            'currency' => $data['currency'] ?? 'NGN',
            'type' => 'product_purchase',
            'status' => 'success',
            'raw_payload' => $data,
            'processed_at' => now(),
        ]);

        // Flat 15% one-time commission on every product purchase by a referred user
        $this->accrueProductCommissionIfReferred($userProduct, $transaction);

        $deployment = Deployment::find($metadata['deployment_id'] ?? null);
        if ($deployment) {
            FulfillProductDeployment::dispatch($deployment);
        }

        // Generate invoice and email it to the client
        try {
            app(InvoiceService::class)->generateForPurchase($userProduct, $reference, $deployment);
        } catch (\Throwable $e) {
            Log::error('Invoice generation failed', ['reference' => $reference, 'error' => $e->getMessage()]);
        }
    }

    protected function processInvoicePayment(string $reference, array $metadata): void
    {
        $invoice = Invoice::find($metadata['invoice_id'] ?? null);
        if (! $invoice) {
            Log::warning('invoice_payment webhook: invoice not found', ['reference' => $reference, 'metadata' => $metadata]);

            return;
        }

        try {
            app(InvoiceService::class)->processPayment($invoice, $reference);
        } catch (\Throwable $e) {
            Log::error('invoice_payment processing failed', [
                'invoice_id' => $invoice->id,
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function accrueProductCommissionIfReferred(UserProduct $userProduct, PaymentTransaction $transaction): void
    {
        $buyer = $userProduct->user;
        $affiliate = $buyer?->referrer;
        if (! $affiliate) {
            return;
        }

        $rate = 15.00;
        $amount = (float) $transaction->amount;

        try {
            $commission = AffiliateCommission::create([
                'affiliate_id' => $affiliate->id,
                'referred_user_id' => $buyer->id,
                'subscription_id' => null,
                'payment_transaction_id' => $transaction->id,
                'billing_month_number' => 1,
                'plan_monthly_price' => $amount,
                'commission_rate' => $rate,
                'commission_amount' => round($amount * $rate / 100, 2),
                'status' => 'accrued',
                'accrued_at' => now(),
            ]);
        } catch (QueryException $e) {
            if ($this->isDuplicateKeyError($e)) {
                return;
            }
            throw $e;
        }

        try {
            Mail::to($affiliate->email)->queue(new AffiliateCommissionEarnedMail($commission));
        } catch (\Throwable $e) {
            Log::error('AffiliateCommission mail failed', [
                'affiliate_id' => $affiliate->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Accrues exactly one commission row for a confirmed (initial or renewal)
     * subscription payment, if the paying user was referred by an affiliate.
     * Months 1-3 earn 15%, month 4 onward earns 8%, for as long as payments
     * keep succeeding - a failed/unpaid month simply never reaches this method.
     */
    protected function accrueCommissionIfReferred(Subscription $subscription, PaymentTransaction $transaction): void
    {
        $referredUser = $subscription->user;
        $affiliate = $referredUser?->referrer;
        if (! $affiliate) {
            return;
        }

        $billingMonthNumber = PaymentTransaction::where('subscription_id', $subscription->id)
            ->whereIn('type', ['subscription_initial', 'subscription_renewal'])
            ->count();

        $rate = $billingMonthNumber <= 3 ? 15.00 : 8.00;
        $monthlyPrice = (float) ($subscription->plan->monthly_price ?? 0);

        try {
            $commission = AffiliateCommission::create([
                'affiliate_id' => $affiliate->id,
                'referred_user_id' => $referredUser->id,
                'subscription_id' => $subscription->id,
                'payment_transaction_id' => $transaction->id,
                'billing_month_number' => $billingMonthNumber,
                'plan_monthly_price' => $monthlyPrice,
                'commission_rate' => $rate,
                'commission_amount' => round($monthlyPrice * $rate / 100, 2),
                'status' => 'accrued',
                'accrued_at' => now(),
            ]);
        } catch (QueryException $e) {
            if ($this->isDuplicateKeyError($e)) {
                return; // Already accrued for this billing month - idempotent no-op.
            }
            throw $e;
        }

        Mail::to($affiliate->email)->queue(new AffiliateCommissionEarnedMail($commission));
    }

    protected function handleChargeFailed(array $data): void
    {
        $metadata = $data['metadata'] ?? [];
        $type = $metadata['type'] ?? null;

        if ($type !== 'product_purchase') {
            return;
        }

        $userProduct = UserProduct::find($metadata['user_product_id'] ?? null);
        if (! $userProduct) {
            return;
        }

        // Only suspend if the product was never successfully activated — a failed
        // retry charge on an already-active product is handled by invoice.payment_failed.
        if ($userProduct->status === 'inactive') {
            $userProduct->update(['status' => 'inactive']);

            $deployment = Deployment::find($metadata['deployment_id'] ?? null);
            $deployment?->update(['status' => 'failed', 'failure_reason' => 'Payment failed']);

            Log::warning('Product purchase payment failed', [
                'user_product_id' => $userProduct->id,
                'reference' => $data['reference'] ?? null,
            ]);
        }
    }

    protected function handleInvoicePaymentFailed(array $data): void
    {
        // Paystack sends this when a recurring subscription renewal charge fails.
        $customerCode = $data['customer']['customer_code'] ?? null;
        if (! $customerCode) {
            return;
        }

        // Wave subscription (plan-based) — cancel it.
        $subscription = Subscription::where('vendor_slug', 'paystack')
            ->where('vendor_customer_id', $customerCode)
            ->where('status', 'active')
            ->first();

        if ($subscription) {
            $subscription->cancel();

            return;
        }

        // Product subscription — look up via the customer email matched to a user.
        $email = $data['customer']['email'] ?? null;
        if (! $email) {
            Log::warning('invoice.payment_failed: no email in payload', ['customer_code' => $customerCode]);

            return;
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            return;
        }

        $userProduct = UserProduct::where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('product_pricing_id')
            ->latest()
            ->first();

        if ($userProduct) {
            $userProduct->update(['status' => 'suspended']);

            Deployment::where('user_product_id', $userProduct->id)
                ->where('status', 'active')
                ->update(['status' => 'suspended']);

            Log::info('UserProduct suspended due to invoice.payment_failed', [
                'user_id' => $user->id,
                'user_product_id' => $userProduct->id,
            ]);
        }
    }

    protected function handleSubscriptionDisabled(array $data): void
    {
        $customerCode = $data['customer']['customer_code'] ?? null;
        if (! $customerCode) {
            return;
        }

        $subscription = Subscription::where('vendor_slug', 'paystack')
            ->where('vendor_customer_id', $customerCode)
            ->where('status', 'active')
            ->first();

        $subscription?->cancel();
    }

    protected function isDuplicateKeyError(QueryException $e): bool
    {
        return ($e->errorInfo[1] ?? null) === 1062;
    }
}
