<?php
    use function Laravel\Folio\{name};
    name('book-demo');
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- Contact Options -->
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 mb-8">Choose Your Preferred Method</h2>
                    
                    <!-- WhatsApp Option -->
                    <div class="p-6 border border-zinc-200 rounded-lg mb-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-zinc-100 rounded-lg flex-shrink-0">
                                <svg class="w-6 h-6 text-zinc-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-2.462-.96-4.779-2.705-6.526-1.746-1.746-4.065-2.711-6.526-2.713-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.092-.634zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-zinc-900 mb-2">WhatsApp (Instant Response)</h3>
                                <p class="text-sm text-zinc-500 mb-4">Get immediate assistance and schedule your demo via WhatsApp chat.</p>
                                <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20book%20a%20free%20demo%20of%20your%20business%20management%20systems" target="_blank" class="inline-flex items-center px-4 py-2 text-white bg-zinc-900 rounded-md hover:bg-zinc-800">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-2.462-.96-4.779-2.705-6.526-1.746-1.746-4.065-2.711-6.526-2.713-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.092-.634zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                    Chat on WhatsApp
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
                                <p class="text-sm text-zinc-500 mb-4">Speak directly with our software consultants for detailed discussions.</p>
                                <a href="tel:+2348012345678" class="inline-flex items-center px-4 py-2 text-white bg-zinc-900 rounded-md hover:bg-zinc-800">
                                    <x-phosphor-phone class="w-4 h-4 mr-2" />
                                    +234 801 234 5678
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Email Option -->
                    <div class="p-6 border border-zinc-200 rounded-lg hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center w-12 h-12 bg-zinc-100 rounded-lg flex-shrink-0">
                                <x-phosphor-envelope class="w-6 h-6 text-zinc-600" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-zinc-900 mb-2">Email</h3>
                                <p class="text-sm text-zinc-500 mb-4">Send us your requirements and we'll schedule a suitable time for your demo.</p>
                                <a href="mailto:demo@valexhub.com?subject=Demo Request&body=Hi, I'd like to book a demo for your business management systems." class="inline-flex items-center px-4 py-2 text-white bg-zinc-900 rounded-md hover:bg-zinc-800"
                                    <x-phosphor-envelope class="w-4 h-4 mr-2" />
                                    demo@valexhub.com
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- What to Expect -->
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 mb-8">What to Expect in Your Demo</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center w-8 h-8 bg-zinc-100 rounded-lg flex-shrink-0">
                                <span class="text-sm font-bold text-zinc-600">1</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-zinc-900 mb-1">Business Assessment (5 mins)</h3>
                                <p class="text-sm text-zinc-500">We'll understand your current challenges and specific needs.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center w-8 h-8 bg-zinc-100 rounded-lg flex-shrink-0">
                                <span class="text-sm font-bold text-zinc-600">2</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-zinc-900 mb-1">Live Software Demo (20 mins)</h3>
                                <p class="text-sm text-zinc-500">See the actual software in action with data relevant to your industry.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center w-8 h-8 bg-zinc-100 rounded-lg flex-shrink-0">
                                <span class="text-sm font-bold text-zinc-600">3</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-zinc-900 mb-1">Q&A Session (5 mins)</h3>
                                <p class="text-sm text-zinc-500">Ask any questions about features, pricing, or implementation.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 p-6 bg-zinc-50 rounded-lg">
                        <h3 class="font-semibold text-zinc-900 mb-3">Demo Preparation Checklist</h3>
                        <ul class="space-y-2 text-sm text-zinc-500">
                            <li>✓ Have your current challenges and goals ready to discuss</li>
                            <li>✓ Know approximate number of users who will use the system</li>
                            <li>✓ Think about your most important features or workflows</li>
                            <li>✓ Prepare any specific questions about implementation</li>
                        </ul>
                    </div>

                    <div class="mt-8">
                        <h3 class="font-semibold text-zinc-900 mb-3">Available Demo Times</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="font-medium text-zinc-900">Monday - Friday</p>
                                <p class="text-zinc-500">9:00 AM - 6:00 PM</p>
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900">Saturday</p>
                                <p class="text-zinc-500">10:00 AM - 4:00 PM</p>
                            </div>
                        </div>
                        <p class="text-xs text-zinc-500 mt-2">All times are West Africa Time (WAT)</p>
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