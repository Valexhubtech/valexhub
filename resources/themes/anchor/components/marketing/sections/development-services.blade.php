<section class="py-20 relative overflow-hidden bg-zinc-50">
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
        <div class="text-center mb-16">
            <h2 class="text-3xl/tight font-bold text-gray-900 sm:text-4xl">
                Development &
                <span class="bg-gradient-to-r from-gray-900 via-blue-800 to-blue-600 bg-clip-text text-transparent">Deployment Timeline</span>
            </h2>
            <p class="mt-4 text-lg text-gray-700 max-w-3xl mx-auto">
                From concept to launch, we follow a structured development process that ensures quality delivery and keeps you informed every step of the way.
            </p>
        </div>

        <!-- Tab Bar and Timeline Container -->
        <div 
            x-data="{
                tabSelected: 1,
                tabId: $id('dev-tabs'),
                tabButtonClicked(tabButton){
                    this.tabSelected = parseInt(tabButton.dataset.tab);
                    this.tabRepositionMarker(tabButton);
                },
                tabRepositionMarker(tabButton){
                    this.$refs.tabMarker.style.width=tabButton.offsetWidth + 'px';
                    this.$refs.tabMarker.style.height=tabButton.offsetHeight + 'px';
                    this.$refs.tabMarker.style.left=tabButton.offsetLeft + 'px';
                },
                tabContentActive(contentId){
                    return this.tabSelected == contentId;
                }
            }"
            x-init="tabRepositionMarker($refs.tabButtons.firstElementChild);" 
            class="max-w-4xl mx-auto">

            <!-- Tab Bar -->
            <div class="flex justify-center mb-12">
                <div class="relative w-full max-w-md">
                    <div x-ref="tabButtons" class="relative inline-grid items-center justify-center w-full h-12 grid-cols-2 p-1 text-gray-600 bg-gray-100 rounded-lg select-none">
                        <button data-tab="1" @click="tabButtonClicked($el);" type="button" class="relative z-20 inline-flex items-center justify-center w-full h-10 px-4 text-sm font-medium transition-all rounded-md cursor-pointer whitespace-nowrap" :class="{'text-gray-900 font-semibold': tabSelected === 1, 'text-gray-600': tabSelected !== 1}">Custom Software</button>
                        <button data-tab="2" @click="tabButtonClicked($el);" type="button" class="relative z-20 inline-flex items-center justify-center w-full h-10 px-4 text-sm font-medium transition-all rounded-md cursor-pointer whitespace-nowrap" :class="{'text-gray-900 font-semibold': tabSelected === 2, 'text-gray-600': tabSelected !== 2}">Pre-built Solutions</button>
                        <div x-ref="tabMarker" class="absolute left-0 z-10 w-1/2 h-full duration-300 ease-out" x-cloak><div class="w-full h-full bg-white rounded-md shadow-sm"></div></div>
                    </div>
                </div>
            </div>

            <!-- Development Timeline -->
            
            <!-- Custom Software Timeline (Tab 1) -->
            <div x-show="tabContentActive(1)" class="relative">
                <ol class="relative space-y-8 before:absolute before:top-0 before:left-1/2 before:h-full before:w-0.5 before:-translate-x-1/2 before:rounded-full before:bg-gray-200">
                    <li class="group relative grid grid-cols-2 odd:-me-3 even:-ms-3">
                        <div class="relative flex items-start gap-4 group-odd:flex-row-reverse group-odd:text-right group-even:order-last">
                            <span class="size-4 shrink-0 rounded-full bg-blue-600 ring-4 ring-white"></span>

                            <div class="-mt-2">
                                <time class="text-xs/none font-medium text-blue-600">Week 1</time>
                                <h3 class="text-lg font-bold text-gray-900">Project Kickoff & Planning</h3>
                                <p class="mt-0.5 text-sm text-gray-700">
                                    Requirements gathering, system architecture design, and project timeline finalization. We set up development environment and establish communication channels.
                                </p>
                            </div>
                        </div>
                        <div aria-hidden="true"></div>
                    </li>

                    <li class="group relative grid grid-cols-2 odd:-me-3 even:-ms-3">
                        <div class="relative flex items-start gap-4 group-odd:flex-row-reverse group-odd:text-right group-even:order-last">
                            <span class="size-4 shrink-0 rounded-full bg-blue-600 ring-4 ring-white"></span>

                            <div class="-mt-2">
                                <time class="text-xs/none font-medium text-blue-600">Week 2-4</time>
                                <h3 class="text-lg font-bold text-gray-900">Core Development</h3>
                                <p class="mt-0.5 text-sm text-gray-700">
                                    Building the core functionality, database design, and API development. Regular progress updates and milestone reviews with client feedback integration.
                                </p>
                            </div>
                        </div>
                        <div aria-hidden="true"></div>
                    </li>

                    <li class="group relative grid grid-cols-2 odd:-me-3 even:-ms-3">
                        <div class="relative flex items-start gap-4 group-odd:flex-row-reverse group-odd:text-right group-even:order-last">
                            <span class="size-4 shrink-0 rounded-full bg-blue-600 ring-4 ring-white"></span>

                            <div class="-mt-2">
                                <time class="text-xs/none font-medium text-blue-600">Week 5-6</time>
                                <h3 class="text-lg font-bold text-gray-900">Testing & Refinement</h3>
                                <p class="mt-0.5 text-sm text-gray-700">
                                    Comprehensive testing, bug fixes, performance optimization, and user acceptance testing. Final adjustments based on client feedback.
                                </p>
                            </div>
                        </div>
                        <div aria-hidden="true"></div>
                    </li>

                    <li class="group relative grid grid-cols-2 odd:-me-3 even:-ms-3">
                        <div class="relative flex items-start gap-4 group-odd:flex-row-reverse group-odd:text-right group-even:order-last">
                            <span class="size-4 shrink-0 rounded-full bg-green-600 ring-4 ring-white"></span>

                            <div class="-mt-2">
                                <time class="text-xs/none font-medium text-green-600">Week 7</time>
                                <h3 class="text-lg font-bold text-gray-900">Launch & Support</h3>
                                <p class="mt-0.5 text-sm text-gray-700">
                                    Production deployment, staff training, documentation handover, and 30-day post-launch support to ensure smooth operation.
                                </p>
                            </div>
                        </div>
                        <div aria-hidden="true"></div>
                    </li>
                </ol>
            </div>

            <!-- Pre-built Solutions Timeline (Tab 2) -->
            <div x-show="tabContentActive(2)" x-cloak class="relative">
                <ol class="relative space-y-8 before:absolute before:top-0 before:left-1/2 before:h-full before:w-0.5 before:-translate-x-1/2 before:rounded-full before:bg-gray-200">
                    <li class="group relative grid grid-cols-2 odd:-me-3 even:-ms-3">
                        <div class="relative flex items-start gap-4 group-odd:flex-row-reverse group-odd:text-right group-even:order-last">
                            <span class="size-4 shrink-0 rounded-full bg-purple-600 ring-4 ring-white"></span>

                            <div class="-mt-2">
                                <time class="text-xs/none font-medium text-purple-600">Day 1-2</time>
                                <h3 class="text-lg font-bold text-gray-900">Solution Selection & Assessment</h3>
                                <p class="mt-0.5 text-sm text-gray-700">
                                    Quick consultation to identify the best pre-built solution for your business needs and assess your current data structure.
                                </p>
                            </div>
                        </div>
                        <div aria-hidden="true"></div>
                    </li>

                    <li class="group relative grid grid-cols-2 odd:-me-3 even:-ms-3">
                        <div class="relative flex items-start gap-4 group-odd:flex-row-reverse group-odd:text-right group-even:order-last">
                            <span class="size-4 shrink-0 rounded-full bg-purple-600 ring-4 ring-white"></span>

                            <div class="-mt-2">
                                <time class="text-xs/none font-medium text-purple-600">Week 1</time>
                                <h3 class="text-lg font-bold text-gray-900">Setup & Configuration</h3>
                                <p class="mt-0.5 text-sm text-gray-700">
                                    Initial system setup, branding customization, user accounts creation, and basic configuration to match your workflow.
                                </p>
                            </div>
                        </div>
                        <div aria-hidden="true"></div>
                    </li>

                    <li class="group relative grid grid-cols-2 odd:-me-3 even:-ms-3">
                        <div class="relative flex items-start gap-4 group-odd:flex-row-reverse group-odd:text-right group-even:order-last">
                            <span class="size-4 shrink-0 rounded-full bg-purple-600 ring-4 ring-white"></span>

                            <div class="-mt-2">
                                <time class="text-xs/none font-medium text-purple-600">Week 2</time>
                                <h3 class="text-lg font-bold text-gray-900">Data Migration & Testing</h3>
                                <p class="mt-0.5 text-sm text-gray-700">
                                    Import your existing data, run comprehensive tests, and fine-tune settings. Staff preview and feedback integration.
                                </p>
                            </div>
                        </div>
                        <div aria-hidden="true"></div>
                    </li>

                    <li class="group relative grid grid-cols-2 odd:-me-3 even:-ms-3">
                        <div class="relative flex items-start gap-4 group-odd:flex-row-reverse group-odd:text-right group-even:order-last">
                            <span class="size-4 shrink-0 rounded-full bg-green-600 ring-4 ring-white"></span>

                            <div class="-mt-2">
                                <time class="text-xs/none font-medium text-green-600">Week 3</time>
                                <h3 class="text-lg font-bold text-gray-900">Training & Launch</h3>
                                <p class="mt-0.5 text-sm text-gray-700">
                                    Comprehensive staff training, final testing, and go-live support. Your business is up and running with minimal downtime.
                                </p>
                            </div>
                        </div>
                        <div aria-hidden="true"></div>
                    </li>
                </ol>
            </div>
        </div>
    </x-container>
</section>