<?php
use function Laravel\Folio\{name};
name('book-demo');

$products = \Wave\Product::where('is_active', true)->orderBy('name')->get();
?>

<x-layouts.marketing
    :seo="[
        'title'       => 'Book a Free Demo — ValexHub',
        'description' => 'Schedule a free 30-minute demo. We\'ll show you the software working for your exact industry and answer every question.',
        'canonical'   => url('/book-demo'),
    ]">

    {{-- Header --}}
    <section class="relative bg-zinc-950 pt-24 pb-20 lg:pt-32 lg:pb-28 overflow-hidden">
        <div class="absolute top-0 left-1/4 w-[500px] h-[400px] bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-[400px] h-[300px] bg-indigo-600/8 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
             style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 60px 60px;"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                {{-- Left: heading --}}
                <div>
                    <p class="text-xs font-semibold tracking-widest uppercase text-zinc-500 mb-5">Free Demo</p>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-[0.95] mb-6">
                        Talk to us.<br>We'll show<br>you the rest.
                    </h1>
                    <p class="text-zinc-400 text-base leading-relaxed max-w-md">
                        Fill in the form, or reach us directly on WhatsApp. Either way, we'll walk you through exactly how the software works for your business — no obligation.
                    </p>
                </div>

                {{-- Right: 3 stat/promise cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-4">
                    @php
                        $promises = [
                            ['num' => '30 min', 'label' => 'Free demo session', 'sub' => 'No sales pressure, just the actual software.'],
                            ['num' => '₦0',     'label' => 'Cost to book',      'sub' => 'Completely free, cancel any time.'],
                            ['num' => '< 1 hr', 'label' => 'Response time',     'sub' => 'We reply on WhatsApp within the hour.'],
                        ];
                    @endphp
                    @foreach($promises as $p)
                        <div class="flex items-center gap-5 bg-white/[0.04] border border-white/[0.07] rounded-2xl px-6 py-5">
                            <p class="text-2xl font-black text-white flex-shrink-0 w-20">{{ $p['num'] }}</p>
                            <div>
                                <p class="text-sm font-semibold text-white">{{ $p['label'] }}</p>
                                <p class="text-xs text-zinc-500 mt-0.5">{{ $p['sub'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </section>

    {{-- Main content --}}
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">

                {{-- Form --}}
                <div>
                    <h2 class="text-2xl font-black text-zinc-900 mb-8">Request your demo</h2>

                    <div x-data="{
                        loading: false,
                        step: 1,
                        form: {
                            name: '',
                            email: '',
                            phone: '',
                            referral_code: '',
                            product_id: '',
                            request_type: 'demo'
                        },
                        async submitForm() {
                            if (!this.form.name || !this.form.email || !this.form.phone || !this.form.product_id) {
                                alert('Please fill in all required fields');
                                return;
                            }
                            this.loading = true;
                            try {
                                const response = await fetch('/demo-request', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                    },
                                    body: JSON.stringify(this.form)
                                });
                                const data = await response.json();
                                if (response.ok) {
                                    this.step = 2;
                                    setTimeout(() => {
                                        if (data.whatsapp_url) { window.open(data.whatsapp_url, '_blank'); }
                                        this.step = 1;
                                        this.form = { name: '', email: '', phone: '', referral_code: '', product_id: '', request_type: 'demo' };
                                    }, 2000);
                                } else {
                                    alert(data.message || 'Something went wrong. Please try again.');
                                }
                            } catch (error) {
                                alert('Something went wrong. Please try again.');
                            }
                            this.loading = false;
                        }
                    }" x-cloak>

                        {{-- Step 1: Form --}}
                        <div x-show="step === 1" class="bg-zinc-50 border border-zinc-200 rounded-2xl p-6 lg:p-8">
                            <form @submit.prevent="submitForm()" class="space-y-5">
                                <div>
                                    <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Full Name *</label>
                                    <input type="text" x-model="form.name"
                                           class="w-full px-4 py-3 bg-white border border-zinc-200 rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                           placeholder="Your full name" required />
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Email *</label>
                                        <input type="email" x-model="form.email"
                                               class="w-full px-4 py-3 bg-white border border-zinc-200 rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                               placeholder="your@email.com" required />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Phone *</label>
                                        <input type="tel" x-model="form.phone"
                                               class="w-full px-4 py-3 bg-white border border-zinc-200 rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                               placeholder="+234 xxx xxx xxxx" required />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Which product? *</label>
                                    <select x-model="form.product_id"
                                            class="w-full px-4 py-3 bg-white border border-zinc-200 rounded-xl text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                            required>
                                        <option value="">Select a product for your demo…</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Referral Code <span class="font-normal normal-case text-zinc-400">(optional)</span></label>
                                    <input type="text" x-model="form.referral_code"
                                           class="w-full px-4 py-3 bg-white border border-zinc-200 rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                           placeholder="Enter referral code if any" />
                                </div>
                                <div class="pt-2">
                                    <button type="submit" :disabled="loading"
                                            class="w-full px-6 py-4 text-white bg-zinc-900 rounded-xl hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed font-semibold text-sm transition-colors flex items-center justify-center gap-2">
                                        <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        <span x-show="!loading">Schedule My Free Demo</span>
                                        <span x-show="loading">Processing…</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Step 2: Success --}}
                        <div x-show="step === 2" class="text-center py-16 bg-zinc-50 border border-zinc-200 rounded-2xl">
                            <div class="w-16 h-16 mx-auto mb-5 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-black text-zinc-900 mb-2">Request submitted!</h3>
                            <p class="text-zinc-500 text-sm">Redirecting you to WhatsApp to confirm your slot…</p>
                        </div>
                    </div>
                </div>

                {{-- Right: Contact Methods + Reassurance --}}
                <div class="space-y-6">

                    <div>
                        <h2 class="text-2xl font-black text-zinc-900 mb-6">Prefer to reach us directly?</h2>
                        <div class="space-y-4">
                            {{-- WhatsApp --}}
                            <div class="flex items-start gap-4 p-5 border border-zinc-200 rounded-2xl hover:border-zinc-300 hover:shadow-sm transition-all duration-200 group">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-50 border border-green-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-zinc-900 text-sm">WhatsApp</p>
                                    <p class="text-zinc-500 text-xs mt-0.5 mb-3">Fastest response — usually within the hour</p>
                                    <a href="https://wa.me/2347082938319?text=Hi%2C%20I%27d%20like%20to%20book%20a%20free%20demo" target="_blank"
                                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-900 hover:underline">
                                        Chat now →
                                    </a>
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="flex items-start gap-4 p-5 border border-zinc-200 rounded-2xl hover:border-zinc-300 hover:shadow-sm transition-all duration-200">
                                <div class="flex-shrink-0 w-10 h-10 bg-zinc-50 border border-zinc-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-zinc-900 text-sm">Phone Call</p>
                                    <p class="text-zinc-500 text-xs mt-0.5 mb-3">Mon – Fri, 9am – 6pm WAT</p>
                                    <a href="tel:+2347082938319" class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-900 hover:underline">
                                        +234 708 293 8319
                                    </a>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="flex items-start gap-4 p-5 border border-zinc-200 rounded-2xl hover:border-zinc-300 hover:shadow-sm transition-all duration-200">
                                <div class="flex-shrink-0 w-10 h-10 bg-zinc-50 border border-zinc-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-zinc-900 text-sm">Email</p>
                                    <p class="text-zinc-500 text-xs mt-0.5 mb-3">We respond within one business day</p>
                                    <a href="mailto:info.valexhub@gmail.com" class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-900 hover:underline break-all">
                                        info.valexhub@gmail.com
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reassurance block --}}
                    <div class="bg-zinc-950 rounded-2xl p-6 text-white">
                        <p class="text-xs font-semibold tracking-widest uppercase text-zinc-500 mb-4">What you get</p>
                        <ul class="space-y-3">
                            @php
                                $guarantees = [
                                    'No commitment required — it\'s just a conversation',
                                    'Software demo using data from your industry',
                                    'A clear answer on what setup looks like for you',
                                    'Exact pricing — no vague "contact us for quote"',
                                ];
                            @endphp
                            @foreach($guarantees as $g)
                                <li class="flex items-center gap-3 text-sm text-zinc-300">
                                    <svg class="w-4 h-4 text-zinc-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ $g }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-layouts.marketing>

<style>[x-cloak] { display: none !important; }</style>
