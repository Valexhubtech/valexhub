<?php
    use function Laravel\Folio\{middleware, name};
    middleware('auth');
    name('dashboard.support');
?>
@php
    /** @var \App\Models\User $user */
    $user = auth()->user();
    $tickets = \Wave\SupportTicket::where('user_id', $user->id)
        ->with(['deployment.product'])
        ->withCount('messages')
        ->latest()
        ->get();

    $statusColors = [
        'open'        => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        'in_progress' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        'resolved'    => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300',
        'closed'      => 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400',
    ];
    $statusLabels = ['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'];
@endphp

<x-layouts.app>
    <x-app.container x-data class="lg:space-y-6" x-cloak>

        <div class="flex items-center justify-between">
            <x-app.heading
                title="Support Tickets"
                description="Track your support requests and conversations."
                :border="false"
            />
            <a href="{{ route('dashboard.deployments') }}"
               class="px-4 py-2 text-sm font-medium text-white rounded-md bg-zinc-900 hover:bg-zinc-800 transition-colors">
                Open New Ticket
            </a>
        </div>

        @if(session('status'))
            <div class="p-4 text-sm text-green-700 bg-green-100 rounded-md dark:bg-green-900/30 dark:text-green-400">{{ session('status') }}</div>
        @endif

        @if($tickets->isEmpty())
            <div class="py-16 text-center">
                <x-phosphor-chat-circle-dots class="w-16 h-16 mx-auto mb-4 text-zinc-400" />
                <h3 class="mb-2 text-xl font-semibold text-zinc-900 dark:text-white">No Support Tickets</h3>
                <p class="text-zinc-500 mb-4">Open a ticket from any deployment's detail page.</p>
                <a href="{{ route('dashboard.deployments') }}" wire:navigate
                   class="inline-block px-4 py-2 text-sm font-medium text-white rounded-md bg-zinc-900 hover:bg-zinc-800">
                    Go to My Deployments
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($tickets as $ticket)
                    <a href="{{ route('dashboard.support.show', $ticket) }}" wire:navigate
                       class="flex items-center justify-between p-4 bg-white border border-zinc-200 rounded-xl dark:bg-zinc-800 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-neutral-600 transition-colors">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-medium text-zinc-900 dark:text-white truncate">{{ $ticket->subject }}</p>
                                @if($ticket->messages_count > 0)
                                    <span class="flex-shrink-0 text-xs text-zinc-400">{{ $ticket->messages_count }} {{ Str::plural('message', $ticket->messages_count) }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-zinc-500 mt-0.5">
                                {{ $ticket->deployment?->product?->name ?? 'General' }}
                                · {{ $ticket->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3 ml-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$ticket->status] ?? '' }}">
                                {{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}
                            </span>
                            <x-phosphor-arrow-right class="w-4 h-4 text-zinc-400" />
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

    </x-app.container>
</x-layouts.app>
