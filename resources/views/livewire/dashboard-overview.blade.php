<div class="space-y-6">

    {{-- ── Row 1: Core stats ────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        {{-- Active Apps --}}
        <a href="{{ route('dashboard.deployments') }}" wire:navigate
           class="flex items-center gap-4 p-5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30">
                <x-phosphor-rocket-launch class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold text-zinc-900 dark:text-white leading-none">{{ $activeDeployments }}</p>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Active app{{ $activeDeployments !== 1 ? 's' : '' }}</p>
                @if($pendingDeployments > 0)
                    <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-400 font-medium">{{ $pendingDeployments }} setting up…</p>
                @endif
            </div>
        </a>

        {{-- Open Tickets --}}
        <a href="{{ route('dashboard.support') }}" wire:navigate
           class="flex items-center gap-4 p-5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-900/30">
                <x-phosphor-chat-circle-dots class="w-5 h-5 text-purple-600 dark:text-purple-400" />
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold text-zinc-900 dark:text-white leading-none">{{ $openTickets }}</p>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Open ticket{{ $openTickets !== 1 ? 's' : '' }}</p>
            </div>
        </a>

        {{-- Unpaid Invoices --}}
        <a href="{{ route('dashboard.invoices') }}" wire:navigate
           class="flex items-center gap-4 p-5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl {{ $unpaidInvoices > 0 ? 'hover:border-red-300 dark:hover:border-red-700' : 'hover:border-blue-300 dark:hover:border-blue-700' }} transition-colors">
            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg {{ $unpaidInvoices > 0 ? 'bg-red-50 dark:bg-red-900/30' : 'bg-green-50 dark:bg-green-900/30' }}">
                <x-phosphor-receipt class="w-5 h-5 {{ $unpaidInvoices > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}" />
            </div>
            <div class="min-w-0">
                @if($unpaidInvoices > 0)
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 leading-none">{{ $unpaidInvoices }}</p>
                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Unpaid · ₦{{ number_format($unpaidTotal, 0) }}</p>
                @else
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 leading-none">All clear</p>
                    <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">No outstanding invoices</p>
                @endif
            </div>
        </a>

    </div>

    {{-- ── Row 2: Domains · Affiliate · Wallet ──────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        {{-- Domains --}}
        <a href="{{ route('dashboard.domains') }}" wire:navigate
           class="flex items-center gap-4 p-5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:border-teal-300 dark:hover:border-teal-700 transition-colors">
            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-teal-50 dark:bg-teal-900/30">
                <x-phosphor-globe class="w-5 h-5 text-teal-600 dark:text-teal-400" />
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold text-zinc-900 dark:text-white leading-none">{{ $domainCount }}</p>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Domain{{ $domainCount !== 1 ? 's' : '' }} registered</p>
            </div>
        </a>

        {{-- Affiliate --}}
        <a href="{{ route('dashboard.affiliate-tracker') }}" wire:navigate
           class="flex items-center gap-4 p-5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-orange-50 dark:bg-orange-900/30">
                <x-phosphor-users-three class="w-5 h-5 text-orange-600 dark:text-orange-400" />
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-baseline gap-2">
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white leading-none">{{ $referralCount }}</p>
                    @if($affiliateEarned > 0)
                        <p class="text-sm font-semibold text-green-600 dark:text-green-400">₦{{ number_format($affiliateEarned, 0) }}</p>
                    @endif
                </div>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                    Referral{{ $referralCount !== 1 ? 's' : '' }}
                    @if($affiliateEarned > 0) · earned @endif
                </p>
                @if(!$hasAffiliate)
                    <p class="mt-1 text-xs text-blue-600 dark:text-blue-400">Start earning →</p>
                @endif
            </div>
        </a>

        {{-- Wallet (Coming soon) --}}
        <div class="relative flex items-center gap-4 p-5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-700">
                <x-phosphor-wallet class="w-5 h-5 text-zinc-400 dark:text-zinc-500" />
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-zinc-400 dark:text-zinc-500">Wallet</p>
                <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-600">Coming soon</p>
            </div>
            <span class="absolute top-3 right-3 text-[10px] font-semibold uppercase tracking-wide px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-700 text-zinc-400 dark:text-zinc-500 rounded">Soon</span>
        </div>

    </div>

    {{-- ── Quick actions ────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('dashboard.products') }}" wire:navigate
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
            <x-phosphor-plus class="w-4 h-4" />
            Deploy Software
        </a>
        <a href="{{ route('dashboard.affiliate-tracker') }}" wire:navigate
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded-lg transition-colors">
            <x-phosphor-users-three class="w-4 h-4" />
            Affiliate Dashboard
        </a>
        <a href="{{ route('dashboard.support') }}" wire:navigate
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded-lg transition-colors">
            <x-phosphor-chat-circle-dots class="w-4 h-4" />
            Get Support
        </a>
    </div>

    {{-- ── Empty state (no apps yet) ───────────────────────────────────────── --}}
    @if($activeDeployments === 0 && $pendingDeployments === 0)
    <div class="flex flex-col items-center justify-center py-12 text-center bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl">
        <div class="w-14 h-14 flex items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-700 mb-4">
            <x-phosphor-rocket-launch class="w-7 h-7 text-zinc-400" />
        </div>
        <h3 class="text-base font-semibold text-zinc-900 dark:text-white mb-1">No apps deployed yet</h3>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-xs">
            Browse the product catalog and deploy your first software in minutes.
        </p>
        <a href="{{ route('dashboard.products') }}" wire:navigate
           class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
            <x-phosphor-package class="w-4 h-4" />
            Browse Products
        </a>
    </div>
    @endif

    {{-- ── Admin system overview ────────────────────────────────────────────── --}}
    @if($adminStats !== null)
    <div class="pt-2">
        <h2 class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider mb-3">System Overview</h2>
        <div class="grid grid-cols-2 gap-4">

            <div class="p-4 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl">
                <p class="text-xl font-bold text-zinc-900 dark:text-white leading-none">{{ $adminStats['total_deployments'] }}</p>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Total deployments</p>
            </div>

            <div class="p-4 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl">
                <p class="text-xl font-bold {{ $adminStats['all_open_tickets'] > 0 ? 'text-purple-600 dark:text-purple-400' : 'text-zinc-900 dark:text-white' }} leading-none">{{ $adminStats['all_open_tickets'] }}</p>
                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Open tickets (all users)</p>
            </div>

        </div>

        @if($adminStats['system_unpaid'] > 0)
        <div class="mt-4 flex items-center gap-3 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm">
            <x-phosphor-warning class="w-4 h-4 text-red-600 dark:text-red-400 flex-shrink-0" />
            <span class="text-red-700 dark:text-red-300">
                <span class="font-semibold">₦{{ number_format($adminStats['system_unpaid'], 0) }}</span> outstanding across all unpaid invoices.
            </span>
            <a href="/admin/invoices" class="ml-auto text-xs font-medium text-red-600 dark:text-red-400 hover:underline">View in admin</a>
        </div>
        @endif
    </div>
    @endif

    {{-- ── Referral banner ─────────────────────────────────────────────────── --}}
    <div class="relative flex flex-col sm:flex-row items-center overflow-hidden rounded-xl border border-blue-100 dark:border-blue-900/50 bg-gradient-to-br from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-900">
        <div class="flex-1 px-7 py-8 sm:py-10 z-10">
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-200 mb-2">Affiliate Programme</p>
            <h3 class="text-2xl sm:text-3xl font-bold text-white leading-tight">
                Refer a business.<br>Earn up to <span class="text-blue-200">15%</span> commission.
            </h3>
            <p class="mt-3 text-sm text-blue-100 max-w-sm leading-relaxed">
                Every time someone you refer deploys software through ValexHub, you earn. A single referral can put
                <span class="font-semibold text-white">₦15,000+</span> in your pocket — recurring, every renewal.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('dashboard.affiliate-tracker') }}" wire:navigate
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold bg-white text-blue-700 hover:bg-blue-50 rounded-lg transition-colors shadow-sm">
                    <x-phosphor-share-network class="w-4 h-4" />
                    Get your referral link
                </a>
                <a href="{{ route('dashboard.affiliate-tracker') }}" wire:navigate
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-blue-100 hover:text-white transition-colors">
                    See how it works
                    <x-phosphor-arrow-right class="w-4 h-4" />
                </a>
            </div>
        </div>
        <div class="shrink-0 px-6 pb-6 sm:pb-0 sm:pr-8 z-10">
            <img src="https://hpanel.hostinger.com/assets/images/referrals/cardBanners/web-hosting.png"
                 alt=""
                 class="w-48 sm:w-56 object-contain drop-shadow-xl"
                 loading="lazy">
        </div>
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute -left-6 -bottom-8 w-32 h-32 rounded-full bg-white/5 pointer-events-none"></div>
    </div>

</div>
