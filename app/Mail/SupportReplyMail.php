<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Wave\SupportMessage;
use Wave\SupportTicket;

class SupportReplyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public SupportTicket $ticket,
        public SupportMessage $message
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: '.$this->ticket->subject.' (Ticket #'.$this->ticket->id.')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.support-reply',
            with: ['ticket' => $this->ticket, 'message' => $this->message],
        );
    }
}
