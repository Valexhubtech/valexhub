<div class="flex items-center justify-between px-4 py-3 rounded-xl border
    {{ $option['available'] === true
        ? 'border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800'
        : ($option['available'] === false
            ? 'border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 opacity-50'
            : 'border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800') }}">

    <div class="flex items-center gap-2 min-w-0">
        @if($option['recommended'])
            <span class="shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Best choice</span>
        @endif
        <div class="min-w-0">
            <p class="font-semibold {{ $option['available'] === false ? 'text-zinc-400 dark:text-zinc-600' : 'text-zinc-900 dark:text-white' }} truncate">{{ $option['domain'] }}</p>
            @if($option['description'])
                <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $option['description'] }}</p>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-3 ml-3 shrink-0">
        @if($option['available'] === false)
            <div class="text-right">
                <p class="font-bold text-gray-300 dark:text-gray-600 line-through text-sm">₦{{ $option['price_naira'] }}</p>
                <p class="text-xs text-red-400 font-medium">Taken</p>
            </div>
            <span class="px-3 py-1.5 bg-gray-100 dark:bg-gray-800 text-gray-400 text-xs rounded-lg">Unavailable</span>
        @else
            <div class="text-right">
                <p class="font-bold text-zinc-900 dark:text-white text-sm">₦{{ $option['price_naira'] }}<span class="text-xs font-normal text-gray-400">/yr</span></p>
                <p class="text-xs text-gray-400">+ ₦{{ $setupFeeNaira }} setup</p>
            </div>
            <button wire:click="select('{{ $option['domain'] }}', '{{ $option['tld'] }}', {{ $option['price_kobo'] }}, '{{ addslashes($option['item_id'] ?? '') }}')"
                    class="{{ $option['available'] === true
                        ? 'bg-blue-600 hover:bg-blue-700 text-white'
                        : 'border border-blue-400 text-blue-600 hover:bg-blue-50' }} px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors">
                Select
            </button>
        @endif
    </div>
</div>
