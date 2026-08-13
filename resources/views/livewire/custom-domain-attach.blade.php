<div class="space-y-6">
    {{-- Step: Form --}}
    @if ($step === 'form')
        <div class="rounded-lg border p-6 dark:border-gray-700">
            <h3 class="mb-4 text-lg font-semibold">Connect your own domain</h3>

            @if ($error)
                <p class="mb-4 rounded bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900/20 dark:text-red-400">{{ $error }}</p>
            @endif

            <div class="flex gap-3">
                <input
                    wire:model="domain"
                    type="text"
                    placeholder="yourdomain.com"
                    class="flex-1 rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800"
                />
                <button
                    wire:click="attach"
                    wire:loading.attr="disabled"
                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="attach">Connect</span>
                    <span wire:loading wire:target="attach">Connecting…</span>
                </button>
            </div>
            @error('domain') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    @endif

    {{-- Step: Records (manual or verifying) --}}
    @if ($step === 'records' && $emailDomain)
        <div class="rounded-lg border p-6 dark:border-gray-700">
            @if ($emailDomain->status === 'manual')
                <h3 class="mb-2 text-lg font-semibold">Add these DNS records</h3>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    Log in to your DNS provider and add the following records to
                    <strong>{{ $emailDomain->domain }}</strong>. Then click Re-check.
                </p>

                <div class="overflow-x-auto rounded-md border dark:border-gray-700">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr class="text-left text-xs text-gray-500">
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Value</th>
                                <th class="px-4 py-3 w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach ($emailDomain->records ?? [] as $record)
                                <tr>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $record['subname'] ?: '@' }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $record['type'] }}</td>
                                    <td class="px-4 py-3 font-mono text-xs break-all max-w-xs">
                                        {{ implode(' ', $record['records'] ?? []) }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <button
                                            onclick="navigator.clipboard.writeText('{{ implode(' ', $record['records'] ?? []) }}')"
                                            class="text-xs text-blue-600 hover:underline"
                                        >Copy</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <h3 class="mb-2 text-lg font-semibold">Verifying DNS…</h3>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    We pushed the records to DNS automatically. Checking verification status for
                    <strong>{{ $emailDomain->domain }}</strong>.
                </p>
            @endif

            <div class="mt-4 flex items-center gap-3">
                <button
                    wire:click="recheck"
                    wire:loading.attr="disabled"
                    class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 disabled:opacity-50 dark:bg-gray-700 dark:hover:bg-gray-600"
                >
                    <span wire:loading.remove wire:target="recheck">I've added them — Re-check</span>
                    <span wire:loading wire:target="recheck">Checking…</span>
                </button>
                <span class="text-xs text-gray-500">DNS can take 5–30 min to propagate</span>
            </div>
        </div>
    @endif

    {{-- Step: Done --}}
    @if ($step === 'done' && $emailDomain)
        <div class="rounded-lg border border-green-200 bg-green-50 p-6 dark:border-green-800 dark:bg-green-900/20">
            <h3 class="mb-1 text-lg font-semibold text-green-800 dark:text-green-300">Domain connected</h3>
            <p class="text-sm text-green-700 dark:text-green-400">
                <strong>{{ $emailDomain->domain }}</strong> is active. Your workspace sends email from this domain.
            </p>
        </div>
    @endif
</div>
