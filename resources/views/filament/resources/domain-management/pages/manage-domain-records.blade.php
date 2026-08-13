<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header info --}}
        <x-filament::section>
            <div class="flex flex-wrap gap-4 text-sm">
                <div><span class="text-gray-500">Domain:</span> <strong>{{ $record->domain }}</strong></div>
                <div><span class="text-gray-500">DNS Host:</span> <strong>{{ $record->dns_host }}</strong></div>
                <div><span class="text-gray-500">Registrar:</span> <strong>{{ $record->registrar }}</strong></div>
                <div><span class="text-gray-500">Managed:</span> <strong>{{ $record->managed ? 'Yes' : 'No' }}</strong></div>
            </div>
        </x-filament::section>

        {{-- Locked record confirmation modal --}}
        @if ($pendingEditIndex !== null)
            <x-filament::section heading="Confirm change to system-managed record">
                <p class="mb-3 text-sm text-red-700 dark:text-red-400">
                    This record is system-managed. Type <strong>{{ $requiredConfirmation }}</strong> to confirm deletion.
                </p>
                <div class="flex gap-3">
                    <input wire:model="confirmationText" type="text" placeholder="{{ $requiredConfirmation }}"
                        class="flex-1 rounded-md border px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800" />
                    <button wire:click="confirmLockedEdit"
                        class="rounded-md bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">Confirm delete</button>
                    <button wire:click="cancelLockedEdit"
                        class="rounded-md border px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-800">Cancel</button>
                </div>
            </x-filament::section>
        @endif

        {{-- DNS records table --}}
        <x-filament::section heading="DNS Records">
            <div class="overflow-x-auto rounded-md border dark:border-gray-700">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr class="text-left text-xs text-gray-500">
                            <th class="px-4 py-3">Subname</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">TTL</th>
                            <th class="px-4 py-3">Value</th>
                            <th class="px-4 py-3">Lock</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        @foreach ($rrsets as $rrset)
                            @php
                                $locked = $this->isLocked($rrset['subname'], $rrset['type']);
                                $reason = $locked ? $this->lockReason($rrset['subname'], $rrset['type']) : '';
                            @endphp
                            <tr @class(['opacity-60' => $locked])>
                                <td class="px-4 py-2 font-mono text-xs">{{ $rrset['subname'] ?: '@' }}</td>
                                <td class="px-4 py-2 font-mono text-xs">{{ $rrset['type'] }}</td>
                                <td class="px-4 py-2 text-xs">{{ $rrset['ttl'] }}</td>
                                <td class="px-4 py-2 font-mono text-xs break-all max-w-xs">
                                    @foreach ($rrset['records'] as $val)
                                        <div>{{ $val }}</div>
                                    @endforeach
                                </td>
                                <td class="px-4 py-2 text-xs">
                                    @if ($locked)
                                        <span title="{{ $reason }}"
                                            class="cursor-help rounded bg-gray-200 px-1.5 py-0.5 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                            🔒
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    <button
                                        wire:click="deleteRecord('{{ $rrset['subname'] }}', '{{ $rrset['type'] }}')"
                                        class="text-xs text-red-600 hover:underline"
                                    >Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Add record form --}}
        <x-filament::section heading="Add Record">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-5">
                <input wire:model="newSubname" type="text" placeholder="Subname (blank = @)"
                    class="rounded-md border px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800" />
                <select wire:model="newType"
                    class="rounded-md border px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800">
                    @foreach (['A','AAAA','CNAME','MX','TXT','SRV'] as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
                <input wire:model="newTtl" type="number" placeholder="TTL" value="3600"
                    class="rounded-md border px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800" />
                <input wire:model="newValue" type="text" placeholder="Value"
                    class="col-span-2 rounded-md border px-3 py-2 text-sm sm:col-span-1 dark:border-gray-600 dark:bg-gray-800" />
            </div>
            <div class="mt-3">
                <button wire:click="addRecord"
                    class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">Add Record</button>
            </div>
        </x-filament::section>

        {{-- dns_changes log --}}
        <x-filament::section heading="Change Log">
            @if (empty($changelog))
                <p class="text-sm text-gray-500">No changes recorded yet.</p>
            @else
                <div class="overflow-x-auto rounded-md border dark:border-gray-700">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr class="text-left text-gray-500">
                                <th class="px-4 py-2">When</th>
                                <th class="px-4 py-2">Actor</th>
                                <th class="px-4 py-2">Action</th>
                                <th class="px-4 py-2">Type</th>
                                <th class="px-4 py-2">Subname</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @foreach ($changelog as $entry)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $entry['created_at'] }}</td>
                                    <td class="px-4 py-2">{{ $entry['actor'] }}</td>
                                    <td class="px-4 py-2">
                                        <span @class([
                                            'rounded px-1.5 py-0.5 font-semibold',
                                            'bg-green-100 text-green-800' => $entry['action'] === 'create',
                                            'bg-blue-100 text-blue-800' => $entry['action'] === 'update',
                                            'bg-red-100 text-red-800' => $entry['action'] === 'delete',
                                        ])>{{ $entry['action'] }}</span>
                                    </td>
                                    <td class="px-4 py-2 font-mono">{{ $entry['record_type'] }}</td>
                                    <td class="px-4 py-2 font-mono">{{ $entry['subname'] ?: '@' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
