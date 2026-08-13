<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdminAlertMail extends Mailable
{
    public function __construct(
        public string $alertSubject,
        public string $alertBody,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "[Action Required] {$this->alertSubject}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-alert');
    }
}
