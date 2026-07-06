<?php
    use function Laravel\Folio\{middleware, name};
    middleware('auth');
    name('dashboard.support.show');
?>
@php
    abort_unless($supportTicket->user_id === auth()->id(), 403);

    $supportTicket->load(['deployment.product']);

    $statusColors = [
        'open'        => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        'in_progress' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        'resolved'    => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300',
        'closed'      => 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400',
    ];
    $statusLabels = ['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'];
    $sc = $supportTicket->status;
@endphp

<x-layouts.app>
    <x-app.container x-data class="lg:space-y-6" x-cloak>

        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard.support') }}" wire:navigate class="flex items-center text-sm text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition-colors">
                <x-phosphor-arrow-left class="w-4 h-4 mr-1" /> Support Tickets
            </a>
        </div>

        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white">{{ $supportTicket->subject }}</h1>
                <p class="text-sm text-zinc-500 mt-0.5">
                    {{ $supportTicket->deployment?->product?->name ?? 'General' }}
                    · Opened {{ $supportTicket->created_at->format('d M Y') }}
                </p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$sc] ?? '' }}">
                {{ $statusLabels[$sc] ?? ucfirst($sc) }}
            </span>
        </div>

        <div class="p-5 bg-white border border-zinc-200 rounded-lg dark:bg-neutral-800 dark:border-neutral-700">
            <livewire:support-thread :ticket="$supportTicket" />
        </div>

    </x-app.container>
</x-layouts.app>
