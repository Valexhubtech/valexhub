<x-filament-panels::page>

    {{-- ── Chart.js (loaded async) ─────────────────────────────────── --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    @endpush

    {{-- ── Date range + group-by filter ───────────────────────────── --}}
    <div class="p-4 mb-6 bg-white border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">From</label>
                <input type="date" wire:model.live="startDate"
                       class="px-3 py-2 text-sm border rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">To</label>
                <input type="date" wire:model.live="endDate"
                       class="px-3 py-2 text-sm border rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Group by</label>
                <select wire:model.live="groupBy"
                        class="px-3 py-2 text-sm border rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="month">Month</option>
                    <option value="week">Week</option>
                    <option value="day">Day</option>
                </select>
            </div>
            <div class="flex gap-2 ml-auto">
                @foreach([
                    ['label' => 'Last 30 days',  'start' => now()->subDays(30)->format('Y-m-d'),       'end' => now()->format('Y-m-d'),              'gb' => 'day'],
                    ['label' => 'Last 3 months',  'start' => now()->subMonths(3)->startOfMonth()->format('Y-m-d'),  'end' => now()->format('Y-m-d'),  'gb' => 'month'],
                    ['label' => 'This year',      'start' => now()->startOfYear()->format('Y-m-d'),     'end' => now()->format('Y-m-d'),              'gb' => 'month'],
                    ['label' => 'Last 12 months', 'start' => now()->subMonths(11)->startOfMonth()->format('Y-m-d'), 'end' => now()->format('Y-m-d'),  'gb' => 'month'],
                ] as $preset)
                <button wire:click="$set('startDate','{{ $preset['start'] }}'); $set('endDate','{{ $preset['end'] }}'); $set('groupBy','{{ $preset['gb'] }}')"
                        class="px-3 py-2 text-xs font-medium border rounded-lg border-gray-300 text-gray-600 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    {{ $preset['label'] }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Summary KPI strip ────────────────────────────────────────── --}}
    @php $s = $this->summary; @endphp
    <div class="grid grid-cols-2 gap-4 mb-6 lg:grid-cols-4">
        @foreach([
            ['label' => 'Total Revenue',    'value' => '₦' . number_format($s['totalRevenue'], 2),    'sub' => '₦'.number_format($s['productRevenue'],2).' invoices · ₦'.number_format($s['subRevenue'],2).' subs · ₦'.number_format($s['deploymentRevenue'],2).' deployments', 'color' => 'text-green-600'],
            ['label' => 'Total Expenditure','value' => '₦' . number_format($s['totalExpenditure'], 2),'sub' => '₦'.number_format($s['serverCostPeriod'],2).' servers · ₦'.number_format($s['payoutsPaid'],2).' payouts', 'color' => 'text-orange-500'],
            ['label' => 'Net Profit',       'value' => '₦' . number_format($s['netProfit'], 2),       'sub' => $s['netProfit'] >= 0 ? 'Profitable period' : 'Loss period', 'color' => $s['netProfit'] >= 0 ? 'text-green-600' : 'text-red-600'],
            ['label' => 'Accrued Commissions','value'=> '₦' . number_format($s['accruedComms'], 2),   'sub' => '₦'.number_format($s['pendingPayouts'],2).' in pending payouts', 'color' => 'text-purple-500'],
        ] as $kpi)
        <div class="p-5 bg-white border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">{{ $kpi['label'] }}</p>
            <p class="text-2xl font-bold {{ $kpi['color'] }}">{{ $kpi['value'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $kpi['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── Revenue chart ───────────────────────────────────────────── --}}
    @php $series = $this->revenueSeries; @endphp
    <div class="p-5 mb-6 bg-white border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700"
         x-data="{
            chart: null,
            labels: {{ json_encode($series['labels']) }},
            data:   {{ json_encode($series['data']) }},
            init() {
                this.renderChart();
            },
            renderChart() {
                if (this.chart) { this.chart.destroy(); }
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#9ca3af' : '#6b7280';
                const gridColor = isDark ? '#374151' : '#f3f4f6';
                this.chart = new Chart(this.$refs.canvas, {
                    type: 'line',
                    data: {
                        labels: this.labels,
                        datasets: [{
                            label: 'Revenue (₦)',
                            data: this.data,
                            fill: true,
                            tension: 0.4,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59,130,246,0.08)',
                            pointBackgroundColor: '#3b82f6',
                            pointRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => '₦' + ctx.parsed.y.toLocaleString('en-NG', {minimumFractionDigits:2})
                                }
                            }
                        },
                        scales: {
                            x: { ticks: { color: textColor }, grid: { color: gridColor } },
                            y: {
                                ticks: { color: textColor, callback: v => '₦' + Number(v).toLocaleString() },
                                grid:  { color: gridColor }
                            }
                        }
                    }
                });
            }
         }"
         wire:key="revenue-chart-{{ $startDate }}-{{ $endDate }}-{{ $groupBy }}"
         x-init="$nextTick(() => { if (typeof Chart !== 'undefined') { renderChart(); } else { document.addEventListener('chartjs-ready', () => renderChart()); } })">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide">Revenue Over Time</h2>
            <span class="text-xs text-gray-400">{{ ucfirst($groupBy) }}ly breakdown</span>
        </div>
        @if(empty($series['labels']))
            <div class="flex items-center justify-center h-40 text-gray-400 text-sm">No paid invoices in this period.</div>
        @else
            <canvas x-ref="canvas" class="w-full" style="max-height:280px"></canvas>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-2">

        {{-- ── Revenue by product ───────────────────────────────────── --}}
        <div class="p-5 bg-white border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700">
            <h2 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide">Revenue by Product</h2>
            @php $byProduct = $this->revenueByProduct; @endphp
            @if(empty($byProduct))
                <p class="text-sm text-gray-400">No data for this period.</p>
            @else
                @php $maxAmount = collect($byProduct)->max('total') ?: 1; @endphp
                <div class="space-y-3">
                    @foreach($byProduct as $row)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300 truncate">{{ $row['product'] }}</span>
                                <span class="text-gray-500 ml-4 whitespace-nowrap">
                                    {{ $row['count'] }} × ₦{{ number_format($row['total'], 2) }}
                                </span>
                            </div>
                            <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full"
                                     style="width: {{ min(100, round($row['total'] / $maxAmount * 100)) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Server costs ──────────────────────────────────────────── --}}
        <div class="p-5 bg-white border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700">
            <h2 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide">Server Costs</h2>
            @php $serverCosts = $this->serverCosts; @endphp
            @if(empty($serverCosts))
                <p class="text-sm text-gray-400">No servers configured yet.</p>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 text-xs text-gray-500 uppercase tracking-wide">
                            <th class="text-left pb-2">Server</th>
                            <th class="text-center pb-2">Slots</th>
                            <th class="text-right pb-2">Monthly</th>
                            <th class="text-right pb-2">Period</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        @foreach($serverCosts as $sc)
                            <tr>
                                <td class="py-2 text-gray-700 dark:text-gray-300">
                                    {{ $sc['name'] }}
                                    <span class="ml-1 text-xs px-1.5 py-0.5 rounded {{ $sc['status']==='active' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">{{ $sc['status'] }}</span>
                                </td>
                                <td class="py-2 text-center text-gray-500">{{ $sc['slots'] }}</td>
                                <td class="py-2 text-right text-gray-700 dark:text-gray-300">₦{{ number_format($sc['monthly_cost'], 2) }}</td>
                                <td class="py-2 text-right font-medium text-gray-900 dark:text-white">₦{{ number_format($sc['period_cost'], 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="border-t-2 border-gray-200 dark:border-gray-600">
                            <td class="pt-2 font-semibold text-gray-900 dark:text-white" colspan="3">Total server cost (period)</td>
                            <td class="pt-2 text-right font-bold text-orange-600">₦{{ number_format(collect($serverCosts)->sum('period_cost'), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- ── Affiliate summary ─────────────────────────────────────────── --}}
    @php $aff = $this->affiliateStats; @endphp
    <div class="grid grid-cols-1 gap-4 mb-6 lg:grid-cols-3">
        @foreach([
            ['label' => 'Commissions Earned (period)', 'value' => '₦' . number_format($aff['earned'], 2),    'color' => 'text-purple-600'],
            ['label' => 'Payouts Paid (period)',        'value' => '₦' . number_format($aff['paid_out'], 2),  'color' => 'text-green-600'],
            ['label' => 'Pending Payout Requests',      'value' => $aff['pending_payouts'] . ' request' . ($aff['pending_payouts'] !== 1 ? 's' : ''), 'color' => $aff['pending_payouts'] > 0 ? 'text-yellow-600' : 'text-gray-400'],
        ] as $kpi)
        <div class="p-5 bg-white border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">{{ $kpi['label'] }}</p>
            <p class="text-xl font-bold {{ $kpi['color'] }}">{{ $kpi['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ── Deployment activity ───────────────────────────────────────── --}}
    @php $dep = $this->deploymentStats; @endphp
    <div class="p-5 bg-white border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-700">
        <h2 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide">Deployment Activity</h2>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-3xl font-bold text-blue-600">{{ $dep['new'] }}</p>
                <p class="text-xs text-gray-500 mt-1">New Deployments (period)</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-green-600">{{ $dep['active'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Currently Active</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-orange-500">{{ $dep['suspended'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Currently Suspended</p>
            </div>
        </div>
    </div>

</x-filament-panels::page>
