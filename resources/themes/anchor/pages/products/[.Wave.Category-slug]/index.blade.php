<?php
    use function Laravel\Folio\{name};
    name('products.category');
?>

<x-layouts.marketing
    :seo="[
        'title' => $category->name . ' Products - ValexHub Software Solutions',
        'description' => 'Browse our ' . $category->name . ' software products designed for Nigerian SMEs.',
    ]"
>

    @php
        $products = $category->products()->where('is_active', true)->orderBy('sort_order')->orderBy('created_at', 'DESC')->get();
        $allCategories = \Wave\Category::whereHas('products', function($query) {
            $query->where('is_active', true);
        })->get();
    @endphp

    <x-container>
        <div class="relative pt-6 pb-16">
            <x-marketing.elements.heading
                title="{{ $category->name }} Products"
                description="Specialized software solutions in the {{ $category->name }} category, designed for Nigerian businesses."
                align="center"
            />
            
            <!-- Categories Filter -->
            <div class="w-full flex items-center justify-center mb-12">
                <ul class="inline-flex self-center px-3 py-2 mt-7 w-auto text-xs font-medium rounded-full border bg-zinc-100 border-zinc-200 text-zinc-600">
                    <li class="mr-4 font-bold uppercase text-zinc-800 md:block hidden">Categories:</li>
                    <li><a href="{{ route('products') }}" class="hover:text-zinc-800">View All</a></li>
                    <li class="mx-2">&middot;</li>
                    @foreach($allCategories as $cat)
                        <li>
                            <a href="{{ route('products.category', ['category' => $cat->slug]) }}" 
                               class="@if($cat->id == $category->id) text-zinc-800 font-semibold @else hover:text-zinc-800 @endif">
                                {{ $cat->name }}
                            </a>
                        </li>
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
                        <div class="p-6 bg-white border border-zinc-200 rounded-lg hover:shadow-md transition-shadow">
                            @if($product->icon)
                                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg overflow-hidden">
                                    <img src="{{ Storage::url($product->icon) }}" alt="{{ $product->name }}" class="w-8 h-8 object-contain">
                                </div>
                            @else
                                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                                    <x-phosphor-package class="w-6 h-6 text-zinc-600" />
                                </div>
                            @endif
                            
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
                                <a href="{{ route('products.show', ['category' => $category->slug, 'product' => $product->slug]) }}" class="text-center px-4 py-2 text-sm font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-800">View Details</a>
                                <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20know%20more%20about%20{{ urlencode($product->name) }}" target="_blank" class="text-center px-4 py-2 text-sm font-medium text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50">Get Quote</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <x-phosphor-package class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                    <h3 class="text-xl font-semibold text-zinc-900 mb-2">No Products in {{ $category->name }}</h3>
                    <p class="text-zinc-500 mb-6">We don't currently have any products in this category.</p>
                    <a href="{{ route('products') }}" class="px-6 py-3 text-white bg-zinc-900 rounded-md hover:bg-zinc-800">View All Products</a>
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