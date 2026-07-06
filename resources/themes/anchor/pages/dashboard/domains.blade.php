<?php
    use function Laravel\Folio\{middleware, name};
    middleware('auth');
    name('dashboard.domains');
?>
@php
    /** @var \App\Models\User $user */
    $user    = auth()->user();
    $domains = \Wave\DomainPurchase::where('user_id', $user->id)
                ->latest()
                ->get();
@endphp

<x-layouts.app>
    <x-app.container class="lg:space-y-6">

        <x-app.heading
            title="My Domains"
            description="Domains you have registered through ValexHub."
            :border="false"
        />

        @if($domains->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl">
                <div class="w-14 h-14 flex items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-700 mb-4">
                    <x-phosphor-globe class="w-7 h-7 text-zinc-400" />
                </div>
                <h3 class="text-base font-semibold text-zinc-900 dark:text-white mb-1">No domains yet</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-xs">
                    You can register a domain when deploying software or from the domain search.
                </p>
            </div>
        @else
            <div class="overflow-hidden bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wide">Domain</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wide hidden sm:table-cell">Registered</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wide">Payment</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wide hidden md:table-cell">Registration</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wide hidden md:table-cell">DNS</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                        @foreach($domains as $domain)
                            @php
                                $payBadge = match($domain->payment_status) {
                                    'paid'    => ['label' => 'Paid',    'class' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'],
                                    'failed'  => ['label' => 'Failed',  'class' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'],
                                    'pending' => ['label' => 'Pending', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'],
                                    default   => ['label' => ucfirst($domain->payment_status ?? '—'), 'class' => 'bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400'],
                                };

                                $regBadge = match($domain->registration_status) {
                                    'active'  => ['label' => 'Active',  'class' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'],
                                    'pending' => ['label' => 'Pending', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'],
                                    'failed'  => ['label' => 'Failed',  'class' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'],
                                    default   => ['label' => ucfirst($domain->registration_status ?? '—'), 'class' => 'bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400'],
                                };

                                $dnsBadge = match($domain->dns_status) {
                                    'active'  => ['label' => 'Active',  'class' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400'],
                                    'pending' => ['label' => 'Pending', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400'],
                                    'failed'  => ['label' => 'Failed',  'class' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400'],
                                    default   => ['label' => ucfirst($domain->dns_status ?? '—'), 'class' => 'bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400'],
                                };
                            @endphp
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/40 transition-colors cursor-pointer"
                                onclick="window.location='{{ route('dashboard.domains.show', ['domainPurchase' => $domain]) }}'">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 flex-shrink-0 flex items-center justify-center rounded-lg bg-teal-50 dark:bg-teal-900/30">
                                            <x-phosphor-globe class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" />
                                        </div>
                                        <div>
                                            <a href="{{ route('dashboard.domains.show', ['domainPurchase' => $domain]) }}" wire:navigate
                                               class="font-semibold text-zinc-900 dark:text-white hover:underline">{{ $domain->domain }}</a>
                                            <p class="text-xs text-zinc-400">.{{ $domain->tld }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-zinc-500 hidden sm:table-cell">
                                    {{ $domain->paid_at?->format('d M Y') ?? $domain->created_at->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payBadge['class'] }}">
                                        {{ $payBadge['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 hidden md:table-cell">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $regBadge['class'] }}">
                                        {{ $regBadge['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 hidden md:table-cell">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dnsBadge['class'] }}">
                                        {{ $dnsBadge['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('dashboard.domains.show', ['domainPurchase' => $domain]) }}" wire:navigate
                                       class="inline-flex items-center gap-1 text-xs font-medium text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">
                                        View <x-phosphor-arrow-right class="w-3.5 h-3.5" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </x-app.container>
</x-layouts.app>
