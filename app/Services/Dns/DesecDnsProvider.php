<?php

namespace App\Services\Dns;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DesecDnsProvider implements DnsProvider
{
    private PendingRequest $http;

    public function __construct()
    {
        $this->http = Http::baseUrl(config('services.desec.base_url'))
            ->withToken(config('services.desec.token'), 'Token')
            ->acceptJson()
            ->timeout(15);
    }

    public function zoneExists(string $domain): bool
    {
        $response = $this->http->get("/domains/{$domain}/");

        return $response->successful();
    }

    public function createZone(string $domain): void
    {
        $response = $this->http->post('/domains/', ['name' => $domain]);

        if ($response->status() === 409) {
            return; // already exists — idempotent
        }

        $this->assertSuccess($response, "create zone {$domain}");
    }

    public function listRecords(string $domain): array
    {
        $response = $this->http->get("/domains/{$domain}/rrsets/");

        $this->assertSuccess($response, "list records {$domain}");

        return $response->json() ?? [];
    }

    public function pushRecords(string $domain, array $rrsets): void
    {
        if (empty($rrsets)) {
            return;
        }

        $response = $this->http->patch("/domains/{$domain}/rrsets/", $rrsets);

        $this->assertSuccess($response, "push records {$domain}");
    }

    public function updateRecord(string $domain, string $subname, string $type, int $ttl, array $records): void
    {
        $response = $this->http->patch(
            "/domains/{$domain}/rrsets/{$subname}/{$type}/",
            ['ttl' => $ttl, 'records' => $records]
        );

        $this->assertSuccess($response, "update {$type} record for {$subname}.{$domain}");
    }

    public function deleteRecord(string $domain, string $subname, string $type): void
    {
        $response = $this->http->patch(
            "/domains/{$domain}/rrsets/{$subname}/{$type}/",
            ['records' => []]
        );

        // 404 = already gone — idempotent
        if ($response->status() === 404) {
            return;
        }

        $this->assertSuccess($response, "delete {$type} record for {$subname}.{$domain}");
    }

    private function assertSuccess(\Illuminate\Http\Client\Response $response, string $action): void
    {
        if ($response->failed()) {
            throw new RuntimeException(
                "deSEC error on {$action}: HTTP {$response->status()} — {$response->body()}"
            );
        }
    }
}
