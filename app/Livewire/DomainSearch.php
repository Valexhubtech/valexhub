<?php

namespace App\Livewire;

use App\Services\ExchangeRateService;
use App\Services\Hostinger\HostingerDomainService;
use App\Services\Paystack\PaystackService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Wave\Deployment;
use Wave\DomainPurchase;

class DomainSearch extends Component
{
    public Deployment $deployment;

    public string $searchQuery  = '';
    public array  $results      = [];
    public bool   $searched     = false;
    public bool   $searching    = false;

    public ?string $selectedDomain   = null;
    public ?string $selectedTld      = null;
    public ?string $selectedItemId   = null;
    public int     $selectedPriceKobo = 0;

    public bool   $showPayment  = false;
    public string $paymentError = '';

    public function search(): void
    {
        $this->validate([
            'searchQuery' => ['required', 'min:2', 'max:70', 'regex:/^[a-zA-Z0-9][a-zA-Z0-9.\-]*$/'],
        ], [
            'searchQuery.regex' => 'Only letters, numbers, and hyphens are allowed.',
        ]);

        $this->searching = true;
        $this->results   = [];
        $this->searched  = false;
        $this->reset(['selectedDomain', 'selectedTld', 'selectedItemId', 'selectedPriceKobo', 'showPayment']);

        $input = strtolower(trim($this->searchQuery));

        // Strip TLD suffix if user typed it (e.g. "mybusiness.com" → "mybusiness")
        // Sort longest TLDs first so "com.ng" is checked before "ng"
        $hostinger   = app(HostingerDomainService::class);
        $catalogTlds = $this->loadCatalog($hostinger);

        if (empty($catalogTlds)) {
            $this->addError('searchQuery', 'Domain search is temporarily unavailable. Please try again in a moment.');
            $this->searching = false;
            return;
        }

        // Filter to our curated whitelist so we don't present 400+ niche TLDs
        $whitelist   = (array) config('domains.tld_whitelist', []);
        $catalogTlds = array_intersect_key($catalogTlds, array_flip($whitelist));

        $allTldKeys = collect(array_keys($catalogTlds))
            ->sortByDesc(fn ($t) => strlen($t))
            ->values()
            ->toArray();

        $name = $input;
        foreach ($allTldKeys as $tld) {
            if (str_ends_with($input, '.' . $tld)) {
                $name = substr($input, 0, strlen($input) - strlen('.' . $tld));
                break;
            }
        }

        if (! preg_match('/^[a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?$/', $name)) {
            $this->addError('searchQuery', 'Please enter a valid domain name — letters, numbers, and hyphens only.');
            $this->searching = false;
            return;
        }

        // Check availability for all catalog TLDs in one API call
        try {
            $availability = $hostinger->checkAvailability($name, $allTldKeys);
        } catch (\Throwable $e) {
            Log::error('DomainSearch: availability check failed', ['error' => $e->getMessage()]);
            $availability = [];
        }

        $liveRate        = app(ExchangeRateService::class)->getUsdToNgn();
        $recommendedTlds = (array) config('domains.recommended_tlds', ['com', 'com.ng']);
        $descriptions    = (array) config('domains.tld_descriptions', []);
        $groups          = (array) config('domains.tld_groups', []);

        $results = [];
        foreach ($catalogTlds as $tld => $catalogItem) {
            $fullDomain = $name . '.' . $tld;
            $available  = $availability[$fullDomain]['available'] ?? null;

            // Use promo (first-period) price. Convert: usd_cents × rate = NGN kobo
            // (usd_cents/100 naira × rate × 100 kobo/naira = usd_cents × rate kobo)
            $priceKobo = (int) round($catalogItem['promo_cents'] * $liveRate);

            $isRecommended = in_array($tld, $recommendedTlds) && $available !== false;

            $results[] = [
                'domain'      => $fullDomain,
                'tld'         => $tld,
                'label'       => '.' . $tld,
                'description' => $descriptions[$tld] ?? '',
                'group'       => $groups[$tld] ?? 'other',
                'recommended' => $isRecommended,
                'price_kobo'  => $priceKobo,
                'price_naira' => number_format($priceKobo / 100, 0),
                'available'   => $available,
                'item_id'     => $catalogItem['item_id'],
            ];
        }

        // Sort: available+recommended > available > unknown > taken
        usort($results, function ($a, $b) {
            $score = fn ($r) => match (true) {
                $r['available'] === true && $r['recommended'] => 3,
                $r['available'] === true                      => 2,
                $r['available'] === null                      => 1,
                default                                       => 0,
            };
            return $score($b) <=> $score($a);
        });

        $this->results   = $results;
        $this->searched  = true;
        $this->searching = false;
    }

    public function select(string $domain, string $tld, int $priceKobo, string $itemId): void
    {
        $this->selectedDomain    = $domain;
        $this->selectedTld       = $tld;
        $this->selectedPriceKobo = $priceKobo;
        $this->selectedItemId    = $itemId;
        $this->showPayment       = true;
    }

    public function cancelSelection(): void
    {
        $this->reset(['selectedDomain', 'selectedTld', 'selectedItemId', 'selectedPriceKobo', 'showPayment']);
    }

    public function proceedToPayment(): void
    {
        if (! $this->selectedDomain || ! $this->selectedTld) {
            return;
        }

        $setupFeeKobo = (int) config('domains.setup_fee_kobo');
        $totalKobo    = $this->selectedPriceKobo + $setupFeeKobo;

        $this->paymentError = '';

        $purchase = DomainPurchase::create([
            'user_id'            => Auth::id(),
            'deployment_id'      => $this->deployment->id,
            'domain'             => $this->selectedDomain,
            'tld'                => $this->selectedTld,
            'hostinger_item_id'  => $this->selectedItemId,
            'domain_price_kobo'  => $this->selectedPriceKobo,
            'setup_fee_kobo'     => $setupFeeKobo,
            'total_kobo'         => $totalKobo,
            'payment_status'     => 'pending',
        ]);

        try {
            $paystack = app(PaystackService::class);
            $result   = $paystack->initializeTransaction(
                email:       Auth::user()->email,
                amountKobo:  $totalKobo,
                metadata: [
                    'type'               => 'domain_purchase',
                    'domain_purchase_id' => $purchase->id,
                    'deployment_id'      => $this->deployment->id,
                    'domain'             => $this->selectedDomain,
                ],
                callbackUrl: route('dashboard.domain.callback'),
            );

            $authUrl   = $result['data']['authorization_url'] ?? null;
            $reference = $result['data']['reference']         ?? null;

            if (! $authUrl || ! $reference) {
                throw new \RuntimeException('Paystack did not return an authorization URL.');
            }

            $purchase->update(['paystack_reference' => $reference]);

            $this->redirect($authUrl);
        } catch (\Throwable $e) {
            Log::error('DomainSearch: Paystack init failed', ['error' => $e->getMessage()]);
            $purchase->update(['payment_status' => 'failed']);
            $this->paymentError = 'Payment could not be initiated: ' . $e->getMessage();
        }
    }

    private function loadCatalog(HostingerDomainService $hostinger): array
    {
        return cache()->remember('hostinger_available_tlds', 86400, function () use ($hostinger) {
            return $hostinger->getAvailableTlds();
        });
    }

    public function render()
    {
        return view('livewire.domain-search', [
            'setupFeeNaira' => number_format(config('domains.setup_fee_kobo') / 100, 0),
        ]);
    }
}
