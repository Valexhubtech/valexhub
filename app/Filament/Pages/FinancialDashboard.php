<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Wave\AffiliateCommission;
use Wave\CoolifyServer;
use Wave\Deployment;
use Wave\Invoice;
use Wave\PaymentTransaction;
use Wave\PayoutRequest;

class FinancialDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'phosphor-chart-line-up-duotone';

    protected static ?string $navigationLabel = 'Financial Dashboard';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance & Billing';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.financial-dashboard';

    // ── Filters ───────────────────────────────────────────────────────────────
    public string $startDate = '';
    public string $endDate   = '';
    public string $groupBy   = 'month';  // month | week | day

    public function mount(): void
    {
        $this->startDate = now()->subMonths(11)->startOfMonth()->format('Y-m-d');
        $this->endDate   = now()->endOfMonth()->format('Y-m-d');
    }

    // ── Computed data ─────────────────────────────────────────────────────────

    /** Monthly/weekly revenue from paid invoices within the selected range. */
    public function getRevenueSeriesProperty(): array
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        $format = $this->groupBy === 'day' ? '%Y-%m-%d' : ($this->groupBy === 'week' ? '%Y-%u' : '%Y-%m');

        $rows = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(paid_at, '{$format}') as period, SUM(amount) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return [
            'labels' => $rows->pluck('period')->toArray(),
            'data'   => $rows->pluck('total')->map(fn ($v) => round((float) $v, 2))->toArray(),
        ];
    }

    /** Revenue breakdown by product. */
    public function getRevenueByProductProperty(): array
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        return Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$start, $end])
            ->with('userProduct.product')
            ->get()
            ->groupBy(fn ($inv) => $inv->userProduct?->product?->name ?? 'Manual / Other')
            ->map(fn ($group, $name) => [
                'product' => $name,
                'count'   => $group->count(),
                'total'   => round($group->sum('amount'), 2),
            ])
            ->sortByDesc('total')
            ->values()
            ->toArray();
    }

    /** Summary KPIs for the selected range. */
    public function getSummaryProperty(): array
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        $productRevenue = (float) Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$start, $end])
            ->sum('amount');

        $subRevenue = (float) PaymentTransaction::whereIn('type', ['subscription_initial', 'subscription_renewal'])
            ->where('status', 'success')
            ->whereBetween('processed_at', [$start, $end])
            ->sum('amount');

        $totalRevenue = $productRevenue + $subRevenue;

        // Expenditure within range
        $months = max(1, (int) Carbon::parse($this->startDate)->diffInMonths(Carbon::parse($this->endDate)) + 1);
        $serverCostPerMonth = (float) CoolifyServer::where('status', 'active')->sum('monthly_cost');
        $serverCostPeriod   = $serverCostPerMonth * $months;

        $payoutsPaid = (float) PayoutRequest::where('status', 'paid')
            ->whereBetween('processed_at', [$start, $end])
            ->sum('amount');

        $totalExpenditure = $serverCostPeriod + $payoutsPaid;
        $netProfit        = $totalRevenue - $totalExpenditure;

        $pendingPayouts = (float) PayoutRequest::where('status', 'pending')->sum('amount');
        $accruedComms   = (float) AffiliateCommission::where('status', 'accrued')->sum('commission_amount');

        return compact(
            'productRevenue', 'subRevenue', 'totalRevenue',
            'serverCostPerMonth', 'serverCostPeriod', 'payoutsPaid',
            'totalExpenditure', 'netProfit',
            'pendingPayouts', 'accruedComms',
        );
    }

    /** Deployment activity within range. */
    public function getDeploymentStatsProperty(): array
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        return [
            'new'       => Deployment::whereBetween('created_at', [$start, $end])->count(),
            'active'    => Deployment::where('status', 'active')->count(),
            'suspended' => Deployment::where('status', 'suspended')->count(),
        ];
    }

    /** Affiliate commission breakdown. */
    public function getAffiliateStatsProperty(): array
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        return [
            'earned'          => (float) AffiliateCommission::whereBetween('accrued_at', [$start, $end])->sum('commission_amount'),
            'paid_out'        => (float) PayoutRequest::where('status', 'paid')->whereBetween('processed_at', [$start, $end])->sum('amount'),
            'pending_payouts' => PayoutRequest::where('status', 'pending')->count(),
        ];
    }

    /** Server costs by server for the period. */
    public function getServerCostsProperty(): array
    {
        $months = max(1, (int) Carbon::parse($this->startDate)->diffInMonths(Carbon::parse($this->endDate)) + 1);

        return CoolifyServer::orderBy('sort_order')->get()->map(fn (CoolifyServer $s) => [
            'name'         => $s->name,
            'status'       => $s->status,
            'slots'        => $s->used_slots . ' / ' . $s->max_deployments,
            'monthly_cost' => (float) $s->monthly_cost,
            'period_cost'  => round((float) $s->monthly_cost * $months, 2),
        ])->toArray();
    }
}
