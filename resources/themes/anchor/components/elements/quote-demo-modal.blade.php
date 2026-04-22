@props([
    'product' => null,
    'size' => 'full', // 'full' for product show page, 'compact' for product cards
])

<div 
    x-data="{
        open: false,
        loading: false,
        step: 1,
        modalType: 'quote',
        form: {
            name: '',
            email: '',
            phone: '',
            referral_code: '',
            product_id: {{ $product->id ?? 'null' }},
            request_type: 'quote'
        },
        
        openModal(type) {
            this.modalType = type;
            this.form.request_type = type;
            this.open = true;
            this.step = 1;
            document.body.style.overflow = 'hidden';
        },
        
        closeModal() {
            this.open = false;
            this.step = 1;
            this.resetForm();
            document.body.style.overflow = 'auto';
        },
        
        async submitForm() {
            if (!this.form.name || !this.form.email || !this.form.phone) {
                alert('Please fill in all required fields');
                return;
            }
            
            this.loading = true;
            
            try {
                const response = await fetch('/demo-request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify(this.form)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.step = 2;
                    // Redirect to WhatsApp after a short delay
                    setTimeout(() => {
                        if (data.whatsapp_url) {
                            window.open(data.whatsapp_url, '_blank');
                        }
                        this.closeModal();
                    }, 2000);
                } else {
                    alert(data.message || 'Something went wrong. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
            }
            
            this.loading = false;
        },
        
        resetForm() {
            this.form.name = '';
            this.form.email = '';
            this.form.phone = '';
            this.form.referral_code = '';
        }
    }"
    x-cloak
>
    <!-- Modal Trigger Buttons -->
    @if($size === 'full')
        <div class="flex flex-col gap-3 md:flex-row">
            <button 
                @click="openModal('quote')"
                class="px-6 py-3 text-white bg-zinc-900 rounded-md hover:bg-zinc-800 text-center font-medium"
            >
                Get Quote
            </button>
            <button 
                @click="openModal('demo')"
                class="px-6 py-3 text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50 text-center font-medium"
            >
                Schedule Demo
            </button>
        </div>
    @else
        <div class="flex gap-2 w-full">
            <button 
                @click="openModal('quote')"
                class="flex-1 text-center px-3 py-2 text-xs font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-800"
            >
                Quote
            </button>
            <button 
                @click="openModal('demo')"
                class="flex-1 text-center px-3 py-2 text-xs font-medium text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50"
            >
                Demo
            </button>
        </div>
    @endif

    <!-- Modal Overlay -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click="closeModal()"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

            <!-- Modal Content -->
            <div 
                class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg relative z-10"
                @click.stop
            >
                <!-- Step 1: Form -->
                <div x-show="step === 1">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-zinc-900">
                            <span x-text="modalType === 'quote' ? 'Get Quote' : 'Schedule Demo'"></span>
                            for {{ $product->name ?? 'Product' }}
                        </h3>
                        <button @click="closeModal()" class="text-zinc-400 hover:text-zinc-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm()" class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Name *</label>
                            <input 
                                type="text" 
                                x-model="form.name"
                                class="w-full px-3 py-2 border border-zinc-300 rounded-md focus:outline-none focus:ring-2 focus:ring-zinc-500"
                                placeholder="Your full name"
                                required
                            />
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Email *</label>
                            <input 
                                type="email" 
                                x-model="form.email"
                                class="w-full px-3 py-2 border border-zinc-300 rounded-md focus:outline-none focus:ring-2 focus:ring-zinc-500"
                                placeholder="your@email.com"
                                required
                            />
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Phone Number *</label>
                            <input 
                                type="tel" 
                                x-model="form.phone"
                                class="w-full px-3 py-2 border border-zinc-300 rounded-md focus:outline-none focus:ring-2 focus:ring-zinc-500"
                                placeholder="+234 xxx xxx xxxx"
                                required
                            />
                        </div>

                        <!-- Referral Code -->
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 mb-1">Referral Code (Optional)</label>
                            <input 
                                type="text" 
                                x-model="form.referral_code"
                                class="w-full px-3 py-2 border border-zinc-300 rounded-md focus:outline-none focus:ring-2 focus:ring-zinc-500"
                                placeholder="Enter referral code if any"
                            />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-3 pt-4">
                            <button 
                                type="button"
                                @click="closeModal()"
                                class="flex-1 px-4 py-2 text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit"
                                :disabled="loading"
                                class="flex-1 px-4 py-2 text-white bg-zinc-900 rounded-md hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span x-show="!loading" x-text="modalType === 'quote' ? 'Get Quote' : 'Schedule Demo'"></span>
                                <span x-show="loading" class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 2: Success Message -->
                <div x-show="step === 2" class="text-center py-6">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-zinc-900 mb-2">Request Submitted!</h3>
                    <p class="text-zinc-600 mb-4">
                        We're redirecting you to WhatsApp to continue the conversation...
                    </p>
                    <div class="inline-flex items-center text-sm text-zinc-500">
                        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Redirecting...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
[x-cloak] {
    display: none !important;
}
</style>