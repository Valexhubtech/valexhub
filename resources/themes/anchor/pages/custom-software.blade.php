<?php
use function Laravel\Folio\{name};
name('custom-software');
?>

<x-layouts.marketing
    :seo="[
        'title'       => 'Custom Software Development — ValexHub',
        'description' => 'Bespoke business software built exactly to your workflow. We design, build, test, and deploy — then support it as your business grows.',
        'canonical'   => url('/custom-software'),
    ]">

    {{-- Hero --}}
    <section class="relative pt-24 pb-20 lg:pt-32 lg:pb-28 bg-white overflow-hidden">
        <div class="absolute inset-0 opacity-40" style="background-image: radial-gradient(circle, #d4d4d8 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="absolute -top-32 -left-32 w-[600px] h-[600px] bg-indigo-100/50 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold tracking-widest uppercase text-blue-600 mb-5">Custom Development</p>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-zinc-900 leading-[0.95] mb-6">
                    Built exactly<br>how your<br>business works
                </h1>
                <p class="text-zinc-500 text-lg leading-relaxed mb-10 max-w-2xl">
                    When none of our pre-built solutions fit your workflow, we build from scratch. Fixed price, local support, and delivered in weeks — not months.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('book-demo') }}" wire:navigate
                       class="inline-flex items-center gap-2 px-7 py-3.5 bg-zinc-900 text-white font-semibold text-sm rounded-xl hover:bg-zinc-800 transition-colors shadow-sm">
                        Start a Discovery Call
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('products') }}" wire:navigate
                       class="inline-flex items-center gap-2 px-7 py-3.5 border border-zinc-200 text-zinc-700 font-semibold text-sm rounded-xl hover:bg-zinc-50 transition-colors">
                        View Pre-built Options
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Process timeline --}}
    <section class="relative py-20 lg:py-28 bg-zinc-900 overflow-hidden">
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
             style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 60px 60px;"></div>
        <span class="absolute right-8 top-1/2 -translate-y-1/2 text-[16rem] font-black text-white/[0.02] select-none leading-none pointer-events-none hidden lg:block">7</span>

        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-14">
                <p class="text-xs font-semibold tracking-widest uppercase text-zinc-500 mb-3">The Process</p>
                <h2 class="text-3xl sm:text-4xl font-black text-white leading-[0.95]">How we build your software</h2>
                <p class="mt-4 text-zinc-400 text-sm max-w-xl mx-auto">From your first call to the day your team goes live — here's every step.</p>
            </div>
            <div class="max-w-2xl mx-auto">
                <ol class="relative space-y-0 before:absolute before:top-0 before:left-5 before:h-full before:w-px before:bg-zinc-700">
                    @php
                        $steps = [
                            ['week' => 'Week 1', 'color' => 'bg-blue-500', 'color_text' => 'text-blue-400', 'title' => 'Discovery Call', 'body' => 'We understand your business, current pain points, and desired outcomes. No commitment required — just a conversation.'],
                            ['week' => 'Week 1', 'color' => 'bg-blue-500', 'color_text' => 'text-blue-400', 'title' => 'Requirements & Fixed Quote', 'body' => 'We produce a detailed scope document with every feature, a timeline, and an exact price. No hourly rates or surprises.'],
                            ['week' => 'Week 2–6', 'color' => 'bg-blue-500', 'color_text' => 'text-blue-400', 'title' => 'Build & Weekly Reviews', 'body' => 'Development with weekly demos so you can see progress, give feedback, and stay in control throughout.'],
                            ['week' => 'Week 7', 'color' => 'bg-green-500', 'color_text' => 'text-green-400', 'title' => 'Deploy, Train & Support', 'body' => 'We go live, train your team, hand over full documentation, and provide 30-day post-launch support.'],
                        ];
                    @endphp
                    @foreach($steps as $step)
                        <li class="relative flex gap-6 pb-10 last:pb-0">
                            <div class="flex-shrink-0 w-10 flex flex-col items-center">
                                <span class="relative z-10 w-4 h-4 mt-1 rounded-full {{ $step['color'] }} ring-4 ring-zinc-900 shadow-lg"></span>
                            </div>
                            <div class="flex-1 pb-2">
                                <time class="text-xs font-semibold uppercase tracking-widest {{ $step['color_text'] }}">{{ $step['week'] }}</time>
                                <h3 class="mt-1 text-lg font-bold text-white">{{ $step['title'] }}</h3>
                                <p class="mt-2 text-sm text-zinc-300 leading-relaxed max-w-lg">{{ $step['body'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- What We Build --}}
    <section class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-14">
                <p class="text-xs font-semibold tracking-widest uppercase text-zinc-500 mb-3">Capabilities</p>
                <h2 class="text-3xl sm:text-4xl font-black text-zinc-900 leading-[0.95]">What we can build for you</h2>
                <p class="mt-4 text-zinc-500 text-sm max-w-xl mx-auto">If you can describe it, we can build it. Here are the most common types of systems we deliver.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $canBuild = [
                        ['title' => 'CRM Systems',           'body' => 'Customer relationship management tailored to your sales and follow-up process.'],
                        ['title' => 'Inventory Management',  'body' => 'Stock control, supplier management, low-stock alerts, and automated reordering.'],
                        ['title' => 'HR & Payroll',          'body' => 'Employee records, attendance tracking, and automated payroll with tax calculations.'],
                        ['title' => 'Accounting Software',   'body' => 'Invoicing, expense tracking, Naira-denominated financial reporting.'],
                        ['title' => 'E-commerce Platforms',  'body' => 'Online stores with Paystack integration, order management, and delivery tracking.'],
                        ['title' => 'Workflow Automation',   'body' => 'Replace manual processes with automated systems that save hours every week.'],
                        ['title' => 'Multi-branch Systems',  'body' => 'Unified dashboards across multiple locations with per-branch reporting.'],
                        ['title' => 'Data Dashboards',       'body' => 'Real-time KPI dashboards that turn your raw data into decisions.'],
                        ['title' => 'Any Bespoke System',    'body' => 'If it doesn\'t exist above, tell us what you need and we\'ll scope it.'],
                    ];
                @endphp
                @foreach($canBuild as $item)
                    <div class="p-6 border border-zinc-100 rounded-2xl hover:border-zinc-300 hover:shadow-sm transition-all duration-200 group">
                        <h3 class="font-bold text-zinc-900 mb-2 group-hover:text-zinc-700 transition-colors">{{ $item['title'] }}</h3>
                        <p class="text-sm text-zinc-500 leading-relaxed">{{ $item['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why Choose Us --}}
    <section class="py-20 lg:py-24 bg-zinc-50 border-y border-zinc-100">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-start">
                <div>
                    <p class="text-xs font-semibold tracking-widest uppercase text-zinc-500 mb-4">Why ValexHub</p>
                    <h2 class="text-3xl sm:text-4xl font-black text-zinc-900 leading-[0.95] mb-8">Everything is included, nothing is hidden</h2>
                    <ul class="space-y-5">
                        @php
                            $reasons = [
                                ['title' => 'Fixed price, no surprises', 'body' => 'We agree on a price before development starts. You know what you\'re paying on day one.'],
                                ['title' => 'Nigerian-first design', 'body' => 'Every system includes Paystack, Naira support, offline mode, and local WhatsApp notifications by default.'],
                                ['title' => 'Ongoing support included', 'body' => 'We don\'t disappear after launch. Updates, bug fixes, and support are part of the monthly fee.'],
                                ['title' => 'You own the code', 'body' => 'The software we build for you belongs to you — not locked in our servers.'],
                            ];
                        @endphp
                        @foreach($reasons as $r)
                            <li class="flex gap-4">
                                <div class="flex-shrink-0 w-6 h-6 bg-zinc-900 rounded-full flex items-center justify-center mt-0.5">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-zinc-900 text-sm">{{ $r['title'] }}</p>
                                    <p class="text-zinc-500 text-sm mt-0.5 leading-relaxed">{{ $r['body'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="space-y-4">
                    <p class="text-xs font-semibold tracking-widest uppercase text-zinc-500 mb-5">Example Projects</p>
                    @php
                        $examples = [
                            ['title' => 'Manufacturing ERP', 'body' => 'Production planning, inventory, and sales system for a Lagos manufacturer with 3 facilities.'],
                            ['title' => 'Logistics Tracking Platform', 'body' => 'Package tracking, route optimization, and driver management for a delivery company.'],
                            ['title' => 'Medical Records System', 'body' => 'Patient management, prescriptions, and electronic health records for a private hospital.'],
                        ];
                    @endphp
                    @foreach($examples as $e)
                        <div class="p-5 bg-white border border-zinc-200 rounded-xl">
                            <h4 class="font-bold text-zinc-900 text-sm mb-1">{{ $e['title'] }}</h4>
                            <p class="text-zinc-500 text-sm leading-relaxed">{{ $e['body'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="relative overflow-hidden bg-zinc-950 py-24">
        <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-3xl mx-auto px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-[0.95] mb-6">
                Let's talk about your project
            </h2>
            <p class="text-zinc-400 text-base leading-relaxed mb-10">
                Book a free discovery call. We'll listen to what you need, tell you exactly how we'd build it, and give you a fixed quote — no obligation.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('book-demo') }}" wire:navigate
                   class="px-8 py-4 text-zinc-900 bg-white rounded-lg hover:bg-zinc-100 font-semibold transition-colors inline-flex items-center justify-center gap-2">
                    Book a Discovery Call
                </a>
                <a href="{{ route('products') }}" wire:navigate
                   class="px-8 py-4 text-white border border-zinc-700 rounded-lg hover:bg-zinc-900 hover:border-zinc-600 font-semibold transition-colors inline-flex items-center justify-center">
                    View Pre-built Solutions
                </a>
            </div>
        </div>
    </section>

</x-layouts.marketing>
