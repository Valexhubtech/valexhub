<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Wave\Deployment;

class DeploymentReactivatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Deployment $deployment) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Service Has Been Reactivated — ' . $this->deployment->product?->name);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.deployment-reactivated');
    }
}
