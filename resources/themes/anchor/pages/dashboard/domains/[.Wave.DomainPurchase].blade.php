<?php
    use function Laravel\Folio\{middleware, name};
    middleware('auth');
    name('dashboard.domains.show');
?>
@php
    abort_unless($domainPurchase->user_id === auth()->id(), 403);

    $domainPurchase->load('deployment');

    $payBadge = match($domainPurchase->payment_status) {
        'paid'    => ['label' => 'Paid',    'class' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'],
        'failed'  => ['label' => 'Failed',  'class' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'],
        'pending' => ['label' => 'Pending', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'],
        default   => ['label' => ucfirst($domainPurchase->payment_status ?? '—'), 'class' => 'bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400'],
    };

    $regBadge = match($domainPurchase->registration_status) {
        'registered', 'active' => ['label' => 'Registered', 'class' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'],
        'pending'              => ['label' => 'Pending',    'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'],
        'failed'               => ['label' => 'Failed',     'class' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'],
        default                => ['label' => ucfirst($domainPurchase->registration_status ?? '—'), 'class' => 'bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400'],
    };

    $dnsBadge = match($domainPurchase->dns_status) {
        'configured', 'active' => ['label' => 'Configured', 'class' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'],
        'pending'              => ['label' => 'Pending',    'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'],
        'failed'               => ['label' => 'Failed',     'class' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'],
        default                => ['label' => ucfirst($domainPurchase->dns_status ?? '—'), 'class' => 'bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400'],
    };

    $nameservers = $domainPurchase->nameservers ?? [];
    $dnsRecords  = $domainPurchase->dns_records ?? [];
@endphp

<x-layouts.app>
    <x-app.container class="lg:space-y-6">

        {{-- Breadcrumb --}}
        <div>
            <a href="{{ route('dashboard.domains') }}" wire:navigate
               class="inline-flex items-center text-sm text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                <x-phosphor-arrow-left class="w-4 h-4 mr-1" /> My Domains
            </a>
        </div>

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-teal-50 dark:bg-teal-900/30">
                    <x-phosphor-globe class="w-5 h-5 text-teal-600 dark:text-teal-400" />
                </div>
                <div>
                    <h1 class="text-xl font-bold text-zinc-900 dark:text-white">{{ $domainPurchase->domain }}</h1>
                    <p class="text-sm text-zinc-500">.{{ $domainPurchase->tld }} domain</p>
                </div>
            </div>
        </div>

        {{-- Status cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

            <div class="p-5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl">
                <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-2">Payment</p>
                <span class="inline-flex px-2.5 py-1 rounded-full text-sm font-medium {{ $payBadge['class'] }}">
                    {{ $payBadge['label'] }}
                </span>
                @if($domainPurchase->paid_at)
                    <p class="mt-2 text-xs text-zinc-400">{{ $domainPurchase->paid_at->format('d M Y, H:i') }}</p>
                @endif
            </div>

            <div class="p-5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl">
                <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-2">Registration</p>
                <span class="inline-flex px-2.5 py-1 rounded-full text-sm font-medium {{ $regBadge['class'] }}">
                    {{ $regBadge['label'] }}
                </span>
                <p class="mt-2 text-xs text-zinc-400">
                    @if(in_array($domainPurchase->registration_status, ['registered', 'active']))
                        Registered on {{ ($domainPurchase->paid_at ?? $domainPurchase->created_at)->format('d M Y') }}
                    @elseif($domainPurchase->registration_status === 'failed')
                        Registration failed — contact support
                    @else
                        Processing with registrar
                    @endif
                </p>
            </div>

            <div class="p-5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl">
                <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-2">DNS</p>
                <span class="inline-flex px-2.5 py-1 rounded-full text-sm font-medium {{ $dnsBadge['class'] }}">
                    {{ $dnsBadge['label'] }}
                </span>
                <p class="mt-2 text-xs text-zinc-400">
                    @if(in_array($domainPurchase->dns_status, ['configured', 'active']))
                        DNS is propagating (up to 48 hours)
                    @elseif($domainPurchase->dns_status === 'failed')
                        DNS configuration failed — contact support
                    @else
                        Awaiting domain registration
                    @endif
                </p>
            </div>

        </div>

        {{-- Nameservers --}}
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-700">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Nameservers</h2>
                <p class="text-xs text-zinc-500 mt-0.5">The nameservers your domain is pointed to.</p>
            </div>
            @if(count($nameservers) > 0)
                <div class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @foreach($nameservers as $ns)
                        <div class="flex items-center justify-between px-5 py-3.5">
                            <span class="font-mono text-sm text-zinc-700 dark:text-zinc-300">{{ $ns }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-5 py-10 text-center">
                    <x-phosphor-clock class="w-8 h-8 mx-auto mb-2 text-zinc-300 dark:text-zinc-600" />
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Nameservers not yet available</p>
                    <p class="text-xs text-zinc-400 mt-1">They will appear here once your domain registration is confirmed by the registrar.</p>
                </div>
            @endif
        </div>

        {{-- DNS Records --}}
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-700">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">DNS Records</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Active DNS configuration for this domain.</p>
            </div>
            @if(count($dnsRecords) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-100 dark:border-zinc-700">
                                <th class="px-5 py-2.5 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wide">Type</th>
                                <th class="px-5 py-2.5 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wide">Name</th>
                                <th class="px-5 py-2.5 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wide">Value</th>
                                <th class="px-5 py-2.5 text-left text-xs font-semibold text-zinc-400 uppercase tracking-wide">TTL</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                            @foreach($dnsRecords as $record)
                                <tr>
                                    <td class="px-5 py-3">
                                        <span class="font-mono text-xs px-1.5 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded">
                                            {{ strtoupper($record['type'] ?? '—') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $record['name'] ?? '—' }}</td>
                                    <td class="px-5 py-3 font-mono text-xs text-zinc-600 dark:text-zinc-400 max-w-xs truncate">{{ $record['value'] ?? '—' }}</td>
                                    <td class="px-5 py-3 text-xs text-zinc-400">{{ $record['ttl'] ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-10 text-center">
                    <x-phosphor-clock class="w-8 h-8 mx-auto mb-2 text-zinc-300 dark:text-zinc-600" />
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">DNS records not yet available</p>
                    <p class="text-xs text-zinc-400 mt-1">Records will appear here once registration is complete and DNS has been configured.</p>
                </div>
            @endif
        </div>

        {{-- Pricing summary --}}
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-700">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Pricing</h2>
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-700">
                <div class="flex justify-between px-5 py-3 text-sm">
                    <span class="text-zinc-500">Domain registration</span>
                    <span class="font-medium text-zinc-900 dark:text-white">₦{{ number_format($domainPurchase->domain_price_naira, 2) }}</span>
                </div>
                <div class="flex justify-between px-5 py-3 text-sm">
                    <span class="text-zinc-500">Setup fee</span>
                    <span class="font-medium text-zinc-900 dark:text-white">₦{{ number_format($domainPurchase->setup_fee_naira, 2) }}</span>
                </div>
                <div class="flex justify-between px-5 py-3 text-sm font-semibold bg-zinc-50 dark:bg-zinc-900/50">
                    <span class="text-zinc-700 dark:text-zinc-300">Total</span>
                    <span class="text-zinc-900 dark:text-white">₦{{ number_format($domainPurchase->total_naira, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Linked deployment --}}
        @if($domainPurchase->deployment)
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-700">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">Linked Deployment</h2>
                </div>
                <div class="px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30">
                            <x-phosphor-rocket-launch class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $domainPurchase->deployment->name ?? 'Deployment #' . $domainPurchase->deployment_id }}
                            </p>
                            <p class="text-xs text-zinc-400">{{ ucfirst($domainPurchase->deployment->status) }}</p>
                        </div>
                    </div>
                    <a href="{{ route('dashboard.deployments.show', ['deployment' => $domainPurchase->deployment]) }}" wire:navigate
                       class="inline-flex items-center gap-1 text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                        View <x-phosphor-arrow-right class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>
        @endif

    </x-app.container>
</x-layouts.app>
