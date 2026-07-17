<?php

namespace App\Livewire;

use App\Services\Paystack\PaystackService;
use Illuminate\Support\Collection;
use Livewire\Component;
use Wave\Product;
use Wave\ProductPricing;

class ProductCheckout extends Component
{
    public string $productSlug    = '';
    public string $deploymentType = '';
    public ?int   $selectedPricingId = null;
    public array  $selectedAddonIds  = [];
    public string $deployFor      = 'self';
    public string $businessName   = '';
    public string $clientName     = '';
    public string $clientEmail    = '';
    public bool   $supportsCloud  = false;
    public bool   $supportsOnPrem = false;

    public function mount(string $productSlug): void
    {
        $this->productSlug = $productSlug;
        $product = $this->getProduct();

        abort_if(! $product || ! $product->isPrebuilt(), 404);

        $this->supportsCloud  = $product->supportsCloud();
        $this->supportsOnPrem = $product->supportsOnPrem();
        $this->deploymentType = $this->supportsCloud ? 'cloud' : 'onprem';
    }

    protected function getProduct(): ?Product
    {
        return Product::where('slug', $this->productSlug)->first();
    }

    protected function buildBreakdown(Collection $setupFees, Collection $onetimeOptions, Collection $selectedAddons): array
    {
        $lines = [];
        $total = 0;

        foreach ($setupFees as $fee) {
            $lines[] = ['label' => $fee['label'], 'amount' => (float) $fee['amount'], 'type' => 'onetime'];
            $total  += (float) $fee['amount'];
        }

        if ($this->selectedPricingId) {
            $pricing = ProductPricing::find($this->selectedPricingId);
            if ($pricing) {
                $lines[] = ['label' => $pricing->label.' (first period)', 'amount' => (float) $pricing->amount, 'type' => 'recurring'];
                $total  += (float) $pricing->amount;
            }
        }

        foreach ($onetimeOptions as $option) {
            $lines[] = ['label' => $option['label'], 'amount' => (float) $option['amount'], 'type' => 'onetime'];
            $total  += (float) $option['amount'];
        }

        foreach ($selectedAddons as $addon) {
            $lines[] = [
                'label'  => $addon['name'].($addon['is_recurring'] ? ' ('.$addon['billing_cycle'].')' : ''),
                'amount' => (float) $addon['price'],
                'type'   => $addon['price_type'],
            ];
            $total += (float) $addon['price'];
        }

        return ['lines' => $lines, 'total' => $total];
    }

    public function checkout(PaystackService $paystack)
    {
        $this->validate([
            'deploymentType' => 'required|in:cloud,onprem',
            'deployFor'      => 'required|in:self,client',
            'businessName'   => 'required|string|min:2|max:255',
            'clientName'     => 'required_if:deployFor,client|nullable|string|max:255',
            'clientEmail'    => 'required_if:deployFor,client|nullable|email|max:255',
        ]);

        $product = $this->getProduct();
        abort_if(! $product, 404);

        $allPricing      = $product->pricingFor($this->deploymentType)->get();
        $setupFees       = $allPricing->whereIn('price_type', ['setup', 'license']);
        $onetimeOptions  = $allPricing->where('price_type', 'onetime');
        $availableAddons = $product->addonsFor($this->deploymentType)->get();
        $selectedAddons  = $availableAddons->whereIn('id', $this->selectedAddonIds);

        $setupFeesArr = $setupFees->map(fn ($fee) => ['label' => $fee->label, 'amount' => (float) $fee->amount]);
        $onetimeArr   = $onetimeOptions->map(fn ($opt) => ['label' => $opt->label, 'amount' => (float) $opt->amount]);
        $addonsArr    = $selectedAddons->map(fn ($a) => [
            'name'          => $a->name,
            'price'         => (float) $a->price,
            'price_type'    => $a->price_type,
            'is_recurring'  => $a->isRecurring(),
            'billing_cycle' => $a->billing_cycle,
        ]);

        $breakdown = $this->buildBreakdown(collect($setupFeesArr), collect($onetimeArr), collect($addonsArr));

        if ($breakdown['total'] <= 0) {
            $this->addError('general', 'Please select a pricing option.');
            return;
        }

        $user = auth()->user();

        $pricing        = $this->selectedPricingId ? \Wave\ProductPricing::find($this->selectedPricingId) : null;
        $nextRenewal    = match ($pricing?->price_type) {
            'monthly'   => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'yearly'    => now()->addYear(),
            default     => null,
        };

        $userProduct = \Wave\UserProduct::create([
            'user_id'             => $user->id,
            'product_id'          => $product->id,
            'amount_paid'         => $breakdown['total'],
            'purchase_date'       => now(),
            'next_renewal_date'   => $nextRenewal,
            'status'              => 'inactive',
            'deployment_type'     => $this->deploymentType,
            'product_pricing_id'  => $this->selectedPricingId,
            'setup_amount'        => collect($breakdown['lines'])->where('type', 'onetime')->sum('amount'),
            'addons_amount'       => $selectedAddons->sum('price'),
        ]);

        \Wave\OrderAddon::where('user_product_id', $userProduct->id)->delete();
        foreach ($selectedAddons as $addon) {
            \Wave\OrderAddon::create([
                'user_product_id'  => $userProduct->id,
                'product_addon_id' => $addon->id,
                'amount_paid'      => $addon->price,
                'price_type'       => $addon->price_type,
            ]);
        }

        $deployment = \Wave\Deployment::create([
            'user_id'         => $user->id,
            'product_id'      => $product->id,
            'user_product_id' => $userProduct->id,
            'deploy_for'      => $this->deployFor,
            'business_name'   => $this->businessName,
            'client_name'     => $this->clientName ?: null,
            'client_email'    => $this->clientEmail ?: null,
            'status'          => 'pending',
        ]);

        $planCode = $this->selectedPricingId
            ? ProductPricing::find($this->selectedPricingId)?->paystack_plan_code
            : null;

        $response = $paystack->initializeTransaction(
            email: $user->email,
            amountKobo: (int) round($breakdown['total'] * 100),
            metadata: [
                'type'            => 'product_purchase',
                'deployment_id'   => $deployment->id,
                'user_product_id' => $userProduct->id,
                'addon_ids'       => $this->selectedAddonIds,
            ],
            callbackUrl: route('products.checkout.callback'),
            planCode: $planCode,
        );

        if (! ($response['status'] ?? false)) {
            $deployment->delete();
            $this->addError('general', 'Unable to start checkout with Paystack. Please try again.');
            return;
        }

        return redirect()->to($response['data']['authorization_url']);
    }

    protected function loadPricingData(Product $product, string $type): array
    {
        $setupFees = $recurringOptions = $onetimeOptions = $availableAddons = [];

        foreach ($product->pricingFor($type)->get() as $row) {
            $item = [
                'id'          => $row->id,
                'label'       => $row->label,
                'amount'      => (float) $row->amount,
                'price_type'  => $row->price_type,
                'is_required' => (bool) $row->is_required,
            ];
            match ($row->price_type) {
                'setup', 'license'               => $setupFees[]        = $item,
                'monthly', 'quarterly', 'yearly' => $recurringOptions[] = $item,
                'onetime'                        => $onetimeOptions[]   = $item,
                default                          => null,
            };
        }

        foreach ($product->addonsFor($type)->get() as $a) {
            $availableAddons[] = [
                'id'            => $a->id,
                'name'          => $a->name,
                'description'   => $a->description,
                'price'         => (float) $a->price,
                'price_type'    => $a->price_type,
                'billing_cycle' => $a->billing_cycle,
                'is_recurring'  => $a->isRecurring(),
            ];
        }

        return compact('setupFees', 'recurringOptions', 'onetimeOptions', 'availableAddons');
    }

    public function render()
    {
        $product = $this->getProduct();
        $empty   = ['setupFees' => [], 'recurringOptions' => [], 'onetimeOptions' => [], 'availableAddons' => []];

        $cloudData  = $empty;
        $onpremData = $empty;

        if ($product) {
            if ($this->supportsCloud)  { $cloudData  = $this->loadPricingData($product, 'cloud'); }
            if ($this->supportsOnPrem) { $onpremData = $this->loadPricingData($product, 'onprem'); }
        }

        return view('livewire.product-checkout', compact('cloudData', 'onpremData'));
    }
}
