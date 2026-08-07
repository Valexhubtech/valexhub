<?php

use function Laravel\Folio\{name};
use Wave\InternshipSession;

name('internship');

$session = InternshipSession::getActive();
$isOpen  = $session && $session->isOpen();
?>

<x-layouts.marketing
    :seo="[
        'title'       => 'Internship Program — ValexHub',
        'description' => 'Apply for the ValexHub internship program. Gain hands-on experience building real software for Nigerian businesses — mentorship, live projects, and a path to full employment.',
        'canonical'   => url('/internship'),
    ]">

    {{-- Hero --}}
    <section class="relative bg-zinc-950 pt-24 pb-20 lg:pt-32 lg:pb-28 overflow-hidden">
        <div class="absolute top-0 left-1/4 w-[500px] h-[400px] bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-[400px] h-[300px] bg-indigo-600/8 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
             style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 60px 60px;"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                <div>
                    <p class="text-xs font-semibold tracking-widest uppercase text-zinc-500 mb-5">
                        {{ $session ? $session->name : 'Internship Program' }}
                    </p>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-[0.95] mb-6">
                        Build real<br>software.<br>
                        <span class="text-zinc-400">Get mentored.</span>
                    </h1>
                    <p class="text-zinc-400 text-base leading-relaxed max-w-md">
                        Join ValexHub and work on live products used by real businesses across Nigeria. This isn't a classroom exercise — you'll ship actual features alongside our engineering and business teams.
                    </p>

                    @if($session && $session->application_deadline)
                        <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 bg-white/[0.05] border border-white/[0.08] rounded-full">
                            <div class="w-2 h-2 rounded-full {{ $isOpen ? 'bg-green-400' : 'bg-red-400' }}"></div>
                            <span class="text-xs text-zinc-400">
                                @if($isOpen)
                                    Applications open · Deadline {{ $session->application_deadline->format('M j, Y') }}
                                @else
                                    Applications closed
                                @endif
                            </span>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-4">
                    @php
                        $highlights = [
                            ['num' => '3–6 mo', 'label' => 'Program duration',      'sub' => 'Structured learning with real deliverables.'],
                            ['num' => 'Live',   'label' => 'Real project exposure',  'sub' => 'Ship code used by actual paying customers.'],
                            ['num' => '→ Hire', 'label' => 'Path to employment',     'sub' => 'Outstanding interns get full-time offers.'],
                        ];
                    @endphp
                    @foreach($highlights as $h)
                        <div class="flex items-center gap-5 bg-white/[0.04] border border-white/[0.07] rounded-2xl px-6 py-5">
                            <p class="text-2xl font-black text-white flex-shrink-0 w-20">{{ $h['num'] }}</p>
                            <div>
                                <p class="text-sm font-semibold text-white">{{ $h['label'] }}</p>
                                <p class="text-xs text-zinc-500 mt-0.5">{{ $h['sub'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </section>

    {{-- Main content --}}
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">

                {{-- Application Form --}}
                <div>
                    <h2 class="text-2xl font-black text-zinc-900 mb-2">Apply now</h2>
                    <p class="text-zinc-500 text-sm mb-8">All fields marked * are required. CV must be PDF, DOC, or DOCX — max 5 MB.</p>

                    @if($isOpen)
                        <div x-data="{
                            step: 1,
                            loading: false,
                            errors: {},
                            async submitForm(e) {
                                this.loading = true;
                                this.errors = {};
                                const formData = new FormData(e.target);
                                try {
                                    const res = await fetch('/internship/apply', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                                        body: formData,
                                    });
                                    const data = await res.json();
                                    if (res.ok && data.success) {
                                        this.step = 2;
                                    } else {
                                        this.errors = data.errors ?? {};
                                        if (!Object.keys(this.errors).length) {
                                            alert(data.message || 'Something went wrong. Please try again.');
                                        }
                                    }
                                } catch {
                                    alert('Something went wrong. Please try again.');
                                }
                                this.loading = false;
                            }
                        }" x-cloak>

                            {{-- Step 1: Form --}}
                            <div x-show="step === 1" class="bg-zinc-50 border border-zinc-200 rounded-2xl p-6 lg:p-8">
                                <form @submit.prevent="submitForm($event)" enctype="multipart/form-data" class="space-y-5">

                                    {{-- Name --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">First Name *</label>
                                            <input type="text" name="first_name" required maxlength="100"
                                                   class="w-full px-4 py-3 bg-white border rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                                   :class="errors.first_name ? 'border-red-300' : 'border-zinc-200'"
                                                   placeholder="First name" />
                                            <p x-show="errors.first_name" class="mt-1 text-xs text-red-500" x-text="errors.first_name?.[0]"></p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Last Name *</label>
                                            <input type="text" name="last_name" required maxlength="100"
                                                   class="w-full px-4 py-3 bg-white border rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                                   :class="errors.last_name ? 'border-red-300' : 'border-zinc-200'"
                                                   placeholder="Last name" />
                                            <p x-show="errors.last_name" class="mt-1 text-xs text-red-500" x-text="errors.last_name?.[0]"></p>
                                        </div>
                                    </div>

                                    {{-- Contact --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Email *</label>
                                            <input type="email" name="email" required maxlength="255"
                                                   class="w-full px-4 py-3 bg-white border rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                                   :class="errors.email ? 'border-red-300' : 'border-zinc-200'"
                                                   placeholder="your@email.com" />
                                            <p x-show="errors.email" class="mt-1 text-xs text-red-500" x-text="errors.email?.[0]"></p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Phone *</label>
                                            <input type="tel" name="phone" required maxlength="20"
                                                   class="w-full px-4 py-3 bg-white border rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                                   :class="errors.phone ? 'border-red-300' : 'border-zinc-200'"
                                                   placeholder="+234 xxx xxx xxxx" />
                                            <p x-show="errors.phone" class="mt-1 text-xs text-red-500" x-text="errors.phone?.[0]"></p>
                                        </div>
                                    </div>

                                    {{-- Education --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Institution *</label>
                                            <input type="text" name="institution" required maxlength="255"
                                                   class="w-full px-4 py-3 bg-white border rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                                   :class="errors.institution ? 'border-red-300' : 'border-zinc-200'"
                                                   placeholder="University or polytechnic" />
                                            <p x-show="errors.institution" class="mt-1 text-xs text-red-500" x-text="errors.institution?.[0]"></p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Course / Field *</label>
                                            <input type="text" name="course" required maxlength="255"
                                                   class="w-full px-4 py-3 bg-white border rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                                   :class="errors.course ? 'border-red-300' : 'border-zinc-200'"
                                                   placeholder="e.g. Computer Science" />
                                            <p x-show="errors.course" class="mt-1 text-xs text-red-500" x-text="errors.course?.[0]"></p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Graduation Year *</label>
                                            <input type="number" name="graduation_year" required min="2020" max="2035"
                                                   class="w-full px-4 py-3 bg-white border rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                                   :class="errors.graduation_year ? 'border-red-300' : 'border-zinc-200'"
                                                   placeholder="{{ date('Y') }}" />
                                            <p x-show="errors.graduation_year" class="mt-1 text-xs text-red-500" x-text="errors.graduation_year?.[0]"></p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Role Applying For *</label>
                                            <select name="role" required
                                                    class="w-full px-4 py-3 bg-white border rounded-xl text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                                    :class="errors.role ? 'border-red-300' : 'border-zinc-200'">
                                                <option value="">Select a role…</option>
                                                @foreach($session->roles as $role)
                                                    <option value="{{ $role }}">{{ $role }}</option>
                                                @endforeach
                                            </select>
                                            <p x-show="errors.role" class="mt-1 text-xs text-red-500" x-text="errors.role?.[0]"></p>
                                        </div>
                                    </div>

                                    {{-- CV Upload --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Upload CV *</label>
                                        <input type="file" name="cv" required accept=".pdf,.doc,.docx"
                                               class="w-full px-4 py-3 bg-white border rounded-xl text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-900 file:text-white hover:file:bg-zinc-700 cursor-pointer"
                                               :class="errors.cv ? 'border-red-300' : 'border-zinc-200'" />
                                        <p class="mt-1 text-xs text-zinc-400">PDF, DOC, or DOCX — max 5 MB</p>
                                        <p x-show="errors.cv" class="mt-1 text-xs text-red-500" x-text="errors.cv?.[0]"></p>
                                    </div>

                                    {{-- Cover Letter --}}
                                    <div>
                                        <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Cover Letter <span class="font-normal normal-case text-zinc-400">(optional)</span></label>
                                        <textarea name="cover_letter" rows="4" maxlength="5000"
                                                  class="w-full px-4 py-3 bg-white border border-zinc-200 rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all resize-none"
                                                  placeholder="Tell us why you want to intern at ValexHub and what you'd bring to the team…"></textarea>
                                    </div>

                                    {{-- Optional links --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">LinkedIn <span class="font-normal normal-case text-zinc-400">(optional)</span></label>
                                            <input type="url" name="linkedin_url" maxlength="500"
                                                   class="w-full px-4 py-3 bg-white border border-zinc-200 rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                                   placeholder="linkedin.com/in/yourname" />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wide mb-2">Portfolio <span class="font-normal normal-case text-zinc-400">(optional)</span></label>
                                            <input type="url" name="portfolio_url" maxlength="500"
                                                   class="w-full px-4 py-3 bg-white border border-zinc-200 rounded-xl text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-300 focus:border-transparent transition-all"
                                                   placeholder="yourportfolio.com" />
                                        </div>
                                    </div>

                                    <div class="pt-2">
                                        <button type="submit" :disabled="loading"
                                                class="w-full px-6 py-4 text-white bg-zinc-900 rounded-xl hover:bg-zinc-800 disabled:opacity-50 disabled:cursor-not-allowed font-semibold text-sm transition-colors flex items-center justify-center gap-2">
                                            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            <span x-show="!loading">Submit Application</span>
                                            <span x-show="loading">Submitting…</span>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- Step 2: Success --}}
                            <div x-show="step === 2" class="text-center py-16 bg-zinc-50 border border-zinc-200 rounded-2xl">
                                <div class="w-16 h-16 mx-auto mb-5 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-black text-zinc-900 mb-2">Application received!</h3>
                                <p class="text-zinc-500 text-sm max-w-xs mx-auto">We'll review your application and get back to you by email. Thank you for applying.</p>
                            </div>
                        </div>

                    @else
                        {{-- Applications closed --}}
                        <div class="bg-zinc-50 border border-zinc-200 rounded-2xl p-10 text-center">
                            <div class="w-14 h-14 mx-auto mb-5 bg-zinc-100 rounded-full flex items-center justify-center">
                                <svg class="w-7 h-7 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-black text-zinc-900 mb-2">Applications are currently closed</h3>
                            <p class="text-zinc-500 text-sm">Follow us on social media or check back here to be the first to know when our next program opens.</p>
                        </div>
                    @endif
                </div>

                {{-- Right: Program Info --}}
                <div class="space-y-6">

                    <div>
                        <h2 class="text-2xl font-black text-zinc-900 mb-6">What to expect</h2>
                        <div class="space-y-4">
                            @php
                                $perks = [
                                    ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Real Projects', 'desc' => 'You\'ll work on actual features in production — the software running live businesses, not dummy exercises.'],
                                    ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'title' => 'Mentorship', 'desc' => 'Paired with a senior team member who reviews your work, answers your questions, and helps you grow.'],
                                    ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Fast Learning', 'desc' => 'Our stack is modern — TypeScript, React, Node.js, Laravel, PostgreSQL. You\'ll level up quickly.'],
                                    ['icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'title' => 'Employment Path', 'desc' => 'Outstanding interns who demonstrate results are offered full-time roles at the end of the program.'],
                                ];
                            @endphp
                            @foreach($perks as $perk)
                                <div class="flex items-start gap-4 p-5 border border-zinc-200 rounded-2xl hover:border-zinc-300 hover:shadow-sm transition-all duration-200">
                                    <div class="flex-shrink-0 w-10 h-10 bg-zinc-50 border border-zinc-100 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $perk['icon'] }}"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-zinc-900 text-sm">{{ $perk['title'] }}</p>
                                        <p class="text-zinc-500 text-xs mt-0.5 leading-relaxed">{{ $perk['desc'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($session && count($session->roles))
                        <div class="bg-zinc-950 rounded-2xl p-6 text-white">
                            <p class="text-xs font-semibold tracking-widest uppercase text-zinc-500 mb-4">Open roles — {{ $session->name }}</p>
                            <ul class="space-y-2">
                                @foreach($session->roles as $role)
                                    <li class="flex items-center gap-3 text-sm text-zinc-300">
                                        <svg class="w-4 h-4 text-zinc-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ $role }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="bg-zinc-50 border border-zinc-200 rounded-2xl p-6">
                        <p class="text-xs font-semibold tracking-widest uppercase text-zinc-500 mb-4">Questions?</p>
                        <p class="text-sm text-zinc-600 mb-4">Reach us on WhatsApp or email — we typically respond within the hour on business days.</p>
                        <a href="mailto:info@valexhub.com" class="text-sm font-semibold text-zinc-900 hover:underline">info@valexhub.com</a>
                    </div>
                </div>

            </div>
        </div>
    </section>

</x-layouts.marketing>

<style>[x-cloak] { display: none !important; }</style>
