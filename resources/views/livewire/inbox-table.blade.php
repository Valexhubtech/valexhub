<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Inbox</h1>
            <p class="mt-1 text-sm text-gray-600">
                Email logs from Resend (last 30 days) - Domain:
                <span class="font-medium text-blue-600">{{ $configuredDomain }}</span>
            </p>
        </div>
        <div class="flex space-x-3">
            <button wire:click="testApiConnection" class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-md shadow-sm text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Test API
            </button>
            <button wire:click="debugEmails" class="inline-flex items-center px-4 py-2 border border-orange-300 rounded-md shadow-sm text-sm font-medium text-orange-700 bg-orange-50 hover:bg-orange-100">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.98-.833-2.75 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                Debug
            </button>
            <button wire:click="refresh" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- API Test Results -->
    @if(session('api_test'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('api_test') }}
        </div>
    @endif

    @if(session('api_error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('api_error') }}
        </div>
    @endif

    @if(session('debug_info'))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <strong>Debug Info:</strong> {{ session('debug_info') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input wire:model.live.debounce.300ms="search" type="text" id="search" placeholder="Search emails..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select wire:model.live="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Statuses</option>
                    <option value="sent">Sent</option>
                    <option value="delivered">Delivered</option>
                    <option value="bounced">Bounced</option>
                    <option value="complained">Complained</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Email Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($emails as $email)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $email['from'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ Str::limit($email['to'], 30) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ Str::limit($email['subject'], 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @switch($email['status'])
                                    @case('delivered') bg-green-100 text-green-800 @break
                                    @case('bounced') bg-red-100 text-red-800 @break
                                    @case('complained') bg-yellow-100 text-yellow-800 @break
                                    @case('sent') bg-blue-100 text-blue-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch">
                                {{ ucfirst($email['status']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($email['created_at'])->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button wire:click="viewEmail('{{ $email['id'] }}')" class="text-blue-600 hover:text-blue-900">View</button>
                                <button wire:click="viewEvents('{{ $email['id'] }}')" class="text-gray-600 hover:text-gray-900">Events</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No emails found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Info -->
    <div class="flex justify-between items-center text-sm text-gray-700">
        <div>
            Showing {{ $emails->count() }} of {{ $totalEmails }} emails from {{ $configuredDomain }}
            @if($status)
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Status: {{ ucfirst($status) }}
                </span>
            @endif
        </div>
        @if($status || $search)
            <button wire:click="clearFilters" class="text-blue-600 hover:text-blue-900 text-xs">
                Clear Filters
            </button>
        @endif
    </div>

    <!-- Email Details Modal -->
    @if($showModal && $selectedEmail)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Email Details</h3>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">From</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $selectedEmail['from'] }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">To</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $selectedEmail['to'] }}</div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Subject</label>
                                <div class="mt-1 text-sm text-gray-900">{{ $selectedEmail['subject'] }}</div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($selectedEmail['status'])
                                                @case('delivered') bg-green-100 text-green-800 @break
                                                @case('bounced') bg-red-100 text-red-800 @break
                                                @case('complained') bg-yellow-100 text-yellow-800 @break
                                                @case('sent') bg-blue-100 text-blue-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch">
                                            {{ ucfirst($selectedEmail['status']) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Sent At</label>
                                    <div class="mt-1 text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($selectedEmail['created_at'])->format('M d, Y H:i:s') }}
                                    </div>
                                </div>
                            </div>

                            @if(isset($selectedEmail['html']) && $selectedEmail['html'])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">HTML Content</label>
                                    <iframe srcdoc="{{ $selectedEmail['html'] }}"
                                            sandbox="allow-same-origin"
                                            class="w-full rounded-lg border border-gray-200"
                                            style="height: 480px;"
                                            onload="this.style.height = Math.min(this.contentDocument.body.scrollHeight + 32, 640) + 'px'">
                                    </iframe>
                                </div>
                            @endif

                            @if(isset($selectedEmail['text']) && $selectedEmail['text'])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Text Content</label>
                                    <div class="border rounded-lg p-4 bg-gray-50 max-h-96 overflow-y-auto">
                                        <pre class="whitespace-pre-wrap text-sm">{{ $selectedEmail['text'] }}</pre>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="closeModal" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Email Events Modal -->
    @if($showEvents)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeEvents"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Email Events</h3>
                            <button wire:click="closeEvents" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-3">
                            @forelse($emailEvents as $event)
                                <div class="border rounded-lg p-3">
                                    <div class="flex justify-between items-start">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($event['type'])
                                                @case('sent') bg-blue-100 text-blue-800 @break
                                                @case('delivered') bg-green-100 text-green-800 @break
                                                @case('bounced') bg-red-100 text-red-800 @break
                                                @case('opened') bg-purple-100 text-purple-800 @break
                                                @case('clicked') bg-orange-100 text-orange-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch">
                                            {{ ucfirst($event['type']) }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($event['created_at'])->format('M d, H:i:s') }}
                                        </span>
                                    </div>
                                    @if(isset($event['data']) && $event['data'])
                                        <div class="mt-2 text-sm text-gray-600">
                                            {{ json_encode($event['data']) }}
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center text-gray-500 py-4">
                                    No events recorded for this email.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="closeEvents" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
