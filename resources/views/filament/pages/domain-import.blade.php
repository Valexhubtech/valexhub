<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Query form --}}
        <x-filament::section heading="1. Domain to import">
            <form wire:submit.prevent="queryRecords">
                {{ $this->form }}
                <div class="mt-4">
                    <x-filament::button type="submit" color="primary">
                        Reconstruct DNS Records
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        {{-- Record review table --}}
        @if ($step !== 'query')
            <x-filament::section heading="2. Review reconstructed records">
                @if (!empty($staleFlags))
                    <div class="mb-4 rounded-md bg-yellow-50 p-4 text-sm text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300">
                        Records marked <strong>stale</strong> look like parking/default entries. Remove them before approving.
                    </div>
                @endif

                <div class="overflow-x-auto rounded-md border dark:border-gray-700">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr class="text-left text-xs text-gray-500">
                                <th class="px-4 py-3">Subname</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">TTL</th>
                                <th class="px-4 py-3">Value</th>
                                <th class="px-4 py-3">Flag</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach ($records as $i => $record)
                                <tr @class(['bg-yellow-50 dark:bg-yellow-900/10' => $record['stale'] ?? false])>
                                    <td class="px-4 py-2 font-mono text-xs">{{ $record['subname'] ?: '@' }}</td>
                                    <td class="px-4 py-2 font-mono text-xs">{{ $record['type'] }}</td>
                                    <td class="px-4 py-2 text-xs">{{ $record['ttl'] }}</td>
                                    <td class="px-4 py-2 font-mono text-xs break-all max-w-xs">
                                        {{ implode(', ', $record['records'] ?? []) }}
                                    </td>
                                    <td class="px-4 py-2 text-xs">
                                        @if ($record['stale'] ?? false)
                                            <span class="rounded bg-yellow-200 px-1.5 py-0.5 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200">stale</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">
                                        <button
                                            wire:click="removeRecord({{ $i }})"
                                            class="text-xs text-red-600 hover:underline"
                                        >Remove</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <x-filament::button
                        color="success"
                        wire:click="approveAndPush"
                        wire:confirm="This will push the approved records to deSEC and set dns_host=desec for this domain. Continue?"
                    >
                        Approve & Push to deSEC
                    </x-filament::button>
                </div>
            </x-filament::section>
        @endif

        {{-- NS switch --}}
        @if (in_array($step, ['ns_switch', 'done']))
            <x-filament::section heading="3. Switch nameservers to deSEC">
                <div class="space-y-3">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Point <strong>{{ $data['domain'] ?? '' }}</strong> to these nameservers:
                    </p>
                    <div class="flex gap-4">
                        @foreach (['ns1.desec.io', 'ns2.desec.org'] as $ns)
                            <code class="rounded bg-gray-100 px-3 py-1 text-sm dark:bg-gray-800">{{ $ns }}</code>
                        @endforeach
                    </div>

                    <div class="flex gap-3 pt-2">
                        @if (($data['registrar'] ?? 'elsewhere') === 'go54')
                            <x-filament::button
                                color="warning"
                                wire:click="switchNameservers"
                                wire:confirm="This will call the GO54 API to point {{ $data['domain'] ?? '' }} at ns1.desec.io / ns2.desec.org. Continue?"
                            >
                                Switch NS to deSEC
                            </x-filament::button>
                        @else
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Log in to your registrar and change the nameservers manually. When done, click:
                            </p>
                        @endif

                        <x-filament::button color="gray" wire:click="confirmManaged">
                            Confirm NS Live — Mark Managed
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Done --}}
        @if ($step === 'done')
            <x-filament::section>
                <p class="font-semibold text-green-700 dark:text-green-400">
                    ✓ {{ $data['domain'] ?? '' }} is now a fully managed domain.
                </p>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
