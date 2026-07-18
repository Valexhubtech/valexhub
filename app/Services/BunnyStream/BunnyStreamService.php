<?php

namespace App\Services\BunnyStream;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BunnyStreamService
{
    private string $apiKey;

    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey   = config('services.deploy.bunny_api_key', '');
        $this->baseUrl  = rtrim(config('services.deploy.bunny_api_url', 'https://api.bunny.net'), '/');
    }

    /**
     * Create a new Bunny Stream library and return the key values needed
     * to inject into a deployment's environment variables.
     *
     * @return array{library_id: int, api_key: string}
     *
     * @throws \RuntimeException
     */
    public function createLibrary(string $name): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('DEPLOY_BUNNY_API_KEY is not configured.');
        }

        $response = Http::withHeaders([
            'AccessKey' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post("{$this->baseUrl}/videolibrary", [
            'Name' => $name,
        ]);

        if (! $response->successful()) {
            Log::error('BunnyStreamService: failed to create library', [
                'name' => $name,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException(
                "Bunny Stream library creation failed ({$response->status()}): {$response->body()}"
            );
        }

        $data = $response->json();

        $libraryId = $data['Id'] ?? null;
        $apiKey    = $data['ApiKey'] ?? null;

        if (! $libraryId || ! $apiKey) {
            throw new \RuntimeException('Bunny Stream response missing Id or ApiKey.');
        }

        Log::info('BunnyStreamService: library created', ['library_id' => $libraryId, 'name' => $name]);

        return [
            'library_id' => (int) $libraryId,
            'api_key'    => $apiKey,
        ];
    }

    /**
     * Update the webhook URL on an existing Bunny Stream library.
     * Called when a custom domain is activated so Bunny points callbacks
     * at the live domain rather than the provisional sslip.io URL.
     */
    public function updateLibraryWebhook(int $libraryId, string $webhookUrl): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('BunnyStreamService: DEPLOY_BUNNY_API_KEY not set — skipping webhook update');

            return false;
        }

        $response = Http::withHeaders([
            'AccessKey' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post("{$this->baseUrl}/videolibrary/{$libraryId}", [
            'WebhookUrl' => $webhookUrl,
        ]);

        if (! $response->successful()) {
            Log::error('BunnyStreamService: failed to update webhook URL', [
                'library_id' => $libraryId,
                'webhook_url' => $webhookUrl,
                'status' => $response->status(),
            ]);

            return false;
        }

        Log::info('BunnyStreamService: webhook URL updated', [
            'library_id' => $libraryId,
            'webhook_url' => $webhookUrl,
        ]);

        return true;
    }
}
