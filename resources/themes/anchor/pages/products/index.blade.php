<?php
    use function Laravel\Folio\{name};
    name('products');

    // Get all active pages to display as products
    $products = \Wave\Page::where('status', 'ACTIVE')->get();
    $categories = \Wave\Category::all();
?>

<x-layouts.marketing
    :seo="[
        'title' => 'Products - ValexHub Software Solutions',
        'description' => 'Discover our complete range of business management systems for Nigerian SMEs. From school management to hotel booking systems, we have the tools you need.',
    ]"
>
    <x-container>
        <div class="relative pt-6 pb-16">
            <x-marketing.elements.heading
                title="Our Software Products"
                description="Complete business management systems designed specifically for Nigerian SMEs. Choose from our ready-to-deploy solutions or get a custom system built for your needs."
                align="center"
            />
            
            <!-- Categories Filter -->
            <div class="w-full flex items-center justify-center mb-12">
                <ul class="inline-flex self-center px-3 py-2 mt-7 w-auto text-xs font-medium rounded-full border bg-zinc-100 border-zinc-200 text-zinc-600">
                    <li class="mr-4 font-bold uppercase text-zinc-800 md:block hidden">Categories:</li>
                    <li><a href="{{ route('products') }}" class="text-zinc-800">View All</a></li>
                    <li class="mx-2">&middot;</li>
                    @foreach($categories as $category)
                        <li><a href="{{ route('blog.category', ['category' => $category]) }}" class="hover:text-zinc-800">{{ $category->name }}</a></li>
                        @if(!$loop->last)
                            <li class="mx-2">&middot;</li>
                        @endif
                    @endforeach
                </ul>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                <!-- School Management System -->
                <div class="p-6 bg-white border border-zinc-200 rounded-lg hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                        <x-phosphor-graduation-cap class="w-6 h-6 text-zinc-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-center text-zinc-900 mb-2">School Management System</h3>
                    <p class="text-sm text-zinc-500 text-center mb-4">Complete student, fees, attendance and reporting tools for private schools.</p>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('products.school') }}" class="text-center px-4 py-2 text-sm font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-800">View Details</a>
                        <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20know%20more%20about%20the%20School%20Management%20System" target="_blank" class="text-center px-4 py-2 text-sm font-medium text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50">Get Quote</a>
                    </div>
                </div>

                <!-- Hotel & Booking System -->
                <div class="p-6 bg-white border border-zinc-200 rounded-lg hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                        <x-phosphor-bed class="w-6 h-6 text-zinc-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-center text-zinc-900 mb-2">Hotel & Booking System</h3>
                    <p class="text-sm text-zinc-500 text-center mb-4">Room management, reservations, POS and guest experience in one system.</p>
                    <div class="flex flex-col gap-2">
                        <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20know%20more%20about%20the%20Hotel%20%26%20Booking%20System" target="_blank" class="text-center px-4 py-2 text-sm font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-800">Get Quote</a>
                        <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20know%20more%20about%20the%20Hotel%20%26%20Booking%20System" target="_blank" class="text-center px-4 py-2 text-sm font-medium text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50">Get Quote</a>
                    </div>
                </div>

                <!-- Pharmacy POS & Inventory -->
                <div class="p-6 bg-white border border-zinc-200 rounded-lg hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                        <x-phosphor-first-aid-kit class="w-6 h-6 text-zinc-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-center text-zinc-900 mb-2">Pharmacy POS & Inventory</h3>
                    <p class="text-sm text-zinc-500 text-center mb-4">Stock control, expiry alerts, sales and supplier management for pharmacies.</p>
                    <div class="flex flex-col gap-2">
                        <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20know%20more%20about%20the%20Pharmacy%20POS%20%26%20Inventory%20System" target="_blank" class="text-center px-4 py-2 text-sm font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-800">Get Quote</a>
                        <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20know%20more%20about%20the%20Pharmacy%20POS%20%26%20Inventory%20System" target="_blank" class="text-center px-4 py-2 text-sm font-medium text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50">Get Quote</a>
                    </div>
                </div>

                <!-- Restaurant POS & Kitchen -->
                <div class="p-6 bg-white border border-zinc-200 rounded-lg hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                        <x-phosphor-fork-knife class="w-6 h-6 text-zinc-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-center text-zinc-900 mb-2">Restaurant POS & Kitchen</h3>
                    <p class="text-sm text-zinc-500 text-center mb-4">Table ordering, kitchen display, inventory and daily sales reporting.</p>
                    <div class="flex flex-col gap-2">
                        <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20know%20more%20about%20the%20Restaurant%20POS%20%26%20Kitchen%20System" target="_blank" class="text-center px-4 py-2 text-sm font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-800">Get Quote</a>
                        <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20know%20more%20about%20the%20Restaurant%20POS%20%26%20Kitchen%20System" target="_blank" class="text-center px-4 py-2 text-sm font-medium text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50">Get Quote</a>
                    </div>
                </div>

                <!-- Property Management System -->
                <div class="p-6 bg-white border border-zinc-200 rounded-lg hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                        <x-phosphor-buildings class="w-6 h-6 text-zinc-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-center text-zinc-900 mb-2">Property Management System</h3>
                    <p class="text-sm text-zinc-500 text-center mb-4">Tenant management, rent collection, maintenance and invoicing.</p>
                    <div class="flex flex-col gap-2">
                        <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20know%20more%20about%20the%20Property%20Management%20System" target="_blank" class="text-center px-4 py-2 text-sm font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-800">Get Quote</a>
                        <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20know%20more%20about%20the%20Property%20Management%20System" target="_blank" class="text-center px-4 py-2 text-sm font-medium text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50">Get Quote</a>
                    </div>
                </div>

                <!-- Real Estate Agency Software -->
                <div class="p-6 bg-white border border-zinc-200 rounded-lg hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                        <x-phosphor-house class="w-6 h-6 text-zinc-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-center text-zinc-900 mb-2">Real Estate Agency Software</h3>
                    <p class="text-sm text-zinc-500 text-center mb-4">Property listings, client management and commission tracking.</p>
                    <div class="flex flex-col gap-2">
                        <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20know%20more%20about%20the%20Real%20Estate%20Agency%20Software" target="_blank" class="text-center px-4 py-2 text-sm font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-800">Get Quote</a>
                        <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20know%20more%20about%20the%20Real%20Estate%20Agency%20Software" target="_blank" class="text-center px-4 py-2 text-sm font-medium text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50">Get Quote</a>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="mt-16 text-center">
                <h3 class="text-2xl font-bold text-zinc-900 mb-4">Don't See What You Need?</h3>
                <p class="text-zinc-500 mb-6">We also build fully custom software solutions tailored to your specific business requirements.</p>
                <div class="flex flex-col gap-3 justify-center items-center md:flex-row">
                    <a href="{{ route('custom-software') }}" class="px-6 py-3 text-white bg-zinc-900 rounded-md hover:bg-zinc-800">Get Custom Software</a>
                    <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20discuss%20a%20custom%20software%20solution" target="_blank" class="px-6 py-3 text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50">Book Consultation</a>
                </div>
            </div>
        </div>
    </x-container>
</x-layouts.marketing>