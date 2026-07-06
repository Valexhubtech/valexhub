<?php
    use function Laravel\Folio\{middleware, name};
    middleware('auth');
    name('dashboard.deployments.show');
?>
@php
    abort_unless($deployment->user_id === auth()->id(), 403);

    $deployment->load([
        'product',
        'userProduct.pricing',
        'userProduct.orderAddons.addon',
    ]);

    $invoices = \Wave\Invoice::where('deployment_id', $deployment->id)
        ->orderByDesc('created_at')
        ->get();

    $userProduct = $deployment->userProduct;
@endphp

<x-layouts.app>
    <x-app.container x-data class="lg:space-y-6" x-cloak>

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard.deployments') }}" wire:navigate class="flex items-center text-sm text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                <x-phosphor-arrow-left class="w-4 h-4 mr-1" /> My Deployments
            </a>
        </div>

        {{-- Status flash --}}
        @if(session('status'))
            <div class="p-4 text-sm text-green-700 bg-green-100 rounded-md dark:bg-green-900/30 dark:text-green-400">
                {{ session('status') }}
            </div>
        @endif

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $deployment->product->name }}</h1>
            <p class="mt-1 text-sm text-zinc-500">
                {{ $userProduct?->deployment_type === 'onprem' ? 'On-Premises' : 'Cloud' }} deployment
                &middot;
                Ordered {{ $userProduct?->purchase_date?->format('d M Y') ?? $deployment->created_at->format('d M Y') }}
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Left: Order Summary --}}
            <div class="space-y-4 lg:col-span-1">

                <div class="p-5 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
                    <h2 class="mb-4 text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Order Summary</h2>

                    <div class="space-y-2.5 text-sm">

                        {{-- Setup fee line --}}
                        @if($userProduct && (float)$userProduct->setup_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-zinc-500">
                                    {{ $userProduct->deployment_type === 'onprem' ? 'License & Setup' : 'Setup Fee' }}
                                </span>
                                <span class="font-medium text-zinc-900 dark:text-white">
                                    ₦{{ number_format($userProduct->setup_amount, 2) }}
                                </span>
                            </div>
                        @endif

                        {{-- Recurring plan --}}
                        @if($userProduct?->pricing)
                            <div class="flex justify-between">
                                <span class="text-zinc-500">{{ $userProduct->pricing->label }}</span>
                                <span class="font-medium text-zinc-900 dark:text-white">
                                    ₦{{ number_format($userProduct->pricing->amount, 2) }}
                                </span>
                            </div>
                        @endif

                        {{-- Addons --}}
                        @if($userProduct && $userProduct->orderAddons->isNotEmpty())
                            @foreach($userProduct->orderAddons as $orderAddon)
                                <div class="flex justify-between">
                                    <span class="text-zinc-500">
                                        {{ $orderAddon->addon?->name ?? 'Addon' }}
                                        @if($orderAddon->price_type === 'recurring')
                                            <span class="text-xs text-zinc-400">({{ $orderAddon->addon?->billing_cycle }})</span>
                                        @endif
                                    </span>
                                    <span class="font-medium text-zinc-900 dark:text-white">
                                        ₦{{ number_format($orderAddon->amount_paid, 2) }}
                                    </span>
                                </div>
                            @endforeach
                        @endif

                        {{-- Total --}}
                        @if($userProduct)
                            <div class="pt-3 border-t border-zinc-200 dark:border-zinc-600 flex justify-between font-semibold">
                                <span class="text-zinc-900 dark:text-white">Total Paid</span>
                                <span class="text-zinc-900 dark:text-white">₦{{ number_format($userProduct->amount_paid, 2) }}</span>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Billing info --}}
                @if($userProduct?->next_renewal_date)
                    <div class="p-4 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
                        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-1">Next Renewal</p>
                        <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $userProduct->next_renewal_date->format('d M Y') }}</p>
                        @if($userProduct->next_renewal_date->isPast())
                            <p class="text-xs text-red-500 mt-0.5">Overdue — payment not received</p>
                        @else
                            <p class="text-xs text-zinc-400 mt-0.5">in {{ $userProduct->next_renewal_date->diffForHumans() }}</p>
                        @endif
                    </div>
                @elseif($userProduct)
                    <div class="p-4 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
                        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-1">License</p>
                        <p class="text-sm font-medium text-zinc-900 dark:text-white">Perpetual (one-time)</p>
                    </div>
                @endif

                {{-- Deployed for --}}
                <div class="p-4 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
                    <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-2">Deploying For</p>
                    @if($deployment->deploy_for === 'client')
                        <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $deployment->client_name }}</p>
                        <p class="text-xs text-zinc-500">{{ $deployment->client_email }}</p>
                    @else
                        <p class="text-sm text-zinc-900 dark:text-white">Yourself</p>
                    @endif
                </div>

            </div>

            {{-- Right: Status + Actions --}}
            <div class="space-y-4 lg:col-span-2">

                {{-- Live-updating status section --}}
                <livewire:deployment-detail :deployment-id="$deployment->id" />

                {{-- Get Help / Open support ticket --}}
                <div x-data="{ openTicket: false }" class="p-5 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Support</h2>
                            <p class="text-xs text-zinc-400 mt-0.5">Need help with this deployment?</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('dashboard.support') }}" wire:navigate
                               class="px-3 py-1.5 text-xs border rounded-md border-zinc-300 text-zinc-600 dark:border-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-neutral-700 transition-colors">
                                View Tickets
                            </a>
                            <button @click="openTicket = !openTicket"
                                    class="px-3 py-1.5 text-xs font-medium text-white rounded-md bg-zinc-900 hover:bg-zinc-800 transition-colors">
                                Get Help
                            </button>
                        </div>
                    </div>
                    <div x-show="openTicket" x-transition class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <form method="POST" action="{{ route('support.tickets.store') }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="deployment_id" value="{{ $deployment->id }}">
                            <div>
                                <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">Subject</label>
                                <input type="text" name="subject" required placeholder="Briefly describe your issue"
                                       class="w-full px-3 py-2 text-sm border rounded-md border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white focus:outline-none focus:ring-1 focus:ring-zinc-400">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-1">Message</label>
                                <textarea name="body" required rows="3" placeholder="Describe your issue in detail"
                                          class="w-full px-3 py-2 text-sm border rounded-md border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white focus:outline-none focus:ring-1 focus:ring-zinc-400 resize-none"></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit"
                                        class="px-4 py-2 text-sm font-medium text-white rounded-md bg-zinc-900 hover:bg-zinc-800 transition-colors">
                                    Submit Ticket
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Deployment info --}}
                <div class="p-5 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
                    <h2 class="mb-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Deployment Info</h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-zinc-500">Deployment ID</dt>
                            <dd class="font-mono text-zinc-900 dark:text-white">#{{ $deployment->id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-zinc-500">Type</dt>
                            <dd class="text-zinc-900 dark:text-white">{{ $userProduct?->deployment_type === 'onprem' ? 'On-Premises' : 'Cloud' }}</dd>
                        </div>
                        @if($deployment->deployed_at)
                            <div class="flex justify-between">
                                <dt class="text-zinc-500">Went Live</dt>
                                <dd class="text-zinc-900 dark:text-white">{{ $deployment->deployed_at->format('d M Y, H:i') }}</dd>
                            </div>
                        @endif
                        @if($deployment->deployment_url)
                            <div class="flex justify-between items-center">
                                <dt class="text-zinc-500">App URL</dt>
                                <dd>
                                    <a href="{{ $deployment->deployment_url }}" target="_blank"
                                       class="text-zinc-900 dark:text-white underline text-xs break-all">
                                        {{ $deployment->deployment_url }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

            </div>

        </div>

        {{-- Invoice History --}}
        @if($invoices->isNotEmpty())
            <div class="p-5 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Invoice History</h2>
                    <a href="{{ route('dashboard.invoices') }}" wire:navigate
                       class="text-xs text-zinc-500 hover:text-zinc-900 dark:hover:text-white underline transition-colors">
                        View all invoices
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-zinc-500 uppercase tracking-wide border-b border-zinc-200 dark:border-zinc-700">
                                <th class="text-left pb-2 pr-4">Invoice</th>
                                <th class="text-left pb-2 pr-4">Date</th>
                                <th class="text-right pb-2 pr-4">Amount</th>
                                <th class="text-center pb-2 pr-4">Status</th>
                                <th class="text-right pb-2">PDF</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td class="py-2.5 pr-4 font-mono text-zinc-900 dark:text-white">
                                        INV-{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="py-2.5 pr-4 text-zinc-500">
                                        {{ $invoice->created_at->format('d M Y') }}
                                    </td>
                                    <td class="py-2.5 pr-4 text-right font-medium text-zinc-900 dark:text-white">
                                        ₦{{ number_format($invoice->amount, 2) }}
                                    </td>
                                    <td class="py-2.5 pr-4 text-center">
                                        @php
                                            $badgeColor = match($invoice->status) {
                                                'paid'  => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                'sent'  => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                                default => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300',
                                            };
                                        @endphp
                                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ $badgeColor }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 text-right">
                                        @if($invoice->pdf_path)
                                            <a href="{{ route('dashboard.invoices.download', $invoice) }}"
                                               class="inline-flex items-center gap-1 text-xs text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                                                <x-phosphor-file-pdf class="w-4 h-4" />
                                                Download
                                            </a>
                                        @else
                                            <span class="text-xs text-zinc-300 dark:text-zinc-600">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </x-app.container>
</x-layouts.app>
