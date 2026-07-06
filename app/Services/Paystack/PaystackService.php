<?php

namespace App\Services\Paystack;

use GuzzleHttp\Client;

class PaystackService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.paystack.co',
            'headers' => [
                'Authorization' => 'Bearer '.config('wave.paystack.secret_key'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Initialize a transaction (one-time charge or first charge of a subscription plan).
     * Amount must be in kobo (NGN * 100).
     */
    public function initializeTransaction(string $email, int $amountKobo, array $metadata = [], ?string $callbackUrl = null, ?string $planCode = null): array
    {
        $payload = array_filter([
            'email' => $email,
            'amount' => $amountKobo,
            'metadata' => $metadata,
            'callback_url' => $callbackUrl,
            'plan' => $planCode,
        ], fn ($value) => ! is_null($value));

        $response = $this->client->post('/transaction/initialize', ['json' => $payload]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Verify a transaction by its reference.
     */
    public function verifyTransaction(string $reference): array
    {
        $response = $this->client->get("/transaction/verify/{$reference}");

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Create a recurring billing plan on Paystack. Returns the plan_code to be
     * stored on the local Plan model (paystack_plan_code_monthly/yearly).
     */
    public function createPlan(string $name, int $amountKobo, string $interval): array
    {
        $response = $this->client->post('/plan', [
            'json' => [
                'name' => $name,
                'amount' => $amountKobo,
                'interval' => $interval, // 'monthly' or 'annually'
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Verify that a webhook payload was genuinely sent by Paystack.
     */
    public function verifyWebhookSignature(string $payload, ?string $signatureHeader): bool
    {
        if (empty($signatureHeader)) {
            return false;
        }

        $expected = hash_hmac('sha512', $payload, (string) config('wave.paystack.secret_key'));

        return hash_equals($expected, $signatureHeader);
    }

    /**
     * Whether the Paystack secret key is configured (used to fail fast/log
     * clearly in dev/staging when keys haven't been added yet).
     */
    public function isConfigured(): bool
    {
        return ! empty(config('wave.paystack.secret_key'));
    }
}
