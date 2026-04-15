<?php
    use function Laravel\Folio\{name};
    name('about');
?>

<x-layouts.marketing
    :seo="[
        'title' => 'About ValexHub - Nigerian Software Solutions Company',
        'description' => 'Learn about ValexHub, a leading Nigerian software company specializing in business management systems for SMEs across schools, hotels, pharmacies, and more.',
    ]"
>
    <x-container>
        <div class="py-16">
            <!-- Header -->
            <div class="text-center mb-16">
                <h1 class="text-4xl font-bold text-zinc-900 mb-4">About ValexHub</h1>
                <p class="text-xl text-zinc-500 max-w-3xl mx-auto">We're on a mission to digitally transform Nigerian businesses with powerful, affordable software solutions that drive growth and efficiency.</p>
            </div>

            <!-- Mission & Vision -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-16">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 mb-6">Our Mission</h2>
                    <p class="text-zinc-500 mb-6">To empower Nigerian SMEs with world-class software solutions that help them recover lost revenue, streamline operations, and scale profitably in the digital economy.</p>
                    <p class="text-zinc-500">We believe every business, regardless of size, deserves access to powerful technology that can transform their operations and drive sustainable growth.</p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 mb-6">Our Vision</h2>
                    <p class="text-zinc-500 mb-6">To become Nigeria's leading provider of business management software, serving over 10,000 SMEs by 2030 and contributing to the digital transformation of the Nigerian economy.</p>
                    <p class="text-zinc-500">We envision a Nigeria where every school, hotel, pharmacy, restaurant, and small business operates with efficient, modern software systems.</p>
                </div>
            </div>

            <!-- Story -->
            <div class="bg-zinc-50 rounded-xl p-8 mb-16">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-2xl font-bold text-zinc-900 mb-6 text-center">Our Story</h2>
                    <div class="prose prose-zinc max-w-none">
                        <p class="text-zinc-500 mb-4">ValexHub was founded in 2021 when our team realized that most Nigerian businesses were still managing their operations manually or with expensive, complex software designed for international markets.</p>
                        
                        <p class="text-zinc-500 mb-4">After visiting over 100 Nigerian businesses across different sectors, we discovered common pain points: lost revenue due to poor tracking, inefficient manual processes, lack of proper reporting, and expensive foreign software that didn't fit local workflows.</p>
                        
                        <p class="text-zinc-500 mb-4">We decided to build something different — affordable, powerful business management systems designed specifically for Nigerian SMEs, with local payment methods, Naira support, and workflows that match how Nigerian businesses actually operate.</p>
                        
                        <p class="text-zinc-500">Today, we're proud to serve hundreds of schools, hotels, pharmacies, restaurants, and other businesses across Nigeria, helping them achieve operational efficiency and sustainable growth.</p>
                    </div>
                </div>
            </div>

            <!-- What Makes Us Different -->
            <div class="mb-16">
                <h2 class="text-2xl font-bold text-zinc-900 mb-8 text-center">What Makes Us Different</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="text-center p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-map-pin class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Nigerian-First Design</h3>
                        <p class="text-sm text-zinc-500">Built specifically for Nigerian businesses with local workflows, payment methods, and regulatory compliance.</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-currency-ngn class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Affordable Pricing</h3>
                        <p class="text-sm text-zinc-500">Transparent, affordable pricing designed for Nigerian SME budgets — no hidden costs or surprise fees.</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-headset class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Local Support</h3>
                        <p class="text-sm text-zinc-500">Nigerian support team available via WhatsApp, phone, and on-site visits when needed.</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-wifi-slash class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Offline Capability</h3>
                        <p class="text-sm text-zinc-500">Systems work reliably even with poor internet connectivity — critical for Nigerian businesses.</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-rocket-launch class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Fast Implementation</h3>
                        <p class="text-sm text-zinc-500">Get up and running in 5-10 days with full training and data migration included.</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-chart-line-up class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Proven Results</h3>
                        <p class="text-sm text-zinc-500">Our clients typically see 40-60% reduction in revenue loss and 15+ hours saved weekly.</p>
                    </div>
                </div>
            </div>

            <!-- Team Values -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-16">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 mb-6">Our Values</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-semibold text-zinc-900 mb-2">🇳🇬 Nigerian Pride</h3>
                            <p class="text-sm text-zinc-500">We're proud to be Nigerian and believe in the potential of Nigerian businesses to compete globally.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-zinc-900 mb-2">🤝 Customer Success</h3>
                            <p class="text-sm text-zinc-500">Your success is our success. We're only successful when our software helps your business grow.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-zinc-900 mb-2">💎 Quality First</h3>
                            <p class="text-sm text-zinc-500">We build software that works reliably, performs well, and provides real business value.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-zinc-900 mb-2">🚀 Continuous Innovation</h3>
                            <p class="text-sm text-zinc-500">We constantly improve our software based on customer feedback and changing business needs.</p>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 mb-6">Our Impact</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center p-4 bg-white border border-zinc-200 rounded-lg">
                            <div class="text-2xl font-bold text-zinc-900">500+</div>
                            <div class="text-sm text-zinc-500">Businesses Served</div>
                        </div>
                        <div class="text-center p-4 bg-white border border-zinc-200 rounded-lg">
                            <div class="text-2xl font-bold text-zinc-900">₦2.5B+</div>
                            <div class="text-sm text-zinc-500">Revenue Tracked</div>
                        </div>
                        <div class="text-center p-4 bg-white border border-zinc-200 rounded-lg">
                            <div class="text-2xl font-bold text-zinc-900">25+</div>
                            <div class="text-sm text-zinc-500">States Covered</div>
                        </div>
                        <div class="text-center p-4 bg-white border border-zinc-200 rounded-lg">
                            <div class="text-2xl font-bold text-zinc-900">98%</div>
                            <div class="text-sm text-zinc-500">Client Satisfaction</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="text-center bg-zinc-50 rounded-xl p-8">
                <h2 class="text-2xl font-bold text-zinc-900 mb-4">Ready to Transform Your Business?</h2>
                <p class="text-zinc-500 mb-8 max-w-2xl mx-auto">Join hundreds of Nigerian businesses already using ValexHub systems to streamline operations and boost revenue.</p>
                <div class="flex flex-col gap-3 justify-center items-center md:flex-row">
                    <a href="{{ route('book-demo') }}" class="px-8 py-3 text-white bg-zinc-900 rounded-md hover:bg-zinc-800">Book Free Demo</a>
                    <a href="{{ route('products') }}" class="px-8 py-3 text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50">View Our Products</a>
                </div>
            </div>
        </div>
    </x-container>
</x-layouts.marketing>