<!-- 2026 Modern Navbar with Glassmorphism and Dynamic States -->
<header 
    x-data="{ 
        mobileMenuOpen: false, 
        scrolled: false, 
        showOverlay: false,
        activeDropdown: null,
        topOffset: '5',
        evaluateScrollPosition(){
            if(window.pageYOffset > this.topOffset){
                this.scrolled = true;
            } else {
                this.scrolled = false;
            }
        },
        closeAllDropdowns() {
            this.activeDropdown = null;
            this.showOverlay = false;
        }
    }"
    x-init="
        window.addEventListener('resize', function() {
            if(window.innerWidth > 768) {
                mobileMenuOpen = false;
                activeDropdown = null;
            }
        });
        $watch('mobileMenuOpen', function(value){
            if(value){ 
                document.body.classList.add('overflow-hidden'); 
            } else { 
                document.body.classList.remove('overflow-hidden'); 
                activeDropdown = null;
            }
        });
        evaluateScrollPosition();
        window.addEventListener('scroll', function() {
            evaluateScrollPosition(); 
        });
        document.addEventListener('click', function(event) {
            if(!event.target.closest('[data-dropdown]')) {
                closeAllDropdowns();
            }
        });
    " 
    :class="{ 
        'bg-white/80 backdrop-blur-xl border-slate-200/60 shadow-lg shadow-slate-900/5' : scrolled, 
        'bg-white/20 backdrop-blur-md border-white/20' : !scrolled 
    }" 
    class="box-content sticky top-0 z-50 w-full h-20 border-b transition-all duration-500" 
>
    <div 
        x-show="showOverlay"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="absolute inset-0 w-full h-screen pt-24" x-cloak>
        <div class="w-screen h-full bg-black/50"></div>
    </div>
    <x-container>
        <div class="z-30 flex items-center justify-between h-20 md:space-x-8">
            <!-- Logo Section with Enhanced Styling -->
            <div class="z-20 flex items-center justify-between w-full md:w-auto">
                <div class="relative z-20 inline-flex">
                    <a href="{{ route('home') }}" wire:navigate class="flex items-center justify-center space-x-3 font-bold transition-all duration-300 hover:scale-105 group">
                        <x-logo class="w-auto h-8 md:h-9 transition-all duration-300 group-hover:brightness-110"></x-logo>
                    </a>
                </div>
                
                <!-- Modern Mobile Menu Button -->
                <div class="flex justify-end flex-grow md:hidden">
                    <button 
                        @click="mobileMenuOpen = !mobileMenuOpen" 
                        type="button" 
                        :class="{ 'bg-slate-100/80 backdrop-blur-sm': mobileMenuOpen }"
                        class="inline-flex items-center justify-center p-3 rounded-2xl transition-all duration-300 ease-out text-slate-600 hover:text-slate-800 hover:bg-slate-100/60 backdrop-blur-sm border border-slate-200/50 hover:border-slate-300/50 shadow-sm hover:shadow-md">
                        <!-- Hamburger to X Animation -->
                        <div class="w-6 h-6 flex flex-col justify-center items-center">
                            <span 
                                :class="{ 'rotate-45 translate-y-1': mobileMenuOpen, 'rotate-0 translate-y-0': !mobileMenuOpen }"
                                class="w-5 h-0.5 bg-current transform transition-all duration-300 origin-center">
                            </span>
                            <span 
                                :class="{ 'opacity-0': mobileMenuOpen, 'opacity-100': !mobileMenuOpen }"
                                class="w-5 h-0.5 bg-current transform transition-all duration-300 mt-1">
                            </span>
                            <span 
                                :class="{ '-rotate-45 -translate-y-2': mobileMenuOpen, 'rotate-0 translate-y-0': !mobileMenuOpen }"
                                class="w-5 h-0.5 bg-current transform transition-all duration-300 mt-1 origin-center">
                            </span>
                        </div>
                    </button>
                </div>
            </div>

            <nav :class="{ 'hidden' : !mobileMenuOpen, 'block md:relative absolute top-0 left-0 md:w-auto w-screen md:h-auto h-screen pointer-events-none md:z-10 z-10' : mobileMenuOpen }" class="h-full md:flex">
                <ul :class="{ 'hidden md:flex' : !mobileMenuOpen, 'flex flex-col absolute md:relative md:w-auto w-screen h-full md:h-full md:overflow-auto overflow-scroll md:pt-0 mt-24 md:pb-0 pb-48 bg-white md:bg-transparent' : mobileMenuOpen }" id="menu" class="flex items-stretch justify-start flex-1 w-full h-full ml-0 border-t border-gray-100 pointer-events-auto md:items-center md:justify-center gap-x-8 md:w-auto md:border-t-0 md:flex-row">
                    <li class="flex-shrink-0 h-16 border-b border-gray-100 md:border-b-0 md:h-full">
                        <a href="{{ route('home') }}" wire:navigate class="flex items-center h-full text-sm font-semibold text-gray-700 transition duration-300 md:px-0 px-7 hover:bg-gray-100 md:hover:bg-transparent hover:text-gray-900">
                            Home
                        </a>
                    </li>
                    <li class="flex-shrink-0 h-16 border-b border-gray-100 md:border-b-0 md:h-full">
                        <a href="{{ route('products') }}" wire:navigate class="flex items-center h-full text-sm font-semibold text-gray-700 transition duration-300 md:px-0 px-7 hover:bg-gray-100 md:hover:bg-transparent hover:text-gray-900">Products</a>
                    </li>
                    <li class="flex-shrink-0 h-16 border-b border-gray-100 md:border-b-0 md:h-full">
                        <a href="{{ route('custom-software') }}" wire:navigate class="flex items-center h-full text-sm font-semibold text-gray-700 transition duration-300 md:px-0 px-7 hover:bg-gray-100 md:hover:bg-transparent hover:text-gray-900">Solutions</a>
                    </li>
                    <li class="flex-shrink-0 h-16 border-b border-gray-100 md:border-b-0 md:h-full">
                        <a href="{{ route('blog') }}" wire:navigate class="flex items-center h-full text-sm font-semibold text-gray-700 transition duration-300 md:px-0 px-7 hover:bg-gray-100 md:hover:bg-transparent hover:text-gray-900">Blog</a>
                    </li>
                    <li class="flex-shrink-0 h-16 border-b border-gray-100 md:border-b-0 md:h-full">
                        <a href="{{ route('book-demo') }}" wire:navigate class="flex items-center h-full text-sm font-semibold text-gray-700 transition duration-300 md:px-0 px-7 hover:bg-gray-100 md:hover:bg-transparent hover:text-gray-900">
                            Book Demo
                        </a>
                    </li>

                    @guest
                        <li class="relative z-30 flex flex-col items-center justify-center flex-shrink-0 w-full h-auto pt-3 space-y-3 text-sm md:hidden px-7">
                            <x-button href="{{ route('login') }}" tag="a" class="w-full text-sm" color="secondary">Login</x-button>
                            <x-button href="{{ route('register') }}" tag="a" class="w-full text-sm">Sign Up</x-button>
                        </li>
                    @else
                        <li class="flex items-center justify-center w-full pt-3 md:hidden px-7">
                            <x-button href="{{ route('login') }}" tag="a" class="w-full text-sm">View Dashboard</x-button>
                        </li>
                    @endguest

                </ul>
            </nav>
            
            @guest
                <div class="relative z-30 items-center justify-center flex-shrink-0 hidden h-full space-x-3 text-sm md:flex">
                    <x-button href="{{ route('login') }}" tag="a" class="text-sm" color="secondary">Login</x-button>
                    <x-button href="{{ route('register') }}" tag="a" class="text-sm">Sign Up</x-button>
                </div>
            @else
                <x-button href="{{ route('login') }}" tag="a" class="text-sm" class="relative z-20 flex-shrink-0 hidden ml-2 md:block">View Dashboard</x-button>
            @endguest

        </div>
    </x-container>

</header>
