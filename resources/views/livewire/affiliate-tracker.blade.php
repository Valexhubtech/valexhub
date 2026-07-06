<div class="space-y-8">

    {{-- Flash messages --}}
    @if(session('client_created'))
        <div class="p-4 text-sm text-green-700 bg-green-100 rounded-md">{{ session('client_created') }}</div>
    @endif

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
        <div class="p-4 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Total Clients</p>
            <p class="mt-1 text-3xl font-bold text-zinc-900 dark:text-white">{{ $kpis['total_clients'] }}</p>
        </div>
        <div class="p-4 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Sub-Affiliates</p>
            <p class="mt-1 text-3xl font-bold text-zinc-900 dark:text-white">{{ $kpis['sub_affiliates'] }}</p>
        </div>
        <div class="p-4 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Claimable Balance</p>
            <p class="mt-1 text-3xl font-bold text-green-600">₦{{ number_format($kpis['current_balance'], 2) }}</p>
        </div>
        <div class="p-4 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Expected Next Month</p>
            <p class="mt-1 text-3xl font-bold text-zinc-900 dark:text-white">₦{{ number_format($kpis['expected_revenue'], 2) }}</p>
        </div>
        <div class="p-4 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">Avg Commission Rate</p>
            <p class="mt-1 text-3xl font-bold text-zinc-900 dark:text-white">{{ $kpis['avg_commission_pct'] }}%</p>
        </div>
    </div>

    {{-- Referral Link --}}
    <div class="p-4 bg-zinc-50 border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
        <p class="mb-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">Your referral link</p>
        <div class="flex gap-2">
            <input type="text" readonly value="{{ $referralLink }}" class="flex-1 px-3 py-2 text-sm bg-white border rounded-md border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
            <button onclick="navigator.clipboard.writeText('{{ $referralLink }}')" class="px-4 py-2 text-sm font-medium text-white rounded-md bg-zinc-900 hover:bg-zinc-800">Copy</button>
        </div>
    </div>

    {{-- Payout Request --}}
    <div class="p-4 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-medium text-zinc-900 dark:text-white">Request Payout</h3>
                <p class="text-sm text-zinc-500">Claimable balance: <span class="font-semibold text-green-600">₦{{ number_format($kpis['current_balance'], 2) }}</span></p>
            </div>
            @if($kpis['current_balance'] > 0)
                <button wire:click="$toggle('showPayoutForm')" class="text-sm text-zinc-500 hover:text-zinc-900">
                    {{ $showPayoutForm ? 'Cancel' : 'Request Payout' }}
                </button>
            @endif
        </div>

        @if(session('payout_requested'))
            <div class="p-3 text-sm text-green-700 bg-green-100 rounded-md">{{ session('payout_requested') }}</div>
        @endif
        @if(session('payout_error'))
            <div class="p-3 text-sm text-red-700 bg-red-100 rounded-md">{{ session('payout_error') }}</div>
        @endif

        @if($showPayoutForm)
            <form wire:submit="requestPayout" class="space-y-3">
                @error('payoutBankName') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                <input type="text" wire:model="payoutBankName" placeholder="Bank name (e.g. GTBank)" class="w-full px-3 py-2 text-sm border rounded-md border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600">

                @error('payoutAccountName') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                <input type="text" wire:model="payoutAccountName" placeholder="Account name" class="w-full px-3 py-2 text-sm border rounded-md border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600">

                @error('payoutAccountNumber') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                <input type="text" wire:model="payoutAccountNumber" placeholder="Account number (10 digits)" class="w-full px-3 py-2 text-sm border rounded-md border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600">

                <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white rounded-md bg-zinc-900 hover:bg-zinc-800">
                    Submit Payout Request
                </button>
            </form>
        @elseif($kpis['current_balance'] <= 0)
            <p class="text-sm text-zinc-500">You have no claimable balance yet. Commissions appear here once a referred client makes a successful payment.</p>
        @endif
    </div>

    {{-- Sign Up a New Client --}}
    <div class="p-4 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-medium text-zinc-900 dark:text-white">Sign Up a New Client</h3>
            <button wire:click="$toggle('showSignUpForm')" class="text-sm text-zinc-500 hover:text-zinc-900">
                {{ $showSignUpForm ? 'Cancel' : 'Add Client' }}
            </button>
        </div>

        @if($showSignUpForm)
            <form wire:submit="signUpClient" class="space-y-3">
                @error('newClientName') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                <input type="text" wire:model="newClientName" placeholder="Full name" class="w-full px-3 py-2 text-sm border rounded-md border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600">

                @error('newClientEmail') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                <input type="email" wire:model="newClientEmail" placeholder="Email address" class="w-full px-3 py-2 text-sm border rounded-md border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600">

                @error('newClientPassword') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                <input type="password" wire:model="newClientPassword" placeholder="Temporary password (min 8 chars)" class="w-full px-3 py-2 text-sm border rounded-md border-zinc-300 dark:bg-zinc-700 dark:border-zinc-600">

                <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white rounded-md bg-zinc-900 hover:bg-zinc-800">
                    Create & Link Client
                </button>
            </form>
        @else
            <p class="text-sm text-zinc-500">Create a new account that's automatically linked to your affiliate profile.</p>
        @endif
    </div>

    {{-- Clients Table --}}
    @if($clients->isNotEmpty())
        <div>
            <h3 class="mb-3 font-medium text-zinc-900 dark:text-white">Your Clients</h3>
            <div class="overflow-hidden border border-zinc-200 rounded-xl dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Client</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Joined</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Sub-affiliates</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-zinc-100 dark:bg-zinc-900 dark:divide-zinc-800">
                        @foreach($clients as $client)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-zinc-900 dark:text-white">{{ $client->name }}</p>
                                    <p class="text-zinc-500">{{ $client->email }}</p>
                                </td>
                                <td class="px-4 py-3 text-zinc-500">{{ $client->created_at->format('M j, Y') }}</td>
                                <td class="px-4 py-3">
                                    @if($client->sub_affiliate_count > 0)
                                        <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">{{ $client->sub_affiliate_count }} sub-affiliates</span>
                                    @else
                                        <span class="text-zinc-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Commission Ledger --}}
    @if($commissions->isNotEmpty())
        <div>
            <h3 class="mb-3 font-medium text-zinc-900 dark:text-white">Commission History</h3>
            <div class="overflow-hidden border border-zinc-200 rounded-xl dark:border-zinc-700">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Client</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Month</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Rate</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-zinc-100 dark:bg-zinc-900 dark:divide-zinc-800">
                        @foreach($commissions as $commission)
                            <tr>
                                <td class="px-4 py-3 text-zinc-900 dark:text-white">{{ $commission->referredUser->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-zinc-500">Month {{ $commission->billing_month_number }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ number_format($commission->commission_rate, 0) }}%</td>
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">₦{{ number_format($commission->commission_amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $commission->status === 'accrued' ? 'bg-green-100 text-green-700' : 'bg-zinc-100 text-zinc-600' }}">
                                        {{ ucfirst($commission->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $commissions->links() }}</div>
        </div>
    @endif
</div>
