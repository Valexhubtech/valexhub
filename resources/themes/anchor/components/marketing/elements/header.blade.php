<header
    x-data="{ mobileMenuOpen: false, scrolled: false }"
    x-init="
        window.addEventListener('scroll', () => { scrolled = window.scrollY > 8; });
        $watch('mobileMenuOpen', v => document.body.classList.toggle('overflow-hidden', v));
    "
    :class="scrolled
        ? 'bg-white/90 backdrop-blur-xl border-zinc-200/80 shadow-sm shadow-zinc-900/5'
        : 'bg-white/10 backdrop-blur-sm border-transparent'"
    class="sticky top-0 z-50 w-full border-b transition-all duration-300"
>
    <div class="max-w-7xl mx-auto px-6 lg:px-8 flex items-center justify-between h-16">

        {{-- Logo --}}
        <a href="{{ route('home') }}" wire:navigate class="flex items-center group flex-shrink-0">
            <x-logo class="h-8 w-auto transition-opacity duration-200 group-hover:opacity-75"></x-logo>
        </a>

        {{-- Desktop nav --}}
        <nav class="hidden md:flex items-center gap-0.5">
            @php
                $navLinks = [
                    ['label' => 'Products',  'route' => 'products'],
                    ['label' => 'Solutions', 'route' => 'custom-software'],
                    ['label' => 'Blog',      'route' => 'blog'],
                    ['label' => 'Affiliate', 'route' => 'dashboard.affiliate-tracker'],
                ];
            @endphp
            @foreach($navLinks as $link)
                <a href="{{ route($link['route']) }}" wire:navigate
                   class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs($link['route'])
                              ? 'text-zinc-900 bg-zinc-100'
                              : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100/80' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Desktop CTAs --}}
        <div class="hidden md:flex items-center gap-3">
            @guest
                <a href="{{ route('login') }}" wire:navigate
                   class="px-4 py-2 text-sm font-semibold text-zinc-600 hover:text-zinc-900 transition-colors duration-200">
                    Login
                </a>
                <a href="{{ route('book-demo') }}" wire:navigate
                   class="px-5 py-2 text-sm font-semibold text-white bg-zinc-900 rounded-xl hover:bg-zinc-800 transition-all duration-200 shadow-sm hover:shadow-md">
                    Book Demo
                </a>
            @else
                <a href="{{ route('dashboard') }}" wire:navigate
                   class="px-5 py-2 text-sm font-semibold text-white bg-zinc-900 rounded-xl hover:bg-zinc-800 transition-all duration-200">
                    Dashboard
                </a>
            @endguest
        </div>

        {{-- Mobile hamburger --}}
        <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                class="md:hidden flex flex-col justify-center items-center w-10 h-10 gap-1.5 rounded-xl border border-zinc-200/80 hover:bg-zinc-50 transition-colors duration-200"
                aria-label="Toggle menu">
            <span :class="mobileMenuOpen ? 'rotate-45 translate-y-2' : ''"
                  class="w-5 h-0.5 bg-zinc-700 transition-all duration-300 origin-center"></span>
            <span :class="mobileMenuOpen ? 'opacity-0 scale-x-0' : ''"
                  class="w-5 h-0.5 bg-zinc-700 transition-all duration-300"></span>
            <span :class="mobileMenuOpen ? '-rotate-45 -translate-y-2' : ''"
                  class="w-5 h-0.5 bg-zinc-700 transition-all duration-300 origin-center"></span>
        </button>

    </div>

    {{-- Mobile menu --}}
    <div x-show="mobileMenuOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="absolute top-full left-0 right-0 bg-white/95 backdrop-blur-xl border-b border-zinc-200 shadow-xl shadow-zinc-900/10 md:hidden">
        <div class="max-w-7xl mx-auto px-4 py-3 space-y-0.5">
            <a href="{{ route('home') }}" wire:navigate @click="mobileMenuOpen = false"
               class="flex items-center px-4 py-3 text-sm font-medium text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50 rounded-xl transition-colors">Home</a>
            <a href="{{ route('products') }}" wire:navigate @click="mobileMenuOpen = false"
               class="flex items-center px-4 py-3 text-sm font-medium text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50 rounded-xl transition-colors">Products</a>
            <a href="{{ route('custom-software') }}" wire:navigate @click="mobileMenuOpen = false"
               class="flex items-center px-4 py-3 text-sm font-medium text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50 rounded-xl transition-colors">Solutions</a>
            <a href="{{ route('blog') }}" wire:navigate @click="mobileMenuOpen = false"
               class="flex items-center px-4 py-3 text-sm font-medium text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50 rounded-xl transition-colors">Blog</a>
            <a href="{{ route('dashboard.affiliate-tracker') }}" wire:navigate @click="mobileMenuOpen = false"
               class="flex items-center px-4 py-3 text-sm font-medium text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50 rounded-xl transition-colors">Affiliate</a>
            <div class="pt-3 mt-2 border-t border-zinc-100 flex flex-col gap-2 pb-2">
                @guest
                    <a href="{{ route('login') }}" wire:navigate @click="mobileMenuOpen = false"
                       class="flex items-center justify-center px-4 py-3 text-sm font-semibold text-zinc-700 border border-zinc-200 rounded-xl hover:bg-zinc-50 transition-colors">Login</a>
                    <a href="{{ route('book-demo') }}" wire:navigate @click="mobileMenuOpen = false"
                       class="flex items-center justify-center px-4 py-3 text-sm font-semibold text-white bg-zinc-900 rounded-xl hover:bg-zinc-800 transition-colors">Book a Free Demo</a>
                @else
                    <a href="{{ route('dashboard') }}" wire:navigate @click="mobileMenuOpen = false"
                       class="flex items-center justify-center px-4 py-3 text-sm font-semibold text-white bg-zinc-900 rounded-xl hover:bg-zinc-800 transition-colors">View Dashboard</a>
                @endguest
            </div>
        </div>
    </div>

</header>
