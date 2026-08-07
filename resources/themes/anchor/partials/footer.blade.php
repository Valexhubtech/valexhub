<footer class="relative bg-zinc-950 border-t border-zinc-800/60 overflow-hidden">

    {{-- Gradient orbs --}}
    <div class="absolute left-1/4 top-0 w-[500px] h-[300px] bg-blue-600/5 rounded-full blur-3xl pointer-events-none -translate-y-1/2"></div>
    <div class="absolute right-1/4 bottom-0 w-[400px] h-[200px] bg-indigo-600/5 rounded-full blur-3xl pointer-events-none translate-y-1/2"></div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-8 pt-16 pb-8">

        {{-- Main grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-6 gap-12 pb-12 border-b border-zinc-800/60">

            {{-- Brand column --}}
            <div class="lg:col-span-2">
                <a href="{{ route('home') }}" wire:navigate class="inline-block mb-5 opacity-80 hover:opacity-100 transition-opacity duration-200 brightness-0 invert">
                    <x-logo class="h-7 w-auto"></x-logo>
                </a>
                <p class="text-zinc-500 text-sm leading-relaxed max-w-xs">
                    Empowering Nigerian businesses with software built for how you actually work — offline-capable, Naira-first, and locally supported.
                </p>
                <div class="flex items-center gap-3 mt-6">
                    <a href="https://twitter.com/valexhub_" target="_blank" rel="noopener" aria-label="X (Twitter)"
                       class="w-9 h-9 flex items-center justify-center rounded-lg border border-zinc-800 text-zinc-600 hover:text-white hover:border-zinc-600 transition-all duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path></svg>
                    </a>
                    <a href="https://www.instagram.com/valexhub__" target="_blank" rel="noopener" aria-label="Instagram"
                       class="w-9 h-9 flex items-center justify-center rounded-lg border border-zinc-800 text-zinc-600 hover:text-white hover:border-zinc-600 transition-all duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path></svg>
                    </a>
                    <a href="https://www.facebook.com/valexhub__" target="_blank" rel="noopener" aria-label="Facebook"
                       class="w-9 h-9 flex items-center justify-center rounded-lg border border-zinc-800 text-zinc-600 hover:text-white hover:border-zinc-600 transition-all duration-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"></path></svg>
                    </a>
                </div>
            </div>

            {{-- Nav columns --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-8 lg:col-span-4">
                <div>
                    <h3 class="text-[10px] font-semibold tracking-widest uppercase text-zinc-500 mb-5">Products</h3>
                    <ul class="space-y-3">
                        @foreach(\Wave\Category::whereHas('products', function($query) { $query->where('is_active', true); })->get() as $category)
                            <li><a href="{{ route('products.category', ['category' => $category->slug]) }}" wire:navigate class="text-zinc-400 hover:text-white text-sm transition-colors duration-200">{{ $category->name }}</a></li>
                        @endforeach
                        <li><a href="{{ route('products') }}" wire:navigate class="text-zinc-600 hover:text-zinc-400 text-sm transition-colors duration-200">View all →</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-[10px] font-semibold tracking-widest uppercase text-zinc-500 mb-5">Company</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('about') }}" wire:navigate class="text-zinc-400 hover:text-white text-sm transition-colors duration-200">About Us</a></li>
                        <li><a href="{{ route('blog') }}" wire:navigate class="text-zinc-400 hover:text-white text-sm transition-colors duration-200">Blog</a></li>
                        <li><a href="{{ route('book-demo') }}" wire:navigate class="text-zinc-400 hover:text-white text-sm transition-colors duration-200">Contact</a></li>
                        <li><a href="{{ route('dashboard.affiliate-tracker') }}" wire:navigate class="text-zinc-400 hover:text-white text-sm transition-colors duration-200">Affiliate Program</a></li>
                        <li><a href="{{ route('internship') }}" wire:navigate class="text-zinc-400 hover:text-white text-sm transition-colors duration-200">Internship</a></li>
                    </ul>
                </div>
                {{-- Resources: admin-created pages --}}
                <div>
                    <h3 class="text-[10px] font-semibold tracking-widest uppercase text-zinc-500 mb-5">Resources</h3>
                    <ul class="space-y-3">
                        @foreach(\Wave\Page::where('status', 'ACTIVE')->get() as $page)
                            <li><a href="{{ route($page->slug) }}" class="text-zinc-400 hover:text-white text-sm transition-colors duration-200">{{ $page->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <h3 class="text-[10px] font-semibold tracking-widest uppercase text-zinc-500 mb-5">Get in Touch</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('book-demo') }}" wire:navigate class="flex items-center gap-2 text-zinc-400 hover:text-white text-sm transition-colors duration-200">
                                <svg class="w-4 h-4 flex-shrink-0 text-zinc-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                WhatsApp Support
                            </a>
                        </li>
                        <li><a href="mailto:info.valexhub@gmail.com" class="text-zinc-400 hover:text-white text-sm transition-colors duration-200 break-all">info.valexhub@gmail.com</a></li>
                    </ul>
                    <div class="mt-6 pt-6 border-t border-zinc-800/60">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-zinc-600 mb-2">Business Hours (WAT)</p>
                        <p class="text-xs text-zinc-500">Mon – Fri: 9:00 AM – 6:00 PM</p>
                        <p class="text-xs text-zinc-500 mt-1">Saturday: 10:00 AM – 4:00 PM</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-zinc-600 order-2 sm:order-1">&copy; {{ date('Y') }} ValexHub. All rights reserved.</p>
            <p class="text-xs text-zinc-700 order-1 sm:order-2">Built for Nigerian businesses</p>
        </div>

    </div>

</footer>