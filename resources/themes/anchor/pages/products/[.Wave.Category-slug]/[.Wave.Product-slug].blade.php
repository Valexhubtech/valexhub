<?php
    use function Laravel\Folio\{name};
    name('products.show');
?>

<x-layouts.marketing
    :seo="[
        'title' => $product->name . ' - ValexHub Software Solutions',
        'description' => $product->short_description ?? 'Professional software solution designed for Nigerian businesses.',
    ]"
>
    <x-container>
        <div class="relative pt-6 pb-16">
            
            <x-elements.back-button
                class="max-w-6xl mx-auto mt-4 md:mt-8"
                text="back to products"
                :href="route('products')"
            />

            <!-- Product Header -->
            <div class="max-w-6xl mx-auto mt-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    
                    <!-- Product Info -->
                    <div>
                        @if($product->category)
                            <span class="inline-block px-3 py-1 text-sm text-zinc-500 bg-zinc-100 rounded-full mb-4">{{ $product->category->name }}</span>
                        @endif
                        
                        <h1 class="text-4xl font-bold text-zinc-900 mb-4">{{ $product->name }}</h1>
                        
                        @if($product->short_description)
                            <p class="text-xl text-zinc-600 mb-6">{{ $product->short_description }}</p>
                        @endif

                        @if($product->low_price || $product->high_price)
                            <div class="mb-6">
                                <span class="text-3xl font-bold text-zinc-900">{{ $product->price_range }}</span>
                            </div>
                        @endif

                        <div class="flex flex-col gap-3 md:flex-row">
                            <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20get%20a%20quote%20for%20{{ urlencode($product->name) }}" target="_blank" class="px-6 py-3 text-white bg-zinc-900 rounded-md hover:bg-zinc-800 text-center">Get Quote</a>
                            <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20schedule%20a%20demo%20for%20{{ urlencode($product->name) }}" target="_blank" class="px-6 py-3 text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50 text-center">Schedule Demo</a>
                        </div>
                    </div>

                    <!-- Product Image/Icon -->
                    <div class="flex justify-center lg:justify-end">
                        @if($product->first_image)
                            <img src="{{ Storage::url($product->first_image) }}" alt="{{ $product->name }}" class="w-full max-w-md h-auto rounded-lg shadow-lg">
                        @elseif($product->icon)
                            <div class="flex items-center justify-center w-64 h-64 bg-zinc-100 rounded-lg">
                                <img src="{{ Storage::url($product->icon) }}" alt="{{ $product->name }}" class="w-32 h-32 object-contain">
                            </div>
                        @else
                            <div class="flex items-center justify-center w-64 h-64 bg-zinc-100 rounded-lg">
                                <x-phosphor-package class="w-32 h-32 text-zinc-400" />
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Product Features -->
            @if($product->features && count($product->features) > 0)
                <div class="max-w-6xl mx-auto mt-16">
                    <h2 class="text-2xl font-bold text-zinc-900 mb-8">Key Features</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($product->features as $feature)
                            <div class="flex items-start">
                                <x-phosphor-check class="w-5 h-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" />
                                <span class="text-zinc-700">{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Product Description -->
            @if($product->description)
                <div class="max-w-6xl mx-auto mt-16">
                    <h2 class="text-2xl font-bold text-zinc-900 mb-8">About {{ $product->name }}</h2>
                    <div class="prose prose-lg text-zinc-700 max-w-none">
                        {!! $product->description !!}
                    </div>
                </div>
            @endif

            <!-- Product Images Gallery -->
            @if($product->images && count($product->images) > 1)
                <div class="max-w-6xl mx-auto mt-16">
                    <h2 class="text-2xl font-bold text-zinc-900 mb-8">Gallery</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($product->images as $image)
                            <img src="{{ Storage::url($image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded-lg shadow-md">
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Related Products -->
            @if($product->category)
                @php
                    $relatedProducts = $product->category->products()
                        ->where('id', '!=', $product->id)
                        ->where('is_active', true)
                        ->limit(3)
                        ->get();
                @endphp

                @if($relatedProducts->count() > 0)
                    <div class="max-w-6xl mx-auto mt-16">
                        <h2 class="text-2xl font-bold text-zinc-900 mb-8">More {{ $product->category->name }} Products</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($relatedProducts as $relatedProduct)
                                <div class="p-6 bg-white border border-zinc-200 rounded-lg hover:shadow-md transition-shadow">
                                    @if($relatedProduct->icon)
                                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg overflow-hidden">
                                            <img src="{{ Storage::url($relatedProduct->icon) }}" alt="{{ $relatedProduct->name }}" class="w-8 h-8 object-contain">
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                                            <x-phosphor-package class="w-6 h-6 text-zinc-600" />
                                        </div>
                                    @endif
                                    
                                    <div class="text-center mb-4">
                                        <h3 class="text-lg font-semibold text-zinc-900 mb-2">{{ $relatedProduct->name }}</h3>
                                        @if($relatedProduct->short_description)
                                            <p class="text-sm text-zinc-500">{{ $relatedProduct->short_description }}</p>
                                        @endif
                                    </div>

                                    @if($relatedProduct->low_price || $relatedProduct->high_price)
                                        <div class="text-center mb-4">
                                            <span class="text-lg font-bold text-zinc-900">{{ $relatedProduct->price_range }}</span>
                                        </div>
                                    @endif
                                    
                                    <div class="flex flex-col gap-2">
                                        <a href="{{ route('products.show', ['category' => $relatedProduct->category->slug, 'product' => $relatedProduct->slug]) }}" class="text-center px-4 py-2 text-sm font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-800">View Details</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <!-- CTA Section -->
            <div class="max-w-6xl mx-auto mt-16 text-center bg-zinc-50 rounded-lg p-8">
                <h3 class="text-2xl font-bold text-zinc-900 mb-4">Ready to Get Started?</h3>
                <p class="text-zinc-600 mb-6">Contact us for pricing, customization options, or to schedule a personalized demo.</p>
                <div class="flex flex-col gap-3 justify-center items-center md:flex-row">
                    <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27m%20interested%20in%20{{ urlencode($product->name) }}%20and%20would%20like%20to%20get%20started" target="_blank" class="px-6 py-3 text-white bg-zinc-900 rounded-md hover:bg-zinc-800">Contact Sales</a>
                    <a href="{{ route('custom-software') }}" class="px-6 py-3 text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50">Need Something Custom?</a>
                </div>
            </div>
        </div>
    </x-container>
</x-layouts.marketing>