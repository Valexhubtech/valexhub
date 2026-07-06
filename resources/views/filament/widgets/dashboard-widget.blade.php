<x-filament-widgets::widget class="fi-filament-info-widget">
    {{-- Top bar: branding + quick link --}}
    <div class="flex items-center justify-between px-1 mb-5">
        <div>
            <a href="/" target="_blank"><x-logo class="w-auto h-7" /></a>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">ValexHub Admin &middot; {{ now()->format('l, d M Y') }}</p>
        </div>
        <x-filament::button color="gray" size="sm" tag="a" href="/" target="_blank"
            icon="heroicon-m-arrow-top-right-on-square">
            Visit Site
        </x-filament::button>
    </div>

    {{-- Operational snapshot --}}
    <div class="grid grid-cols-2 gap-4 mb-5 md:grid-cols-4">
        @php
            $ops = [
                ['label' => 'Total Users',        'value' => \Wave\User::count(),                                       'icon' => 'phosphor-users-duotone',             'color' => 'text-blue-500'],
                ['label' => 'Active Deployments', 'value' => \Wave\Deployment::where('status','active')->count(),        'icon' => 'phosphor-rocket-launch-duotone',      'color' => 'text-green-500'],
                ['label' => 'Suspended',          'value' => \Wave\Deployment::where('status','suspended')->count(),     'icon' => 'phosphor-pause-circle-duotone',       'color' => 'text-orange-500'],
                ['label' => 'Open Tickets',       'value' => \Wave\SupportTicket::whereIn('status',['open','in_progress'])->count(), 'icon' => 'phosphor-chat-circle-dots-duotone', 'color' => 'text-purple-500'],
                ['label' => 'Products',           'value' => \Wave\Product::count(),                                    'icon' => 'phosphor-package-duotone',           'color' => 'text-blue-400'],
                ['label' => 'Demo Requests',      'value' => \Wave\DemoRequest::count(),                                'icon' => 'phosphor-chats-circle-duotone',       'color' => 'text-sky-500'],
                ['label' => 'Pending Demos',      'value' => \Wave\DemoRequest::where('status','pending')->count(),      'icon' => 'phosphor-clock-duotone',             'color' => 'text-yellow-500'],
                ['label' => 'Unpaid Invoices',    'value' => \Wave\Invoice::whereIn('status',['draft','sent'])->count(), 'icon' => 'phosphor-receipt-duotone',           'color' => 'text-red-400'],
            ];
        @endphp
        @foreach($ops as $op)
            <x-filament::section>
                <div class="flex items-center gap-3">
                    <x-dynamic-component :component="$op['icon']" class="w-8 h-8 {{ $op['color'] }}" />
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($op['value']) }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $op['label'] }}</div>
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    </div>

    {{-- Quick links --}}
    <x-filament::section>
        <div class="flex flex-wrap gap-3 items-center">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide mr-2">Quick links</span>
            @foreach([
                ['/admin/deployments',         'phosphor-rocket-launch-duotone', 'Deployments'],
                ['/admin/invoices',            'phosphor-receipt-duotone',        'Invoices'],
                ['/admin/support-tickets',     'phosphor-chat-circle-dots-duotone','Support'],
                ['/admin/payout-requests',     'phosphor-money-wavy-duotone',     'Payouts'],
                ['/admin/coolify-servers',     'phosphor-hard-drives-duotone',    'Servers'],
                ['/admin/financial-dashboard', 'phosphor-chart-line-up-duotone',  'Financials'],
            ] as [$href, $icon, $label])
                <a href="{{ $href }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-md border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <x-dynamic-component :component="$icon" class="w-4 h-4" />
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
