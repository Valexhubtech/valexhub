<section class="py-20 relative overflow-hidden bg-zinc-50" x-data="{ devServicesTab: 'custom' }">
    <!-- Dotted Grid Background -->
    <div class="absolute inset-0">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="devServicesDotGrid" width="30" height="30" patternUnits="userSpaceOnUse">
                    <circle cx="15" cy="15" r="1.5" fill="#a1a1aa" opacity="0.6" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#devServicesDotGrid)" />
        </svg>
    </div>

    <x-container class="relative z-10">


        <!-- Vertical line positioned to the left -->
        <div class="absolute inset-0 flex justify-center pointer-events-none">
            <div class="w-full max-w-7xl relative">
                <div class="absolute top-0 bottom-0 w-1 bg-zinc-900 opacity-60" style="left: 150px;"></div>
            </div>
        </div>



    </x-container>
</section>