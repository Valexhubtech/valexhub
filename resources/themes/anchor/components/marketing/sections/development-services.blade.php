<section class="relative py-24 lg:py-32 overflow-hidden bg-zinc-900">

    {{-- Ghost watermark number --}}
    <span class="absolute right-8 top-1/2 -translate-y-1/2 text-[20rem] font-black text-white/[0.02] select-none leading-none pointer-events-none hidden lg:block">7</span>

    {{-- Subtle grid pattern --}}
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
         style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 60px 60px;"></div>

    {{-- Abstract annotation — "from order to live" (desktop absolute, mobile inline-ish via z-layer) --}}
    <div class="absolute top-10 right-10 hidden lg:flex items-center gap-3 transform rotate-[-6deg] opacity-60">
        <svg width="42" height="42" viewBox="0 0 36 36" fill="none" class="text-zinc-600 flex-shrink-0">
            <path d="M4 8 C4 8, 20 4, 30 18 C36 27, 28 32, 24 32" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none"/>
            <path d="M20 30 L24 32 L22 28" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="text-2xl font-black text-zinc-500 whitespace-nowrap tracking-tight">from order to live</span>
    </div>

    {{-- Abstract annotation — bottom left --}}
    <div class="absolute bottom-12 left-8 hidden xl:flex items-center gap-3 transform rotate-[5deg] opacity-50">
        <span class="text-2xl font-black text-zinc-600 whitespace-nowrap tracking-tight">we handle everything</span>
        <svg width="32" height="22" viewBox="0 0 28 20" fill="none" class="text-zinc-600">
            <path d="M2 10 C8 4, 18 4, 26 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none"/>
            <path d="M22 7 L26 10 L22 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>


    <x-container class="relative z-10">

        {{-- Heading --}}
        <div class="text-center mb-10 lg:mb-16">
            <p class="text-xs font-semibold tracking-widest uppercase text-zinc-600 mb-4">Our process</p>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-[0.95]">
                Development &
                <span class="bg-gradient-to-r from-zinc-300 via-blue-400 to-blue-300 bg-clip-text text-transparent"> Deployment Timeline</span>
            </h2>
            <p class="mt-4 text-sm sm:text-base text-zinc-300 max-w-2xl mx-auto leading-relaxed">
                From concept to launch we follow a structured process — keeping you informed and in control at every step.
            </p>
        </div>

        {{-- Mobile annotations (inline below heading) --}}
        <div class="lg:hidden flex flex-wrap gap-3 justify-center mb-10 opacity-60">
            <span class="text-xl font-black text-zinc-500 inline-block" style="transform: rotate(-2deg)">from order to live</span>
            <span class="text-zinc-600 font-black self-center">·</span>
            <span class="text-xl font-black text-zinc-600 inline-block" style="transform: rotate(2deg)">we handle everything</span>
        </div>

        {{-- Tab switcher --}}
        <div
            x-data="{
                tabSelected: 1,
                tabId: $id('dev-tabs'),
                tabButtonClicked(tabButton){
                    this.tabSelected = parseInt(tabButton.dataset.tab);
                    this.tabRepositionMarker(tabButton);
                },
                tabRepositionMarker(tabButton){
                    this.$refs.tabMarker.style.width=tabButton.offsetWidth + 'px';
                    this.$refs.tabMarker.style.height=tabButton.offsetHeight + 'px';
                    this.$refs.tabMarker.style.left=tabButton.offsetLeft + 'px';
                },
                tabContentActive(contentId){
                    return this.tabSelected == contentId;
                }
            }"
            x-init="tabRepositionMarker($refs.tabButtons.firstElementChild);"
            class="max-w-3xl mx-auto">

            {{-- Tab bar --}}
            <div class="flex justify-center mb-14">
                <div class="relative">
                    <div x-ref="tabButtons"
                         class="relative inline-grid items-center justify-center h-12 grid-cols-2 p-1 text-zinc-400 bg-zinc-800 rounded-xl select-none border border-zinc-700">
                        <button data-tab="1" @click="tabButtonClicked($el);" type="button"
                                class="relative z-20 inline-flex items-center justify-center w-full h-10 px-6 text-sm font-medium transition-all rounded-lg cursor-pointer whitespace-nowrap"
                                :class="{'text-zinc-900 font-semibold': tabSelected === 1, 'text-zinc-400 hover:text-zinc-200': tabSelected !== 1}">
                            Custom Software
                        </button>
                        <button data-tab="2" @click="tabButtonClicked($el);" type="button"
                                class="relative z-20 inline-flex items-center justify-center w-full h-10 px-6 text-sm font-medium transition-all rounded-lg cursor-pointer whitespace-nowrap"
                                :class="{'text-zinc-900 font-semibold': tabSelected === 2, 'text-zinc-400 hover:text-zinc-200': tabSelected !== 2}">
                            Pre-built Solutions
                        </button>
                        <div x-ref="tabMarker" class="absolute left-0 z-10 h-full duration-300 ease-out" x-cloak>
                            <div class="w-full h-full bg-white rounded-lg shadow-sm"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Custom Software Timeline --}}
            <div x-show="tabContentActive(1)">
                <ol class="relative space-y-0 before:absolute before:top-0 before:left-5 before:h-full before:w-px before:bg-zinc-700">
                    @php
                        $customSteps = [
                            ['week' => 'Week 1',   'color' => 'bg-blue-500',  'title' => 'Project Kickoff & Planning',   'desc' => 'Requirements gathering, system architecture, and timeline sign-off. We set up development tools and open a dedicated communication channel.'],
                            ['week' => 'Week 2–4', 'color' => 'bg-blue-500',  'title' => 'Core Development',              'desc' => 'Building core features, database schema, and API layer. Regular progress updates with milestone reviews and client feedback built in.'],
                            ['week' => 'Week 5–6', 'color' => 'bg-blue-500',  'title' => 'Testing & Refinement',           'desc' => 'Comprehensive QA, bug fixes, performance tuning, and user acceptance testing. Final adjustments from your feedback.'],
                            ['week' => 'Week 7',   'color' => 'bg-green-500', 'title' => 'Launch & Post-Launch Support',  'desc' => 'Production deployment, staff training, full documentation handover, and 30-day post-launch support.'],
                        ];
                    @endphp
                    @foreach($customSteps as $i => $step)
                        <li class="relative flex gap-6 pb-10 last:pb-0">
                            <div class="flex-shrink-0 w-10 flex flex-col items-center">
                                <span class="relative z-10 w-4 h-4 mt-1 rounded-full {{ $step['color'] }} ring-4 ring-zinc-900 shadow-lg"></span>
                            </div>
                            <div class="flex-1 pb-2">
                                <time class="text-xs font-semibold uppercase tracking-widest {{ $i === 3 ? 'text-green-400' : 'text-blue-400' }}">{{ $step['week'] }}</time>
                                <h3 class="mt-1 text-lg font-bold text-white">{{ $step['title'] }}</h3>
                                <p class="mt-2 text-sm text-zinc-300 leading-relaxed max-w-lg">{{ $step['desc'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Pre-built Solutions Timeline --}}
            <div x-show="tabContentActive(2)" x-cloak>
                <ol class="relative space-y-0 before:absolute before:top-0 before:left-5 before:h-full before:w-px before:bg-zinc-700">
                    @php
                        $prebuiltSteps = [
                            ['week' => 'Day 1–2',  'color' => 'bg-purple-500', 'title' => 'Solution Selection & Assessment', 'desc' => 'A quick call to identify the best pre-built system for your business and assess your existing data structure.'],
                            ['week' => 'Week 1',   'color' => 'bg-purple-500', 'title' => 'Setup & Configuration',            'desc' => 'System installation, branding customisation, user accounts, and workflow configuration to match your business.'],
                            ['week' => 'Week 2',   'color' => 'bg-purple-500', 'title' => 'Data Migration & Testing',         'desc' => 'Import your existing data, run comprehensive tests, and fine-tune settings. Staff preview and feedback round.'],
                            ['week' => 'Week 3',   'color' => 'bg-green-500',  'title' => 'Training & Go Live',               'desc' => 'Full staff training, final testing, and go-live support. Your business is up and running with minimal downtime.'],
                        ];
                    @endphp
                    @foreach($prebuiltSteps as $i => $step)
                        <li class="relative flex gap-6 pb-10 last:pb-0">
                            <div class="flex-shrink-0 w-10 flex flex-col items-center">
                                <span class="relative z-10 w-4 h-4 mt-1 rounded-full {{ $step['color'] }} ring-4 ring-zinc-900 shadow-lg"></span>
                            </div>
                            <div class="flex-1 pb-2">
                                <time class="text-xs font-semibold uppercase tracking-widest {{ $i === 3 ? 'text-green-400' : 'text-purple-400' }}">{{ $step['week'] }}</time>
                                <h3 class="mt-1 text-lg font-bold text-white">{{ $step['title'] }}</h3>
                                <p class="mt-2 text-sm text-zinc-300 leading-relaxed max-w-lg">{{ $step['desc'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

        </div>
    </x-container>
</section>
