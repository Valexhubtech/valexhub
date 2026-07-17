<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayoutRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $affiliate,
        public float $amount,
        public array $bankDetails,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Affiliate Payout Request — '.$this->affiliate->name.' (₦'.number_format($this->amount, 2).')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payout-request',
            with: [
                'affiliate' => $this->affiliate,
                'amount' => $this->amount,
                'bankDetails' => $this->bankDetails,
            ],
        );
    }
}
