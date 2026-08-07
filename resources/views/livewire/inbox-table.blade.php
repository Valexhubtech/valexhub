<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Email Logs</h1>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                Sent via Resend &mdash; domain:
                <span class="font-medium text-primary-600 dark:text-primary-400">{{ $configuredDomain }}</span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="refresh"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
            <button wire:click="testApiConnection"
                class="inline-flex items-center gap-1.5 rounded-lg border border-primary-300 dark:border-primary-700 bg-primary-50 dark:bg-primary-900/20 px-3 py-2 text-sm font-medium text-primary-700 dark:text-primary-400 shadow-sm hover:bg-primary-100 dark:hover:bg-primary-900/40 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Test API
            </button>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('api_test'))
        <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-700 dark:text-green-400">
            {{ session('api_test') }}
        </div>
    @endif
    @if(session('api_error'))
        <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-400">
            {{ session('api_error') }}
        </div>
    @endif
    @if(session('debug_info'))
        <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 px-4 py-3 text-sm text-amber-700 dark:text-amber-400">
            {{ session('debug_info') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Search</label>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="From, to, or subject…"
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                <select wire:model.live="status"
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All statuses</option>
                    <option value="sent">Sent</option>
                    <option value="delivered">Delivered</option>
                    <option value="bounced">Bounced</option>
                    <option value="complained">Complained</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">From</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">To</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sent</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($emails as $email)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                        <td class="px-5 py-3.5 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">
                            {{ $email['from'] }}
                        </td>
                        <td class="px-5 py-3.5 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">
                            {{ Str::limit($email['to'], 35) }}
                        </td>
                        <td class="px-5 py-3.5 text-sm text-gray-900 dark:text-white max-w-xs truncate">
                            {{ $email['subject'] ?: '(no subject)' }}
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @php
                                $statusClass = match($email['status']) {
                                    'delivered' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'bounced'   => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'complained'=> 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    'sent'      => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    default     => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($email['status']) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($email['created_at'])->diffForHumans() }}
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button wire:click="viewEmail('{{ $email['id'] }}')"
                                    class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:underline">
                                    View
                                </button>
                                <button wire:click="viewEvents('{{ $email['id'] }}')"
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:underline">
                                    Events
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                            No emails found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer count --}}
    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
        <span>Showing {{ $emails->count() }} of {{ $totalEmails }} emails</span>
        @if($status || $search)
            <button wire:click="clearFilters" class="text-primary-600 dark:text-primary-400 hover:underline">
                Clear filters
            </button>
        @endif
    </div>

    {{-- ── Email Detail Modal ─────────────────────────────────────────────── --}}
    @if($showModal && $selectedEmail)
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto p-4 sm:p-8"
             x-data x-on:keydown.escape.window="$wire.closeModal()">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm" wire:click="closeModal"></div>

            {{-- Panel --}}
            <div class="relative z-10 w-full max-w-4xl rounded-2xl bg-white dark:bg-gray-900 shadow-2xl ring-1 ring-black/10 dark:ring-white/10 my-8">

                {{-- Modal header --}}
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 px-6 py-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ $selectedEmail['subject'] ?: '(no subject)' }}</h2>
                        <p class="mt-0.5 text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($selectedEmail['created_at'])->format('M j, Y · g:i A') }}
                        </p>
                    </div>
                    <button wire:click="closeModal" class="rounded-lg p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Meta grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-px bg-gray-100 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-800">
                    @foreach([
                        ['label' => 'From',   'value' => $selectedEmail['from']],
                        ['label' => 'To',     'value' => $selectedEmail['to']],
                        ['label' => 'Status', 'value' => ucfirst($selectedEmail['status'])],
                        ['label' => 'Reply-to','value' => $selectedEmail['reply_to'] ?: '—'],
                    ] as $meta)
                        <div class="bg-white dark:bg-gray-900 px-5 py-3">
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ $meta['label'] }}</p>
                            <p class="mt-0.5 text-sm text-gray-900 dark:text-white truncate">{{ $meta['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-5">
                    @if(!empty($selectedEmail['html']))
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Preview</p>
                            {{-- iframe isolates the email's own CSS from the admin UI --}}
                            <iframe srcdoc="{{ $selectedEmail['html'] }}"
                                    sandbox="allow-same-origin"
                                    class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white"
                                    style="height: 520px;"
                                    onload="this.style.height = Math.min(this.contentDocument.body.scrollHeight + 32, 700) + 'px'">
                            </iframe>
                        </div>
                    @elseif(!empty($selectedEmail['text']))
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Plain Text</p>
                            <pre class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap overflow-x-auto">{{ $selectedEmail['text'] }}</pre>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-8">No content available for this email.</p>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex justify-end border-t border-gray-100 dark:border-gray-800 px-6 py-4">
                    <button wire:click="closeModal"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Events Modal ──────────────────────────────────────────────────── --}}
    @if($showEvents)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-on:keydown.escape.window="$wire.closeEvents()">

            <div class="fixed inset-0 bg-black/50 dark:bg-black/70 backdrop-blur-sm" wire:click="closeEvents"></div>

            <div class="relative z-10 w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-2xl ring-1 ring-black/10 dark:ring-white/10">

                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Delivery Events</h2>
                    <button wire:click="closeEvents" class="rounded-lg p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-2 max-h-96 overflow-y-auto">
                    @forelse($emailEvents as $event)
                        @php
                            $evClass = match($event['type'] ?? '') {
                                'sent'      => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'delivered' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                'bounced'   => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'opened'    => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                'clicked'   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                default     => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                            };
                        @endphp
                        <div class="flex items-center justify-between rounded-lg border border-gray-100 dark:border-gray-800 px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $evClass }}">
                                {{ ucfirst($event['type'] ?? 'unknown') }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($event['created_at'])->format('M j, g:i:s A') }}
                            </span>
                        </div>
                    @empty
                        <p class="text-center text-sm text-gray-400 dark:text-gray-500 py-6">No events recorded.</p>
                    @endforelse
                </div>

                <div class="flex justify-end border-t border-gray-100 dark:border-gray-800 px-6 py-4">
                    <button wire:click="closeEvents"
                        class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
