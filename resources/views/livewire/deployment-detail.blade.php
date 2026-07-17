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
    @php
        // Step 2 = container started (Coolify deployment_success fired, URL saved)
        // Step 3 = bootstrap running (container up, waiting for setup-complete webhook)
        $containerStarted = (bool) $deployment->deployment_url;
    @endphp
    <div class="p-6 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center flex-shrink-0">
                <x-phosphor-gear-six class="w-5 h-5 text-yellow-600 animate-spin" />
            </div>
            <div>
                <h3 class="font-semibold text-zinc-900 dark:text-white">Setting Up Your App</h3>
                <p class="text-sm text-zinc-500">This page refreshes automatically — usually ready in 2–5 minutes.</p>
            </div>
        </div>

        {{-- Progress steps --}}
        <ol class="relative border-l border-zinc-200 dark:border-zinc-700 ml-3 space-y-6">

            {{-- Step 1: Payment --}}
            <li class="ml-6">
                <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/40 ring-4 ring-white dark:ring-zinc-800">
                    <x-phosphor-check class="w-3 h-3 text-green-600 dark:text-green-400" />
                </span>
                <p class="text-sm font-medium text-green-700 dark:text-green-400">Payment confirmed</p>
                <p class="text-xs text-zinc-400 mt-0.5">Your order has been received.</p>
            </li>

            {{-- Step 2: Container starting --}}
            <li class="ml-6">
                <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full ring-4 ring-white dark:ring-zinc-800
                    {{ $containerStarted ? 'bg-green-100 dark:bg-green-900/40' : 'bg-yellow-100 dark:bg-yellow-900/40' }}">
                    @if($containerStarted)
                        <x-phosphor-check class="w-3 h-3 text-green-600 dark:text-green-400" />
                    @else
                        <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                    @endif
                </span>
                <p class="text-sm font-medium {{ $containerStarted ? 'text-green-700 dark:text-green-400' : 'text-yellow-700 dark:text-yellow-400' }}">
                    Container starting
                </p>
                <p class="text-xs text-zinc-400 mt-0.5">Pulling the Docker image and booting the container.</p>
            </li>

            {{-- Step 3: App initialising --}}
            <li class="ml-6">
                <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full ring-4 ring-white dark:ring-zinc-800
                    {{ $containerStarted ? 'bg-yellow-100 dark:bg-yellow-900/40' : 'bg-zinc-100 dark:bg-zinc-700' }}">
                    @if($containerStarted)
                        <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-zinc-300 dark:bg-zinc-500"></span>
                    @endif
                </span>
                <p class="text-sm font-medium {{ $containerStarted ? 'text-yellow-700 dark:text-yellow-400' : 'text-zinc-400' }}">
                    App initialising
                </p>
                <p class="text-xs text-zinc-400 mt-0.5">Creating your database, running migrations, seeding your account.</p>
            </li>

            {{-- Step 4: Ready --}}
            <li class="ml-6">
                <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full bg-zinc-100 dark:bg-zinc-700 ring-4 ring-white dark:ring-zinc-800">
                    <span class="w-2 h-2 rounded-full bg-zinc-300 dark:bg-zinc-500"></span>
                </span>
                <p class="text-sm font-medium text-zinc-400">Ready</p>
                <p class="text-xs text-zinc-400 mt-0.5">Login credentials will be emailed to you.</p>
            </li>

        </ol>
    </div>
    @endif

    {{-- Active --}}
    @if($deployment->status === 'active')
        <div class="p-5 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
            <h2 class="mb-4 text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Access Your App</h2>

            @php
                $loginPath = ltrim($deployment->product->login_path ?? '/dashboard', '/');
                $loginUrl  = $deployment->deployment_url
                    ? rtrim($deployment->deployment_url, '/') . '/' . $loginPath
                    : null;
            @endphp
            <div class="flex flex-wrap gap-3">
                @if($loginUrl)
                    <a href="{{ URL::temporarySignedRoute('deployments.one-click-login', now()->addMinutes(10), ['deployment' => $deployment->id]) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-md bg-zinc-900 hover:bg-zinc-800 transition-colors">
                        <x-phosphor-sign-in class="w-4 h-4" />
                        One-Click Login
                    </a>
                    <a href="{{ $loginUrl }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm border rounded-md border-zinc-300 text-zinc-700 dark:border-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-neutral-700 transition-colors">
                        <x-phosphor-arrow-square-out class="w-4 h-4" />
                        Open App
                    </a>
                @elseif($deployment->deployment_url)
                    <a href="{{ $deployment->deployment_url }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm border rounded-md border-zinc-300 text-zinc-700 dark:border-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-neutral-700 transition-colors">
                        <x-phosphor-arrow-square-out class="w-4 h-4" />
                        Open App
                    </a>
                @else
                    <p class="text-sm text-zinc-500">Your app URL will appear here once the container is assigned a domain.</p>
                @endif
            </div>

            @php
                $creds = $deployment->credentials_encrypted ?? [];
                $email    = $creds['email']    ?? $creds['username'] ?? null;
                $password = $creds['password'] ?? null;
            @endphp
            @if($email || $password)
                <div x-data="{ revealed: false }" class="mt-5 pt-5 border-t border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Login Credentials</p>
                        <button @click="revealed = !revealed"
                                class="text-xs text-zinc-500 hover:text-zinc-900 dark:hover:text-white underline transition-colors"
                                x-text="revealed ? 'Hide' : 'Show credentials'"></button>
                    </div>
                    <div x-show="revealed" x-transition class="p-3 bg-zinc-50 dark:bg-zinc-900 rounded-md space-y-2 font-mono text-xs">
                        @if($email)
                        <div class="flex items-center gap-3">
                            <span class="text-zinc-400 w-20 flex-shrink-0">Email</span>
                            <span class="text-zinc-900 dark:text-white break-all">{{ $email }}</span>
                        </div>
                        @endif
                        @if($password)
                        <div class="flex items-center gap-3">
                            <span class="text-zinc-400 w-20 flex-shrink-0">Password</span>
                            <span class="text-zinc-900 dark:text-white break-all">{{ $password }}</span>
                        </div>
                        @endif
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
