<?php

namespace App\Filament\Widgets;

use App\Services\Coolify\CoolifyServerSelector;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Wave\AffiliateCommission;
use Wave\Deployment;
use Wave\Invoice;
use Wave\PaymentTransaction;
use Wave\PayoutRequest;

class AdminKpiWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $now   = now();
        $month = $now->copy()->startOfMonth();

        // ── Revenue ────────────────────────────────────────────────────────────
        // Product purchases tracked via paid invoices (avoid double-counting
        // with PaymentTransaction which also records the initial purchase).
        $productRevenueAllTime = (float) Invoice::where('status', 'paid')->sum('amount');
        $productRevenueMonth   = (float) Invoice::where('status', 'paid')
            ->where('paid_at', '>=', $month)->sum('amount');

        // Subscription revenue via PaymentTransactions (invoices not generated for subs yet).
        $subRevenueAllTime = (float) PaymentTransaction::whereIn('type', ['subscription_initial', 'subscription_renewal'])
            ->where('status', 'success')->sum('amount');
        $subRevenueMonth   = (float) PaymentTransaction::whereIn('type', ['subscription_initial', 'subscription_renewal'])
            ->where('status', 'success')->where('processed_at', '>=', $month)->sum('amount');

        $totalRevenueAllTime = $productRevenueAllTime + $subRevenueAllTime;
        $totalRevenueMonth   = $productRevenueMonth + $subRevenueMonth;

        // ── Expenditure ────────────────────────────────────────────────────────
        $serverMonthlyCost    = (float) \Wave\CoolifyServer::where('status', 'active')->sum('monthly_cost');
        $affiliatePayoutsTotal = (float) PayoutRequest::where('status', 'paid')->sum('amount');
        $totalExpenditure     = $serverMonthlyCost + $affiliatePayoutsTotal;

        $netProfit = $totalRevenueAllTime - $totalExpenditure;

        // ── Affiliates ─────────────────────────────────────────────────────────
        $activeAffiliates = AffiliateCommission::select('affiliate_id')
            ->where('status', 'accrued')
            ->distinct()
            ->count('affiliate_id');

        $pendingPayoutAmount = (float) PayoutRequest::where('status', 'pending')->sum('amount');
        $pendingPayoutCount  = PayoutRequest::where('status', 'pending')->count();

        // ── Servers ────────────────────────────────────────────────────────────
        $capacity = app(CoolifyServerSelector::class)->capacitySummary();

        return [
            Stat::make('Active Deployments', number_format(Deployment::where('status', 'active')->count()))
                ->description(Deployment::where('status', 'provisioning')->count() . ' provisioning · '
                    . Deployment::where('status', 'suspended')->count() . ' suspended')
                ->icon('phosphor-rocket-launch-duotone')
                ->color('success'),

            Stat::make('Monthly Revenue', '₦' . number_format($totalRevenueMonth, 2))
                ->description('This month (products + subs)')
                ->icon('phosphor-trend-up-duotone')
                ->color('success'),

            Stat::make('All-time Revenue', '₦' . number_format($totalRevenueAllTime, 2))
                ->description('₦' . number_format($subRevenueAllTime, 2) . ' subs · ₦' . number_format($productRevenueAllTime, 2) . ' products')
                ->icon('phosphor-money-wavy-duotone')
                ->color('success'),

            Stat::make('Expenditure (running)', '₦' . number_format($totalExpenditure, 2))
                ->description('₦' . number_format($serverMonthlyCost, 2) . '/mo servers · ₦' . number_format($affiliatePayoutsTotal, 2) . ' affiliate payouts')
                ->icon('phosphor-arrow-circle-down-duotone')
                ->color('warning'),

            Stat::make('Net Profit', '₦' . number_format($netProfit, 2))
                ->description('All-time revenue minus expenditure')
                ->icon('phosphor-chart-line-up-duotone')
                ->color($netProfit >= 0 ? 'success' : 'danger'),

            Stat::make('Active Affiliates', number_format($activeAffiliates))
                ->description($pendingPayoutCount . ' pending payout' . ($pendingPayoutCount !== 1 ? 's' : '')
                    . ' · ₦' . number_format($pendingPayoutAmount, 2))
                ->icon('phosphor-users-three-duotone')
                ->color($pendingPayoutCount > 0 ? 'warning' : 'gray'),

            Stat::make('Server Capacity', $capacity['used_slots'] . ' / ' . $capacity['total_slots'] . ' slots')
                ->description($capacity['active_servers'] . ' active server' . ($capacity['active_servers'] !== 1 ? 's' : '')
                    . ' · ' . $capacity['available_slots'] . ' slots free')
                ->icon('phosphor-hard-drives-duotone')
                ->color($capacity['all_full'] ? 'danger' : ($capacity['available_slots'] < 5 ? 'warning' : 'gray')),
        ];
    }
}
