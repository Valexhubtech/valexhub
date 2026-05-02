<?php

use function Laravel\Folio\{name};

name('about');
?>

<x-layouts.marketing
    :seo="[
        'title' => 'About ValexHub - Nigerian Business Management Software Solutions',
        'description' => 'ValexHub builds powerful, affordable business management systems for Nigerian SMEs. Trusted by schools, hotels, pharmacies & growing businesses across Nigeria.',
        'keywords' => 'business management software Nigeria, SME software solutions, school management system, hotel management software, pharmacy management system, Nigerian software company'
    ]">
    <x-container>
        <div class="py-16">
            <!-- Header -->
            <div class="text-center mb-20 relative">
                <!-- Decorative Elements -->
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-12 h-0.5 bg-zinc-300"></div>
                        <x-phosphor-code class="w-6 h-6 text-zinc-400" />
                        <div class="w-12 h-0.5 bg-zinc-300"></div>
                    </div>
                </div>
                
                <h1 class="text-5xl font-bold text-zinc-900 mb-6 tracking-tight relative">
                    About ValexHub
                    <!-- Underline accent -->
                    <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-zinc-900 rounded"></div>
                </h1>
                
                <p class="text-xl text-zinc-600 max-w-4xl mx-auto leading-relaxed relative">
                    We build tailored digital solutions that help Nigerian businesses recover lost revenue, streamline operations, and scale profitably.
                </p>
                
                <!-- Side decorative elements -->
                <div class="hidden lg:block absolute top-1/2 left-8 transform -translate-y-1/2">
                    <div class="flex flex-col items-center space-y-3">
                        <div class="w-2 h-2 bg-zinc-300 rounded-full"></div>
                        <div class="w-1 h-16 bg-zinc-200"></div>
                        <div class="w-2 h-2 bg-zinc-300 rounded-full"></div>
                    </div>
                </div>
                
                <div class="hidden lg:block absolute top-1/2 right-8 transform -translate-y-1/2">
                    <div class="flex flex-col items-center space-y-3">
                        <div class="w-2 h-2 bg-zinc-300 rounded-full"></div>
                        <div class="w-1 h-16 bg-zinc-200"></div>
                        <div class="w-2 h-2 bg-zinc-300 rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Our Story -->
            <div class="bg-zinc-50 rounded-2xl p-12 mb-20 border border-zinc-100">
                <div class="max-w-6xl mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div class="prose prose-lg prose-zinc max-w-none">
                            <h2 class="text-3xl font-bold text-zinc-900 mb-8">Our Story</h2>
                            <p class="text-zinc-600 mb-6 text-lg leading-relaxed">ValexHub was founded with a clear mission: to empower Nigerian SMEs with powerful, affordable software solutions that help them compete in the digital economy.</p>

                            <p class="text-zinc-600 mb-6 text-lg leading-relaxed">After working with hundreds of Nigerian businesses, we realized that most were still managing operations manually or using expensive foreign software that didn't fit local workflows. We decided to build something different.</p>

                            <p class="text-zinc-600 text-lg leading-relaxed">Today, we're proud to serve schools, hotels, pharmacies, restaurants, and other businesses across Nigeria with systems specifically designed for the Nigerian market - complete with local payment integration, Naira support, and workflows that match how Nigerian businesses actually operate.</p>
                        </div>
                        <div class="flex justify-center lg:justify-end">
                            <div class="max-w-lg w-full">
                                <img src="/wave/img/community.png" alt="ValexHub Team Collaboration" class="w-full h-auto">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mission & Vision -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-20">
                <div class="bg-white rounded-xl p-8 border border-zinc-200 shadow-sm">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-zinc-900 rounded-lg flex items-center justify-center mr-4">
                            <x-phosphor-target class="w-6 h-6 text-white" />
                        </div>
                        <h2 class="text-3xl font-bold text-zinc-900">Our Mission</h2>
                    </div>
                    <p class="text-zinc-600 text-lg leading-relaxed">To empower Nigerian SMEs with world-class software solutions that help them recover lost revenue, streamline operations, and scale profitably in the digital economy. We believe every business, regardless of size, deserves access to powerful technology that can transform their operations.</p>
                </div>
                <div class="bg-white rounded-xl p-8 border border-zinc-200 shadow-sm">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-zinc-900 rounded-lg flex items-center justify-center mr-4">
                            <x-phosphor-eye class="w-6 h-6 text-white" />
                        </div>
                        <h2 class="text-3xl font-bold text-zinc-900">Our Vision</h2>
                    </div>
                    <p class="text-zinc-600 text-lg leading-relaxed">To become Nigeria's leading provider of business management software, serving over 10,000 SMEs by 2030 and contributing to the digital transformation of the Nigerian economy. We envision a Nigeria where every business operates with efficient, modern software systems.</p>
                </div>
            </div>

            <!-- What We Do -->
            <div class="bg-zinc-50 rounded-2xl p-12 mb-20 border border-zinc-100">
                <div class="max-w-5xl mx-auto">
                    <h2 class="text-3xl font-bold text-zinc-900 mb-12 text-center">What We Do</h2>
                    
                    <!-- Service Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div class="bg-white rounded-lg p-6 border border-zinc-200">
                            <div class="flex items-center mb-4">
                                <x-phosphor-buildings class="w-8 h-8 text-zinc-700 mr-3" />
                                <h3 class="text-xl font-semibold text-zinc-900">Pre-built Solutions</h3>
                            </div>
                            <p class="text-zinc-600">Ready-to-deploy systems for schools, hotels, pharmacies, and restaurants with Nigerian-specific features.</p>
                        </div>
                        <div class="bg-white rounded-lg p-6 border border-zinc-200">
                            <div class="flex items-center mb-4">
                                <x-phosphor-wrench class="w-8 h-8 text-zinc-700 mr-3" />
                                <h3 class="text-xl font-semibold text-zinc-900">Custom Development</h3>
                            </div>
                            <p class="text-zinc-600">Bespoke software built exactly to your workflow, budget, and business requirements.</p>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-zinc-600 text-lg leading-relaxed max-w-4xl mx-auto">Every system we build includes offline capability, local payment integration (bank transfers, POS), real-time reporting in Naira, and workflows designed specifically for how Nigerian businesses operate. Whether you need a ready-made solution or custom system, we deliver software that helps you recover lost revenue and scale profitably.</p>
                    </div>
                </div>
            </div>

            <!-- What Makes Us Different -->
            <div class="mb-16">
                <h2 class="text-2xl font-bold text-zinc-900 mb-8 text-center">What Makes Us Different</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="text-center p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-rocket-launch class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Innovation First</h3>
                        <p class="text-sm text-zinc-500">We leverage cutting-edge technologies and methodologies to deliver forward-thinking solutions.</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-handshake class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Collaborative Partnership</h3>
                        <p class="text-sm text-zinc-500">We work as an extension of your team, ensuring seamless integration and shared success.</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-chart-line-up class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Results-Driven</h3>
                        <p class="text-sm text-zinc-500">Every solution is designed with measurable outcomes and clear KPIs to track success.</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-gear class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Custom Solutions</h3>
                        <p class="text-sm text-zinc-500">Tailored solutions that fit your unique business requirements and growth objectives.</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-clock class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Agile Delivery</h3>
                        <p class="text-sm text-zinc-500">Fast, iterative development process that adapts to your changing needs and market demands.</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-headset class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Ongoing Support</h3>
                        <p class="text-sm text-zinc-500">Comprehensive support and maintenance to ensure your solutions continue to perform optimally.</p>
                    </div>
                </div>
            </div>

            <!-- Core Values & Impact -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-20">
                <div>
                    <h2 class="text-3xl font-bold text-zinc-900 mb-8">Our Core Values</h2>
                    <div class="space-y-6">
                        <div>
                            <h3 class="font-semibold text-zinc-900 mb-2">Excellence</h3>
                            <p class="text-sm text-zinc-500">We strive for excellence in every project, delivering solutions that exceed expectations and set new standards.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-zinc-900 mb-2">Integrity</h3>
                            <p class="text-sm text-zinc-500">We believe in honest communication, transparent processes, and ethical business practices in all our interactions.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-zinc-900 mb-2">Innovation</h3>
                            <p class="text-sm text-zinc-500">We embrace new technologies and creative approaches to solve complex challenges and drive digital transformation.</p>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-zinc-900 mb-8">Our Impact</h2>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center p-6 bg-white border border-zinc-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            <div class="text-4xl font-bold text-zinc-900 mb-2">500+</div>
                            <div class="text-sm font-medium text-zinc-600">Businesses Served</div>
                        </div>
                        <div class="text-center p-6 bg-white border border-zinc-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            <div class="text-4xl font-bold text-zinc-900 mb-2">₦2.5B+</div>
                            <div class="text-sm font-medium text-zinc-600">Revenue Tracked</div>
                        </div>
                        <div class="text-center p-6 bg-white border border-zinc-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            <div class="text-4xl font-bold text-zinc-900 mb-2">25+</div>
                            <div class="text-sm font-medium text-zinc-600">States Covered</div>
                        </div>
                        <div class="text-center p-6 bg-white border border-zinc-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            <div class="text-4xl font-bold text-zinc-900 mb-2">98%</div>
                            <div class="text-sm font-medium text-zinc-600">Client Satisfaction</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="text-center bg-gradient-to-r from-zinc-900 to-zinc-800 rounded-2xl p-12 text-white">
                <h2 class="text-3xl font-bold mb-6">Ready to Transform Your Business?</h2>
                <p class="text-zinc-200 mb-10 max-w-2xl mx-auto text-lg">Join hundreds of Nigerian businesses already using ValexHub systems to streamline operations and boost revenue.</p>
                <div class="flex flex-col gap-4 justify-center items-center md:flex-row">
                    <a href="{{ route('book-demo') }}" class="px-8 py-4 text-zinc-900 bg-white rounded-lg hover:bg-zinc-100 font-semibold transition-colors">Book Free Demo</a>
                    <a href="{{ route('products') }}" class="px-8 py-4 text-white border border-zinc-600 rounded-lg hover:bg-zinc-800 font-semibold transition-colors">View Our Products</a>
                </div>
            </div>
        </div>
    </x-container>
</x-layouts.marketing>