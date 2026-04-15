<?php
    use function Laravel\Folio\{name};
    name('custom-software');
?>

<x-layouts.marketing
    :seo="[
        'title' => 'Custom Software Development - ValexHub',
        'description' => 'Get bespoke software solutions built exactly to your business workflow. From CRM systems to inventory management, we build what you need.',
    ]"
>
    <x-container>
        <div class="py-16">
            <!-- Header -->
            <div class="text-center mb-16">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-zinc-100 rounded-xl">
                    <x-phosphor-code class="w-8 h-8 text-zinc-600" />
                </div>
                <h1 class="text-4xl font-bold text-zinc-900 mb-4">Custom Software Development</h1>
                <p class="text-xl text-zinc-500 max-w-3xl mx-auto">When ready-made solutions don't fit your unique workflow, we build bespoke software tailored exactly to your business processes and requirements.</p>
            </div>

            <!-- Process -->
            <div class="mb-16">
                <h2 class="text-2xl font-bold text-zinc-900 mb-8 text-center">Our Development Process</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <span class="font-bold text-zinc-600">1</span>
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Discovery Call</h3>
                        <p class="text-sm text-zinc-500">We understand your business, current challenges, and desired outcomes.</p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <span class="font-bold text-zinc-600">2</span>
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Requirements & Quote</h3>
                        <p class="text-sm text-zinc-500">Detailed scope document with features, timeline, and fixed-price quote.</p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <span class="font-bold text-zinc-600">3</span>
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Build & Test</h3>
                        <p class="text-sm text-zinc-500">Agile development with weekly progress updates and your feedback.</p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <span class="font-bold text-zinc-600">4</span>
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Deploy & Support</h3>
                        <p class="text-sm text-zinc-500">Launch, train your team, and provide ongoing support and updates.</p>
                    </div>
                </div>
            </div>

            <!-- What We Build -->
            <div class="bg-zinc-50 rounded-xl p-8 mb-16">
                <h2 class="text-2xl font-bold text-zinc-900 mb-8 text-center">What We Can Build For You</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-lg">
                        <h3 class="font-semibold text-zinc-900 mb-2">CRM Systems</h3>
                        <p class="text-sm text-zinc-500">Customer relationship management tailored to your sales process.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg">
                        <h3 class="font-semibold text-zinc-900 mb-2">Inventory Management</h3>
                        <p class="text-sm text-zinc-500">Stock control, supplier management, and automated reordering.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg">
                        <h3 class="font-semibold text-zinc-900 mb-2">HR & Payroll</h3>
                        <p class="text-sm text-zinc-500">Employee management, attendance, and automated payroll processing.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg">
                        <h3 class="font-semibold text-zinc-900 mb-2">Accounting Software</h3>
                        <p class="text-sm text-zinc-500">Invoicing, expense tracking, and financial reporting.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg">
                        <h3 class="font-semibold text-zinc-900 mb-2">E-commerce Platforms</h3>
                        <p class="text-sm text-zinc-500">Online stores with payment integration and order management.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg">
                        <h3 class="font-semibold text-zinc-900 mb-2">Workflow Automation</h3>
                        <p class="text-sm text-zinc-500">Automate repetitive tasks and business processes.</p>
                    </div>
                </div>
            </div>

            <!-- Why Choose Us -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 mb-6">Why Choose ValexHub for Custom Development?</h2>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-zinc-900">Fixed-Price Projects</h3>
                                <p class="text-sm text-zinc-500">No hourly rates or surprise costs. You know the exact price upfront.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-zinc-900">Nigerian-First Design</h3>
                                <p class="text-sm text-zinc-500">Built for Nigerian businesses with local payment methods and workflows.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-zinc-900">Ongoing Support</h3>
                                <p class="text-sm text-zinc-500">We maintain and update your software as your business grows.</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-zinc-900 mb-4">Recent Custom Projects</h3>
                    <div class="space-y-4">
                        <div class="p-4 border border-zinc-200 rounded-lg">
                            <h4 class="font-semibold text-zinc-900">Manufacturing ERP</h4>
                            <p class="text-sm text-zinc-500">Complete production planning, inventory, and sales system for a Lagos manufacturer.</p>
                        </div>
                        <div class="p-4 border border-zinc-200 rounded-lg">
                            <h4 class="font-semibold text-zinc-900">Logistics Platform</h4>
                            <p class="text-sm text-zinc-500">Package tracking and route optimization for a delivery company.</p>
                        </div>
                        <div class="p-4 border border-zinc-200 rounded-lg">
                            <h4 class="font-semibold text-zinc-900">Medical Records System</h4>
                            <p class="text-sm text-zinc-500">Patient management and electronic health records for a private hospital.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="text-center mb-16">
                <h2 class="text-2xl font-bold text-zinc-900 mb-4">Investment Range</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="p-6 border border-zinc-200 rounded-lg">
                        <h3 class="text-lg font-semibold text-zinc-900 mb-2">Simple Systems</h3>
                        <div class="text-2xl font-bold text-zinc-900 mb-4">₦300K - ₦800K</div>
                        <p class="text-sm text-zinc-500">Basic CRUD operations, simple workflows, up to 5 modules</p>
                    </div>
                    <div class="p-6 border border-zinc-200 rounded-lg bg-zinc-50">
                        <h3 class="text-lg font-semibold text-zinc-900 mb-2">Medium Systems</h3>
                        <div class="text-2xl font-bold text-zinc-900 mb-4">₦800K - ₦2M</div>
                        <p class="text-sm text-zinc-500">Complex workflows, integrations, reporting, mobile apps</p>
                    </div>
                    <div class="p-6 border border-zinc-200 rounded-lg">
                        <h3 class="text-lg font-semibold text-zinc-900 mb-2">Enterprise Systems</h3>
                        <div class="text-2xl font-bold text-zinc-900 mb-4">₦2M+</div>
                        <p class="text-sm text-zinc-500">Full ERP systems, multiple integrations, advanced features</p>
                    </div>
                </div>
                <p class="text-sm text-zinc-500 mt-6">All projects include design, development, testing, deployment, training, and 3 months support.</p>
            </div>

            <!-- CTA -->
            <div class="text-center">
                <h2 class="text-2xl font-bold text-zinc-900 mb-4">Ready to Build Your Custom Solution?</h2>
                <p class="text-zinc-500 mb-8 max-w-2xl mx-auto">Let's discuss your requirements and provide you with a detailed proposal and fixed-price quote.</p>
                <div class="flex flex-col gap-3 justify-center items-center md:flex-row">
                    <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20discuss%20a%20custom%20software%20project" target="_blank" class="px-8 py-3 text-white bg-zinc-900 rounded-md hover:bg-zinc-800">Start Your Project</a>
                    <a href="{{ route('book-demo') }}" class="px-8 py-3 text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50">Book Consultation</a>
                </div>
            </div>
        </div>
    </x-container>
</x-layouts.marketing>