<section id="how-it-works" class="border-t border-zinc-200 dark:border-zinc-800">

    {{-- Mobile: static vertical layout --}}
    <div class="md:hidden py-16 px-6 max-w-2xl mx-auto">
        <div class="mb-12">
            <p class="text-xs font-semibold tracking-widest uppercase text-zinc-400 mb-3">Simple process</p>
            <h2 class="text-3xl font-bold text-zinc-900 dark:text-white">How It Works</h2>
            <p class="mt-3 text-zinc-500 dark:text-zinc-400">From browsing to a live running system — your way.</p>
        </div>
        <div class="space-y-10">
            <div class="flex gap-5">
                <span class="text-5xl font-black text-zinc-100 dark:text-zinc-800 leading-none w-14 flex-shrink-0">01</span>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white text-lg">Browse Everything, Commit to Nothing</h3>
                    <p class="mt-1.5 text-sm text-zinc-500 leading-relaxed">Create a free account and explore every product, pricing plan, and add-on before you decide. No credit card needed.</p>
                </div>
            </div>
            <div class="flex gap-5">
                <span class="text-5xl font-black text-zinc-100 dark:text-zinc-800 leading-none w-14 flex-shrink-0">02</span>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white text-lg">Pick Your Software & Pay in Naira</h3>
                    <p class="mt-1.5 text-sm text-zinc-500 leading-relaxed">Choose your product and a billing cycle that fits your budget — monthly, quarterly, or yearly. Pay securely in Naira. No USD, no conversion fees, no long contracts.</p>
                </div>
            </div>
            <div class="flex gap-5">
                <span class="text-5xl font-black text-zinc-100 dark:text-zinc-800 leading-none w-14 flex-shrink-0">03</span>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white text-lg">Cloud Hosting — We Handle Everything</h3>
                    <p class="mt-1.5 text-sm text-zinc-500 leading-relaxed">No servers to buy or manage. We host, maintain, and update your system automatically. Just log in and use it — from any device, anywhere.</p>
                </div>
            </div>
            <div class="flex gap-5">
                <span class="text-5xl font-black text-zinc-100 dark:text-zinc-800 leading-none w-14 flex-shrink-0">04</span>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white text-lg">On-Premises — Your Servers, Your Control</h3>
                    <p class="mt-1.5 text-sm text-zinc-500 leading-relaxed">Prefer to keep data on your own infrastructure? We install and configure everything on your servers. Full control, no dependency on external hosting.</p>
                </div>
            </div>
            <div class="flex gap-5">
                <span class="text-5xl font-black text-zinc-100 dark:text-zinc-800 leading-none w-14 flex-shrink-0">05</span>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white text-lg">Walk Into a Running System</h3>
                    <p class="mt-1.5 text-sm text-zinc-500 leading-relaxed">No setup calls, no IT team, no back-and-forth. Your system is live the moment payment is confirmed. Login details and your URL are waiting in your dashboard.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Desktop: pinned horizontal scroll --}}
    <div id="how-it-works-desktop" class="hidden md:flex h-screen overflow-hidden">

        {{-- Left: fixed heading column --}}
        <div class="w-[38%] flex-shrink-0 flex items-center px-12 lg:px-20 bg-zinc-50 dark:bg-zinc-900/60 border-r border-zinc-200 dark:border-zinc-800">
            <div>
                <p class="text-xs font-semibold tracking-widest uppercase text-zinc-400 mb-4">Simple process</p>
                <h2 class="text-5xl lg:text-6xl font-black leading-[0.95] text-zinc-900 dark:text-white">
                    How It<br>Works
                </h2>
                <p class="mt-6 text-zinc-500 dark:text-zinc-400 leading-relaxed text-base lg:text-lg">
                    From browsing to a live running system — cloud or on-premises, your choice.
                </p>
                <div class="mt-10 inline-flex items-center gap-2 text-sm text-zinc-400">
                    <span>Scroll through the steps</span>
                    <svg class="w-4 h-4 how-it-works-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Right: horizontally scrolling cards --}}
        <div class="flex-1 overflow-hidden relative flex items-center">
            <div class="how-it-works-track flex items-stretch gap-6 px-10 h-[70%]">

                {{-- Step 1 --}}
                <div class="step-card flex-shrink-0 w-80 lg:w-96 flex flex-col justify-between p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl">
                    <div>
                        <span class="block text-7xl font-black text-zinc-100 dark:text-zinc-800 leading-none mb-6">01</span>
                        <div class="w-12 h-12 bg-zinc-900 dark:bg-white rounded-xl flex items-center justify-center mb-5">
                            <x-phosphor-user-plus class="w-6 h-6 text-white dark:text-zinc-900" />
                        </div>
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">Browse Everything, Commit to Nothing</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">Create a free account and explore every product, pricing plan, and add-on before you decide. No credit card needed to look around.</p>
                    </div>
                    <a href="{{ route('register') }}" class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-zinc-900 dark:text-white hover:gap-2.5 transition-all">
                        Create free account
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                {{-- Step 2 --}}
                <div class="step-card flex-shrink-0 w-80 lg:w-96 flex flex-col justify-between p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl">
                    <div>
                        <span class="block text-7xl font-black text-zinc-100 dark:text-zinc-800 leading-none mb-6">02</span>
                        <div class="w-12 h-12 bg-zinc-900 dark:bg-white rounded-xl flex items-center justify-center mb-5">
                            <x-phosphor-sliders class="w-6 h-6 text-white dark:text-zinc-900" />
                        </div>
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">Pick Your Software & Pay in Naira</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">Choose your product and a billing cycle that works for you — monthly, quarterly, or yearly. Pay securely in Naira via Paystack. No USD, no conversion fees, no long contracts.</p>
                    </div>
                    <div class="mt-6 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs text-zinc-400">Secured by Paystack · Naira payments</span>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="step-card flex-shrink-0 w-80 lg:w-96 flex flex-col justify-between p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl">
                    <div>
                        <span class="block text-7xl font-black text-zinc-100 dark:text-zinc-800 leading-none mb-6">03</span>
                        <div class="w-12 h-12 bg-zinc-900 dark:bg-white rounded-xl flex items-center justify-center mb-5">
                            <x-phosphor-cloud class="w-6 h-6 text-white dark:text-zinc-900" />
                        </div>
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">Cloud Hosting — We Handle Everything</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">No servers to buy or manage. We host, maintain, and update your system automatically. Log in and use it from any device, anywhere — we handle the rest.</p>
                    </div>
                    <div class="mt-6 flex items-center gap-2">
                        <x-phosphor-lightning class="w-4 h-4 text-yellow-500" />
                        <span class="text-xs text-zinc-400">Automated deployment · Live in under an hour</span>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="step-card flex-shrink-0 w-80 lg:w-96 flex flex-col justify-between p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl">
                    <div>
                        <span class="block text-7xl font-black text-zinc-100 dark:text-zinc-800 leading-none mb-6">04</span>
                        <div class="w-12 h-12 bg-zinc-900 dark:bg-white rounded-xl flex items-center justify-center mb-5">
                            <x-phosphor-hard-drives class="w-6 h-6 text-white dark:text-zinc-900" />
                        </div>
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">On-Premises — Your Servers, Your Control</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">Prefer to keep data on your own infrastructure? We install and configure everything on your servers. Full control over your data, no dependency on external hosting.</p>
                    </div>
                    <a href="{{ route('book-demo') }}" class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-zinc-900 dark:text-white hover:gap-2.5 transition-all">
                        Ask us about on-prem
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                {{-- Step 5 --}}
                <div class="step-card flex-shrink-0 w-80 lg:w-96 flex flex-col justify-between p-8 bg-zinc-900 dark:bg-white border border-zinc-800 dark:border-zinc-200 rounded-2xl">
                    <div>
                        <span class="block text-7xl font-black text-zinc-700 dark:text-zinc-200 leading-none mb-6">05</span>
                        <div class="w-12 h-12 bg-white dark:bg-zinc-900 rounded-xl flex items-center justify-center mb-5">
                            <x-phosphor-rocket-launch class="w-6 h-6 text-zinc-900 dark:text-white" />
                        </div>
                        <h3 class="text-xl font-bold text-white dark:text-zinc-900 mb-3">Walk Into a Running System</h3>
                        <p class="text-sm text-zinc-400 dark:text-zinc-500 leading-relaxed">No setup calls, no back-and-forth. Your system is live the moment payment is confirmed. Login credentials and your URL are waiting in your dashboard.</p>
                    </div>
                    <a href="{{ route('register') }}" class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-white dark:text-zinc-900 hover:gap-2.5 transition-all">
                        Get started now
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

            </div>
        </div>

    </div>
</section>
