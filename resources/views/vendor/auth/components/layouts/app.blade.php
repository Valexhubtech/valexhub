<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('auth::includes.head')
</head>
<body id="auth-body" class="overflow-hidden relative w-screen h-screen" style="background-color:#ffffff;">
    @php
        $dyanicPageId = str_replace('/', '-', str_replace('.', '', Request::path()));
    @endphp
    <div x-data data-auth="{{ $dyanicPageId }}" class="relative w-full h-full" x-cloak>

        <div style="display:flex; width:100%; height:100vh;">

            {{-- Left brand panel (desktop only) --}}
            <div style="display:none; width:45%; flex-direction:column; justify-content:space-between; padding:3rem; background-color:#09090b; position:relative; overflow:hidden;" class="auth-brand-panel">
                {{-- Dot grid --}}
                <div style="position:absolute; inset:0; opacity:0.04; background-image:radial-gradient(circle, white 1px, transparent 1px); background-size:28px 28px; pointer-events:none;"></div>
                {{-- Blue glow orb --}}
                <div style="position:absolute; top:-180px; left:-180px; width:480px; height:480px; background:rgba(37,99,235,0.1); border-radius:50%; filter:blur(80px); pointer-events:none;"></div>

                {{-- Logo --}}
                <div style="position:relative; z-index:10;">
                    <a href="{{ config('app.url') }}">
                        <img src="/wave/img/logo.png" alt="{{ config('app.name') }}" style="height:32px; width:auto; filter:brightness(0) invert(1);">
                    </a>
                </div>

                {{-- Tagline --}}
                <div style="position:relative; z-index:10;">
                    <p style="font-size:0.65rem; font-weight:600; letter-spacing:0.12em; text-transform:uppercase; color:#52525b; margin-bottom:1rem;">Business Software Platform</p>
                    <h2 style="font-size:1.875rem; font-weight:900; color:#ffffff; line-height:1.25; margin-bottom:1rem;">
                        Manage your business<br>from anywhere.
                    </h2>
                    <p style="font-size:0.875rem; color:#71717a; line-height:1.65; max-width:340px;">
                        School management, hotel operations, property tracking, payroll — all in one place, built for how Nigerian businesses actually work.
                    </p>
                </div>

                {{-- Footer note --}}
                <div style="position:relative; z-index:10;">
                    <p style="font-size:0.7rem; color:#3f3f46;">
                        &copy; {{ date('Y') }} {{ config('app.name') }} &middot; Built for Nigerian businesses
                    </p>
                </div>
            </div>

            {{-- Right form panel --}}
            <main id="auth-main-content" style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:100vh; overflow-y:auto; padding:1.5rem; position:relative;">

                {{-- Mobile logo --}}
                <div class="auth-mobile-logo" style="margin-bottom:1.75rem;">
                    <a href="{{ config('app.url') }}">
                        <img src="/wave/img/logo.png" alt="{{ config('app.name') }}" style="height:32px; width:auto; display:block; margin:0 auto;">
                    </a>
                </div>

                <div style="width:100%; max-width:400px;">
                    {{ $slot }}
                </div>

                {{-- Secured by ValexHub badge --}}
                <div style="margin-top:1.5rem; display:flex; align-items:center; gap:0.375rem; font-size:0.7rem; color:#a1a1aa;">
                    <svg style="width:13px; height:13px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Secured by {{ config('app.name') }}</span>
                </div>

            </main>
        </div>

    </div>

    <style>
        @media (min-width: 1024px) {
            .auth-brand-panel {
                display: flex !important;
            }
            .auth-mobile-logo {
                display: none !important;
            }
        }
    </style>
</body>
</html>
