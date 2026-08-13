<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section heading="Platform Email Domain">
            <div class="space-y-3 text-sm">
                <div class="flex items-center gap-2">
                    <span class="font-medium text-gray-500">Domain:</span>
                    <code class="rounded bg-gray-100 px-2 py-0.5 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                        {{ config('services.plume.mail_domain') ?: '— set PLATFORM_MAIL_DOMAIN in .env —' }}
                    </code>
                </div>

                @if ($provisionStatus)
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-500">Last status:</span>
                        <span @class([
                            'rounded px-2 py-0.5 text-xs font-semibold',
                            'bg-green-100 text-green-800' => $provisionStatus === 'verified',
                            'bg-yellow-100 text-yellow-800' => str_contains($provisionStatus, 'propagation'),
                            'bg-blue-100 text-blue-800' => $provisionStatus === 'dns_pushed',
                        ])>{{ $provisionStatus }}</span>
                    </div>
                @endif
            </div>
        </x-filament::section>

        @if (!empty($provisionedRecords))
            <x-filament::section heading="DNS Records Pushed to deSEC">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-gray-500">
                                <th class="pb-2 pr-4">Name</th>
                                <th class="pb-2 pr-4">Type</th>
                                <th class="pb-2 pr-4">TTL</th>
                                <th class="pb-2">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($provisionedRecords as $record)
                                <tr>
                                    <td class="py-2 pr-4 font-mono">{{ $record['subname'] ?: '@' }}</td>
                                    <td class="py-2 pr-4 font-mono">{{ $record['type'] }}</td>
                                    <td class="py-2 pr-4">{{ $record['ttl'] }}</td>
                                    <td class="py-2 font-mono text-xs break-all">
                                        {{ implode(', ', $record['records']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        <x-filament::section heading="Instructions">
            <ol class="list-decimal space-y-2 pl-5 text-sm text-gray-600 dark:text-gray-400">
                <li>Set <code>PLATFORM_MAIL_DOMAIN</code> in your <code>.env</code> (e.g. <code>notif.valexhub.com</code>).</li>
                <li>Click <strong>Provision Platform Domain</strong> — registers in Plume, pushes DNS to deSEC.</li>
                <li>Wait 5–30 minutes for DNS propagation, then click <strong>Re-check Verification</strong>.</li>
                <li>Once verified, every new instance provisioned will automatically get a Plume API key scoped to this domain.</li>
            </ol>
        </x-filament::section>
    </div>
</x-filament-panels::page>
