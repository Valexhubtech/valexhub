<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\RawMessage;

class PlumeTransport implements TransportInterface
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct(string $baseUrl, string $apiKey)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey  = $apiKey;
    }

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        $email = MessageConverter::toEmail($message);

        $from = $email->getFrom()[0] ?? null;
        $to   = array_map(fn ($addr) => $addr->getAddress(), $email->getTo());

        $payload = [
            'from'    => $from ? "{$from->getName()} <{$from->getAddress()}>" : config('mail.from.address'),
            'to'      => $to,
            'subject' => $email->getSubject() ?? '',
            'html'    => $email->getHtmlBody() ?? null,
            'text'    => $email->getTextBody() ?? null,
        ];

        if ($replyTo = $email->getReplyTo()) {
            $payload['reply_to'] = array_map(fn ($addr) => $addr->getAddress(), $replyTo);
        }

        Http::withToken($this->apiKey)
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout(15)
            ->post('/emails', array_filter($payload, fn ($v) => $v !== null));

        return new SentMessage($message, $envelope ?? Envelope::create($message));
    }

    public function __toString(): string
    {
        return 'plume';
    }
}
