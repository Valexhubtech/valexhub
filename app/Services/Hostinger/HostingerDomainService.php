<?php

namespace App\Services\Hostinger;

use Hostinger\Api\BillingCatalogApi;
use Hostinger\Api\DNSZoneApi;
use Hostinger\Api\DomainsAvailabilityApi;
use Hostinger\Api\DomainsPortfolioApi;
use Hostinger\Configuration;
use Hostinger\Model\DNSV1ZoneUpdateRequest;
use Hostinger\Model\DNSV1ZoneUpdateRequestZoneInner;
use Hostinger\Model\DNSV1ZoneUpdateRequestZoneInnerRecordsInner;
use Hostinger\Model\DomainsV1AvailabilityAvailabilityRequest;
use Hostinger\Model\DomainsV1PortfolioPurchaseRequest;
use Hostinger\Model\DomainsV1PortfolioUpdateNameserversRequest;
use Illuminate\Support\Facades\Log;

class HostingerDomainService
{
    private Configuration $config;

    public function __construct()
    {
        $this->config = Configuration::getDefaultConfiguration()
            ->setAccessToken(config('services.hostinger.api_token'));
    }

    /**
     * Check availability of a domain name across given TLDs.
     * $domainName should be WITHOUT the TLD (e.g. "myhotel" not "myhotel.com").
     * $tlds is an array like ['.com', '.ng', '.com.ng'].
     *
     * Returns array of results keyed by full domain name.
     */
    public function checkAvailability(string $domainName, array $tlds = ['.com', '.com.ng', '.ng']): array
    {
        try {
            $api = new DomainsAvailabilityApi(config: $this->config);

            $request = (new DomainsV1AvailabilityAvailabilityRequest())
                ->setDomain($domainName)
                ->setTlds($tlds)
                ->setWithAlternatives(false);

            $result = $api->checkDomainAvailabilityV1($request);

            $out = [];
            foreach ($result as $item) {
                $out[$item->getDomain()] = [
                    'domain' => $item->getDomain(),
                    'available' => $item->getIsAvailable() ?? false,
                ];
            }

            return $out;
        } catch (\Throwable $e) {
            Log::error('Hostinger domain availability check failed', [
                'domain' => $domainName,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get all domain catalog items from Hostinger, expanding every price tier.
     * Each returned row represents one billing-period option for one TLD.
     * Includes both the regular price and the first-period (promo) price.
     */
    public function getPricing(?string $nameFilter = null): array
    {
        try {
            $api = new BillingCatalogApi(config: $this->config);
            $result = $api->getCatalogItemListV1('DOMAIN', $nameFilter);

            $items = [];
            foreach ($result as $item) {
                foreach ($item->getPrices() ?? [] as $price) {
                    $period = (int) ($price->getPeriod() ?? 0);
                    $periodUnit = $price->getPeriodUnit() ?? '';

                    if ($period <= 0 || $periodUnit !== 'year') {
                        continue; // skip transfers / period-0 entries
                    }

                    $items[] = [
                        'item_id' => $price->getId(),        // price-specific ID e.g. hostingercom-domain-biz-usd-1y
                        'parent_item_id' => $item->getId(),          // TLD-level ID e.g. hostingercom-domain-biz
                        'name' => $item->getName(),
                        'price_cents' => (int) $price->getPrice(),
                        'promo_cents' => (int) ($price->getFirstPeriodPrice() ?? $price->getPrice()),
                        'period' => $period,
                        'period_unit' => $periodUnit,
                        'currency' => $price->getCurrency() ?? 'USD',
                    ];
                }
            }

            return $items;
        } catch (\Throwable $e) {
            Log::error('Hostinger catalog pricing failed', ['error' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Return all domain TLDs available on Hostinger, keyed by TLD string.
     * Prefers the 1-year billing option and uses the first-period (promo) price
     * so clients see the same promotional price as on Hostinger's website.
     *
     * Each entry: item_id (price-specific), promo_cents (first year), regular_cents,
     *             registration_period, currency.
     */
    public function getAvailableTlds(): array
    {
        $raw = $this->getPricing();
        $tlds = [];

        foreach ($raw as $item) {
            $tld = $this->parseTld($item);
            if (! $tld) {
                continue;
            }

            $period = $item['period'];

            // Prefer 1-year option; if we already have a better (shorter) option, skip
            if (isset($tlds[$tld])) {
                if ($period >= $tlds[$tld]['registration_period']) {
                    continue;
                }
            }

            $tlds[$tld] = [
                'item_id' => $item['item_id'],      // use for purchase
                'promo_cents' => $item['promo_cents'],  // first-year promo price
                'regular_cents' => $item['price_cents'],  // renewal price
                'registration_period' => $period,
                'currency' => $item['currency'],
            ];
        }

        return $tlds;
    }

    /**
     * Parse the TLD from a Hostinger catalog item.
     *
     * Hostinger item names follow the consistent pattern "<.TLD> Domain"
     * where TLD is uppercase and may contain dots for second-level domains:
     *   ".COM Domain"    → com
     *   ".COM.NG Domain" → com.ng
     *   ".CO.UK Domain"  → co.uk
     *
     * Transfer items (".NG Domain Transfer") are excluded by the trailing `$`.
     */
    private function parseTld(array $item): ?string
    {
        $name = $item['name'] ?? '';

        // Matches ".COM Domain", ".COM.NG Domain", ".BIZ Domain (billed every year)", etc.
        // The trailing content after "Domain" (like billing period note) is allowed.
        if (preg_match('/^\s*\.([A-Z.]+)\s+Domain/i', $name, $m)) {
            return strtolower($m[1]);
        }

        return null;
    }

    /**
     * Purchase and register a domain.
     * $domain: full domain name e.g. "myhotel.com"
     * $itemId: catalog item ID e.g. "hostingercom-domain-com-usd-1y" from getPricing()
     * Uses default payment method on the Hostinger account if none specified.
     */
    public function purchaseDomain(string $domain, string $itemId, ?int $paymentMethodId = null): bool
    {
        try {
            $api = new DomainsPortfolioApi(config: $this->config);

            $request = (new DomainsV1PortfolioPurchaseRequest())
                ->setDomain($domain)
                ->setItemId($itemId)
                ->setPaymentMethodId($paymentMethodId);

            $api->purchaseNewDomainV1($request);

            Log::info('Domain purchased via Hostinger', ['domain' => $domain]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Hostinger domain purchase failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Update nameservers for a domain in the Hostinger portfolio.
     */
    public function updateNameservers(string $domain, string $ns1, string $ns2, ?string $ns3 = null, ?string $ns4 = null): bool
    {
        try {
            $api = new DomainsPortfolioApi(config: $this->config);

            $request = (new DomainsV1PortfolioUpdateNameserversRequest())
                ->setNs1($ns1)
                ->setNs2($ns2)
                ->setNs3($ns3)
                ->setNs4($ns4);

            $api->updateDomainNameserversV1($domain, $request);

            return true;
        } catch (\Throwable $e) {
            Log::error('Hostinger nameserver update failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Add A records pointing the domain (@ and www) at a server IP.
     * Hostinger manages DNS natively — no Cloudflare needed.
     */
    public function addARecord(string $domain, string $ip, int $ttl = 3600): bool
    {
        try {
            $api = new DNSZoneApi(config: $this->config);

            $record = (new DNSV1ZoneUpdateRequestZoneInnerRecordsInner())
                ->setContent($ip);

            $rootEntry = (new DNSV1ZoneUpdateRequestZoneInner())
                ->setName('@')
                ->setType(DNSV1ZoneUpdateRequestZoneInner::TYPE_A)
                ->setTtl($ttl)
                ->setRecords([$record]);

            $wwwEntry = (new DNSV1ZoneUpdateRequestZoneInner())
                ->setName('www')
                ->setType(DNSV1ZoneUpdateRequestZoneInner::TYPE_A)
                ->setTtl($ttl)
                ->setRecords([$record]);

            $request = (new DNSV1ZoneUpdateRequest())
                ->setOverwrite(false)
                ->setZone([$rootEntry, $wwwEntry]);

            $api->updateDNSRecordsV1($domain, $request);

            Log::info('DNS A records added via Hostinger', ['domain' => $domain, 'ip' => $ip]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Hostinger DNS A record failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Full setup after purchase: point DNS at Coolify server.
     * Since Hostinger manages DNS natively for their registered domains,
     * no nameserver change is needed — just add the A record.
     */
    public function configureDomainForDeployment(string $domain, string $serverIp): bool
    {
        return $this->addARecord($domain, $serverIp);
    }
}
