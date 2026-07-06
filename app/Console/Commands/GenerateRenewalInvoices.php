<?php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;
use Wave\UserProduct;

class GenerateRenewalInvoices extends Command
{
    protected $signature   = 'invoices:generate-renewals {--days=7 : Days before renewal date to generate the invoice}';
    protected $description = 'Generate and email renewal invoices for subscriptions due within the given number of days.';

    public function handle(InvoiceService $invoiceService): int
    {
        $days = (int) $this->option('days');

        // Find active recurring user products whose renewal is exactly $days away
        // (window: from start of that day to end of that day to avoid duplicates on re-runs)
        $targetDate = now()->addDays($days)->startOfDay();

        $due = UserProduct::query()
            ->where('status', 'active')
            ->whereNotNull('next_renewal_date')
            ->whereNotNull('product_pricing_id')
            ->whereDate('next_renewal_date', $targetDate)
            ->with(['user', 'pricing', 'orderAddons.addon', 'product', 'deployments'])
            ->get();

        $count = 0;

        foreach ($due as $userProduct) {
            // Skip if we already generated a renewal invoice for this billing period
            $alreadyGenerated = \Wave\Invoice::where('user_product_id', $userProduct->id)
                ->whereDate('created_at', '>=', now()->startOfDay())
                ->where('paystack_reference', null)
                ->exists();

            if ($alreadyGenerated) {
                continue;
            }

            try {
                $deployment = $userProduct->deployments()->latest()->first();

                // Build renewal line items from the current pricing
                $lineItems = [];

                if ($userProduct->pricing) {
                    $lineItems[] = [
                        'label'  => $userProduct->pricing->label . ' (renewal)',
                        'amount' => (float) $userProduct->pricing->amount,
                        'type'   => 'recurring',
                    ];
                }

                foreach ($userProduct->orderAddons as $oa) {
                    if ($oa->price_type !== 'recurring') {
                        continue;
                    }
                    $lineItems[] = [
                        'label'  => ($oa->addon?->name ?? 'Add-on') . ' (' . ($oa->addon?->billing_cycle ?? 'recurring') . ')',
                        'amount' => (float) $oa->amount_paid,
                        'type'   => 'recurring',
                    ];
                }

                if (empty($lineItems)) {
                    continue;
                }

                $amount = collect($lineItems)->sum('amount');

                $invoice = \Wave\Invoice::create([
                    'user_id'        => $userProduct->user_id,
                    'user_product_id'=> $userProduct->id,
                    'deployment_id'  => $deployment?->id,
                    'amount'         => $amount,
                    'currency'       => 'NGN',
                    'status'         => 'sent',
                    'line_items'     => $lineItems,
                    'due_date'       => $userProduct->next_renewal_date,
                ]);

                $invoiceService->generatePdf($invoice);
                $invoiceService->emailInvoice($invoice);

                $count++;
                $this->line("Renewal invoice generated for user #{$userProduct->user_id} — {$userProduct->product->name}");
            } catch (\Throwable $e) {
                $this->error("Failed for UserProduct #{$userProduct->id}: {$e->getMessage()}");
                \Illuminate\Support\Facades\Log::error('Renewal invoice generation failed', [
                    'user_product_id' => $userProduct->id,
                    'error'           => $e->getMessage(),
                ]);
            }
        }

        $this->info("Done. {$count} renewal invoice(s) generated.");
        return self::SUCCESS;
    }
}
