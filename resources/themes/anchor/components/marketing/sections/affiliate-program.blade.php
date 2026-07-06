<section class="relative py-20 lg:py-32 bg-white overflow-hidden">

    {{-- Decorative blobs --}}
    <div class="absolute top-0 right-0 w-[700px] h-[500px] bg-blue-50/70 rounded-full blur-3xl pointer-events-none -translate-y-1/3 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[300px] bg-zinc-100/80 rounded-full blur-3xl pointer-events-none translate-y-1/3 -translate-x-1/3"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-20 items-center">

            {{-- Left: Pitch --}}
            <div>
                <p class="text-xs font-semibold tracking-widest uppercase text-blue-600 mb-4">Affiliate Program</p>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-zinc-900 leading-[0.95] mb-6">
                    Earn While<br>You Refer
                </h2>
                <p class="text-zinc-500 text-base leading-relaxed mb-10 max-w-lg">
                    Refer a business that deploys any of our software and earn a commission every month they stay — for as long as they stay.
                </p>

                @php
                    $benefits = [
                        ['symbol' => '₦', 'title' => '15–20% for the first 3 months', 'desc' => 'Earn 15–20% of every payment your referred client makes during their first 3 months.'],
                        ['symbol' => '%', 'title' => '8% from month 4 onwards', 'desc' => 'After the initial period, you continue earning 8% for every month they remain a client.'],
                        ['symbol' => '✓', 'title' => 'Monthly bank transfer payouts', 'desc' => 'Request payouts to any Nigerian bank account directly from your affiliate dashboard.'],
                    ];
                @endphp

                <ul class="space-y-5 mb-10">
                    @foreach($benefits as $b)
                        <li class="flex gap-4">
                            <div class="flex-shrink-0 w-10 h-10 bg-zinc-900 rounded-xl flex items-center justify-center">
                                <span class="text-white font-black text-sm leading-none">{{ $b['symbol'] }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-zinc-900 text-sm leading-snug">{{ $b['title'] }}</p>
                                <p class="text-zinc-500 text-sm mt-1 leading-relaxed">{{ $b['desc'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 px-7 py-3.5 bg-zinc-900 text-white font-semibold text-sm rounded-xl hover:bg-zinc-800 transition-all duration-200 shadow-sm">
                        Join as an Affiliate
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="{{ route('book-demo') }}"
                       class="inline-flex items-center gap-2 px-7 py-3.5 border border-zinc-200 text-zinc-700 font-semibold text-sm rounded-xl hover:bg-zinc-50 hover:border-zinc-300 transition-all duration-200">
                        Ask us a question
                    </a>
                </div>
            </div>

            {{-- Right: Dashboard Mockup --}}
            <div class="relative">

                {{-- Main card --}}
                <div class="bg-white border border-zinc-200 rounded-2xl shadow-2xl shadow-zinc-900/8 overflow-hidden">

                    {{-- Header bar --}}
                    <div class="px-5 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400">Affiliate Dashboard</p>
                            <p class="text-sm font-bold text-zinc-900 mt-0.5">Amara Okafor</p>
                        </div>
                        <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Active</span>
                    </div>

                    {{-- KPI grid --}}
                    <div class="grid grid-cols-3 divide-x divide-zinc-100 border-b border-zinc-100">
                        <div class="px-4 py-4">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-zinc-400">Total Clients</p>
                            <p class="mt-1.5 text-2xl font-black text-zinc-900">9</p>
                        </div>
                        <div class="px-4 py-4">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-zinc-400">Avg Rate</p>
                            <p class="mt-1.5 text-2xl font-black text-zinc-900">12%</p>
                        </div>
                        <div class="px-4 py-4">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-zinc-400">Claimable</p>
                            <p class="mt-1.5 text-2xl font-black text-green-600">₦28,400</p>
                        </div>
                    </div>

                    {{-- Referral link --}}
                    <div class="px-5 py-4 border-b border-zinc-100">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-zinc-400 mb-2">Your referral link</p>
                        <div class="flex gap-2 items-center">
                            <div class="flex-1 px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-xs text-zinc-500 font-mono truncate">
                                valexhub.com/ref/amara-ok
                            </div>
                            <div class="px-3 py-2 bg-zinc-900 text-white text-xs font-semibold rounded-lg cursor-default select-none">
                                Copy
                            </div>
                        </div>
                    </div>

                    {{-- Commission rows --}}
                    <div class="px-5 py-4">
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-zinc-400 mb-3">Recent Commissions</p>
                        @php
                            $mockRows = [
                                ['name' => 'Westfield Hospital', 'rate' => '20% (mo. 2)', 'amt' => '₦8,200',  'status' => 'accrued'],
                                ['name' => 'Sunrise Academy',    'rate' => '15% (mo. 1)', 'amt' => '₦4,500',  'status' => 'accrued'],
                                ['name' => 'QuickPharma Ltd',    'rate' => '8% (mo. 5)',  'amt' => '₦2,960',  'status' => 'paid'],
                            ];
                        @endphp
                        <div class="space-y-3">
                            @foreach($mockRows as $row)
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-zinc-900 truncate">{{ $row['name'] }}</p>
                                        <p class="text-xs text-zinc-400">{{ $row['rate'] }} commission rate</p>
                                    </div>
                                    <div class="flex items-center gap-2.5 ml-4 flex-shrink-0">
                                        <p class="text-sm font-bold text-zinc-900">{{ $row['amt'] }}</p>
                                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $row['status'] === 'accrued' ? 'bg-green-100 text-green-700' : 'bg-zinc-100 text-zinc-500' }}">
                                            {{ ucfirst($row['status']) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- Floating "expected next month" badge --}}
                <div class="absolute -top-5 -right-5 hidden lg:block bg-zinc-900 text-white px-4 py-3 rounded-2xl shadow-xl shadow-zinc-900/20 border border-zinc-800">
                    <p class="text-[10px] text-zinc-400 font-semibold uppercase tracking-wide">Expected next month</p>
                    <p class="text-lg font-black text-white mt-0.5">₦15,660</p>
                </div>

                {{-- Dot grid --}}
                <div class="absolute -bottom-8 -left-8 w-36 h-36 opacity-[0.15] pointer-events-none hidden lg:block"
                     style="background-image: radial-gradient(circle, #3b82f6 1.5px, transparent 1.5px); background-size: 14px 14px;"></div>

                {{-- Disclaimer --}}
                <p class="mt-4 text-xs text-zinc-400 text-center lg:text-left">
                    Sample dashboard — commissions and clients shown for illustration.
                </p>
            </div>

        </div>
    </div>
</section>
