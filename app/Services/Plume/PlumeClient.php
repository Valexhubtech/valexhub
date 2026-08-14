<?php

namespace App\Services\Plume;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class PlumeClient
{
    private PendingRequest $http;

    public function __construct()
    {
        $this->http = Http::baseUrl(rtrim(config('services.plume.base_url'), '/'))
            ->withToken(config('services.plume.master_key'), 'Bearer')
            ->acceptJson()
            ->timeout(15);
    }

    public function registerDomain(string $domain): array
    {
        $response = $this->http->post('/domains', ['domain' => $domain]);

        if ($response->status() === 409) {
            throw new PlumeDomainExistsException("Domain {$domain} already registered in Plume.");
        }

        $this->assertSuccess($response, "register domain {$domain}");

        return $response->json();
    }

    public function getDomain(string $domain): array
    {
        $response = $this->http->get("/domains/{$domain}");

        $this->assertSuccess($response, "get domain {$domain}");

        return $response->json();
    }

    public function verifyDomain(string $domain): array
    {
        $response = $this->http->post("/domains/{$domain}/verify");

        if ($response->status() === 422) {
            throw new PlumeNotVerifiedException("Domain {$domain} DNS records not yet verified.");
        }

        $this->assertSuccess($response, "verify domain {$domain}");

        return $response->json();
    }

    public function createApiKey(string $domain, string $name): array
    {
        $response = $this->http->post('/api-keys', [
            'domain' => $domain,
            'label'  => $name,
        ]);

        $this->assertSuccess($response, "create API key for {$domain}");

        return $response->json();
    }

    private function assertSuccess(\Illuminate\Http\Client\Response $response, string $action): void
    {
        if ($response->failed()) {
            throw new PlumeException(
                "Plume error on {$action}: HTTP {$response->status()} — {$response->body()}"
            );
        }
    }
}
