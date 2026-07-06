<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;

class ReferralController extends Controller
{
    /**
     * Captures a referral code from a shared link and stores it in a signed
     * cookie, consumed later by User::creating() at registration time.
     */
    public function capture(string $referralCode): RedirectResponse
    {
        $redirect = redirect('/register');

        if (User::where('referral_code', $referralCode)->exists()) {
            $redirect->withCookie(Cookie::make('referral_code', $referralCode, 60 * 24 * 30));
        }

        return $redirect;
    }
}
