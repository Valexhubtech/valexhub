<?php
    use function Laravel\Folio\{name};
    name('book-demo');
    
    // Get all active products for the dropdown
    $products = \Wave\Product::where('is_active', true)->orderBy('name')->get();
?>

<x-layouts.marketing
    :seo="[
        'title' => 'Book a Free Demo - ValexHub Software Solutions',
        'description' => 'Schedule a free 30-minute demo to see how our business management systems can transform your operations and boost revenue.',
    ]"
>
    <x-container>
        <div class="py-16">
            <!-- Header -->
            <div class="text-center mb-16">
                <h1 class="text-4xl font-bold text-zinc-900 mb-4">Book Your Free Demo</h1>
                <p class="text-xl text-zinc-500 max-w-3xl mx-auto">See exactly how our software will work for your business. Get a personalized 30-minute demo tailored to your industry and needs.</p>
            </div>

            <!-- What to Expect Section -->
            <div class="mb-16">
                <h2 class="text-2xl font-bold text-zinc-900 text-center mb-8">What to Expect in Your Demo</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-zinc-100 rounded-full">
                            <span class="text-xl font-bold text-zinc-600">1</span>
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Business Assessment</h3>
                        <p class="text-sm text-zinc-500">5-minute discussion about your current challenges and specific needs</p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-zinc-100 rounded-full">
                            <span class="text-xl font-bold text-zinc-600">2</span>
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Live Software Demo</h3>
                        <p class="text-sm text-zinc-500">20-minute walkthrough of the actual software with relevant data</p>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-zinc-100 rounded-full">
                            <span class="text-xl font-bold text-zinc-600">3</span>
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Q&A Session</h3>
                        <p class="text-sm text-zinc-500">5 minutes for questions about features, pricing, and implementation</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- Demo Request Form -->
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 mb-8">Request Your Demo</h2>
                    
                    <div 
                        x-data="{
                            loading: false,
                            step: 1,
                            form: {
                                name: '',
                                email: '',
                                phone: '',
                                referral_code: '',
                                product_id: '',
                                request_type: 'demo'
                            },
                            
                            async submitForm() {
                                if (!this.form.name || !this.form.email || !this.form.phone || !this.form.product_id) {
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
                                            this.step = 1;
                                            this.resetForm();
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
                                this.form.product_id = '';
                            }
                        }"
                        x-cloak
                    >
                        <!-- Step 1: Form -->
                        <div x-show="step === 1" class="bg-white p-6 border border-zinc-200 rounded-lg">
                            <form @submit.prevent="submitForm()" class="space-y-6">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 mb-2">Full Name *</label>
                                    <input 
                                        type="text" 
                                        x-model="form.name"
                                        class="w-full px-4 py-3 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500"
                                        placeholder="Your full name"
                                        required
                                    />
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 mb-2">Email Address *</label>
                                    <input 
                                        type="email" 
                                        x-model="form.email"
                                        class="w-full px-4 py-3 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500"
                                        placeholder="your@email.com"
                                        required
                                    />
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 mb-2">Phone Number *</label>
                                    <input 
                                        type="tel" 
                                        x-model="form.phone"
                                        class="w-full px-4 py-3 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500"
                                        placeholder="+234 xxx xxx xxxx"
                                        required
                                    />
                                </div>

                                <!-- Product Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 mb-2">Which Product Demo? *</label>
                                    <select 
                                        x-model="form.product_id"
                                        class="w-full px-4 py-3 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500"
                                        required
                                    >
                                        <option value="">Select a product for demo...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Referral Code -->
                                <div>
                                    <label class="block text-sm font-medium text-zinc-700 mb-2">Referral Code (Optional)</label>
                                    <input 
                                        type="text" 
                                        x-model="form.referral_code"
                                        class="w-full px-4 py-3 border border-zinc-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-zinc-500"
                                        placeholder="Enter referral code if any"
                                    />
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4">
                                    <button 
                                        type="submit"
                                        :disabled="loading"
                                        class="w-full px-6 py-4 text-white bg-zinc-900 rounded-lg hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed font-medium"
                                    >
                                        <span x-show="!loading">Schedule My Free Demo</span>
                                        <span x-show="loading" class="inline-flex items-center">
                                            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Processing...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Step 2: Success Message -->
                        <div x-show="step === 2" class="text-center py-8 bg-white border border-zinc-200 rounded-lg">
                            <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-zinc-900 mb-2">Demo Request Submitted!</h3>
                            <p class="text-zinc-600 mb-4">
                                We're redirecting you to WhatsApp to schedule your demo...
                            </p>
                            <div class="inline-flex items-center text-sm text-zinc-500">
                                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Redirecting...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Options & Additional Info -->
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 mb-8">Other Contact Methods</h2>
                    
                    <!-- WhatsApp Option -->
                    <div class="p-6 border border-zinc-200 rounded-lg mb-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-zinc-100 rounded-lg flex-shrink-0">
                                <svg class="w-6 h-6 text-zinc-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-2.462-.96-4.779-2.705-6.526-1.746-1.746-4.065-2.711-6.526-2.713-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.092-.634zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-zinc-900 mb-2">WhatsApp</h3>
                                <p class="text-sm text-zinc-500 mb-4">Quick chat for instant assistance</p>
                                <a href="https://wa.me/2347082938319?text=Hi%2C%20I%27d%20like%20to%20book%20a%20free%20demo%20of%20your%20business%20management%20systems" target="_blank" class="inline-flex items-center px-4 py-2 text-white bg-zinc-900 rounded-md hover:bg-zinc-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-2.462-.96-4.779-2.705-6.526-1.746-1.746-4.065-2.711-6.526-2.713-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.092-.634zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                    Chat Now
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Phone Call Option -->
                    <div class="p-6 border border-zinc-200 rounded-lg mb-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-zinc-100 rounded-lg flex-shrink-0">
                                <x-phosphor-phone class="w-6 h-6 text-zinc-600" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-zinc-900 mb-2">Phone Call</h3>
                                <p class="text-sm text-zinc-500 mb-4">Speak directly with our consultants</p>
                                <a href="tel:+2347082938319" class="inline-flex items-center px-4 py-2 text-white bg-zinc-900 rounded-md hover:bg-zinc-800">
                                    <x-phosphor-phone class="w-4 h-4 mr-2" />
                                    +234 708 293 8319
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Email Option -->
                    <div class="p-6 border border-zinc-200 rounded-lg mb-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-zinc-100 rounded-lg flex-shrink-0">
                                <x-phosphor-envelope class="w-6 h-6 text-zinc-600" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-zinc-900 mb-2">Email</h3>
                                <p class="text-sm text-zinc-500 mb-4">Send us your requirements for scheduling</p>
                                <a href="mailto:demo@valexhub.com?subject=Demo Request&body=Hi, I'd like to book a demo for your business management systems." class="inline-flex items-center px-4 py-2 text-white bg-zinc-900 rounded-md hover:bg-zinc-800">
                                    <x-phosphor-envelope class="w-4 h-4 mr-2" />
                                    demo@valexhub.com
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Why Book a Demo -->
            <div class="mt-16 text-center">
                <h2 class="text-2xl font-bold text-zinc-900 mb-8">Why Book a Demo?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-eye class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">See Before You Buy</h3>
                        <p class="text-sm text-zinc-500">Experience the software firsthand to ensure it meets your needs.</p>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-chat-circle class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">Ask Questions</h3>
                        <p class="text-sm text-zinc-500">Get answers about features, customization, and implementation process.</p>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-zinc-100 rounded-lg">
                            <x-phosphor-handshake class="w-6 h-6 text-zinc-600" />
                        </div>
                        <h3 class="font-semibold text-zinc-900 mb-2">No Commitment</h3>
                        <p class="text-sm text-zinc-500">Absolutely free with no obligation to purchase.</p>
                    </div>
                </div>
            </div>
        </div>
    </x-container>
</x-layouts.marketing>

<style>
[x-cloak] {
    display: none !important;
}
</style>