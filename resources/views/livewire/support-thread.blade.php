<div class="space-y-4" wire:poll.10000ms="$refresh">

    {{-- Message thread --}}
    <div class="space-y-3">
        @forelse($messages as $message)
            <div class="flex gap-3 {{ $message->is_admin ? '' : 'flex-row-reverse' }}">
                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                    {{ $message->is_admin ? 'bg-zinc-900 text-white' : 'bg-zinc-200 text-zinc-700 dark:bg-neutral-700 dark:text-zinc-300' }}">
                    {{ $message->is_admin ? 'VH' : strtoupper(substr($message->user->name, 0, 1)) }}
                </div>
                <div class="flex-1 max-w-[80%] {{ $message->is_admin ? '' : 'text-right' }}">
                    <div class="inline-block text-left px-4 py-2.5 rounded-lg text-sm
                        {{ $message->is_admin
                            ? 'bg-zinc-900 text-white'
                            : 'bg-zinc-100 text-zinc-900 dark:bg-neutral-700 dark:text-white' }}">
                        {!! nl2br(e($message->body)) !!}
                    </div>
                    <p class="text-xs text-zinc-400 mt-1 {{ $message->is_admin ? 'text-left pl-1' : 'pr-1' }}">
                        {{ $message->is_admin ? 'ValexHub Support' : $message->user->name }}
                        · {{ $message->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-sm text-zinc-400 text-center py-4">No messages yet. Send your first message below.</p>
        @endforelse
    </div>

    {{-- Reply box --}}
    @if($ticket->isOpen())
        <div class="pt-4 border-t border-zinc-200 dark:border-neutral-700">
            @error('reply') <p class="text-sm text-red-600 mb-2">{{ $message }}</p> @enderror
            <textarea
                wire:model="reply"
                rows="3"
                placeholder="Write a reply..."
                class="w-full px-3 py-2 text-sm border rounded-md border-zinc-300 dark:bg-neutral-700 dark:border-neutral-600 dark:text-white focus:outline-none focus:ring-1 focus:ring-zinc-400 resize-none"
            ></textarea>
            <div class="flex justify-end mt-2">
                <button
                    wire:click="sendReply"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-not-allowed"
                    class="px-4 py-2 text-sm font-medium text-white rounded-md bg-zinc-900 hover:bg-zinc-800 transition-colors">
                    <span wire:loading.remove>Send Reply</span>
                    <span wire:loading>Sending…</span>
                </button>
            </div>
        </div>
    @else
        <p class="pt-4 text-sm text-center text-zinc-400 border-t border-zinc-200 dark:border-neutral-700">
            This ticket is {{ $ticket->status }}. Contact support to reopen it.
        </p>
    @endif

</div>
