<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    /**
     * Return the current USD → NGN exchange rate.
     * Fetched from open.er-api.com (free, no API key) and cached for 6 hours.
     * Falls back to the config value if the API is unavailable.
     */
    public function getUsdToNgn(): float
    {
        return cache()->remember('exchange_rate_usd_ngn', 21600, function () {
            try {
                $response = Http::timeout(5)->get('https://open.er-api.com/v6/latest/USD');

                if ($response->successful()) {
                    $rate = $response->json('rates.NGN');
                    if ($rate && is_numeric($rate) && $rate > 500) {
                        Log::debug('ExchangeRateService: fetched live USD/NGN', ['rate' => $rate]);

                        return (float) $rate;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('ExchangeRateService: API failed, using fallback rate', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Fallback to config value if API unreachable
            return (float) config('domains.fallback_usd_to_ngn_rate', 1600);
        });
    }

    /**
     * Convert a USD cent amount to NGN kobo at the live rate.
     */
    public function usdCentsToKobo(int $usdCents): int
    {
        // usdCents × rate = NGN (since usdCents/100 × rate × 100 = usdCents × rate)
        return (int) round($usdCents * $this->getUsdToNgn());
    }
}
