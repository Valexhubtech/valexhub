<?php

namespace App\Services\Go54;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Go54RegistrarProvider
{
    private string $endpoint;
    private string $username;
    private string $apiKey;

    public function __construct()
    {
        $this->endpoint = rtrim(config('services.go54.endpoint'), '/');
        $this->username = config('services.go54.username');
        $this->apiKey   = config('services.go54.token');
    }

    public function checkAvailability(string $domain): array
    {
        $response = $this->call('checkDomain', ['domain' => $domain]);

        return [
            'domain'    => $domain,
            'available' => ($response['status'] ?? '') === 'available',
            'price'     => $response['price'] ?? null,
            'currency'  => $response['currency'] ?? null,
        ];
    }

    /**
     * @param  string[]  $nameservers  e.g. ['ns1.desec.io', 'ns2.desec.org']
     */
    public function register(string $domain, array $nameservers): array
    {
        $params = ['domain' => $domain];

        foreach ($nameservers as $i => $ns) {
            $params['ns' . ($i + 1)] = $ns;
        }

        return $this->call('registerDomain', $params);
    }

    public function renew(string $domain, int $years = 1): array
    {
        return $this->call('renewDomain', ['domain' => $domain, 'years' => $years]);
    }

    /**
     * @param  string[]  $nameservers
     */
    public function setNameservers(string $domain, array $nameservers): array
    {
        $params = ['domain' => $domain];

        foreach ($nameservers as $i => $ns) {
            $params['ns' . ($i + 1)] = $ns;
        }

        return $this->call('modifyNameservers', $params);
    }

    private function call(string $action, array $params = []): array
    {
        $response = Http::withHeaders($this->authHeaders())
            ->asJson()
            ->post("{$this->endpoint}/domain", array_merge(['action' => $action], $params));

        if ($response->status() === 403) {
            $body = $response->json() ?? [];
            $message = $body['message'] ?? 'Access denied or insufficient wallet balance.';

            if (stripos($message, 'wallet') !== false || stripos($message, 'funds') !== false || stripos($message, 'money') !== false) {
                throw new InsufficientWalletBalanceException($message);
            }

            throw new Go54ApiException("GO54 403: {$message}");
        }

        if ($response->failed()) {
            throw new Go54ApiException(
                "GO54 {$action} failed: HTTP {$response->status()} — {$response->body()}"
            );
        }

        $body = $response->json();

        Log::info('GO54 API call', ['action' => $action, 'status' => $body['status'] ?? null]);

        return $body ?? [];
    }

    private function authHeaders(): array
    {
        // Token = base64(HMAC-SHA256(apiKey, email:gmdate("y-m-d H")))
        $timeKey = gmdate('y-m-d H');
        $token   = base64_encode(hash_hmac('sha256', $this->apiKey, "{$this->username}:{$timeKey}"));

        return [
            'username' => $this->username,
            'token'    => $token,
        ];
    }
}
