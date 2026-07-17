<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use App\Services\Paystack\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Wave\Invoice;

class InvoiceController extends Controller
{
    public function download(Invoice $invoice, InvoiceService $invoiceService): StreamedResponse
    {
        abort_unless($invoice->user_id === Auth::id(), 403);

        if (! $invoice->pdf_path || ! Storage::exists($invoice->pdf_path)) {
            $invoiceService->generatePdf($invoice);
            $invoice->refresh();
        }

        return Storage::download($invoice->pdf_path, $invoice->invoiceNumber().'.pdf');
    }

    public function pay(Request $request, Invoice $invoice, PaystackService $paystack): RedirectResponse
    {
        abort_unless($invoice->user_id === Auth::id(), 403);
        abort_if($invoice->status === 'paid', 302, route('dashboard.invoices'));

        $callbackUrl = route('dashboard.invoices.pay.callback', $invoice);

        try {
            $result = $paystack->initializeTransaction(
                email: $request->user()->email,
                amountKobo: (int) round((float) $invoice->amount * 100),
                metadata: [
                    'type' => 'invoice_payment',
                    'invoice_id' => $invoice->id,
                    'deployment_id' => $invoice->deployment_id,
                ],
                callbackUrl: $callbackUrl,
            );
        } catch (\Throwable $e) {
            Log::error('Invoice payment initialization failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard.invoices')
                ->with('error', 'Could not initialize payment. Please try again or contact support.');
        }

        $authorizationUrl = $result['data']['authorization_url'] ?? null;
        if (! $authorizationUrl) {
            return redirect()->route('dashboard.invoices')
                ->with('error', 'Paystack did not return a payment URL. Please try again.');
        }

        return redirect($authorizationUrl);
    }

    public function payCallback(Request $request, Invoice $invoice, PaystackService $paystack, InvoiceService $invoiceService): RedirectResponse
    {
        abort_unless($invoice->user_id === Auth::id(), 403);

        $reference = $request->query('reference');

        if (! $reference) {
            return redirect()->route('dashboard.invoices')
                ->with('error', 'Payment reference missing. If you completed payment, it will be reflected shortly.');
        }

        // Already handled by webhook — just redirect
        if ($invoice->paystack_reference === $reference) {
            return $this->reactivationRedirect($invoice);
        }

        try {
            $result = $paystack->verifyTransaction($reference);
            $status = $result['data']['status'] ?? null;
        } catch (\Throwable $e) {
            Log::error('Invoice payment verification failed', [
                'invoice_id' => $invoice->id,
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard.invoices')
                ->with('error', 'Could not verify payment. If you paid, it will be reflected within a few minutes.');
        }

        if ($status !== 'success') {
            return redirect()->route('dashboard.invoices')
                ->with('error', 'Payment was not completed. Please try again.');
        }

        $invoiceService->processPayment($invoice, $reference);

        return $this->reactivationRedirect($invoice);
    }

    private function reactivationRedirect(Invoice $invoice): RedirectResponse
    {
        $invoice->refresh();

        if ($invoice->deployment_id) {
            return redirect()->route('dashboard.deployments.show', ['deployment' => $invoice->deployment_id])
                ->with('status', 'Payment received — your service has been reactivated.');
        }

        return redirect()->route('dashboard.invoices')
            ->with('status', 'Payment received. Your invoice is now marked as paid.');
    }
}
