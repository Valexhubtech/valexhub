<?php
    use function Laravel\Folio\{name};
    name('products');

    // Get all active products
    $products = \Wave\Product::where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('created_at', 'DESC')
        ->with('category')
        ->get();
    
    // Get categories that have products
    $categories = \Wave\Category::whereHas('products', function($query) {
        $query->where('is_active', true);
    })->get();
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
                        <li><a href="{{ route('products.category', ['category' => $category->slug]) }}" class="hover:text-zinc-800">{{ $category->name }}</a></li>
                        @if(!$loop->last)
                            <li class="mx-2">&middot;</li>
                        @endif
                    @endforeach
                </ul>
            </div>

            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($products as $product)
                        <div class="overflow-hidden bg-white border border-zinc-200 rounded-lg hover:shadow-md transition-shadow">
                            <!-- Cover Image Section -->
                            <div class="relative">
                                @if($product->icon)
                                    <img src="{{ Storage::url($product->icon) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-zinc-100 flex items-center justify-center">
                                        <x-phosphor-package class="w-16 h-16 text-zinc-600" />
                                    </div>
                                @endif
                                
                                <!-- Category Badge - Top Right -->
                                @if($product->category)
                                    <div class="absolute top-3 right-3">
                                        <span class="px-3 py-1.5 text-xs font-medium text-zinc-600 bg-white/90 backdrop-blur-sm rounded-full shadow-sm">{{ $product->category->name }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-6">
                                <div class="text-center mb-4">
                                    <h3 class="text-lg font-semibold text-zinc-900 mb-2">{{ $product->name }}</h3>
                                    @if($product->short_description)
                                        <p class="text-sm text-zinc-500 mb-4">{{ $product->short_description }}</p>
                                    @endif
                                </div>

                                @if($product->low_price || $product->high_price)
                                    <div class="text-center mb-4">
                                        <span class="text-lg font-bold text-zinc-900">{{ $product->price_range }}</span>
                                    </div>
                                @endif

                                @if($product->features && count($product->features) > 0)
                                    <ul class="text-xs text-zinc-600 mb-4 space-y-1">
                                        @foreach(array_slice($product->features, 0, 3) as $feature)
                                            <li class="flex items-center">
                                                <x-phosphor-check class="w-3 h-3 text-green-500 mr-2 flex-shrink-0" />
                                                {{ $feature }}
                                            </li>
                                        @endforeach
                                        @if(count($product->features) > 3)
                                            <li class="text-zinc-400">+{{ count($product->features) - 3 }} more features</li>
                                        @endif
                                    </ul>
                                @endif
                                
                                <div class="flex flex-col gap-2">
                                    <a href="{{ $product->category ? route('products.show', ['category' => $product->category->slug, 'product' => $product->slug]) : route('products') }}" class="text-center px-4 py-2 text-sm font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-800">View Details</a>
                                    <x-elements.quote-demo-modal :product="$product" size="compact" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <x-phosphor-package class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                    <h3 class="text-xl font-semibold text-zinc-900 mb-2">No Products Available</h3>
                    <p class="text-zinc-500">Products will appear here once they are added through the admin panel.</p>
                </div>
            @endif

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