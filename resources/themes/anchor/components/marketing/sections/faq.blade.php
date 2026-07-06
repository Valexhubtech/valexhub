<section class="w-full bg-zinc-950">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-32">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-24">

            {{-- Left: sticky heading --}}
            <div class="lg:col-span-2 lg:sticky lg:top-24 lg:self-start">
                <p class="text-xs font-semibold tracking-widest uppercase text-zinc-600 mb-4">Common questions</p>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-white leading-[0.95]">
                    Got<br>Questions?
                </h2>
                <p class="mt-4 sm:mt-6 text-sm sm:text-base text-zinc-400 leading-relaxed">
                    Everything you need to know about our software and how we work with Nigerian businesses.
                </p>
                <a href="{{ route('book-demo') }}"
                   class="mt-8 inline-flex items-center gap-2 px-5 py-3 text-sm font-semibold text-zinc-900 bg-white rounded-lg hover:bg-zinc-100 transition-colors">
                    Talk to us directly
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            {{-- Right: accordion --}}
            <div class="lg:col-span-3 divide-y divide-zinc-800/60">

                @php
                    $faqs = [
                        ['q' => 'What kind of software do you offer?',
                         'a' => 'We provide ready-to-use management systems for schools, hotels, pharmacies, restaurants, property managers, and real estate agencies. We also build fully custom software tailored to your exact workflow.'],
                        ['q' => 'Do I need to pay everything upfront?',
                         'a' => 'No. You pay a one-time setup fee that covers installation, customisation, and training. After that, a small monthly fee covers hosting, updates, and ongoing support.'],
                        ['q' => 'Will the software work offline?',
                         'a' => 'Yes. Most of our systems have strong offline capabilities — especially important for pharmacies, restaurants, and schools where internet can be unreliable.'],
                        ['q' => 'How long does it take to get the system running?',
                         'a' => 'Usually 5–10 working days after payment and requirements gathering. We handle installation, configuration, staff training, and data migration.'],
                        ['q' => 'Can I get a custom solution built for my business?',
                         'a' => 'Absolutely. If none of our pre-built systems fit perfectly, we build bespoke software from scratch based on your exact workflow and requirements.'],
                        ['q' => 'Do you provide training and support?',
                         'a' => 'Yes. Full training for you and your team is included. Ongoing support is part of the monthly fee — via WhatsApp, phone, and remote access.'],
                        ['q' => 'What if I want to cancel?',
                         'a' => 'You can cancel anytime. Your data will be exported and handed over, and we\'ll help you transition smoothly with no penalty.'],
                        ['q' => 'Are your systems suitable for small businesses?',
                         'a' => 'Yes — all our systems are designed specifically for Nigerian SMEs. Naira support, local tax compliance, WhatsApp notifications, and Paystack payments are all built in.'],
                    ];
                @endphp

                @foreach($faqs as $i => $faq)
                    <div x-data="{ open: false }">
                        <button @click="open = !open"
                                class="w-full flex items-start justify-between gap-6 py-6 text-left group">
                            <span class="text-white font-semibold text-base lg:text-lg leading-snug group-hover:text-zinc-300 transition-colors">
                                {{ $faq['q'] }}
                            </span>
                            <span class="flex-shrink-0 mt-0.5 w-7 h-7 rounded-full border border-zinc-700 flex items-center justify-center transition-all duration-200"
                                  :class="open ? 'bg-white border-white' : 'group-hover:border-zinc-500'">
                                <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                     :class="open ? 'text-zinc-900 rotate-45' : 'text-zinc-400'"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                            </span>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="pb-6">
                            <p class="text-zinc-400 leading-relaxed text-sm lg:text-base">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</section>
