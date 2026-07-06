<section class="relative overflow-hidden bg-zinc-950 py-28">

    {{-- Gradient orbs --}}
    <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>

    {{-- Subtle grid overlay --}}
    <div class="absolute inset-0 opacity-[0.04] pointer-events-none"
         style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 60px 60px;"></div>

    {{--
        BACKGROUND IMAGE SLOT
        When you have a photo (e.g. Nigerian city or office), drop it here:
        <img src="{{ asset('images/cta-bg.jpg') }}"
             class="absolute inset-0 w-full h-full object-cover opacity-[0.08] mix-blend-luminosity"
             alt="">
    --}}

    <div class="relative z-10 max-w-5xl mx-auto px-6 lg:px-8 text-center">

        {{-- Stats row --}}
        <div class="inline-flex flex-wrap justify-center items-center gap-8 sm:gap-12 mb-16">
            <div>
                <p class="text-4xl font-black text-white">5–10</p>
                <p class="text-xs text-zinc-500 mt-1.5 tracking-wide uppercase">Days to go live</p>
            </div>
            <div class="hidden sm:block w-px h-10 bg-zinc-800"></div>
            <div>
                <p class="text-4xl font-black text-white">₦</p>
                <p class="text-xs text-zinc-500 mt-1.5 tracking-wide uppercase">Naira payments</p>
            </div>
            <div class="hidden sm:block w-px h-10 bg-zinc-800"></div>
            <div>
                <p class="text-4xl font-black text-white">24/7</p>
                <p class="text-xs text-zinc-500 mt-1.5 tracking-wide uppercase">WhatsApp support</p>
            </div>
            <div class="hidden sm:block w-px h-10 bg-zinc-800"></div>
            <div>
                <p class="text-4xl font-black text-white">100%</p>
                <p class="text-xs text-zinc-500 mt-1.5 tracking-wide uppercase">Data ownership</p>
            </div>
        </div>

        {{-- Headline --}}
        <h2 class="text-3xl sm:text-4xl lg:text-6xl font-black text-white leading-[0.95] mb-6">
            Ready to Transform<br class="hidden sm:block"> Your Business?
        </h2>

        <p class="text-zinc-400 text-sm sm:text-base lg:text-lg mb-10 max-w-2xl mx-auto leading-relaxed">
            Software built specifically for Nigerian businesses — Naira payments, local support, and systems that keep working even when the internet doesn't.
        </p>

        {{-- CTAs --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('book-demo') }}"
               class="px-8 py-4 text-zinc-900 bg-white rounded-lg hover:bg-zinc-100 font-semibold transition-colors inline-flex items-center justify-center gap-2">
                <x-phosphor-calendar-blank class="w-5 h-5" />
                Book a Free Demo
            </a>
            <a href="{{ route('products') }}"
               class="px-8 py-4 text-white border border-zinc-700 rounded-lg hover:bg-zinc-900 hover:border-zinc-600 font-semibold transition-colors inline-flex items-center justify-center gap-2">
                <x-phosphor-squares-four class="w-5 h-5" />
                View Our Products
            </a>
        </div>

    </div>
</section>
