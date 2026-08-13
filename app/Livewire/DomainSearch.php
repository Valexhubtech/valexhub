<?php

namespace App\Livewire;

use App\Models\DomainOrder;
use App\Services\Go54\Go54RegistrarProvider;
use App\Services\Paystack\PaystackService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Wave\Deployment;
use Wave\DomainPurchase;

class DomainSearch extends Component
{
    public ?Deployment $deployment = null;

    public bool $deploymentBound = false;

    public string $searchQuery = '';

    public array $results = [];

    public bool $searched = false;

    public bool $searching = false;

    public ?string $selectedDomain = null;

    public ?string $selectedTld = null;

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
        $this->results   = [];
        $this->searched  = false;
        $this->reset(['selectedDomain', 'selectedTld', 'selectedPriceKobo', 'showPayment', 'showDeploymentSelector', 'availableDeployments']);

        $input = strtolower(trim($this->searchQuery));
        $tlds  = (array) config('domains.tld_whitelist', ['com', 'com.ng', 'ng']);

        // Strip TLD if user typed the full domain
        $name = $input;
        foreach (array_map(fn ($t) => '.' . $t, $tlds) as $dotTld) {
            if (str_ends_with($input, $dotTld)) {
                $name = substr($input, 0, -strlen($dotTld));
                break;
            }
        }

        if (! preg_match('/^[a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?$/', $name)) {
            $this->addError('searchQuery', 'Please enter a valid domain name — letters, numbers, and hyphens only.');
            $this->searching = false;

            return;
        }

        $go54        = app(Go54RegistrarProvider::class);
        $recommended = (array) config('domains.recommended_tlds', ['com', 'com.ng']);
        $descriptions = (array) config('domains.tld_descriptions', []);

        $results = [];
        foreach ($tlds as $tld) {
            $fullDomain = $name . '.' . $tld;

            try {
                $check     = $go54->checkAvailability($fullDomain);
                $available = $check['available'] ?? null;
                $priceKobo = isset($check['price']) ? (int) round((float) $check['price'] * 100) : 0;
            } catch (\Throwable $e) {
                Log::warning('DomainSearch: GO54 availability check failed', ['domain' => $fullDomain, 'error' => $e->getMessage()]);
                $available = null;
                $priceKobo = 0;
            }

            $setupFeeKobo = (int) config('domains.setup_fee_kobo', 0);

            $results[] = [
                'domain'      => $fullDomain,
                'tld'         => $tld,
                'label'       => '.' . $tld,
                'description' => $descriptions[$tld] ?? '',
                'recommended' => in_array($tld, $recommended) && $available !== false,
                'price_kobo'  => $priceKobo,
                'price_naira' => number_format($priceKobo / 100, 0),
                'available'   => $available,
            ];
        }

        usort($results, function ($a, $b) {
            $score = function ($r) {
                if ($r['available'] === true && $r['recommended']) return 3;
                if ($r['available'] === true) return 2;
                if ($r['available'] === null) return 1;

                return 0;
            };

            return $score($b) <=> $score($a);
        });

        $this->results  = $results;
        $this->searched = true;
        $this->searching = false;
    }

    public function select(string $domain, string $tld, int $priceKobo): void
    {
        $this->selectedDomain   = $domain;
        $this->selectedTld      = $tld;
        $this->selectedPriceKobo = $priceKobo;

        if ($this->deployment !== null) {
            $this->showPayment = true;

            return;
        }

        $paidIds = DomainPurchase::where('user_id', Auth::id())
            ->where('payment_status', 'paid')
            ->pluck('deployment_id')
            ->toArray();

        $this->availableDeployments = Deployment::with('product')
            ->where('user_id', Auth::id())
            ->whereNotIn('id', $paidIds)
            ->get()
            ->map(fn ($d) => ['id' => $d->id, 'name' => $d->product->name ?? 'Deployment #' . $d->id])
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

        $this->deployment             = $deployment;
        $this->showDeploymentSelector = false;
        $this->showPayment            = true;
    }

    public function cancelDeploymentSelection(): void
    {
        $this->reset(['selectedDomain', 'selectedTld', 'selectedPriceKobo', 'showDeploymentSelector', 'availableDeployments']);
    }

    public function cancelSelection(): void
    {
        if (! $this->deploymentBound) {
            $this->deployment = null;
        }
        $this->reset(['selectedDomain', 'selectedTld', 'selectedPriceKobo', 'showPayment', 'showDeploymentSelector', 'availableDeployments']);
    }

    public function proceedToPayment(): void
    {
        if (! $this->selectedDomain || ! $this->selectedTld || ! $this->deployment) {
            return;
        }

        $setupFeeKobo = (int) config('domains.setup_fee_kobo', 0);
        $totalKobo    = $this->selectedPriceKobo + $setupFeeKobo;

        $this->paymentError = '';

        $purchase = DomainPurchase::create([
            'user_id'          => Auth::id(),
            'deployment_id'    => $this->deployment->id,
            'domain'           => $this->selectedDomain,
            'tld'              => $this->selectedTld,
            'domain_price_kobo' => $this->selectedPriceKobo,
            'setup_fee_kobo'   => $setupFeeKobo,
            'total_kobo'       => $totalKobo,
            'payment_status'   => 'pending',
        ]);

        try {
            $result   = app(PaystackService::class)->initializeTransaction(
                email: Auth::user()->email,
                amountKobo: $totalKobo,
                metadata: [
                    'type'               => 'domain_purchase',
                    'domain_purchase_id' => $purchase->id,
                    'deployment_id'      => $this->deployment->id,
                    'domain'             => $this->selectedDomain,
                ],
                callbackUrl: route('dashboard.domain.callback'),
            );

            $authUrl   = $result['data']['authorization_url'] ?? null;
            $reference = $result['data']['reference'] ?? null;

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

    public function render()
    {
        return view('livewire.domain-search', [
            'setupFeeNaira' => number_format(config('domains.setup_fee_kobo', 0) / 100, 0),
        ]);
    }
}
