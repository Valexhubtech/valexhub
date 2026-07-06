<?php
    use function Laravel\Folio\{middleware, name};
    middleware('auth');
    name('dashboard.products');

    $prebuilt = \Wave\Product::where('is_active', true)
        ->where('type', 'prebuilt')
        ->orderBy('sort_order')
        ->orderBy('id')
        ->with(['category', 'pricingOptions'])
        ->get();

    $customProducts = \Wave\Product::where('is_active', true)
        ->where('type', 'custom')
        ->orderBy('sort_order')
        ->orderBy('id')
        ->with('category')
        ->get();
?>

<x-layouts.app>
    <x-app.container x-data class="lg:space-y-6" x-cloak>

        <x-app.heading
            title="Software Products"
            description="Choose a product, pick your deployment type and plan, and go live instantly."
            :border="false"
        />

        @if(session('error'))
            <div class="p-4 text-sm text-red-700 bg-red-100 rounded-md">{{ session('error') }}</div>
        @endif

        {{-- Pre-built products --}}
        @if($prebuilt->count() > 0)
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($prebuilt as $product)
                    <div class="flex flex-col overflow-hidden bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700 hover:shadow-md transition-shadow">
                        <div class="relative">
                            @if($product->icon)
                                <img src="{{ Storage::url($product->icon) }}" alt="{{ $product->name }}" class="object-cover w-full h-40">
                            @else
                                <div class="flex items-center justify-center w-full h-40 bg-zinc-100 dark:bg-neutral-700">
                                    <x-phosphor-package class="w-12 h-12 text-zinc-400" />
                                </div>
                            @endif
                            @if($product->category)
                                <div class="absolute top-3 right-3">
                                    <span class="px-2.5 py-1 text-xs font-medium bg-white/90 text-zinc-600 rounded-full">{{ $product->category->name }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col flex-1 p-5">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $product->name }}</h3>

                            @if($product->short_description)
                                <p class="mt-1 text-sm text-zinc-500 dark:text-neutral-400">{{ $product->short_description }}</p>
                            @endif

                            {{-- Available deployment types --}}
                            <div class="flex gap-2 mt-3">
                                @if($product->supportsCloud())
                                    <span class="px-2 py-0.5 text-xs bg-blue-50 text-blue-600 rounded-full border border-blue-100">Cloud</span>
                                @endif
                                @if($product->supportsOnPrem())
                                    <span class="px-2 py-0.5 text-xs bg-orange-50 text-orange-600 rounded-full border border-orange-100">On-Prem</span>
                                @endif
                                @if(!$product->supportsCloud() && !$product->supportsOnPrem())
                                    <span class="px-2 py-0.5 text-xs bg-zinc-100 text-zinc-500 rounded-full">Contact for pricing</span>
                                @endif
                            </div>

                            @if($product->features && count($product->features) > 0)
                                <ul class="mt-3 space-y-1">
                                    @foreach(array_slice($product->features, 0, 3) as $feature)
                                        <li class="flex items-center text-xs text-zinc-600 dark:text-zinc-400">
                                            <x-phosphor-check class="w-3 h-3 text-green-500 mr-2 flex-shrink-0" />
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <div class="mt-auto pt-4 space-y-2">
                                @if($product->supportsCloud() || $product->supportsOnPrem())
                                    <a href="{{ route('dashboard.product.checkout', ['product' => $product->slug]) }}"
                                       class="block w-full px-4 py-2 text-sm font-medium text-center text-white bg-zinc-900 rounded-md hover:bg-zinc-800">
                                        Configure & Order
                                    </a>
                                @else
                                    <x-elements.quote-demo-modal :product="$product" size="compact" />
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center">
                <x-phosphor-package class="w-16 h-16 mx-auto mb-4 text-zinc-400" />
                <h3 class="mb-2 text-xl font-semibold text-zinc-900 dark:text-white">No Products Available Yet</h3>
                <p class="text-zinc-500">Products will appear here once they are added through the admin panel.</p>
            </div>
        @endif

        {{-- Custom Software Section --}}
        <div class="p-6 mt-4 border border-dashed border-zinc-300 rounded-xl dark:border-neutral-600 text-center">
            <x-phosphor-wrench-duotone class="w-10 h-10 mx-auto mb-3 text-zinc-400" />
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-1">Need something custom?</h3>
            <p class="text-sm text-zinc-500 mb-4">We build fully tailored software solutions. Tell us what you need and we'll put together a quote.</p>

            @if($customProducts->count() > 0)
                <div class="flex flex-wrap justify-center gap-3 mb-4">
                    @foreach($customProducts as $cp)
                        <x-elements.quote-demo-modal :product="$cp" size="compact" />
                    @endforeach
                </div>
            @else
                <a href="{{ route('custom-software') }}" class="inline-block px-6 py-2 text-sm font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-800">
                    Request a Quote
                </a>
            @endif
        </div>

    </x-app.container>
</x-layouts.app>
