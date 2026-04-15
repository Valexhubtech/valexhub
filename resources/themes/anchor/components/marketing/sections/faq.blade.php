<section class="w-full">
    <x-marketing.elements.heading 
        level="h2" 
        title="Frequently Asked Questions" 
        description="Everything you need to know about our software solutions and services for Nigerian businesses." 
    />
    
    <div class="mx-auto max-w-4xl">
        <div class="space-y-4">
            <div x-data="{ open: false }" class="border border-zinc-200 rounded-lg">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-zinc-50 focus:outline-none focus:bg-zinc-50">
                    <h3 class="font-medium text-zinc-900">What kind of software do you offer?</h3>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-zinc-500 transform transition-transform duration-200">
                        <path fill="currentColor" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-sm text-zinc-500">
                        We provide ready-to-use management systems for schools, hotels, pharmacies, restaurants, property managers, and real estate agencies. We also build fully custom software tailored to your specific business needs.
                    </p>
                </div>
            </div>

            <div x-data="{ open: false }" class="border border-zinc-200 rounded-lg">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-zinc-50 focus:outline-none focus:bg-zinc-50">
                    <h3 class="font-medium text-zinc-900">Do I need to pay everything upfront?</h3>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-zinc-500 transform transition-transform duration-200">
                        <path fill="currentColor" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-sm text-zinc-500">
                        No. You pay a one-time setup fee for installation, training, and customization. After that, you pay a small monthly fee for hosting, updates, and ongoing support.
                    </p>
                </div>
            </div>

            <div x-data="{ open: false }" class="border border-zinc-200 rounded-lg">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-zinc-50 focus:outline-none focus:bg-zinc-50">
                    <h3 class="font-medium text-zinc-900">Will the software work offline?</h3>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-zinc-500 transform transition-transform duration-200">
                        <path fill="currentColor" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-sm text-zinc-500">
                        Yes. Most of our systems have strong offline capabilities, especially for pharmacies, restaurants, and schools where internet can be unreliable.
                    </p>
                </div>
            </div>

            <div x-data="{ open: false }" class="border border-zinc-200 rounded-lg">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-zinc-50 focus:outline-none focus:bg-zinc-50">
                    <h3 class="font-medium text-zinc-900">How long does it take to get the system running?</h3>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-zinc-500 transform transition-transform duration-200">
                        <path fill="currentColor" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-sm text-zinc-500">
                        Usually 5–10 working days after payment and requirements gathering. We handle installation, training, and data migration.
                    </p>
                </div>
            </div>

            <div x-data="{ open: false }" class="border border-zinc-200 rounded-lg">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-zinc-50 focus:outline-none focus:bg-zinc-50">
                    <h3 class="font-medium text-zinc-900">Can I get a custom solution built for my business?</h3>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-zinc-500 transform transition-transform duration-200">
                        <path fill="currentColor" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-sm text-zinc-500">
                        Absolutely. If none of our pre-built systems fit perfectly, we can develop a bespoke solution from scratch based on your exact workflow.
                    </p>
                </div>
            </div>

            <div x-data="{ open: false }" class="border border-zinc-200 rounded-lg">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-zinc-50 focus:outline-none focus:bg-zinc-50">
                    <h3 class="font-medium text-zinc-900">Do you provide training and support?</h3>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-zinc-500 transform transition-transform duration-200">
                        <path fill="currentColor" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-sm text-zinc-500">
                        Yes. We provide full training for you and your team. Ongoing support is included in the monthly fee via WhatsApp, phone, and remote access.
                    </p>
                </div>
            </div>

            <div x-data="{ open: false }" class="border border-zinc-200 rounded-lg">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-zinc-50 focus:outline-none focus:bg-zinc-50">
                    <h3 class="font-medium text-zinc-900">What if I want to cancel the monthly subscription?</h3>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-zinc-500 transform transition-transform duration-200">
                        <path fill="currentColor" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-sm text-zinc-500">
                        You can cancel anytime. Your data will be exported, and we'll help you transition smoothly.
                    </p>
                </div>
            </div>

            <div x-data="{ open: false }" class="border border-zinc-200 rounded-lg">
                <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-zinc-50 focus:outline-none focus:bg-zinc-50">
                    <h3 class="font-medium text-zinc-900">Are your systems suitable for small and medium businesses in Nigeria?</h3>
                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-zinc-500 transform transition-transform duration-200">
                        <path fill="currentColor" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                </button>
                <div x-show="open" x-transition class="px-6 pb-4">
                    <p class="text-sm text-zinc-500">
                        Yes. All our systems are designed specifically for Nigerian businesses — with Naira support, tax compliance, WhatsApp notifications, and local payment options.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>