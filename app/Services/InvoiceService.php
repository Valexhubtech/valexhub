<?php

namespace App\Services;

use App\Mail\DeploymentReactivatedMail;
use App\Mail\InvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Wave\Deployment;
use Wave\Invoice;
use Wave\UserProduct;

class InvoiceService
{
    public function generateForPurchase(UserProduct $userProduct, string $paystackReference, ?Deployment $deployment = null): Invoice
    {
        $userProduct->load(['user', 'pricing', 'orderAddons.addon', 'product']);

        $lineItems = $this->buildLineItems($userProduct);

        $invoice = Invoice::create([
            'user_id'             => $userProduct->user_id,
            'user_product_id'     => $userProduct->id,
            'deployment_id'       => $deployment?->id,
            'amount'              => $userProduct->amount_paid,
            'currency'            => 'NGN',
            'status'              => 'paid',
            'paystack_reference'  => $paystackReference,
            'line_items'          => $lineItems,
            'paid_at'             => now(),
        ]);

        $this->generatePdf($invoice);
        $this->emailInvoice($invoice);

        return $invoice;
    }

    public function generatePdf(Invoice $invoice): void
    {
        $invoice->load(['user', 'userProduct.product', 'deployment']);

        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $invoice]);

        $path = 'invoices/' . $invoice->invoiceNumber() . '.pdf';
        Storage::put($path, $pdf->output());

        $invoice->update(['pdf_path' => $path]);
    }

    public function emailInvoice(Invoice $invoice): void
    {
        $invoice->load(['user']);
        Mail::to($invoice->user->email)->queue(new InvoiceMail($invoice));
    }

    /**
     * Mark an invoice as paid and, if its deployment was suspended, reactivate it.
     * Returns true if processed, false if already handled (idempotent).
     */
    public function processPayment(Invoice $invoice, string $paystackReference): bool
    {
        // Idempotency: already processed by webhook or a previous callback hit
        if ($invoice->paystack_reference) {
            return false;
        }

        $invoice->update([
            'status'             => 'paid',
            'paid_at'            => now(),
            'paystack_reference' => $paystackReference,
        ]);

        // Regenerate PDF so it reflects "paid" status
        try {
            $this->generatePdf($invoice->fresh());
        } catch (\Throwable $e) {
            Log::error('Invoice PDF regeneration failed after payment', [
                'invoice_id' => $invoice->id,
                'error'      => $e->getMessage(),
            ]);
        }

        // Advance the renewal date on the linked user product (for recurring invoices)
        if ($invoice->user_product_id) {
            $userProduct = \Wave\UserProduct::with('pricing')->find($invoice->user_product_id);
            if ($userProduct && $userProduct->pricing?->isRecurring()) {
                $base = $userProduct->next_renewal_date ?? now();
                $next = match ($userProduct->pricing->price_type) {
                    'monthly'   => $base->copy()->addMonth(),
                    'quarterly' => $base->copy()->addMonths(3),
                    'yearly'    => $base->copy()->addYear(),
                    default     => null,
                };
                if ($next) {
                    $userProduct->update(['next_renewal_date' => $next]);
                }
            }
        }

        // Reactivate suspended deployment if one is linked
        if ($invoice->deployment_id) {
            $deployment = Deployment::with('user', 'userProduct')->find($invoice->deployment_id);

            if ($deployment && $deployment->status === 'suspended') {
                $deployment->update(['status' => 'active']);
                $deployment->userProduct?->update(['status' => 'active']);

                // Notify client
                try {
                    Mail::to($deployment->user->email)->queue(new DeploymentReactivatedMail($deployment));
                } catch (\Throwable $e) {
                    Log::error('Reactivation email failed', [
                        'deployment_id' => $deployment->id,
                        'error'         => $e->getMessage(),
                    ]);
                }

                // Coolify API reactivation (deferred until Coolify is configured)
                if ($deployment->coolify_app_id) {
                    try {
                        // TODO: app(CoolifyService::class)->start($deployment->coolify_app_id);
                        Log::info('Coolify reactivation pending implementation', [
                            'deployment_id' => $deployment->id,
                            'app_id'        => $deployment->coolify_app_id,
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('Coolify reactivation failed', [
                            'deployment_id' => $deployment->id,
                            'error'         => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        return true;
    }

    public function buildLineItems(UserProduct $userProduct): array
    {
        $items = [];

        if ((float) $userProduct->setup_amount > 0) {
            $items[] = [
                'label'  => $userProduct->deployment_type === 'onprem' ? 'License & Setup Fee' : 'Setup Fee',
                'amount' => (float) $userProduct->setup_amount,
                'type'   => 'onetime',
            ];
        }

        if ($userProduct->pricing) {
            $items[] = [
                'label'  => $userProduct->pricing->label . ' (first period)',
                'amount' => (float) $userProduct->pricing->amount,
                'type'   => 'recurring',
            ];
        }

        foreach ($userProduct->orderAddons as $orderAddon) {
            $name = $orderAddon->addon?->name ?? 'Add-on';
            if ($orderAddon->price_type === 'recurring') {
                $name .= ' (' . ($orderAddon->addon?->billing_cycle ?? 'recurring') . ')';
            }
            $items[] = [
                'label'  => $name,
                'amount' => (float) $orderAddon->amount_paid,
                'type'   => $orderAddon->price_type,
            ];
        }

        return $items;
    }
}
