<?php
use function Laravel\Folio\{middleware, name};
middleware(['auth']);
name('dashboard.deployments.domain');
?>

@php
    abort_unless($deployment->user_id === auth()->id(), 403);

    $activePurchase = \Wave\DomainPurchase::where('deployment_id', $deployment->id)
        ->where('payment_status', 'paid')
        ->latest()
        ->first();
@endphp

<x-layouts.app>
<x-app.container>

    {{-- Back link --}}
    <div class="mb-5">
        <a href="{{ route('dashboard.deployments.show', ['deployment' => $deployment->id]) }}"
           class="inline-flex items-center gap-1.5 text-sm text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 transition-colors">
            <x-phosphor-arrow-left class="w-4 h-4" />
            Back to {{ $deployment->product->name ?? 'Deployment' }}
        </a>
    </div>

    <x-app.heading
        title="Get Your Domain"
        :description="'Connect a custom domain to ' . ($deployment->product->name ?? 'your deployment') . '. We\'ll register it and point it to your system automatically.'"
        :border="false"
    />

    <div class="mt-6 max-w-2xl space-y-4">

        {{-- Already purchased --}}
        @if($activePurchase)
        <div class="p-5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl">
            <div class="flex items-start gap-3">
                <x-phosphor-check-circle class="w-5 h-5 text-green-500 mt-0.5 shrink-0" />
                <div>
                    <p class="font-semibold text-zinc-900 dark:text-white">Domain purchase confirmed</p>
                    <p class="text-sm text-zinc-500 mt-0.5">
                        <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $activePurchase->domain }}</span>
                        &middot;
                        @if($activePurchase->registration_status === 'registered')
                            Registered &middot;
                        @elseif($activePurchase->registration_status === 'processing')
                            Registering… &middot;
                        @elseif($activePurchase->registration_status === 'failed')
                            <span class="text-red-500">Registration failed — contact support</span> &middot;
                        @else
                            Pending registration &middot;
                        @endif
                        DNS:
                        @if($activePurchase->dns_status === 'configured')
                            <span class="text-green-600 font-medium">Active</span>
                        @elseif($activePurchase->dns_status === 'failed')
                            <span class="text-red-500">Setup failed — contact support</span>
                        @else
                            setting up…
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if(session('status'))
            <div class="p-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-xl dark:bg-green-900/20 dark:border-green-800 dark:text-green-300">
                {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        @if(!$activePurchase)
        <div class="p-6 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl">
            <livewire:domain-search :deployment="$deployment" />
        </div>
        @endif

        {{-- What happens next --}}
        @if(!$activePurchase)
        <div class="p-5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl">
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-200 mb-3">What happens after you pay?</h3>
            <div class="space-y-2 text-sm text-zinc-500 dark:text-zinc-400">
                <div class="flex gap-2"><span class="text-blue-500 font-bold shrink-0">1.</span> Your domain is reserved and registered</div>
                <div class="flex gap-2"><span class="text-blue-500 font-bold shrink-0">2.</span> DNS is configured and pointed to your system</div>
                <div class="flex gap-2"><span class="text-blue-500 font-bold shrink-0">3.</span> Your domain goes live — usually within 30 minutes</div>
                <div class="flex gap-2"><span class="text-blue-500 font-bold shrink-0">4.</span> You'll see the domain on your deployment page</div>
            </div>
        </div>
        @endif

    </div>

</x-app.container>
</x-layouts.app>
