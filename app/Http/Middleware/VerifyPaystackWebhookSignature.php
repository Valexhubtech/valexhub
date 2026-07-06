<?php

namespace App\Http\Middleware;

use App\Services\Paystack\PaystackService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class VerifyPaystackWebhookSignature
{
    public const SIGNATURE_HEADER = 'x-paystack-signature';

    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header(self::SIGNATURE_HEADER);

        $isValid = app(PaystackService::class)->verifyWebhookSignature($request->getContent(), $signature);

        if (! $isValid) {
            throw new AccessDeniedHttpException('Invalid Paystack webhook signature.');
        }

        return $next($request);
    }
}
