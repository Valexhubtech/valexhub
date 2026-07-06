<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Wave\SupportTicket;

class SupportTicketCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public SupportTicket $ticket,
        public string $messageBody
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Support] ' . $this->ticket->subject . ' (Ticket #' . $this->ticket->id . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.support-ticket-created',
            with: ['ticket' => $this->ticket, 'messageBody' => $this->messageBody],
        );
    }
}
