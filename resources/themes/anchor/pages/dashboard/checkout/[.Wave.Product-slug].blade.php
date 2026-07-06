<?php
    use function Laravel\Folio\{middleware, name};
    middleware('auth');
    name('dashboard.product.checkout');
?>

<x-layouts.app>
    <x-app.container x-data class="lg:space-y-6" x-cloak>

        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard.products') }}" wire:navigate class="flex items-center text-sm text-zinc-500 hover:text-zinc-900">
                <x-phosphor-arrow-left class="w-4 h-4 mr-1" /> Back to Products
            </a>
        </div>

        <x-app.heading
            :title="'Order — ' . $product->name"
            :description="$product->short_description ?? 'Configure your deployment and complete your order below.'"
            :border="false"
        />

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

            {{-- Left: Product summary --}}
            <div class="lg:col-span-1">
                <div class="p-5 bg-white border border-zinc-200 rounded-lg dark:bg-neutral-800 dark:border-neutral-700 sticky top-6">
                    @if($product->icon)
                        <img src="{{ Storage::url($product->icon) }}" alt="{{ $product->name }}" class="w-full h-32 object-cover rounded-md mb-4">
                    @else
                        <div class="flex items-center justify-center w-full h-32 bg-zinc-100 dark:bg-neutral-700 rounded-md mb-4">
                            <x-phosphor-package class="w-12 h-12 text-zinc-400" />
                        </div>
                    @endif
                    <h2 class="font-semibold text-zinc-900 dark:text-white">{{ $product->name }}</h2>
                    @if($product->short_description)
                        <p class="mt-1 text-sm text-zinc-500">{{ $product->short_description }}</p>
                    @endif
                    @if($product->features && count($product->features) > 0)
                        <ul class="mt-4 space-y-1.5">
                            @foreach($product->features as $feature)
                                <li class="flex items-center text-xs text-zinc-600 dark:text-zinc-400">
                                    <x-phosphor-check class="w-3 h-3 text-green-500 mr-2 flex-shrink-0" />
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-neutral-700">
                        <p class="text-xs text-zinc-400">Need something customized? Contact us after ordering or request a custom quote.</p>
                        <x-elements.quote-demo-modal :product="$product" size="compact" />
                    </div>
                </div>
            </div>

            {{-- Right: Checkout builder --}}
            <div class="lg:col-span-2">
                <div class="p-6 bg-white border border-zinc-200 rounded-lg dark:bg-neutral-800 dark:border-neutral-700">
                    <livewire:product-checkout :product-slug="$product->slug" />
                </div>
            </div>

        </div>
    </x-app.container>
</x-layouts.app>
