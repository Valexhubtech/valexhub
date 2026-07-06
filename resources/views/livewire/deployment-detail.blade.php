<div @if($isPolling) wire:poll.5000ms="$refresh" @endif>

    @php
        $statusConfig = [
            'pending'      => ['color' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300',        'label' => 'Pending Payment'],
            'provisioning' => ['color' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300', 'label' => 'Being Set Up'],
            'active'       => ['color' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',    'label' => 'Active'],
            'failed'       => ['color' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',            'label' => 'Failed'],
            'suspended'    => ['color' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300','label' => 'Suspended'],
            'terminated'   => ['color' => 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-400',            'label' => 'Terminated'],
        ];
        $sc = $statusConfig[$deployment->status] ?? $statusConfig['pending'];
    @endphp

    {{-- Status badge row --}}
    <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Deployment Status</h2>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-full {{ $sc['color'] }}">
            @if($deployment->status === 'provisioning')
                <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
            @else
                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
            @endif
            {{ $sc['label'] }}
        </span>
    </div>

    {{-- Pending --}}
    @if($deployment->status === 'pending')
        <div class="p-6 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700 text-center">
            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center">
                <x-phosphor-clock class="w-6 h-6 text-zinc-400 animate-pulse" />
            </div>
            <h3 class="font-semibold text-zinc-900 dark:text-white">Waiting for Payment Confirmation</h3>
            <p class="mt-1 text-sm text-zinc-500">Paystack is confirming your payment. This updates automatically.</p>
        </div>
    @endif

    {{-- Provisioning --}}
    @if($deployment->status === 'provisioning')
        <div class="p-6 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700 text-center">
            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                <x-phosphor-gear-six class="w-6 h-6 text-yellow-600 animate-spin" />
            </div>
            <h3 class="font-semibold text-zinc-900 dark:text-white">Setting Up Your App</h3>
            <p class="mt-1 text-sm text-zinc-500">Provisioning {{ $deployment->product->name }}. Usually takes 2–5 minutes.</p>
        </div>
    @endif

    {{-- Active --}}
    @if($deployment->status === 'active')
        <div class="p-5 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
            <h2 class="mb-4 text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Access Your App</h2>

            <div class="flex flex-wrap gap-3">
                @if($deployment->deployment_url)
                    <a href="{{ URL::temporarySignedRoute('deployments.one-click-login', now()->addMinutes(10), ['deployment' => $deployment->id]) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-md bg-zinc-900 hover:bg-zinc-800 transition-colors">
                        <x-phosphor-sign-in class="w-4 h-4" />
                        One-Click Login
                    </a>
                    <a href="{{ $deployment->deployment_url }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm border rounded-md border-zinc-300 text-zinc-700 dark:border-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-neutral-700 transition-colors">
                        <x-phosphor-arrow-square-out class="w-4 h-4" />
                        Open App
                    </a>
                @else
                    <p class="text-sm text-zinc-500">Your app is running. A URL will appear once Coolify assigns one.</p>
                @endif
            </div>

            @if($deployment->credentials_encrypted)
                <div x-data="{ revealed: false }" class="mt-5 pt-5 border-t border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Login Credentials</p>
                        <button @click="revealed = !revealed"
                                class="text-xs text-zinc-500 hover:text-zinc-900 dark:hover:text-white underline transition-colors"
                                x-text="revealed ? 'Hide' : 'Show credentials'"></button>
                    </div>
                    <div x-show="revealed" x-transition class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded-md space-y-1.5 font-mono text-xs">
                        @foreach($deployment->credentials_encrypted ?? [] as $key => $value)
                            <div class="flex items-center gap-2">
                                <span class="text-zinc-400 w-24 flex-shrink-0">{{ Str::title(str_replace('_', ' ', $key)) }}</span>
                                <span class="text-zinc-900 dark:text-white break-all">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p x-show="!revealed" class="text-xs text-zinc-400">Credentials are hidden for security.</p>
                </div>
            @endif
        </div>

        {{-- Domain card --}}
        @php
            $domainPurchase = \Wave\DomainPurchase::where('deployment_id', $deployment->id)
                ->where('payment_status', 'paid')->latest()->first();
        @endphp
        <div class="p-5 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
            <h2 class="mb-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Your Domain</h2>

            @if($domainPurchase)
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <p class="font-semibold text-zinc-900 dark:text-white text-base">{{ $domainPurchase->domain }}</p>
                        <p class="text-xs text-zinc-500 mt-0.5">
                            @if($domainPurchase->dns_status === 'configured')
                                <span class="text-green-600 font-medium">● Live</span> — DNS active
                            @else
                                <span class="text-amber-500">● Setting up</span> — usually ready in ~30 min
                            @endif
                        </p>
                    </div>
                </div>
            @elseif($deployment->custom_domain)
                <p class="font-semibold text-zinc-900 dark:text-white text-base">{{ $deployment->custom_domain }}</p>
                <p class="text-xs text-zinc-500 mt-0.5">Custom domain configured</p>
            @else
                <p class="text-sm text-zinc-500 mb-4">Add a custom domain to give clients a professional address.</p>
                <a href="{{ route('dashboard.deployments.domain', ['deployment' => $deployment->id]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-lg bg-zinc-900 hover:bg-zinc-800 transition-colors">
                    <x-phosphor-globe class="w-4 h-4" />
                    Get Your Domain
                </a>
            @endif
        </div>
    @endif

    {{-- Failed --}}
    @if($deployment->status === 'failed')
        <div class="p-5 bg-white border border-red-200 rounded-xl dark:bg-zinc-800 dark:border-red-900">
            <div class="flex items-start gap-3">
                <x-phosphor-x-circle class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" />
                <div class="flex-1">
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Deployment Failed</h3>
                    @if($deployment->failure_reason)
                        <p class="mt-1 text-sm font-mono bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded p-2 break-all">{{ $deployment->failure_reason }}</p>
                    @endif
                    <p class="mt-2 text-sm text-zinc-500">Your payment has been recorded. You can retry or contact support.</p>
                </div>
            </div>
            <div class="mt-4 flex gap-3">
                <button wire:click="retry"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-60 cursor-wait"
                        class="px-4 py-2 text-sm font-medium text-white rounded-md bg-zinc-900 hover:bg-zinc-800 transition-colors">
                    <span wire:loading.remove wire:target="retry">Retry Deployment</span>
                    <span wire:loading wire:target="retry">Retrying…</span>
                </button>
                <a href="mailto:support@valexhub.com?subject=Failed Deployment #{{ $deployment->id }}"
                   class="px-4 py-2 text-sm border rounded-md border-zinc-300 text-zinc-600 hover:bg-zinc-50 dark:border-zinc-600 dark:text-zinc-400 dark:hover:bg-zinc-700 transition-colors">
                    Contact Support
                </a>
            </div>
        </div>
    @endif

    {{-- Suspended --}}
    @if($deployment->status === 'suspended')
        <div class="p-5 bg-white border border-orange-200 rounded-xl dark:bg-zinc-800 dark:border-orange-900">
            <div class="flex items-start gap-3">
                <x-phosphor-pause-circle class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" />
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Service Suspended</h3>
                    <p class="mt-1 text-sm text-zinc-500">Your service has been suspended due to a missed renewal. Contact support to reactivate.</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="mailto:support@valexhub.com?subject=Reactivate Deployment #{{ $deployment->id }}"
                   class="px-4 py-2 text-sm font-medium text-white rounded-md bg-zinc-900 hover:bg-zinc-800 transition-colors">
                    Contact Support to Reactivate
                </a>
            </div>
        </div>
    @endif

    {{-- Terminated --}}
    @if($deployment->status === 'terminated')
        <div class="p-5 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
            <div class="flex items-start gap-3">
                <x-phosphor-prohibit class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" />
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white">Service Terminated</h3>
                    <p class="mt-1 text-sm text-zinc-500">This deployment has been permanently terminated. Contact support if this was in error.</p>
                </div>
            </div>
        </div>
    @endif

</div>
