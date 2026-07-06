<section id="how-it-works" class="border-t border-zinc-200 dark:border-zinc-800">

    {{-- Mobile: static vertical layout --}}
    <div class="md:hidden py-16 px-6 max-w-2xl mx-auto">
        <div class="mb-12">
            <p class="text-xs font-semibold tracking-widest uppercase text-zinc-400 mb-3">Simple process</p>
            <h2 class="text-3xl font-bold text-zinc-900 dark:text-white">How It Works</h2>
            <p class="mt-3 text-zinc-500 dark:text-zinc-400">From order to live system in days, not months.</p>
        </div>
        <div class="space-y-10">
            <div class="flex gap-5">
                <span class="text-5xl font-black text-zinc-100 dark:text-zinc-800 leading-none w-14 flex-shrink-0">01</span>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white text-lg">Browse Our Products</h3>
                    <p class="mt-1.5 text-sm text-zinc-500 leading-relaxed">Choose from pre-built systems for schools, hotels, pharmacies, restaurants, and property managers — each ready to deploy.</p>
                </div>
            </div>
            <div class="flex gap-5">
                <span class="text-5xl font-black text-zinc-100 dark:text-zinc-800 leading-none w-14 flex-shrink-0">02</span>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white text-lg">Place Your Order</h3>
                    <p class="mt-1.5 text-sm text-zinc-500 leading-relaxed">Pay a one-time setup fee and pick a monthly hosting plan. Naira payments accepted — no hidden costs, no long contracts.</p>
                </div>
            </div>
            <div class="flex gap-5">
                <span class="text-5xl font-black text-zinc-100 dark:text-zinc-800 leading-none w-14 flex-shrink-0">03</span>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white text-lg">We Set It Up</h3>
                    <p class="mt-1.5 text-sm text-zinc-500 leading-relaxed">Our team installs, configures, and trains your staff within 5–10 working days. We handle all the technical work.</p>
                </div>
            </div>
            <div class="flex gap-5">
                <span class="text-5xl font-black text-zinc-100 dark:text-zinc-800 leading-none w-14 flex-shrink-0">04</span>
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white text-lg">Run Your Business</h3>
                    <p class="mt-1.5 text-sm text-zinc-500 leading-relaxed">Your software is live, your team is trained, your data is migrated. We handle ongoing updates and support.</p>
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
                    From the moment you order to the day your team goes live — we handle everything so you don't have to.
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
                            <x-phosphor-squares-four class="w-6 h-6 text-white dark:text-zinc-900" />
                        </div>
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">Browse Our Products</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">Choose from pre-built systems for schools, hotels, pharmacies, restaurants, and property managers. Each one is ready to deploy.</p>
                    </div>
                    <a href="{{ route('products') }}" class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-zinc-900 dark:text-white hover:gap-2.5 transition-all">
                        View products
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
                            <x-phosphor-credit-card class="w-6 h-6 text-white dark:text-zinc-900" />
                        </div>
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">Place Your Order</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">Pay a one-time setup fee and pick a monthly hosting plan. Naira payments accepted — no hidden costs, no long contracts.</p>
                    </div>
                    <div class="mt-6 flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs text-zinc-400">Secured by Paystack</span>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="step-card flex-shrink-0 w-80 lg:w-96 flex flex-col justify-between p-8 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl">
                    <div>
                        <span class="block text-7xl font-black text-zinc-100 dark:text-zinc-800 leading-none mb-6">03</span>
                        <div class="w-12 h-12 bg-zinc-900 dark:bg-white rounded-xl flex items-center justify-center mb-5">
                            <x-phosphor-rocket-launch class="w-6 h-6 text-white dark:text-zinc-900" />
                        </div>
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">We Set It Up For You</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">Our team installs, configures, and trains your staff within 5–10 working days. We handle data migration, customisation, and onboarding.</p>
                    </div>
                    <div class="mt-6 flex items-center gap-2">
                        <x-phosphor-clock class="w-4 h-4 text-zinc-400" />
                        <span class="text-xs text-zinc-400">5–10 working days to go live</span>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="step-card flex-shrink-0 w-80 lg:w-96 flex flex-col justify-between p-8 bg-zinc-900 dark:bg-white border border-zinc-800 dark:border-zinc-200 rounded-2xl">
                    <div>
                        <span class="block text-7xl font-black text-zinc-700 dark:text-zinc-200 leading-none mb-6">04</span>
                        <div class="w-12 h-12 bg-white dark:bg-zinc-900 rounded-xl flex items-center justify-center mb-5">
                            <x-phosphor-chart-line-up class="w-6 h-6 text-zinc-900 dark:text-white" />
                        </div>
                        <h3 class="text-xl font-bold text-white dark:text-zinc-900 mb-3">Run Your Business</h3>
                        <p class="text-sm text-zinc-400 dark:text-zinc-500 leading-relaxed">Your software is live, your team is trained, your data is migrated. We handle hosting, updates, and ongoing support — you focus on growth.</p>
                    </div>
                    <a href="{{ route('book-demo') }}" class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-white dark:text-zinc-900 hover:gap-2.5 transition-all">
                        Book a free demo
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

            </div>
        </div>

    </div>
</section>
