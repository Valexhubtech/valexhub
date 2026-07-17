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
    public ?Deployment $deployment = null;

    // Tracks whether deployment was injected as a prop vs chosen via selector
    public bool $deploymentBound = false;

    public string $searchQuery = '';

    public array $results = [];

    public bool $searched = false;

    public bool $searching = false;

    public ?string $selectedDomain = null;

    public ?string $selectedTld = null;

    public ?string $selectedItemId = null;

    public int $selectedPriceKobo = 0;

    public bool $showPayment = false;

    public bool $showDeploymentSelector = false;

    public array $availableDeployments = [];

    public string $paymentError = '';

    public function mount(): void
    {
        $this->deploymentBound = $this->deployment !== null;
    }

    public function search(): void
    {
        $this->validate([
            'searchQuery' => ['required', 'min:2', 'max:70', 'regex:/^[a-zA-Z0-9][a-zA-Z0-9.\-]*$/'],
        ], [
            'searchQuery.regex' => 'Only letters, numbers, and hyphens are allowed.',
        ]);

        $this->searching = true;
        $this->results = [];
        $this->searched = false;
        $this->reset(['selectedDomain', 'selectedTld', 'selectedItemId', 'selectedPriceKobo', 'showPayment', 'showDeploymentSelector', 'availableDeployments']);

        $input = strtolower(trim($this->searchQuery));

        $hostinger = app(HostingerDomainService::class);
        $catalogTlds = $this->loadCatalog($hostinger);

        if (empty($catalogTlds)) {
            $this->addError('searchQuery', 'Domain search is temporarily unavailable. Please try again in a moment.');
            $this->searching = false;

            return;
        }

        $whitelist = (array) config('domains.tld_whitelist', []);
        $catalogTlds = array_intersect_key($catalogTlds, array_flip($whitelist));

        $allTldKeys = collect(array_keys($catalogTlds))
            ->sortByDesc(fn ($t) => strlen($t))
            ->values()
            ->toArray();

        $name = $input;
        foreach ($allTldKeys as $tld) {
            if (str_ends_with($input, '.'.$tld)) {
                $name = substr($input, 0, strlen($input) - strlen('.'.$tld));
                break;
            }
        }

        if (! preg_match('/^[a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?$/', $name)) {
            $this->addError('searchQuery', 'Please enter a valid domain name — letters, numbers, and hyphens only.');
            $this->searching = false;

            return;
        }

        try {
            $availability = $hostinger->checkAvailability($name, $allTldKeys);
        } catch (\Throwable $e) {
            Log::error('DomainSearch: availability check failed', ['error' => $e->getMessage()]);
            $availability = [];
        }

        $liveRate = app(ExchangeRateService::class)->getUsdToNgn();
        $recommendedTlds = (array) config('domains.recommended_tlds', ['com', 'com.ng']);
        $descriptions = (array) config('domains.tld_descriptions', []);
        $groups = (array) config('domains.tld_groups', []);

        $results = [];
        foreach ($catalogTlds as $tld => $catalogItem) {
            $fullDomain = $name.'.'.$tld;
            $available = $availability[$fullDomain]['available'] ?? null;

            $priceKobo = (int) round($catalogItem['promo_cents'] * $liveRate);

            $isRecommended = in_array($tld, $recommendedTlds) && $available !== false;

            $results[] = [
                'domain' => $fullDomain,
                'tld' => $tld,
                'label' => '.'.$tld,
                'description' => $descriptions[$tld] ?? '',
                'group' => $groups[$tld] ?? 'other',
                'recommended' => $isRecommended,
                'price_kobo' => $priceKobo,
                'price_naira' => number_format($priceKobo / 100, 0),
                'available' => $available,
                'item_id' => $catalogItem['item_id'],
            ];
        }

        usort($results, function ($a, $b) {
            $score = function ($r) {
                if ($r['available'] === true && $r['recommended']) {
                    return 3;
                }
                if ($r['available'] === true) {
                    return 2;
                }
                if ($r['available'] === null) {
                    return 1;
                }

                return 0;
            };

            return $score($b) <=> $score($a);
        });

        $this->results = $results;
        $this->searched = true;
        $this->searching = false;
    }

    public function select(string $domain, string $tld, int $priceKobo, string $itemId): void
    {
        $this->selectedDomain = $domain;
        $this->selectedTld = $tld;
        $this->selectedPriceKobo = $priceKobo;
        $this->selectedItemId = $itemId;

        if ($this->deployment !== null) {
            $this->showPayment = true;

            return;
        }

        // No deployment bound — find ones available for this user
        $paidIds = DomainPurchase::where('user_id', Auth::id())
            ->where('payment_status', 'paid')
            ->pluck('deployment_id')
            ->toArray();

        $this->availableDeployments = Deployment::with('product')
            ->where('user_id', Auth::id())
            ->whereNotIn('id', $paidIds)
            ->get()
            ->map(function ($d) {
                return ['id' => $d->id, 'name' => $d->product->name ?? 'Deployment #'.$d->id];
            })
            ->toArray();

        $this->showDeploymentSelector = true;
    }

    public function selectDeployment(int $deploymentId): void
    {
        $deployment = Deployment::where('id', $deploymentId)
            ->where('user_id', Auth::id())
            ->first();

        if (! $deployment) {
            return;
        }

        $hasDomain = DomainPurchase::where('deployment_id', $deploymentId)
            ->where('payment_status', 'paid')
            ->exists();

        if ($hasDomain) {
            $this->addError('deploymentSelector', 'This deployment already has a domain attached.');

            return;
        }

        $this->deployment = $deployment;
        $this->showDeploymentSelector = false;
        $this->showPayment = true;
    }

    public function cancelDeploymentSelection(): void
    {
        $this->reset(['selectedDomain', 'selectedTld', 'selectedItemId', 'selectedPriceKobo', 'showDeploymentSelector', 'availableDeployments']);
    }

    public function cancelSelection(): void
    {
        if (! $this->deploymentBound) {
            $this->deployment = null;
        }
        $this->reset(['selectedDomain', 'selectedTld', 'selectedItemId', 'selectedPriceKobo', 'showPayment', 'showDeploymentSelector', 'availableDeployments']);
    }

    public function proceedToPayment(): void
    {
        if (! $this->selectedDomain || ! $this->selectedTld || ! $this->deployment) {
            return;
        }

        $setupFeeKobo = (int) config('domains.setup_fee_kobo');
        $totalKobo = $this->selectedPriceKobo + $setupFeeKobo;

        $this->paymentError = '';

        $purchase = DomainPurchase::create([
            'user_id' => Auth::id(),
            'deployment_id' => $this->deployment->id,
            'domain' => $this->selectedDomain,
            'tld' => $this->selectedTld,
            'hostinger_item_id' => $this->selectedItemId,
            'domain_price_kobo' => $this->selectedPriceKobo,
            'setup_fee_kobo' => $setupFeeKobo,
            'total_kobo' => $totalKobo,
            'payment_status' => 'pending',
        ]);

        try {
            $paystack = app(PaystackService::class);
            $result = $paystack->initializeTransaction(
                email: Auth::user()->email,
                amountKobo: $totalKobo,
                metadata: [
                    'type' => 'domain_purchase',
                    'domain_purchase_id' => $purchase->id,
                    'deployment_id' => $this->deployment->id,
                    'domain' => $this->selectedDomain,
                ],
                callbackUrl: route('dashboard.domain.callback'),
            );

            $authUrl = $result['data']['authorization_url'] ?? null;
            $reference = $result['data']['reference'] ?? null;

            if (! $authUrl || ! $reference) {
                throw new \RuntimeException('Paystack did not return an authorization URL.');
            }

            $purchase->update(['paystack_reference' => $reference]);

            $this->redirect($authUrl);
        } catch (\Throwable $e) {
            Log::error('DomainSearch: Paystack init failed', ['error' => $e->getMessage()]);
            $purchase->update(['payment_status' => 'failed']);
            $this->paymentError = 'Payment could not be initiated: '.$e->getMessage();
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
