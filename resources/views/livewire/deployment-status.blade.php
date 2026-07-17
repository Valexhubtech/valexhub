<div @if($hasPending) wire:poll.5000ms="$refresh" @endif>

    @if(session('status'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-md dark:bg-green-900/30 dark:text-green-400">{{ session('status') }}</div>
    @endif

    @if($deployments->isEmpty())
        <div class="py-16 text-center">
            <x-phosphor-rocket-launch class="w-16 h-16 mx-auto mb-4 text-zinc-400" />
            <h3 class="mb-2 text-xl font-semibold text-zinc-900 dark:text-white">No Deployments Yet</h3>
            <p class="text-zinc-500">Deploy a pre-built product from the Products page to see it here.</p>
            <a href="{{ route('dashboard.products') }}" wire:navigate class="inline-block px-4 py-2 mt-4 text-sm font-medium text-white rounded-md bg-zinc-900 hover:bg-zinc-800">Browse Products</a>
        </div>
    @else

    {{-- Desktop table --}}
    <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700 text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Application</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider hidden sm:table-cell">Client / Type</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider hidden md:table-cell">Domain</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-100 dark:divide-zinc-800">
                @foreach($deployments as $deployment)
                @php
                    $statusConfig = [
                        'pending'      => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300',
                        'provisioning' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/60 dark:text-yellow-300',
                        'active'       => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                        'failed'       => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                        'suspended'    => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
                        'terminated'   => 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-400',
                    ];
                    $statusLabels = [
                        'pending'      => 'Pending',
                        'provisioning' => 'Provisioning',
                        'active'       => 'Active',
                        'failed'       => 'Failed',
                        'suspended'    => 'Suspended',
                        'terminated'   => 'Terminated',
                    ];
                    $statusClass = $statusConfig[$deployment->status] ?? 'bg-zinc-100 text-zinc-600';
                    $statusLabel = $statusLabels[$deployment->status] ?? ucfirst($deployment->status);

                    $domainPurchase = $deployment->domainPurchases->first();
                @endphp
                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors">

                    {{-- App name + renewal --}}
                    <td class="px-4 py-3">
                        <p class="font-semibold text-zinc-900 dark:text-white">{{ $deployment->product->name }}</p>
                        @if($deployment->userProduct?->next_renewal_date)
                            <p class="text-xs text-zinc-400 mt-0.5">
                                Renews {{ $deployment->userProduct->next_renewal_date->format('d M Y') }}
                                @if($deployment->userProduct->next_renewal_date->isPast())
                                    <span class="text-red-500">(overdue)</span>
                                @endif
                            </p>
                        @endif
                    </td>

                    {{-- Client / type --}}
                    <td class="px-4 py-3 hidden sm:table-cell text-zinc-600 dark:text-zinc-400">
                        <p>{{ $deployment->deploy_for === 'client' ? $deployment->client_name : 'Yourself' }}</p>
                        <p class="text-xs text-zinc-400 mt-0.5">{{ $deployment->userProduct?->deployment_type === 'onprem' ? 'On-Premises' : 'Cloud' }}</p>
                    </td>

                    {{-- Domain --}}
                    <td class="px-4 py-3 hidden md:table-cell">
                        @if($domainPurchase)
                            <div>
                                <p class="font-medium text-zinc-800 dark:text-zinc-200 text-xs">{{ $domainPurchase->domain }}</p>
                                <span class="inline-flex items-center text-xs mt-0.5
                                    {{ $domainPurchase->dns_status === 'configured' ? 'text-green-600 dark:text-green-400' : ($domainPurchase->dns_status === 'failed' ? 'text-red-500' : 'text-zinc-400') }}">
                                    @if($domainPurchase->dns_status === 'configured')
                                        <x-phosphor-check-circle class="w-3 h-3 mr-1" /> Live
                                    @elseif($domainPurchase->dns_status === 'failed')
                                        <x-phosphor-warning class="w-3 h-3 mr-1" /> DNS failed
                                    @else
                                        <x-phosphor-clock class="w-3 h-3 mr-1" /> Setting up…
                                    @endif
                                </span>
                            </div>
                        @elseif($deployment->custom_domain)
                            <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ $deployment->custom_domain }}</p>
                        @elseif($deployment->status === 'active')
                            <a href="{{ route('dashboard.deployments.domain', ['deployment' => $deployment->id]) }}"
                               wire:navigate
                               class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                <x-phosphor-globe class="w-3.5 h-3.5" />
                                Get a Domain
                            </a>
                        @else
                            <span class="text-xs text-zinc-400">—</span>
                        @endif
                    </td>

                    {{-- Status badge --}}
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                            @if($deployment->status === 'provisioning' || $deployment->status === 'pending')
                                <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                            @endif
                            {{ $statusLabel }}
                        </span>
                        @if($deployment->status === 'pending')
                            <p class="text-xs text-zinc-400 mt-1">Awaiting payment…</p>
                        @elseif($deployment->status === 'provisioning')
                            @if($deployment->deployment_url)
                                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">Initialising app…</p>
                            @else
                                <p class="text-xs text-zinc-400 mt-1">Starting container…</p>
                            @endif
                        @elseif($deployment->status === 'failed' && $deployment->failure_reason)
                            <p class="text-xs text-red-500 mt-1 truncate max-w-[140px]" title="{{ $deployment->failure_reason }}">
                                {{ Str::limit($deployment->failure_reason, 40) }}
                            </p>
                        @elseif($deployment->status === 'suspended')
                            <p class="text-xs text-orange-500 mt-1">Missed payment</p>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2 flex-wrap">
                            @if($deployment->status === 'active' && $deployment->deployment_url)
                                <a href="{{ URL::temporarySignedRoute('deployments.one-click-login', now()->addMinutes(10), ['deployment' => $deployment->id]) }}"
                                   target="_blank"
                                   class="px-3 py-1.5 text-xs font-medium text-white rounded-md bg-zinc-900 hover:bg-zinc-700 dark:bg-zinc-700 dark:hover:bg-zinc-600 transition-colors whitespace-nowrap">
                                    Login
                                </a>
                            @endif

                            @if($deployment->status === 'failed')
                                <button wire:click="retry({{ $deployment->id }})"
                                        wire:loading.attr="disabled"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-700 transition-colors">
                                    Retry
                                </button>
                            @endif

                            @if(in_array($deployment->status, ['pending', 'failed']))
                                <div x-data="{ confirming: false }">
                                    <button x-show="!confirming" @click="confirming = true"
                                            class="px-3 py-1.5 text-xs border rounded-md border-red-300 text-red-600 hover:bg-red-50 transition-colors">Cancel</button>
                                    <span x-show="confirming" class="inline-flex items-center gap-1.5 text-xs">
                                        <span class="text-zinc-500">Sure?</span>
                                        <button wire:click="cancel({{ $deployment->id }})" @click="confirming = false"
                                                class="px-2 py-1 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700">Yes</button>
                                        <button @click="confirming = false"
                                                class="px-2 py-1 text-xs border rounded border-zinc-300 text-zinc-600">No</button>
                                    </span>
                                </div>
                            @endif

                            <a href="{{ route('dashboard.deployments.show', ['deployment' => $deployment->id]) }}" wire:navigate
                               class="px-3 py-1.5 text-xs font-medium border rounded-md border-zinc-300 text-zinc-600 hover:bg-zinc-50 dark:border-zinc-600 dark:text-zinc-400 dark:hover:bg-zinc-700 transition-colors whitespace-nowrap">
                                Details
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @endif
</div>
