<!-- Marquee Divider -->
<section class="relative w-full">
    <style>
        @keyframes marquee {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-100%);
            }
        }
        @keyframes marquee-reverse {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(0);
            }
        }
        .animate-marquee {
            animation: marquee 20s linear infinite;
        }
        .animate-marquee-reverse {
            animation: marquee-reverse 20s linear infinite;
        }
        .container-block-02 {
            container-type: inline-size;
        }
        @container (max-width: 1100px) {
            .container-block-02 *:nth-child(2),
            .container-block-02 *:nth-child(3) {
                display: none;
            }
        }
        @container (max-width: 1100px) {
            .container-block-02 > div{
                font-size:12px !important;
            }
        }
    </style>
    
    <div class="flex flex-col w-full h-full">
        <div x-data x-init="
                $nextTick(() => {
                    $refs.content.appendChild($refs.item.cloneNode(true));
                });
            " 
            class="w-full overflow-hidden text-lg italic tracking-wide text-white uppercase bg-gray-900 sm:text-xs md:text-sm lg:text-base xl:text-xl"
            >
            <div class="relative w-full mx-auto overflow-hidden max-w-7xl">
                <div class="absolute left-0 z-20 w-40 h-full bg-gradient-to-r from-gray-900 to-transparent"></div>
                <div class="absolute right-0 z-20 w-40 h-full bg-gradient-to-l from-gray-900 to-transparent"></div>
                <div x-ref="content" class="flex animate-marquee">
                    <div x-ref="item" class="flex items-center justify-center flex-shrink-0 w-full py-2 space-x-12 container-block-02">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Pre-built Solutions
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Fast Deployment
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div x-data x-init="
                $nextTick(() => {
                    $refs.content.appendChild($refs.item.cloneNode(true));
                });
            " 
            class="w-full overflow-hidden text-lg italic tracking-wide text-white uppercase bg-gray-900 sm:text-xs md:text-sm lg:text-base xl:text-xl"
            >
            <div class="relative w-full mx-auto overflow-hidden max-w-7xl">
                <div class="absolute left-0 z-20 w-40 h-full bg-gradient-to-r from-gray-900 to-transparent"></div>
                <div class="absolute right-0 z-20 w-40 h-full bg-gradient-to-l from-gray-900 to-transparent"></div>
                <div x-ref="content" class="flex animate-marquee-reverse">
                    <div x-ref="item" class="flex items-center justify-center flex-shrink-0 w-full py-2 space-x-12 container-block-02">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                            Custom Development
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            24/7 Support
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>