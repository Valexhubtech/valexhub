<!-- Clean Hero Section -->
<section
    x-data="{ 
        scrollY: 0,
        scale: 1,
        blur: 0,
        opacity: 1,
        heroInView: true,
        init() {
            this.updateScale();
            window.addEventListener('scroll', () => {
                this.scrollY = window.scrollY;
                this.updateScale();
            });
            // Also update on resize
            window.addEventListener('resize', () => {
                this.updateScale();
            });
        },
        updateScale() {
            // Only animate when hero is in view
            if (!this.heroInView) {
                return;
            }
            
            const heroHeight = this.$el.offsetHeight; // e.g., ~844px on iPhone 13, ~1080px on desktop
            const windowHeight = window.innerHeight;
            
            // Use the full hero height as reference for animation range
            const animationRange = heroHeight; // Full hero height for animation
            
            const scaleStartPos = 0;
            const blurStartPos = animationRange * 0.5; // Start blur halfway through hero
            const fadeStartPos = animationRange * 0.7; // Start fade at 70% through hero  
            const completelyGonePos = animationRange * 1.2; // Gone slightly after hero section
            
            // Calculate effects based on realistic scroll positions
            if (this.scrollY <= scaleStartPos) {
                this.scale = 1.0;
                this.blur = 0;
                this.opacity = 1;
            } else if (this.scrollY >= completelyGonePos) {
                this.scale = 0.3;
                this.blur = 15;
                this.opacity = 0;
            } else {
                // Scale calculation - gradual throughout hero scroll
                const scaleProgress = this.scrollY / completelyGonePos;
                this.scale = Math.max(0.3, 1 - (scaleProgress * 0.7));
                
                // Blur calculation - starts at 60% toward hero exit
                if (this.scrollY >= blurStartPos) {
                    const blurProgress = (this.scrollY - blurStartPos) / (completelyGonePos - blurStartPos);
                    this.blur = Math.min(blurProgress * 15, 15);
                } else {
                    this.blur = 0;
                }
                
                // Fade calculation - starts at 80% toward hero exit
                if (this.scrollY >= fadeStartPos) {
                    const fadeProgress = (this.scrollY - fadeStartPos) / (completelyGonePos - fadeStartPos);
                    this.opacity = Math.max(0, 1 - fadeProgress);
                } else {
                    this.opacity = 1;
                }
            }
        }
    }"
    x-intersect:enter="heroInView = true"
    x-intersect:leave="heroInView = false"
    x-init="init()"
    class="relative top-0 flex flex-col justify-center items-center -mt-24 w-full min-h-screen">

    <!-- SVG Background -->
    <div class="absolute inset-0" style="background-color: #f5fdfdff"></div>
    <div class="absolute inset-0 bg-center bg-no-repeat" style="background-image: url('/wave/img/bg.svg'); background-size: cover;"></div>

    <!-- Content Container -->
    <div
        :style="{ 
            transform: `scale(${scale})`, 
            filter: `blur(${blur}px)`,
            opacity: opacity
        }"
        class="flex flex-col gap-8 justify-center items-center px-8 pt-32 mx-auto w-full max-w-4xl text-center transition-all duration-300 ease-out md:px-12 xl:px-20 lg:pt-32 lg:pb-16 relative z-10">

        <div class="w-full space-y-6">
            <h1 class="text-4xl font-black tracking-tight text-center sm:text-6xl md:text-7xl lg:text-[84px] text-balance leading-none">
                <span class="block text-slate-900 relative">
                    Powerful
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-deep-blue rounded-full opacity-60"></span>
                </span>
                <span class="block text-deep-blue font-extrabold relative">
                    Business Tools
                    <span class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-deep-blue to-deep-blue-light opacity-30"></span>
                </span>
                <span class="block text-slate-900 relative">
                    Built for
                    <span class=" bg-clip-text bg-gradient-to-r from-deep-blue to-deep-blue-light font-extrabold">Growth</span>
                </span>
            </h1>

            <p class="mx-auto mt-6 text-sm font-medium text-center md:text-lg lg:text-xl max-w-2xl text-slate-600 leading-relaxed">
                We build tailored digital solutions that help businesses recover lost revenue, streamline operations, and scale profitably with modern technology.
            </p>

            <div class="flex flex-row gap-3 justify-center items-center mx-auto mt-8">
                <x-button
                    size="lg"
                    class="w-full md:w-auto bg-deep-blue hover:bg-deep-blue-dark border-0 shadow-lg hover:shadow-xl"
                    tag="a"
                    href="{{ route('book-demo') }}"
                    wire:navigate>
                    Book a Demo
                </x-button>

                <x-button
                    size="lg"
                    color="secondary"
                    class="w-full md:w-auto border-2 border-slate-200 hover:border-deep-blue bg-white shadow-md hover:shadow-lg"
                    tag="a"
                    href="{{ route('products') }}"
                    wire:navigate>
                    Explore Products
                </x-button>
            </div>

            <div class="mx-auto mt-8 max-w-lg pb-8 md:pb-4">
                <p class="text-sm font-semibold text-center text-slate-500 mb-2">
                    Trusted by businesses across Nigeria
                </p>
                <div class="flex items-center justify-center gap-4 text-xs text-slate-400">
                    <span>Schools</span>
                    <span>Hotels</span>
                    <span>Pharmacies</span>
                    <span>SMEs</span>
                </div>
            </div>
        </div>
    </div>
    <div class="flex-shrink-0 lg:h-[150px] flex border-t border-zinc-200 items-center w-full bg-white">
        <div class="grid grid-cols-1 px-8 py-10 mx-auto space-y-5 max-w-7xl h-auto divide-y lg:space-y-0 lg:divide-y-0 divide-zinc-200 lg:py-0 lg:divide-x md:px-12 lg:px-20 lg:divide-zinc-200 lg:grid-cols-3">

            <div class="">
                <h3 class="flex items-center font-medium text-zinc-900">
                    <svg class="w-5 h-5 text-deep-blue mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Pre-built Systems
                </h3>
                <p class="mt-2 text-sm font-medium text-zinc-500">
                    Ready-to-deploy solutions for common Nigerian business needs.
                </p>
            </div>

            <div class="pt-5 lg:pt-0 lg:px-10">
                <h3 class="flex items-center font-medium text-zinc-900">
                    <svg class="w-5 h-5 text-deep-blue mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    Custom Development
                </h3>
                <p class="mt-2 text-sm text-zinc-500">
                    Bespoke software built exactly to your workflow and budget.
                </p>
            </div>

            <div class="pt-5 lg:pt-0 lg:px-10">
                <h3 class="flex items-center font-medium text-zinc-900">
                    <svg class="w-5 h-5 text-deep-blue mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Fast Results
                </h3>
                <p class="mt-2 text-sm text-zinc-500">
                    Systems that help you recover lost revenue and hit ₦30M+ in transactions quickly.
                </p>
            </div>

        </div>
    </div>
</section>