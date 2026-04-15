<section class="flex relative top-0 flex-col justify-center items-center -mt-24 w-full min-h-screen bg-white lg:min-h-screen">
    <div class="flex flex-col flex-1 gap-6 justify-between items-center px-8 pt-32 mx-auto w-full max-w-2xl text-left md:px-12 xl:px-20 lg:pt-32 lg:pb-16 lg:max-w-7xl lg:flex-row">
        <div class="w-full lg:w-1/2">
            <h1 class="text-6xl font-bold tracking-tighter text-left sm:text-7xl md:text-[84px] sm:text-center lg:text-left text-zinc-900 text-balance">
                <span class="block origin-left lg:scale-90">Powerful Tools</span> <span class="pr-4 text-transparent bg-clip-text bg-gradient-to-b text-neutral-600 from-neutral-900 to-neutral-500">Built for SMEs</span> <span class="block origin-left lg:scale-90">& ERP</span>
            </h1>
            <p class="mx-auto mt-5 text-lg font-normal text-left md:text-xl sm:max-w-md lg:ml-0 lg:max-w-lg sm:text-center lg:text-left text-zinc-500">
                We build tailored digital solutions that help businesses recover lost revenue, streamline operations, and scale profitably.
            </p>
            <div class="flex flex-col gap-3 justify-center items-center mx-auto mt-8 md:gap-2 lg:justify-start md:ml-0 md:flex-row">
                <x-button size="lg" class="w-full lg:w-auto" href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20book%20a%20free%20demo" target="_blank">Book a Free Demo</x-button>
                <x-button size="lg" color="secondary" class="w-full lg:w-auto" href="{{ route('blog') }}" wire:navigate>Explore Products</x-button>
            </div>
            <p class="mx-auto mt-6 text-sm font-medium text-left lg:ml-0 sm:text-center lg:text-left text-zinc-600">
                Trusted by schools, hotels, pharmacies & growing businesses across Nigeria
            </p>
        </div>
        <div class="flex justify-center items-center mt-12 w-full lg:w-1/2 lg:mt-0">
            <img alt="Wave Character" class="relative w-full lg:scale-125 xl:translate-x-6" src="/wave/img/character.png" style="max-width:450px;">
        </div>
    </div>
    <div class="flex-shrink-0 lg:h-[150px] flex border-t border-zinc-200 items-center w-full bg-white">
        <div class="grid grid-cols-1 px-8 py-10 mx-auto space-y-5 max-w-7xl h-auto divide-y lg:space-y-0 lg:divide-y-0 divide-zinc-200 lg:py-0 lg:divide-x md:px-12 lg:px-20 lg:divide-zinc-200 lg:grid-cols-3">
            <div class="">
                <h3 class="flex items-center font-medium text-zinc-900">
                    Pre-built Systems
                </h3>
                <p class="mt-2 text-sm font-medium text-zinc-500">
                    Ready-to-deploy solutions for common Nigerian business needs.
                </p>
            </div>
            <div class="pt-5 lg:pt-0 lg:px-10">
                <h3 class="font-medium text-zinc-900">Custom Development</h3>
                <p class="mt-2 text-sm text-zinc-500">
                    Bespoke software built exactly to your workflow and budget.
                </p>
            </div>
            <div class="pt-5 lg:pt-0 lg:px-10">
                <h3 class="font-medium text-zinc-900">Fast Results</h3>
                <p class="mt-2 text-sm text-zinc-500">
                    Systems that help you recover lost revenue and hit ₦30M+ in transactions quickly.
                </p>
            </div>
        </div>
    </div>
</section>