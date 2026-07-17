<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Wave\Invoice;

class InvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice '.$this->invoice->invoiceNumber().' — ValexHub',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: ['invoice' => $this->invoice],
        );
    }

    public function attachments(): array
    {
        if ($this->invoice->pdf_path && Storage::exists($this->invoice->pdf_path)) {
            return [
                Attachment::fromStorage($this->invoice->pdf_path)
                    ->as($this->invoice->invoiceNumber().'.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
