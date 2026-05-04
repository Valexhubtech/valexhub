<section class="py-5 bg-white">
    <!-- Header -->
    <div class="text-center mb-16">
        <h2 class="text-4xl md:text-5xl font-bold text-zinc-900 mb-6">
            Custom Software &
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-zinc-900 to-zinc-600">Ready Systems</span>
        </h2>
        <p class="text-lg text-zinc-600 max-w-2xl mx-auto">
            Complete software solutions designed specifically for Nigerian SMEs. From schools to hotels, pharmacies to restaurants.
        </p>
    </div>

    <!-- Mobile/Tablet: Custom Development + Horizontal Scroll | Desktop: Bento Grid -->
    <div class="xl:space-y-6">
        <!-- Mobile & Tablet Layout -->
        <div class="xl:hidden space-y-6">
            <!-- Custom Development - Mobile (full width) -->
            <div class="relative bg-zinc-900 rounded-3xl p-0 group hover:scale-[1.02] transition-transform duration-300">
                <!-- Terminal Header -->
                <div class="flex items-center justify-between px-6 py-4 bg-zinc-800 border-b border-zinc-700">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    <div class="text-zinc-400 text-sm font-mono">custom-development.js</div>
                </div>

                <!-- Code Content -->
                <div class="p-6 h-full flex flex-col">
                    <div class="font-mono text-sm leading-relaxed mb-6 text-green-400 flex-1">
                        <div class="text-zinc-500">// Custom Software Development</div>
                        <div class="mt-2">
                            <span class="text-blue-400">const</span> <span class="text-yellow-300">solutions</span> = {<br>
                            &nbsp;&nbsp;<span class="text-purple-400">approach</span>: <span class="text-green-300">'bespoke'</span>,<br>
                            &nbsp;&nbsp;<span class="text-purple-400">tailored</span>: <span class="text-green-300">'exactly to your workflow'</span>,<br>
                            &nbsp;&nbsp;<span class="text-purple-400">scalable</span>: <span class="text-orange-400">true</span>,<br>
                            &nbsp;&nbsp;<span class="text-purple-400">support</span>: <span class="text-green-300">'24/7'</span><br>
                            };
                        </div>
                        <div class="mt-4 text-zinc-500">
                            <span class="text-green-300">// Perfect for unique business requirements</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white">Custom Development</h3>
                        <div class="flex items-center text-sm text-white">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-3 animate-pulse"></span>
                            Available
                        </div>
                    </div>
                </div>
            </div>

            <!-- Horizontal Scrolling Cards -->
            <div class="relative overflow-hidden pb-4 -mx-6 px-6 scroll-container"
                x-data="autoScroll()"
                x-init="startAutoScroll()">
                <div class="flex space-x-4 transition-transform duration-75 ease-linear"
                    x-ref="scrollContainer"
                    @mouseenter="pauseAutoScroll()"
                    @mouseleave="resumeAutoScroll()"
                    @touchstart="pauseAutoScroll()"
                    @touchend="resumeAutoScroll()"
                    :style="`transform: translateX(-${scrollPosition}px)`">
                    <!-- School Management -->
                    <div class="w-72 bg-gradient-to-br from-zinc-50 to-white rounded-2xl p-6 border border-zinc-200 hover:shadow-lg transition-all duration-300 group flex-shrink-0">
                        <div class="w-12 h-12 bg-zinc-900 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <x-phosphor-graduation-cap class="w-6 h-6 text-white" />
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 mb-3">School Management</h3>
                        <p class="text-zinc-600 text-sm leading-relaxed">Complete student, fees, attendance and reporting tools for private schools.</p>
                    </div>

                    <!-- Hotel & Booking -->
                    <div class="w-72 bg-gradient-to-br from-zinc-50 to-white rounded-2xl p-6 border border-zinc-200 hover:shadow-lg transition-all duration-300 group flex-shrink-0">
                        <div class="w-12 h-12 bg-zinc-900 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <x-phosphor-bed class="w-6 h-6 text-white" />
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 mb-3">Hotel & Booking</h3>
                        <p class="text-zinc-600 text-sm leading-relaxed">Room management, reservations, POS and guest experience in one system.</p>
                    </div>

                    <!-- Pharmacy POS -->
                    <div class="w-72 bg-gradient-to-br from-zinc-50 to-white rounded-2xl p-6 border border-zinc-200 hover:shadow-lg transition-all duration-300 group flex-shrink-0">
                        <div class="w-12 h-12 bg-zinc-900 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <x-phosphor-first-aid-kit class="w-6 h-6 text-white" />
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 mb-3">Pharmacy POS</h3>
                        <p class="text-zinc-600 text-sm leading-relaxed">Stock control, expiry alerts, sales and supplier management.</p>
                    </div>

                    <!-- Restaurant POS -->
                    <div class="w-72 bg-gradient-to-br from-zinc-50 to-white rounded-2xl p-6 border border-zinc-200 hover:shadow-lg transition-all duration-300 group flex-shrink-0">
                        <div class="w-12 h-12 bg-zinc-900 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <x-phosphor-fork-knife class="w-6 h-6 text-white" />
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 mb-3">Restaurant POS</h3>
                        <p class="text-zinc-600 text-sm leading-relaxed">Kitchen display, table ordering and inventory management.</p>
                    </div>

                    <!-- Property Management -->
                    <div class="w-72 bg-gradient-to-br from-zinc-50 to-white rounded-2xl p-6 border border-zinc-200 hover:shadow-lg transition-all duration-300 group flex-shrink-0">
                        <div class="w-12 h-12 bg-zinc-900 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <x-phosphor-buildings class="w-6 h-6 text-white" />
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 mb-3">Property Management</h3>
                        <p class="text-zinc-600 text-sm leading-relaxed">Tenant management, rent collection, maintenance scheduling and comprehensive invoicing system.</p>
                    </div>

                    <!-- Real Estate -->
                    <div class="w-72 bg-gradient-to-br from-zinc-50 to-white rounded-2xl p-6 border border-zinc-200 hover:shadow-lg transition-all duration-300 group flex-shrink-0">
                        <div class="w-12 h-12 bg-zinc-900 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <x-phosphor-house class="w-6 h-6 text-white" />
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 mb-3">Real Estate Software</h3>
                        <p class="text-zinc-600 text-sm leading-relaxed">Property listings, client management, commission tracking and lead generation tools.</p>
                    </div>

                    <!-- Auto Workshop -->
                    <div class="w-72 bg-gradient-to-br from-zinc-50 to-white rounded-2xl p-6 border border-zinc-200 hover:shadow-lg transition-all duration-300 group flex-shrink-0">
                        <div class="w-12 h-12 bg-zinc-900 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <x-phosphor-wrench class="w-6 h-6 text-white" />
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 mb-3">Auto Workshop</h3>
                        <p class="text-zinc-600 text-sm leading-relaxed">Job cards, parts inventory, customer vehicle history and service scheduling.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top row: Custom Development + Column of cards -->
        <div class="hidden xl:block">
            <div class="grid grid-cols-2 gap-6">
                <!-- Custom Development - Code Terminal -->
                <div class="relative overflow-hidden bg-zinc-900 rounded-3xl p-0 group hover:scale-[1.02] transition-transform duration-300">
            <!-- Terminal Header -->
            <div class="flex items-center justify-between px-6 py-4 bg-zinc-800 border-b border-zinc-700">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                </div>
                <div class="text-zinc-400 text-sm font-mono">custom-development.js</div>
            </div>

            <!-- Code Content -->
            <div class="p-6 h-full flex flex-col">
                <div class="font-mono text-sm leading-relaxed mb-6 text-green-400 flex-1">
                    <div class="text-zinc-500">// Custom Software Development</div>
                    <div class="mt-2">
                        <span class="text-blue-400">const</span> <span class="text-yellow-300">solutions</span> = {<br>
                        &nbsp;&nbsp;<span class="text-purple-400">approach</span>: <span class="text-green-300">'bespoke'</span>,<br>
                        &nbsp;&nbsp;<span class="text-purple-400">tailored</span>: <span class="text-green-300">'exactly to your workflow'</span>,<br>
                        &nbsp;&nbsp;<span class="text-purple-400">scalable</span>: <span class="text-orange-400">true</span>,<br>
                        &nbsp;&nbsp;<span class="text-purple-400">support</span>: <span class="text-green-300">'24/7'</span><br>
                        };
                    </div>
                    <div class="mt-4 text-zinc-500">
                        <span class="text-green-300">// Perfect for unique business requirements</span>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white">Custom Development</h3>
                    <div class="flex items-center text-sm text-white">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-3 animate-pulse"></span>
                        Available
                    </div>
                </div>
            </div>
            </div>
                
                <!-- Column of cards beside Custom Development -->
                <div class="space-y-4">
                    <!-- Restaurant POS -->
                    <div class="bg-gradient-to-br from-zinc-50 to-white rounded-2xl p-4 border border-zinc-200 hover:shadow-lg transition-all duration-300 group">
                        <div class="w-10 h-10 bg-zinc-900 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                            <x-phosphor-fork-knife class="w-5 h-5 text-white" />
                        </div>
                        <h3 class="text-base font-semibold text-zinc-900 mb-2">Restaurant POS</h3>
                        <p class="text-zinc-600 text-xs leading-relaxed">Kitchen display, table ordering and inventory management.</p>
                    </div>

                    <!-- Property Management -->
                    <div class="bg-gradient-to-br from-zinc-50 to-white rounded-2xl p-4 border border-zinc-200 hover:shadow-lg transition-all duration-300 group">
                        <div class="w-10 h-10 bg-zinc-900 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                            <x-phosphor-buildings class="w-5 h-5 text-white" />
                        </div>
                        <h3 class="text-base font-semibold text-zinc-900 mb-2">Property Management</h3>
                        <p class="text-zinc-600 text-xs leading-relaxed">Tenant management, rent collection, maintenance scheduling and comprehensive invoicing system.</p>
                    </div>

                    <!-- Real Estate -->
                    <div class="bg-gradient-to-br from-zinc-50 to-white rounded-2xl p-4 border border-zinc-200 hover:shadow-lg transition-all duration-300 group">
                        <div class="w-10 h-10 bg-zinc-900 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                            <x-phosphor-house class="w-5 h-5 text-white" />
                        </div>
                        <h3 class="text-base font-semibold text-zinc-900 mb-2">Real Estate Software</h3>
                        <p class="text-zinc-600 text-xs leading-relaxed">Property listings, client management, commission tracking and lead generation tools.</p>
                    </div>

                </div>
            </div>
        </div>

        <!-- Bottom row: Horizontal row of 4 cards -->
        <div class="hidden xl:block">
            <div class="grid grid-cols-4 gap-6">
                <!-- School Management -->
                <div class="bg-gradient-to-br from-zinc-50 to-white rounded-2xl p-6 border border-zinc-200 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-12 h-12 bg-zinc-900 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <x-phosphor-graduation-cap class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 mb-3">School Management</h3>
                    <p class="text-zinc-600 text-sm leading-relaxed">Complete student, fees, attendance and reporting tools for private schools.</p>
                </div>

                <!-- Hotel & Booking -->
                <div class="bg-gradient-to-br from-zinc-50 to-white rounded-2xl p-6 border border-zinc-200 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-12 h-12 bg-zinc-900 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <x-phosphor-bed class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 mb-3">Hotel & Booking</h3>
                    <p class="text-zinc-600 text-sm leading-relaxed">Room management, reservations, POS and guest experience in one system.</p>
                </div>

                <!-- Pharmacy POS -->
                <div class="bg-gradient-to-br from-zinc-50 to-white rounded-2xl p-6 border border-zinc-200 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-12 h-12 bg-zinc-900 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <x-phosphor-first-aid-kit class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 mb-3">Pharmacy POS</h3>
                    <p class="text-zinc-600 text-sm leading-relaxed">Stock control, expiry alerts, sales and supplier management.</p>
                </div>

                <!-- Auto Workshop -->
                <div class="bg-gradient-to-br from-zinc-50 to-white rounded-2xl p-6 border border-zinc-200 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-12 h-12 bg-zinc-900 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <x-phosphor-wrench class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 mb-3">Auto Workshop</h3>
                    <p class="text-zinc-600 text-sm leading-relaxed">Job cards, parts inventory, customer vehicle history and service scheduling.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function autoScroll() {
        return {
            scrollPosition: 0,
            scrollInterval: null,
            scrollDirection: 1, // 1 for right, -1 for left
            userInteracting: false,
            maxScrollPosition: 0,

            startAutoScroll() {
                // Calculate the maximum scroll position
                this.$nextTick(() => {
                    const container = this.$refs.scrollContainer;
                    const containerWidth = this.$el.clientWidth;
                    this.maxScrollPosition = Math.max(0, container.scrollWidth - containerWidth);

                    // Start the animation
                    this.scrollInterval = setInterval(() => {
                        if (!this.userInteracting) {
                            const scrollSpeed = 0.5; // pixels per frame

                            // Check boundaries and reverse direction
                            if (this.scrollPosition >= this.maxScrollPosition - 10) {
                                this.scrollDirection = -1;
                            } else if (this.scrollPosition <= 10) {
                                this.scrollDirection = 1;
                            }

                            this.scrollPosition += scrollSpeed * this.scrollDirection;

                            // Clamp values
                            this.scrollPosition = Math.max(0, Math.min(this.scrollPosition, this.maxScrollPosition));
                        }
                    }, 16); // ~60fps for smooth animation
                });
            },

            pauseAutoScroll() {
                this.userInteracting = true;
            },

            resumeAutoScroll() {
                setTimeout(() => {
                    this.userInteracting = false;
                }, 2000); // Resume after 2 seconds
            }
        }
    }
</script>

<style>
    /* Ensure smooth GPU-accelerated animations */
    .flex {
        will-change: transform;
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    /* Removed fade edges as requested */

    /* Disable any browser scroll behaviors */
    .relative.overflow-hidden {
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
</style>