<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Wave\AffiliateCommission;
use Wave\Deployment;
use Wave\DomainPurchase;
use Wave\Invoice;
use Wave\SupportTicket;

class DashboardOverview extends Component
{
    public function render()
    {
        /** @var \App\Models\User $user */
        $user   = Auth::user();
        $userId = $user->id;

        $activeDeployments  = Deployment::where('user_id', $userId)->where('status', 'active')->count();
        $pendingDeployments = Deployment::where('user_id', $userId)->whereIn('status', ['pending', 'provisioning'])->count();
        $openTickets        = SupportTicket::where('user_id', $userId)->whereIn('status', ['open', 'in_progress'])->count();
        $unpaidInvoices     = Invoice::where('user_id', $userId)->where('status', 'sent')->count();
        $unpaidTotal        = Invoice::where('user_id', $userId)->where('status', 'sent')->sum('amount');

        $domainCount      = DomainPurchase::where('user_id', $userId)->where('payment_status', 'paid')->count();
        $referralCount    = $user->referrals()->count();
        $affiliateEarned  = AffiliateCommission::where('affiliate_id', $userId)->sum('commission_amount');
        $hasAffiliate     = $referralCount > 0 || $affiliateEarned > 0;

        $adminStats = null;
        if ($user->isAdmin()) {
            $adminStats = [
                'total_deployments' => Deployment::count(),
                'all_open_tickets'  => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
                'system_unpaid'     => Invoice::where('status', 'sent')->sum('amount'),
            ];
        }

        return view('livewire.dashboard-overview', compact(
            'activeDeployments',
            'pendingDeployments',
            'openTickets',
            'unpaidInvoices',
            'unpaidTotal',
            'domainCount',
            'referralCount',
            'affiliateEarned',
            'hasAffiliate',
            'adminStats',
        ));
    }
}
