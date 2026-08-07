<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ResendEmailService
{
    protected string $apiKey;

    protected string $baseUrl = 'https://api.resend.com';

    public function __construct()
    {
        $this->apiKey = config('services.resend.key');
    }

    /**
     * Test API connection
     */
    public function testConnection(): array
    {
        if (! $this->apiKey) {
            return [
                'success' => false,
                'error' => 'No Resend API key configured',
            ];
        }

        // Try to get domains as a simple test
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ])
            ->get("{$this->baseUrl}/domains");

        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $response->json(),
            ];
        }

        return [
            'success' => false,
            'error' => $response->body(),
            'status' => $response->status(),
        ];
    }

    /**
     * Get sent emails from Resend API
     */
    public function getSentEmails(int $limit = 50, ?string $after = null, ?string $before = null): array
    {
        $params = [
            'limit' => min($limit, 100), // Max limit is 100
        ];

        if ($after) {
            $params['after'] = $after;
        }

        if ($before) {
            $params['before'] = $before;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ])
            ->get("{$this->baseUrl}/emails", $params);

        if ($response->successful()) {
            $data = $response->json();
            // Log the response for debugging
            \Log::info('Resend API Response:', [
                'total_emails' => count($data['data'] ?? []),
                'params_used' => $params,
                'sample_emails' => array_slice($data['data'] ?? [], 0, 2), // Show first 2 emails for debugging
            ]);

            // Filter by domain on the client side since API filtering might not work as expected
            $domain = $this->getConfiguredDomain();
            \Log::info('Domain filtering config:', [
                'configured_domain' => $domain,
                'env_resend_domain' => env('RESEND_DOMAIN'),
                'env_mail_from_domain' => env('MAIL_FROM_DOMAIN'),
            ]);

            if ($domain && isset($data['data'])) {
                $originalCount = count($data['data']);

                $data['data'] = array_filter($data['data'], function ($email) use ($domain) {
                    // Extract email from the full "From" field like "Wave" <no-reply@valexhub.com>
                    $fromField = $email['from'] ?? '';

                    // Extract email address from format: "Name" <email@domain.com> or just email@domain.com
                    if (preg_match('/<([^>]+)>/', $fromField, $matches)) {
                        $emailAddress = $matches[1];
                    } else {
                        $emailAddress = $fromField;
                    }

                    // Check if the email address contains the domain
                    $contains = str_contains($emailAddress, $domain);

                    \Log::info('Domain filter check:', [
                        'from_field' => $fromField,
                        'extracted_email' => $emailAddress,
                        'domain' => $domain,
                        'contains_domain' => $contains,
                    ]);

                    return $contains;
                });

                // Re-index the array
                $data['data'] = array_values($data['data']);

                \Log::info('After domain filtering:', [
                    'domain' => $domain,
                    'original_count' => $originalCount,
                    'filtered_count' => count($data['data']),
                    'sample_filtered' => array_slice($data['data'], 0, 2),
                ]);
            }

            return $data;
        } else {
            // Log errors for debugging
            \Log::error('Resend API Error:', [
                'status' => $response->status(),
                'response' => $response->json(),
                'params' => $params,
            ]);
        }

        return ['data' => [], 'object' => 'list'];
    }

    /**
     * Get a specific email by ID
     */
    public function getEmail(string $emailId): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ])
            ->get("{$this->baseUrl}/emails/{$emailId}");

        if ($response->successful()) {
            return $response->json();
        }

        \Log::error('Resend Get Email Error:', [
            'status' => $response->status(),
            'response' => $response->body(),
            'email_id' => $emailId,
        ]);

        return null;
    }

    /**
     * Get email events (delivered, opened, clicked, etc.)
     */
    public function getEmailEvents(string $emailId): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ])
            ->get("{$this->baseUrl}/emails/{$emailId}/events");

        if ($response->successful()) {
            return $response->json()['data'] ?? [];
        }

        \Log::error('Resend Get Email Events Error:', [
            'status' => $response->status(),
            'response' => $response->body(),
            'email_id' => $emailId,
        ]);

        return [];
    }

    /**
     * Get the configured domain for filtering
     */
    public function getConfiguredDomain(): ?string
    {
        return env('RESEND_DOMAIN') ?? env('MAIL_FROM_DOMAIN') ?? config('app.mail_domain') ?? null;
    }

    /**
     * Format email data for display
     */
    public function formatEmailsForDisplay(array $emails): Collection
    {
        return collect($emails['data'] ?? [])->map(function ($email) {
            return [
                'id' => $email['id'],
                'to' => is_array($email['to']) ? implode(', ', $email['to']) : ($email['to'] ?? ''),
                'from' => $email['from'] ?? '',
                'subject' => $email['subject'] ?? '',
                'status' => $email['last_event'] ?? 'sent',
                'created_at' => $email['created_at'] ?? '',
                'html' => $email['html'] ?? null,
                'text' => $email['text'] ?? null,
                'bcc' => is_array($email['bcc'] ?? null) ? implode(', ', $email['bcc']) : ($email['bcc'] ?? ''),
                'cc' => is_array($email['cc'] ?? null) ? implode(', ', $email['cc']) : ($email['cc'] ?? ''),
                'reply_to' => is_array($email['reply_to'] ?? null) ? implode(', ', $email['reply_to']) : ($email['reply_to'] ?? ''),
            ];
        });
    }

    /**
     * Get domains configured in Resend
     */
    public function getDomains(): array
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/domains");

        if ($response->successful()) {
            return $response->json()['data'] ?? [];
        }

        return [];
    }
}
