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
    <x-app.container class="lg:space-y-8">

        <x-app.heading
            title="Domains"
            description="Search for a custom domain and attach it to one of your deployed services."
            :border="false"
        />

        {{-- Domain search --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <livewire:domain-search />
            </div>

            <div class="space-y-4">
                <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-5">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">How it works</h3>
                    <ol class="space-y-3 text-sm text-zinc-500 dark:text-zinc-400">
                        <li class="flex gap-2.5">
                            <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold">1</span>
                            Search for your preferred domain name
                        </li>
                        <li class="flex gap-2.5">
                            <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold">2</span>
                            Select an available domain from the results
                        </li>
                        <li class="flex gap-2.5">
                            <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold">3</span>
                            Choose which of your deployments to attach it to
                        </li>
                        <li class="flex gap-2.5">
                            <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold">4</span>
                            Pay via Paystack — domain goes live within 30 minutes
                        </li>
                    </ol>
                </div>

                <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-5">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-zinc-500 mb-2">Before You Register</h3>
                    <ul class="space-y-2 text-xs text-zinc-500 dark:text-zinc-400">
                        <li>• Each deployment can have one custom domain</li>
                        <li>• Domains are registered for 1 year and renew annually</li>
                        <li>• DNS is configured automatically — no technical setup needed</li>
                        <li>• Contact support if you already own a domain and want to transfer it</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- My Domains table --}}
        @if($domains->isNotEmpty())
        <div>
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">My Domains</h2>
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
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payBadge['class'] }}">{{ $payBadge['label'] }}</span>
                                </td>
                                <td class="px-5 py-4 hidden md:table-cell">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $regBadge['class'] }}">{{ $regBadge['label'] }}</span>
                                </td>
                                <td class="px-5 py-4 hidden md:table-cell">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dnsBadge['class'] }}">{{ $dnsBadge['label'] }}</span>
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
        </div>
        @endif

    </x-app.container>
</x-layouts.app>
