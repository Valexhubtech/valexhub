<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Wave\Deployment;

class AuthRelayController extends Controller
{
    private const GOOGLE_AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';

    private const GOOGLE_TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private const GOOGLE_USERINFO_URL = 'https://openidconnect.googleapis.com/v1/userinfo';

    private const NONCE_TTL_SECONDS = 300;

    private const HANDOFF_TTL_SECONDS = 300;

    /**
     * Step 1 — instance redirects here. Validate request JWT, then send user to Google.
     */
    public function start(Request $request)
    {
        $token = $request->query('token');

        if (! $token) {
            return redirect('/')->with('error', 'Missing relay token.');
        }

        // Decode header+payload without verifying yet to get tenant_id for lookup
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return redirect('/')->with('error', 'Malformed relay token.');
        }

        $payload = $this->decodeBase64Url($parts[1]);
        if ($payload === null) {
            return redirect('/')->with('error', 'Malformed relay token.');
        }

        $tenantId = $payload['tenant_id'] ?? null;
        $returnUrl = $payload['return_url'] ?? null;
        $nonce = $payload['nonce'] ?? null;

        if (! $tenantId || ! $returnUrl || ! $nonce) {
            return redirect('/')->with('error', 'Invalid relay token payload.');
        }

        // Look up deployment to get the per-tenant secret
        $deployment = Deployment::find((int) $tenantId);
        if (! $deployment || ! $deployment->auth_relay_secret) {
            Log::warning('AuthRelay: deployment not found or has no relay secret', ['tenant_id' => $tenantId]);

            return redirect('/')->with('error', 'Unknown tenant.');
        }

        // Verify the JWT signature and expiry
        if (! $this->verifyJwt($token, $deployment->auth_relay_secret)) {
            Log::warning('AuthRelay: invalid JWT signature', ['tenant_id' => $tenantId]);

            return redirect('/')->with('error', 'Invalid relay token.');
        }

        // Single-use nonce — store in DB. Duplicate = replay attack.
        $inserted = \DB::table('google_auth_nonces')->insertOrIgnore([
            'nonce' => $nonce,
            'deployment_id' => $deployment->id,
            'expires_at' => now()->addSeconds(self::NONCE_TTL_SECONDS),
        ]);

        if (! $inserted) {
            Log::warning('AuthRelay: replayed nonce', ['nonce' => $nonce, 'tenant_id' => $tenantId]);

            return redirect('/')->with('error', 'Token already used.');
        }

        $clientId = config('services.google.client_id');
        if (! $clientId) {
            return redirect('/')->with('error', 'Google login not configured.');
        }

        // Store state in session so callback can retrieve it
        $state = Str::random(40);
        $request->session()->put("relay_state.{$state}", [
            'tenant_id' => $tenantId,
            'nonce' => $nonce,
            'return_url' => $returnUrl,
        ]);

        $callbackUrl = url('/auth-relay/callback');

        $googleUrl = self::GOOGLE_AUTH_URL.'?'.http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $callbackUrl,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ]);

        return redirect($googleUrl);
    }

    /**
     * Step 2 — Google redirects here with a code. Issue handoff JWT to instance.
     */
    public function callback(Request $request)
    {
        $code = $request->query('code');
        $state = $request->query('state');

        if (! $code || ! $state) {
            return redirect('/')->with('error', 'Google login failed.');
        }

        $stateData = $request->session()->pull("relay_state.{$state}");
        if (! $stateData) {
            return redirect('/')->with('error', 'Invalid or expired session state.');
        }

        $tenantId = $stateData['tenant_id'];
        $nonce = $stateData['nonce'];
        $returnUrl = rtrim($stateData['return_url'], '/');

        $deployment = Deployment::find((int) $tenantId);
        if (! $deployment || ! $deployment->auth_relay_secret) {
            return redirect('/')->with('error', 'Unknown tenant.');
        }

        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        // Exchange code for tokens
        try {
            $tokenResponse = Http::asForm()->post(self::GOOGLE_TOKEN_URL, [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => url('/auth-relay/callback'),
                'grant_type' => 'authorization_code',
            ])->throw()->json();
        } catch (\Throwable $e) {
            Log::error('AuthRelay: Google token exchange failed', ['error' => $e->getMessage()]);

            return redirect('/')->with('error', 'Google login failed.');
        }

        $accessToken = $tokenResponse['access_token'] ?? null;
        if (! $accessToken) {
            return redirect('/')->with('error', 'Google login failed.');
        }

        // Fetch user info
        try {
            $userInfo = Http::withToken($accessToken)
                ->get(self::GOOGLE_USERINFO_URL)
                ->throw()
                ->json();
        } catch (\Throwable $e) {
            Log::error('AuthRelay: Google userinfo failed', ['error' => $e->getMessage()]);

            return redirect('/')->with('error', 'Google login failed.');
        }

        $sub = $userInfo['sub'] ?? null;
        $email = $userInfo['email'] ?? null;

        if (! $sub || ! $email) {
            return redirect('/')->with('error', 'Google did not return required user info.');
        }

        // Issue handoff JWT signed with the per-tenant secret
        $now = time();
        $handoffPayload = [
            'sub' => $sub,
            'email' => $email,
            'name' => $userInfo['name'] ?? $email,
            'picture' => $userInfo['picture'] ?? '',
            'nonce' => $nonce,
            'iat' => $now,
            'exp' => $now + self::HANDOFF_TTL_SECONDS,
        ];

        $handoffToken = $this->signJwt($handoffPayload, $deployment->auth_relay_secret);

        return redirect($returnUrl.'/api/auth/google-handoff?token='.urlencode($handoffToken));
    }

    // ── JWT helpers (HS256, zero external deps) ───────────────────────────────

    private function signJwt(array $payload, string $secret): string
    {
        $header = $this->base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $body = $this->base64UrlEncode(json_encode($payload));
        $sig = $this->base64UrlEncode(hash_hmac('sha256', "{$header}.{$body}", $secret, true));

        return "{$header}.{$body}.{$sig}";
    }

    private function verifyJwt(string $token, string $secret): bool
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        [$header, $body, $sig] = $parts;

        $expected = $this->base64UrlEncode(hash_hmac('sha256', "{$header}.{$body}", $secret, true));

        if (! hash_equals($expected, $sig)) {
            return false;
        }

        $payload = $this->decodeBase64Url($body);
        if ($payload === null) {
            return false;
        }

        $exp = $payload['exp'] ?? null;

        return $exp === null || $exp >= time();
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /** @return array<string, mixed>|null */
    private function decodeBase64Url(string $data): ?array
    {
        $json = base64_decode(strtr($data, '-_', '+/'));
        if ($json === false) {
            return null;
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : null;
    }
}
