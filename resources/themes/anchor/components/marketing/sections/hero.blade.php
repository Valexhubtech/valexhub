<section class="hero-section relative lg:min-h-screen flex items-start lg:items-center overflow-hidden bg-white">

    {{-- Subtle grid background --}}
    <div class="absolute inset-0 opacity-[0.04] pointer-events-none"
         style="background-image: linear-gradient(rgba(0,0,0,1) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,1) 1px, transparent 1px); background-size: 60px 60px;"></div>

    {{-- Gradient fade at bottom --}}
    <div class="absolute bottom-0 left-0 right-0 h-40 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>

    {{-- Blue orb top-right --}}
    <div class="absolute -top-32 -right-32 w-[600px] h-[600px] bg-blue-100/70 rounded-full blur-3xl pointer-events-none"></div>

    {{-- Mobile "Pay & Go Live" annotation — standalone, tilted, visible --}}
    <div class="lg:hidden absolute top-[72px] right-3 transform rotate-[8deg] z-20 pointer-events-none select-none opacity-60" aria-hidden="true">
        <div class="flex items-start gap-1">
            <svg width="18" height="18" viewBox="0 0 28 28" fill="none" class="text-zinc-500 mt-0.5 flex-shrink-0">
                <path d="M4 6 C4 6, 20 2, 24 16 C26 23, 20 26, 16 26" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                <path d="M13 23 L16 26 L13.5 29" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="font-black text-base text-zinc-500 leading-tight" style="font-style:italic; letter-spacing:-0.03em;">Pay &amp;<br>Go Live</span>
        </div>
    </div>

    {{-- Content --}}
    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 pt-20 lg:pt-24 pb-8 lg:pb-16 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">

            {{-- LEFT: Text --}}
            <div class="text-center lg:text-left">
                {{-- Tag --}}
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-zinc-200 bg-white/90 text-xs font-semibold text-zinc-600 mb-4 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse flex-shrink-0"></span>
                    Built for businesses
                </div>

                {{-- Headline: 2 lines on mobile/tablet, 3-4 lines on desktop --}}
                <h1 class="text-3xl sm:text-5xl lg:text-6xl xl:text-7xl font-black text-zinc-900 leading-[0.95] tracking-tight">
                    Run Your<br class="hidden lg:block">
                    Business on<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-blue-400">Software That Works</span>
                </h1>

                {{-- Subtitle --}}
                <p class="mt-5 text-base sm:text-lg text-zinc-500 leading-relaxed max-w-md mx-auto lg:mx-0">
                    Pre-built management systems for schools, hotels, property and more — sign up, pay, and go live in under an hour.
                </p>

                {{-- CTAs --}}
                <div class="mt-7 flex flex-row flex-wrap gap-2 justify-center lg:justify-start">
                    <a href="{{ route('book-demo') }}"
                       class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 sm:px-6 sm:py-3.5 text-xs sm:text-sm font-semibold text-white bg-zinc-900 rounded-xl hover:bg-zinc-800 transition-colors shadow-lg shadow-zinc-900/20">
                        Book a Free Demo
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 7l5 5-5 5"/>
                        </svg>
                    </a>
                    <a href="{{ route('products') }}"
                       class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 sm:px-6 sm:py-3.5 text-xs sm:text-sm font-semibold text-zinc-700 bg-white border border-zinc-200 rounded-xl hover:bg-zinc-50 hover:border-zinc-300 transition-colors shadow-sm">
                        Browse Products
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                {{-- Mobile "Ready in Minutes" accent --}}
                <div class="lg:hidden flex justify-center items-center gap-1.5 mt-4 transform rotate-[2deg] opacity-45 pointer-events-none select-none" aria-hidden="true">
                    <span class="font-black text-xs text-zinc-500 tracking-wide" style="font-style:italic;">Ready in Minutes</span>
                    <svg width="14" height="18" viewBox="0 0 24 32" fill="none" class="text-zinc-500">
                        <path d="M4 4 C8 4, 20 8, 18 18 C16 26, 8 28, 8 28" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none"/>
                        <path d="M5 25 L8 28 L11 25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

            </div>

            {{-- RIGHT: Floating product cards --}}
            <div class="hidden lg:block relative h-[560px]" id="hero-cards">

                {{-- School card --}}
                <div class="hero-float-card absolute top-6 left-6 w-52 bg-white rounded-2xl p-4 shadow-xl border border-zinc-100 rotate-[-2deg]">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 bg-zinc-900 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-zinc-900 leading-none">School Management</p>
                            <p class="text-[10px] text-zinc-400 mt-0.5">Deploy ready</p>
                        </div>
                    </div>
                    <div class="bg-zinc-50 rounded-lg p-2.5">
                        <p class="text-[10px] text-zinc-500">Students enrolled</p>
                        <p class="text-lg font-black text-zinc-900 leading-none mt-0.5">1,247</p>
                        <div class="h-1 bg-zinc-200 rounded-full mt-2 overflow-hidden">
                            <div class="h-full w-3/4 bg-blue-500 rounded-full"></div>
                        </div>
                    </div>
                </div>

                {{-- Hotel card --}}
                <div class="hero-float-card absolute top-2 right-4 w-48 bg-white rounded-2xl p-4 shadow-xl border border-zinc-100 rotate-[3deg]">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-zinc-900 leading-none">Hotel & Booking</p>
                            <p class="text-[10px] text-zinc-400 mt-0.5">12 rooms live</p>
                        </div>
                    </div>
                    <div class="flex gap-1">
                        @for($i = 0; $i < 8; $i++)
                            <div class="flex-1 h-6 rounded {{ $i < 5 ? 'bg-blue-500' : 'bg-zinc-100' }}"></div>
                        @endfor
                    </div>
                    <p class="text-[10px] text-zinc-400 mt-1.5">5 rooms available</p>
                </div>

                {{-- Pharmacy card --}}
                <div class="hero-float-card absolute top-[190px] left-12 w-56 bg-white rounded-2xl p-4 shadow-xl border border-zinc-100 rotate-[-1deg]">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 bg-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6"/>
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-zinc-900">Pharmacy POS</p>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 bg-red-50 text-red-600 rounded-full font-medium">2 expiring</span>
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-[10px]">
                            <span class="text-zinc-500">Today's sales</span>
                            <span class="font-bold text-zinc-900">₦84,200</span>
                        </div>
                        <div class="flex justify-between text-[10px]">
                            <span class="text-zinc-500">Items in stock</span>
                            <span class="font-bold text-zinc-900">1,832</span>
                        </div>
                    </div>
                </div>

                {{-- Restaurant card --}}
                <div class="hero-float-card absolute bottom-[140px] right-8 w-48 bg-white rounded-2xl p-4 shadow-xl border border-zinc-100 rotate-[2deg]">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div class="w-9 h-9 bg-orange-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-zinc-900">Restaurant POS</p>
                    </div>
                    <div class="grid grid-cols-3 gap-1">
                        @foreach(['T1','T2','T3','T4','T5','T6'] as $t)
                            <div class="rounded text-center py-1 text-[9px] font-bold {{ $loop->index < 4 ? 'bg-orange-100 text-orange-700' : 'bg-zinc-100 text-zinc-400' }}">{{ $t }}</div>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-zinc-400 mt-1.5">4 tables occupied</p>
                </div>

                {{-- Property card --}}
                <div class="hero-float-card absolute bottom-6 left-8 w-52 bg-zinc-950 rounded-2xl p-4 shadow-xl border border-zinc-800 rotate-[-3deg]">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white leading-none">Property Management</p>
                            <p class="text-[10px] text-zinc-500 mt-0.5">8 units managed</p>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <p class="text-[10px] text-zinc-500">Rent collected</p>
                            <p class="text-sm font-black text-white">₦1.2M</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-zinc-500">Overdue</p>
                            <p class="text-sm font-black text-red-400">₦80k</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    {{-- GSAP floating card animations --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof gsap === 'undefined') return;
        var cards = document.querySelectorAll('.hero-float-card');
        if (!cards.length) return;

        // Stagger in
        gsap.from(cards, { opacity: 0, y: 30, scale: 0.92, stagger: 0.12, duration: 0.7, ease: 'back.out(1.4)', delay: 0.3 });

        // Continuous float — each card gets unique timing
        var offsets = [0, 0.8, 1.5, 2.2, 3.0];
        cards.forEach(function(card, i) {
            gsap.to(card, {
                y: '-=12',
                duration: 2.4 + i * 0.3,
                ease: 'sine.inOut',
                yoyo: true,
                repeat: -1,
                delay: offsets[i]
            });
        });
    });
    </script>

</section>
