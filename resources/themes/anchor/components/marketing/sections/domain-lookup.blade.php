<section class="relative py-16 sm:py-20 bg-zinc-950 overflow-hidden">

    {{-- Gradient orb --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-blue-600/8 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-3xl mx-auto px-6 lg:px-8 text-center">

        <p class="text-xs font-semibold tracking-widest uppercase text-zinc-400 mb-4">Your business, your brand</p>
        <div class="relative mb-3">
            {{-- Arrow accent — left --}}
            <div class="hidden sm:flex absolute left-0 top-1/2 -translate-y-1/2 -translate-x-[110%] items-center gap-1.5 transform rotate-[-6deg] opacity-65 pointer-events-none" aria-hidden="true">
                <span class="font-black text-sm text-zinc-400 leading-tight" style="font-style:italic;">check if<br>it's yours</span>
                <svg width="28" height="22" viewBox="0 0 32 24" fill="none" class="text-zinc-400 flex-shrink-0">
                    <path d="M4 12 C8 8, 18 6, 28 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                    <path d="M25 7 L28 10 L25 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            {{-- Arrow accent — right --}}
            <div class="hidden sm:flex absolute right-0 top-1/2 -translate-y-1/2 translate-x-[110%] items-center gap-1.5 transform rotate-[6deg] opacity-65 pointer-events-none" aria-hidden="true">
                <svg width="28" height="22" viewBox="0 0 32 24" fill="none" class="text-zinc-400 flex-shrink-0 scale-x-[-1]">
                    <path d="M4 12 C8 8, 18 6, 28 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                    <path d="M25 7 L28 10 L25 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="font-black text-sm text-zinc-400 leading-tight" style="font-style:italic;">own your<br>brand</span>
            </div>
            <h2 class="text-3xl sm:text-4xl font-black text-white leading-tight">
                Own Your Domain
            </h2>
        </div>
        <p class="text-sm sm:text-base text-zinc-300 mb-10">
            Every deployment comes with the option to add a custom domain. Check if yours is available.
        </p>

        {{-- Search form --}}
        <div x-data="{
                domain: '',
                loading: false,
                result: null,
                check() {
                    if (!this.domain.trim()) return;
                    this.loading = true;
                    this.result = null;
                    var d = this.domain.trim().toLowerCase().replace(/^https?:\/\//, '').replace(/^www\./, '');
                    setTimeout(() => {
                        this.loading = false;
                        this.result = { domain: d };
                    }, 800);
                }
             }"
             @submit.prevent="check()">
            <form class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <input
                        x-model="domain"
                        type="text"
                        placeholder="yourbusiness.com"
                        class="w-full px-5 py-4 bg-zinc-800 border border-zinc-400 text-white placeholder-zinc-400 rounded-xl text-sm focus:outline-none focus:border-white/60 focus:ring-1 focus:ring-white/10 transition-colors"
                        @keydown.enter.prevent="check()"
                    >
                </div>
                <button
                    @click.prevent="check()"
                    :disabled="loading || !domain.trim()"
                    class="px-7 py-4 bg-white text-zinc-900 font-bold text-sm rounded-xl hover:bg-zinc-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap flex items-center justify-center gap-2 shadow-lg shadow-black/20">
                    <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-show="!loading">Check Availability</span>
                    <span x-show="loading">Checking…</span>
                </button>
            </form>

            {{-- Result --}}
            <div x-show="result" x-transition class="mt-6 p-5 bg-zinc-900 border border-zinc-700 rounded-xl text-left">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-zinc-800 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm" x-text="result?.domain"></p>
                            <p class="text-xs text-zinc-500 mt-0.5">Domain availability checked on registration</p>
                        </div>
                    </div>
                    @auth
                    <a href="{{ route('dashboard.domains') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-zinc-900 bg-white rounded-lg hover:bg-zinc-100 transition-colors flex-shrink-0">
                        Search in Dashboard
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @else
                    <a href="{{ route('register') }}?domain_intent=1"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-zinc-900 bg-white rounded-lg hover:bg-zinc-100 transition-colors flex-shrink-0">
                        Register &amp; Deploy
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @endauth
                </div>
                @guest
                <p class="mt-3 text-xs text-zinc-400">
                    Create an account to complete your domain search and connect it to your deployment.
                </p>
                @endguest
            </div>
        </div>

        <p class="mt-6 text-xs text-zinc-400">Domain registration is optional — every deployment works on a free subdomain by default.</p>

    </div>
</section>
