<?php
    use function Laravel\Folio\{middleware, name};
    middleware('auth');
    name('dashboard.invoices');
?>
@php
    /** @var \App\Models\User $user */
    $user = auth()->user();
    $invoices = $user->invoices()->with(['userProduct.product', 'deployment'])->latest()->get();
@endphp

<x-layouts.app>
    <x-app.container x-data class="lg:space-y-6" x-cloak>

        <x-app.heading
            title="Invoice History"
            description="View, download, and pay invoices for all your services."
            :border="false"
        />

        {{-- Flash messages --}}
        @if(session('status'))
            <div class="p-4 text-sm text-green-700 bg-green-100 rounded-md dark:bg-green-900/30 dark:text-green-400">
                {{ session('status') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 text-sm text-red-700 bg-red-100 rounded-md dark:bg-red-900/30 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        @if($invoices->isEmpty())
            <div class="py-16 text-center">
                <x-phosphor-receipt class="w-16 h-16 mx-auto mb-4 text-zinc-400" />
                <h3 class="mb-2 text-xl font-semibold text-zinc-900 dark:text-white">No Invoices Yet</h3>
                <p class="text-zinc-500">Invoices are generated automatically after each payment.</p>
            </div>
        @else
            <div class="overflow-hidden bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-neutral-700 bg-zinc-50 dark:bg-zinc-900">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wide">Invoice</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wide">Service</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wide">Date</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wide">Due</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wide">Amount</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wide">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-neutral-700">
                        @foreach($invoices as $invoice)
                            @php
                                $isPaid     = $invoice->status === 'paid';
                                $isOverdue  = ! $isPaid && $invoice->due_date && $invoice->due_date->isPast();
                                $isPending  = ! $isPaid && ! $isOverdue;

                                if ($isPaid) {
                                    $badgeLabel = 'Paid';
                                    $badgeClass = 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400';
                                } elseif ($isOverdue) {
                                    $badgeLabel = 'Overdue';
                                    $badgeClass = 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400';
                                } else {
                                    $badgeLabel = 'Pending';
                                    $badgeClass = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400';
                                }
                            @endphp
                            <tr class="hover:bg-zinc-50 dark:hover:bg-neutral-700/50 transition-colors">
                                <td class="px-5 py-3.5 font-mono text-xs text-zinc-600 dark:text-zinc-400">
                                    {{ 'INV-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($invoice->deployment_id)
                                        <a href="{{ route('dashboard.deployments.show', ['deployment' => $invoice->deployment_id]) }}" wire:navigate
                                           class="font-medium text-zinc-900 dark:text-white hover:underline">
                                            {{ $invoice->userProduct?->product?->name ?? 'Service' }}
                                        </a>
                                    @else
                                        <span class="font-medium text-zinc-900 dark:text-white">
                                            {{ $invoice->userProduct?->product?->name ?? '—' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-zinc-500">
                                    {{ $invoice->created_at->format('d M Y') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($invoice->due_date)
                                        <span class="{{ $isOverdue ? 'text-red-600 dark:text-red-400 font-medium' : 'text-zinc-500' }}">
                                            {{ $invoice->due_date->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-zinc-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right font-semibold text-zinc-900 dark:text-white">
                                    ₦{{ number_format((float) $invoice->amount, 2) }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                        {{ $badgeLabel }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-3">
                                        {{-- Pay Now --}}
                                        @if(! $isPaid)
                                            <a href="{{ route('dashboard.invoices.pay', $invoice) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-white bg-zinc-900 hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 rounded-md transition-colors">
                                                <x-phosphor-credit-card class="w-3.5 h-3.5" />
                                                Pay Now
                                            </a>
                                        @endif

                                        {{-- Download PDF --}}
                                        <a href="{{ route('dashboard.invoices.download', $invoice) }}"
                                           class="inline-flex items-center gap-1 text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition-colors">
                                            <x-phosphor-download-simple class="w-3.5 h-3.5" />
                                            PDF
                                        </a>

                                        {{-- View Deployment --}}
                                        @if($invoice->deployment_id)
                                            <a href="{{ route('dashboard.deployments.show', ['deployment' => $invoice->deployment_id]) }}" wire:navigate
                                               class="inline-flex items-center gap-1 text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition-colors">
                                                <x-phosphor-arrow-square-out class="w-3.5 h-3.5" />
                                                View
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </x-app.container>
</x-layouts.app>
