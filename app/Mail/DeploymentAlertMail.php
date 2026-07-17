<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Wave\Deployment;

class DeploymentAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  string  $reason  Human-readable reason / event type
     * @param  string  $audience  'support' or 'user'
     */
    public function __construct(
        public Deployment $deployment,
        public string $reason,
        public string $audience,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->audience === 'support'
            ? '[Action Required] Deployment issue: '.($this->deployment->user->email ?? '?')
            : 'Important update about your '.($this->deployment->product->name ?? 'deployment');

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.deployment-alert',
            with: [
                'deployment' => $this->deployment,
                'reason' => $this->reason,
                'audience' => $this->audience,
            ],
        );
    }
}
