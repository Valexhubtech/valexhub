<div>

    {{-- ── Search form ─────────────────────────────────────────────────────── --}}
    @if(!$showPayment)
    <div class="mb-6">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
            Enter a name for your website — keep it short and easy to remember.
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">
            e.g. if your business is "Blue Ocean Hotel", try <span class="font-medium text-gray-600">blueoceanhotel</span>.
            You can also type a full domain like <span class="font-medium text-gray-600">blueoceanhotel.com</span>.
        </p>

        {{-- Setup fee banner --}}
        <div class="flex items-center gap-2 mb-4 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-lg text-xs text-blue-700 dark:text-blue-300">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            A ₦{{ $setupFeeNaira }} one-time setup & DNS configuration fee is added to all domain orders.
        </div>

        <form wire:submit="search" class="flex gap-3">
            <div class="flex-1 relative">
                <input
                    type="text"
                    wire:model="searchQuery"
                    placeholder="e.g. blueoceanhotel or blueoceanhotel.com"
                    class="w-full px-4 py-3 text-base border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    autocomplete="off"
                >
            </div>
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors disabled:opacity-60 whitespace-nowrap">
                <span wire:loading.remove wire:target="search">Check Availability</span>
                <span wire:loading wire:target="search">Checking…</span>
            </button>
        </form>

        @error('searchQuery')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- ── Results ──────────────────────────────────────────────────────────── --}}
    @if($searched && count($results))
    @php
        $groups = [
            'popular'  => ['label' => 'Popular extensions', 'items' => []],
            'nigerian' => ['label' => 'Nigerian extensions', 'items' => []],
            'other'    => ['label' => 'More extensions',    'items' => []],
        ];
        foreach ($results as $option) {
            $g = $option['group'] ?? 'other';
            $groups[$g]['items'][] = $option;
        }
    @endphp

    <div class="space-y-6">
        @foreach($groups as $groupKey => $group)
        @php
            $available = array_values(array_filter($group['items'], fn ($o) => $o['available'] !== false));
            $taken     = array_values(array_filter($group['items'], fn ($o) => $o['available'] === false));
        @endphp
        @if(count($group['items']))
        <div>
            <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">{{ $group['label'] }}</h3>

            {{-- Available / unknown rows --}}
            @if(count($available))
            <div class="space-y-2">
                @foreach($available as $option)
                @include('livewire.partials.domain-option-row', ['option' => $option, 'setupFeeNaira' => $setupFeeNaira])
                @endforeach
            </div>
            @endif

            {{-- Taken rows — collapsed by default, expandable via Alpine --}}
            @if(count($taken))
            <div x-data="{ open: false }" class="{{ count($available) ? 'mt-3' : '' }}">
                <button @click="open = !open"
                        class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg x-show="!open" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    <svg x-show="open"  class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                    <span x-text="open ? 'Hide unavailable' : 'Show {{ count($taken) }} unavailable'"></span>
                </button>
                <div x-show="open" x-transition class="mt-2 space-y-2">
                    @foreach($taken as $option)
                    @include('livewire.partials.domain-option-row', ['option' => $option, 'setupFeeNaira' => $setupFeeNaira])
                    @endforeach
                </div>
            </div>
            @endif

        </div>
        @endif
        @endforeach
    </div>

    @elseif($searched)
    <p class="text-sm text-gray-500 mt-4">No results returned. Please try a different name.</p>
    @endif
    @endif

    {{-- ── Payment review ───────────────────────────────────────────────────── --}}
    @if($showPayment && $selectedDomain)
    @php
        $domainNaira = number_format($selectedPriceKobo / 100, 0);
        $totalNaira  = number_format(($selectedPriceKobo + (int)config('domains.setup_fee_kobo')) / 100, 0);
    @endphp

    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-5 mb-5">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Domain: <span class="font-medium text-gray-900 dark:text-white">{{ $selectedDomain }}</span> (1 year)</span>
                <span class="font-medium text-gray-900 dark:text-white">₦{{ $domainNaira }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Setup & DNS configuration fee</span>
                <span class="font-medium text-gray-900 dark:text-white">₦{{ $setupFeeNaira }}</span>
            </div>
            <div class="border-t border-blue-200 dark:border-blue-700 pt-2 mt-2 flex justify-between">
                <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                <span class="font-bold text-lg text-blue-700 dark:text-blue-300">₦{{ $totalNaira }}</span>
            </div>
        </div>
    </div>

    <div class="text-sm text-gray-500 dark:text-gray-400 mb-5 space-y-1">
        <p>✓ Domain registered in your name</p>
        <p>✓ DNS configured and pointed to your deployment</p>
        <p>✓ Invoice issued immediately after payment</p>
        <p>✓ Domain live within ~30 minutes</p>
    </div>

    @if($paymentError)
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            {{ $paymentError }}
        </div>
    @endif

    <div class="flex gap-3">
        <button wire:click="proceedToPayment"
                wire:loading.attr="disabled"
                class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-base transition-colors disabled:opacity-60">
            <span wire:loading.remove wire:target="proceedToPayment">Pay ₦{{ $totalNaira }} via Paystack</span>
            <span wire:loading wire:target="proceedToPayment">Redirecting to payment…</span>
        </button>
        <button wire:click="cancelSelection"
                class="px-5 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300 font-medium rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
            Back
        </button>
    </div>
    @endif

</div>
