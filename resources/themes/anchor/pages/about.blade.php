<?php
use function Laravel\Folio\{name};
name('about');
?>

<x-layouts.marketing
    :seo="[
        'title'       => 'About ValexHub — Nigerian Business Software Built Differently',
        'description' => 'ValexHub builds management systems for Nigerian SMEs — offline-capable, Naira-first, and designed for how local businesses actually operate.',
        'canonical'   => url('/about'),
    ]">

    {{-- Hero --}}
    <section class="relative pt-24 pb-20 lg:pt-32 lg:pb-28 bg-white overflow-hidden">
        <div class="absolute inset-0 opacity-40" style="background-image: radial-gradient(circle, #d4d4d8 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="absolute -top-32 -right-32 w-[600px] h-[600px] bg-blue-100/60 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold tracking-widest uppercase text-blue-600 mb-5">About ValexHub</p>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-zinc-900 leading-[0.95] mb-6">
                    Nigerian software<br>built differently
                </h1>
                <p class="text-zinc-500 text-lg leading-relaxed mb-10 max-w-2xl">
                    We build management systems for businesses that need software to work the way Nigeria works — offline when the internet drops, in Naira, and supported by people who pick up the phone.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('book-demo') }}" wire:navigate
                       class="inline-flex items-center gap-2 px-7 py-3.5 bg-zinc-900 text-white font-semibold text-sm rounded-xl hover:bg-zinc-800 transition-colors">
                        Book a Free Demo
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('products') }}" wire:navigate
                       class="inline-flex items-center gap-2 px-7 py-3.5 border border-zinc-200 text-zinc-700 font-semibold text-sm rounded-xl hover:bg-zinc-50 transition-colors">
                        View Our Products
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Our Story --}}
    <section class="py-20 lg:py-28 bg-zinc-50 border-y border-zinc-100">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div>
                    <p class="text-xs font-semibold tracking-widest uppercase text-zinc-500 mb-4">Our Story</p>
                    <h2 class="text-3xl sm:text-4xl font-black text-zinc-900 leading-[0.95] mb-6">
                        Why we started building
                    </h2>
                    <div class="space-y-4 text-zinc-600 leading-relaxed">
                        <p>ValexHub was started with a clear observation: Nigerian businesses were managing operations with pen-and-paper, WhatsApp groups, or expensive foreign software that didn't fit how local businesses work.</p>
                        <p>Schools tracking fees in notebooks. Hotels managing bookings over the phone. Pharmacies losing money because stock-outs weren't visible until it was too late.</p>
                        <p>We decided to build software that solves Nigerian problems — designed for local payment methods, tested against unreliable internet, and priced for the Nigerian market. Every product we ship reflects that focus.</p>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-white border border-zinc-200 rounded-2xl p-8 shadow-sm">
                        <div class="grid grid-cols-2 gap-6">
                            @php
                                $pillars = [
                                    ['label' => 'Offline-capable', 'desc' => 'Works when the internet doesn\'t'],
                                    ['label' => 'Naira-first', 'desc' => 'All transactions in Nigerian currency'],
                                    ['label' => '5–10 day setup', 'desc' => 'From order to live, fast'],
                                    ['label' => 'Local support', 'desc' => 'WhatsApp, phone, remote access'],
                                ];
                            @endphp
                            @foreach($pillars as $p)
                                <div class="p-4 bg-zinc-50 rounded-xl border border-zinc-100">
                                    <p class="font-bold text-zinc-900 text-sm">{{ $p['label'] }}</p>
                                    <p class="text-xs text-zinc-500 mt-1">{{ $p['desc'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-24 h-24 opacity-20 pointer-events-none"
                         style="background-image: radial-gradient(circle, #3b82f6 1.5px, transparent 1.5px); background-size: 12px 12px;"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Mission & Vision --}}
    <section class="py-20 lg:py-28 bg-zinc-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
             style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 60px 60px;"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="bg-zinc-800/50 border border-zinc-700/50 rounded-2xl p-8 lg:p-10">
                    <div class="w-10 h-10 bg-zinc-700 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <p class="text-xs font-semibold tracking-widest uppercase text-zinc-500 mb-3">Our Mission</p>
                    <h3 class="text-2xl font-black text-white mb-4 leading-tight">Make world-class software accessible to every Nigerian business</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed">We believe the school in Owerri, the pharmacy in Kano, and the hotel in Abuja all deserve software that actually solves their problems — not scaled-down versions of tools built for foreign markets.</p>
                </div>
                <div class="bg-white/[0.03] border border-zinc-700/50 rounded-2xl p-8 lg:p-10">
                    <div class="w-10 h-10 bg-zinc-700 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <p class="text-xs font-semibold tracking-widest uppercase text-zinc-500 mb-3">Our Vision</p>
                    <h3 class="text-2xl font-black text-white mb-4 leading-tight">Be the software layer behind Nigeria's growing SME economy</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed">As more Nigerian businesses move online and formalize operations, we want to be the infrastructure they run on — reliable, affordable, and built to grow with them.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- What Makes Us Different --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-14">
                <p class="text-xs font-semibold tracking-widest uppercase text-zinc-500 mb-3">Why ValexHub</p>
                <h2 class="text-3xl sm:text-4xl font-black text-zinc-900 leading-[0.95]">What makes us different</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $differentiators = [
                        ['title' => 'Built for Nigerian workflows', 'body' => 'Every feature reflects how Nigerian businesses actually operate — from multi-branch fee collection to offline stock management.'],
                        ['title' => 'Fixed-price projects', 'body' => 'No hourly billing, no surprise invoices. You agree on a price before we start and that\'s what you pay.'],
                        ['title' => 'Paystack & bank transfer payments', 'body' => 'All systems support Naira payments via Paystack, bank transfer, and POS — no foreign payment gateways.'],
                        ['title' => 'Offline-first design', 'body' => 'Software that keeps running when the internet drops, syncing data automatically when connectivity returns.'],
                        ['title' => 'WhatsApp-based support', 'body' => 'Support happens where Nigerian business owners are — on WhatsApp, not a ticketing system.'],
                        ['title' => 'You own your data', 'body' => 'Your data is yours. If you ever cancel, we export everything and hand it to you — no hostage software.'],
                    ];
                @endphp
                @foreach($differentiators as $d)
                    <div class="p-6 border border-zinc-100 rounded-2xl hover:border-zinc-200 hover:shadow-sm transition-all duration-200">
                        <h3 class="font-bold text-zinc-900 mb-2">{{ $d['title'] }}</h3>
                        <p class="text-sm text-zinc-500 leading-relaxed">{{ $d['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="relative overflow-hidden bg-zinc-950 py-24">
        <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-3xl mx-auto px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-[0.95] mb-6">
                Ready to see it in action?
            </h2>
            <p class="text-zinc-400 text-base leading-relaxed mb-10">
                Book a free 30-minute demo and we'll show you exactly how our software works for your industry.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('book-demo') }}" wire:navigate
                   class="px-8 py-4 text-zinc-900 bg-white rounded-lg hover:bg-zinc-100 font-semibold transition-colors inline-flex items-center justify-center gap-2">
                    Book a Free Demo
                </a>
                <a href="{{ route('products') }}" wire:navigate
                   class="px-8 py-4 text-white border border-zinc-700 rounded-lg hover:bg-zinc-900 hover:border-zinc-600 font-semibold transition-colors inline-flex items-center justify-center gap-2">
                    View Our Products
                </a>
            </div>
        </div>
    </section>

</x-layouts.marketing>
