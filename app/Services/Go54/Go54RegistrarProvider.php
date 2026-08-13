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
        // GO54 has no dedicated availability-check endpoint.
        // Returns available=true; actual availability is confirmed at registration time.
        return [
            'domain'    => $domain,
            'available' => true,
            'price'     => null,
            'currency'  => 'NGN',
        ];
    }

    /**
     * @param  string[]  $nameservers
     */
    public function register(string $domain, array $nameservers): array
    {
        $params = ['domain' => $domain];
        foreach ($nameservers as $i => $ns) {
            $params['ns' . ($i + 1)] = $ns;
        }

        return $this->post('/order/domains/register', $params);
    }

    public function renew(string $domain, int $years = 1): array
    {
        return $this->post('/order/domains/renew', [
            'domain'    => $domain,
            'regperiod' => $years,
        ]);
    }

    /**
     * @param  string[]  $nameservers
     */
    public function setNameservers(string $domain, array $nameservers): array
    {
        $params = [];
        foreach ($nameservers as $i => $ns) {
            $params['ns' . ($i + 1)] = $ns;
        }

        return $this->post("/domains/{$domain}/nameservers", $params);
    }

    public function getNameservers(string $domain): array
    {
        return $this->get("/domains/{$domain}/nameservers");
    }

    public function getDomainInfo(string $domain): array
    {
        return $this->get("/domains/{$domain}/information");
    }

    public function getCredits(): array
    {
        return $this->get('/billing/credits');
    }

    public function getAvailableTlds(): array
    {
        return $this->get('/tlds');
    }


    private function post(string $path, array $params = []): array
    {
        $response = Http::withHeaders($this->authHeaders())
            ->asForm()
            ->post($this->endpoint . $path, $params);

        return $this->handleResponse($path, $response);
    }

    private function get(string $path): array
    {
        $response = Http::withHeaders($this->authHeaders())
            ->get($this->endpoint . $path);

        return $this->handleResponse($path, $response);
    }

    private function handleResponse(string $path, \Illuminate\Http\Client\Response $response): array
    {
        $body = $response->json() ?? [];

        if (isset($body['error'])) {
            $msg = $body['error'];

            if (stripos($msg, 'Token') !== false || stripos($msg, 'Invalid API') !== false) {
                throw new Go54ApiException("GO54 auth failed on {$path}: {$msg}");
            }

            if (stripos($msg, 'credit') !== false || stripos($msg, 'balance') !== false || stripos($msg, 'funds') !== false) {
                throw new InsufficientWalletBalanceException($msg);
            }

            throw new Go54ApiException("GO54 {$path}: {$msg}");
        }

        Log::info('GO54 API call', ['path' => $path, 'status' => $body['status'] ?? 'ok']);

        return $body;
    }

    private function authHeaders(): array
    {
        $token = base64_encode(hash_hmac('sha256', $this->apiKey, "{$this->username}:" . gmdate('y-m-d H')));

        return [
            'username' => $this->username,
            'token'    => $token,
        ];
    }
}
