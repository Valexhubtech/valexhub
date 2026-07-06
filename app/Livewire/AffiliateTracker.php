<?php

namespace App\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Wave\AffiliateCommission;

class AffiliateTracker extends Component
{
    use WithPagination;

    public string $newClientName = '';
    public string $newClientEmail = '';
    public string $newClientPassword = '';
    public bool $showSignUpForm = false;

    public string $payoutAccountName = '';
    public string $payoutAccountNumber = '';
    public string $payoutBankName = '';
    public bool $showPayoutForm = false;

    public function getKpisProperty(): array
    {
        $user = auth()->user();

        $clientIds = $user->referrals()->pluck('id');

        $currentBalance = AffiliateCommission::where('affiliate_id', $user->id)
            ->where('status', 'accrued')
            ->sum('commission_amount');

        $avgRate = AffiliateCommission::where('affiliate_id', $user->id)->avg('commission_rate') ?? 0;

        return [
            'total_clients' => $clientIds->count(),
            'sub_affiliates' => $user->referrals()->whereHas('referrals')->count(),
            'current_balance' => (float) $currentBalance,
            'expected_revenue' => $this->calculateExpectedRevenue($user),
            'avg_commission_pct' => round((float) $avgRate, 1),
        ];
    }

    protected function calculateExpectedRevenue($user): float
    {
        return $user->referrals()
            ->with(['subscriptions' => fn ($q) => $q->where('status', 'active')->where('cycle', 'month')->with('plan')])
            ->get()
            ->flatMap->subscriptions
            ->sum(function ($sub) {
                $confirmed = \Wave\PaymentTransaction::where('subscription_id', $sub->id)
                    ->whereIn('type', ['subscription_initial', 'subscription_renewal'])
                    ->count();
                $nextMonth = $confirmed + 1;
                $rate = $nextMonth <= 3 ? 15 : 8;

                return ((float) ($sub->plan->monthly_price ?? 0)) * $rate / 100;
            });
    }

    public function requestPayout(): void
    {
        $this->validate([
            'payoutAccountName'   => 'required|string|max:255',
            'payoutAccountNumber' => 'required|string|max:20',
            'payoutBankName'      => 'required|string|max:255',
        ]);

        $balance = \Wave\AffiliateCommission::where('affiliate_id', auth()->id())
            ->where('status', 'accrued')
            ->sum('commission_amount');

        if ($balance <= 0) {
            session()->flash('payout_error', 'You have no claimable balance to request a payout.');
            $this->showPayoutForm = false;
            return;
        }

        $bankDetails = [
            'bank_name'      => $this->payoutBankName,
            'account_name'   => $this->payoutAccountName,
            'account_number' => $this->payoutAccountNumber,
        ];

        // Persist payout request in DB so admin can action it
        \Wave\PayoutRequest::create([
            'affiliate_id'   => auth()->id(),
            'amount'         => $balance,
            'bank_name'      => $bankDetails['bank_name'],
            'account_name'   => $bankDetails['account_name'],
            'account_number' => $bankDetails['account_number'],
            'status'         => 'pending',
        ]);

        // Also notify admin by email
        \Illuminate\Support\Facades\Mail::to(config('mail.from.address'))->queue(
            new \App\Mail\PayoutRequestMail(auth()->user(), $balance, $bankDetails)
        );

        $this->reset(['payoutAccountName', 'payoutAccountNumber', 'payoutBankName', 'showPayoutForm']);
        session()->flash('payout_requested', 'Payout request submitted! We will process it within 3-5 business days.');
    }

    public function signUpClient(): void
    {
        $this->validate([
            'newClientName' => 'required|string|max:255',
            'newClientEmail' => ['required', 'email', Rule::unique('users', 'email')],
            'newClientPassword' => 'required|min:8',
        ]);

        \App\Models\User::create([
            'name' => $this->newClientName,
            'email' => $this->newClientEmail,
            'password' => bcrypt($this->newClientPassword),
            'referrer_id' => auth()->id(),
        ]);

        $this->reset(['newClientName', 'newClientEmail', 'newClientPassword', 'showSignUpForm']);
        session()->flash('client_created', 'Client account created and linked to your affiliate profile.');
    }

    public function render()
    {
        $user = auth()->user();

        $commissions = AffiliateCommission::where('affiliate_id', $user->id)
            ->with('referredUser', 'subscription.plan')
            ->latest('accrued_at')
            ->paginate(10);

        $clients = $user->referrals()
            ->withCount('referrals as sub_affiliate_count')
            ->latest()
            ->get();

        return view('livewire.affiliate-tracker', [
            'kpis' => $this->kpis,
            'commissions' => $commissions,
            'clients' => $clients,
            'referralLink' => url('/r/'.($user->referral_code ?? '')),
        ]);
    }
}
