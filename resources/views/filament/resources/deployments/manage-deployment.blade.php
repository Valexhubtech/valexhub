<x-filament-panels::page>

    @php
        $record = $this->record;
        $statusBadge = [
            'pending'      => ['color' => 'bg-zinc-100 text-zinc-600',        'label' => 'Pending'],
            'provisioning' => ['color' => 'bg-yellow-100 text-yellow-700',    'label' => 'Provisioning'],
            'active'       => ['color' => 'bg-green-100 text-green-700',      'label' => 'Active'],
            'failed'       => ['color' => 'bg-red-100 text-red-700',          'label' => 'Failed'],
            'suspended'    => ['color' => 'bg-orange-100 text-orange-700',    'label' => 'Suspended'],
            'terminated'   => ['color' => 'bg-red-100 text-red-800',          'label' => 'Terminated'],
        ];
        $sb  = $statusBadge[$record->status] ?? $statusBadge['pending'];
        $up  = $record->userProduct;

        $allModules = [
            'auth'             => 'Auth & Users',
            'org'              => 'Organisation',
            'dashboard'        => 'Dashboard',
            'contacts'         => 'Contacts / CRM',
            'catalog'          => 'Product Catalog',
            'inventory'        => 'Inventory',
            'pos'              => 'Point of Sale',
            'invoicing'        => 'Invoicing',
            'staff'            => 'Staff Management',
            'booking'          => 'Booking & Scheduling',
            'notifications'    => 'Notifications',
            'audit_log'        => 'Audit Log',
            'platform'         => 'Platform / Settings',
            'digital_products' => 'Digital Products',
            'video_courses'    => 'Video Courses',
        ];
    @endphp

    {{-- ── Info strip (always visible above tabs) ────────────────────────── --}}
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-5 mb-2">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-zinc-900 dark:text-white">
                    {{ $record->product->name ?? 'Deployment' }}
                    <span class="ml-2 text-zinc-400 font-normal text-base">#{{ $record->id }}</span>
                </h2>
                <p class="text-sm text-zinc-500 mt-0.5">
                    {{ $record->user->name }} &lt;{{ $record->user->email }}&gt;
                    @if($record->business_name) &mdash; {{ $record->business_name }} @endif
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-full {{ $sb['color'] }}">
                @if($record->status === 'provisioning')
                    <span class="w-2 h-2 rounded-full bg-current animate-pulse"></span>
                @else
                    <span class="w-2 h-2 rounded-full bg-current opacity-60"></span>
                @endif
                {{ $sb['label'] }}
            </span>
        </div>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
            <div>
                <p class="text-zinc-400 text-xs uppercase tracking-wide mb-1">App URL</p>
                @if($record->deployment_url)
                    <a href="{{ $record->deployment_url }}" target="_blank" class="text-blue-600 dark:text-blue-400 underline break-all">{{ $record->deployment_url }}</a>
                @else
                    <span class="text-zinc-400 italic">Not yet assigned</span>
                @endif
            </div>
            <div>
                <p class="text-zinc-400 text-xs uppercase tracking-wide mb-1">Custom Domain</p>
                @if($record->custom_domain)
                    <a href="https://{{ $record->custom_domain }}" target="_blank" class="text-blue-600 dark:text-blue-400 underline break-all text-xs">{{ $record->custom_domain }}</a>
                @else
                    <span class="text-zinc-400 italic">Not set</span>
                @endif
            </div>
            <div>
                <p class="text-zinc-400 text-xs uppercase tracking-wide mb-1">Coolify App ID</p>
                <code class="text-zinc-700 dark:text-zinc-300 text-xs">{{ $record->coolify_app_id ?? '—' }}</code>
            </div>
            <div>
                <p class="text-zinc-400 text-xs uppercase tracking-wide mb-1">Ordered</p>
                <span class="text-zinc-700 dark:text-zinc-300">{{ $record->created_at?->format('d M Y, H:i') }}</span>
            </div>
        </div>

        @if($record->failure_reason)
            <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <p class="text-xs font-medium text-red-700 dark:text-red-400 mb-1">Failure Reason</p>
                <p class="text-xs text-red-600 dark:text-red-300 font-mono break-all">{{ $record->failure_reason }}</p>
            </div>
        @endif
    </div>

    {{-- ── Tabs ────────────────────────────────────────────────────────────── --}}
    <div x-data="{ tab: 'overview' }">

        {{-- Tab bar --}}
        <div class="flex gap-1 border-b border-zinc-200 dark:border-zinc-700 mb-6">
            @foreach([
                ['overview',  'Overview',  'phosphor-squares-four'],
                ['modules',   'Modules',   'phosphor-puzzle-piece'],
                ['billing',   'Billing',   'phosphor-receipt'],
            ] as [$key, $label, $icon])
            <button @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}'
                        ? 'border-b-2 border-zinc-900 dark:border-white text-zinc-900 dark:text-white'
                        : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium transition-colors -mb-px">
                <x-dynamic-component :component="$icon" class="w-4 h-4" />
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- TAB 1: Overview                                            --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div x-show="tab === 'overview'" x-cloak class="space-y-5">

            {{-- Custom Domain --}}
            @if($record->coolify_app_id)
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Set Custom Domain</h3>
                <div class="flex items-center gap-3">
                    <input type="text"
                           wire:model="customDomainInput"
                           placeholder="yourdomain.com"
                           class="flex-1 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <button wire:click="setCustomDomain"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors">
                        Apply
                    </button>
                </div>
                <p class="mt-2 text-xs text-zinc-400">Enter the domain without https://. DNS A record must already point to the Coolify server IP. The container will restart automatically.</p>
            </div>
            @endif

            {{-- Coolify controls --}}
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">
                    Coolify Container Controls
                </h3>

                @if(! $record->coolify_app_id)
                    <p class="text-sm text-zinc-400 italic mb-3">No Coolify app linked yet.</p>
                    @if($record->status === 'failed')
                        <div x-data="{ confirming: false }">
                            <button x-show="!confirming" @click="confirming = true"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-blue-400 text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 transition-colors">
                                <x-phosphor-arrow-clockwise class="w-4 h-4" /> Retry Deployment
                            </button>
                            <div x-show="confirming" x-cloak class="flex items-center gap-2">
                                <span class="text-sm text-zinc-500">Re-attempt provisioning?</span>
                                <button @click="$wire.retryDeployment(); confirming = false"
                                        wire:loading.attr="disabled"
                                        class="px-3 py-1.5 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                    Confirm
                                </button>
                                <button @click="confirming = false"
                                        class="px-3 py-1.5 text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="flex flex-wrap gap-3">
                        @foreach([
                            ['restart',          'Restart',              'phosphor-arrows-clockwise',  'zinc',   'Restart container?'],
                            ['fixAndRedeploy',   'Fix & Redeploy',       'phosphor-wrench',             'amber',  'Re-inject env vars & redeploy?'],
                            ['reprovisionBunny', 'Re-provision Bunny',   'phosphor-video-camera',       'purple', 'This will create a NEW Bunny Stream library and update the client env vars. Old videos will not be migrated. Continue?'],
                            ['wipeAndRecreate',  'Wipe & Recreate',      'phosphor-trash',              'red',    'Deletes the container — are you sure?'],
                        ] as [$method, $btnLabel, $icon, $color, $prompt])
                        @php
                            $btn = match($color) {
                                'amber'  => 'border-amber-400 text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100',
                                'red'    => 'border-red-400 text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100',
                                'purple' => 'border-purple-400 text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100',
                                default  => 'border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-700',
                            };
                            $confirmBtn = match($color) {
                                'amber'  => 'bg-amber-600 hover:bg-amber-700',
                                'red'    => 'bg-red-600 hover:bg-red-700',
                                'purple' => 'bg-purple-600 hover:bg-purple-700',
                                default  => 'bg-zinc-800 hover:bg-zinc-700',
                            };
                        @endphp
                        <div x-data="{ confirming: false }">
                            <button x-show="!confirming" @click="confirming = true"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border transition-colors {{ $btn }}">
                                <x-dynamic-component :component="$icon" class="w-4 h-4" />
                                {{ $btnLabel }}
                            </button>
                            <div x-show="confirming" x-cloak class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm @if($color === 'red') text-red-600 dark:text-red-400 font-medium @else text-zinc-500 @endif">{{ $prompt }}</span>
                                <button @click="$wire.{{ $method }}(); confirming = false"
                                        wire:loading.attr="disabled"
                                        class="px-3 py-1.5 text-sm font-medium rounded-lg text-white transition-colors {{ $confirmBtn }}">
                                    Confirm
                                </button>
                                <button @click="confirming = false"
                                        class="px-3 py-1.5 text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs text-zinc-400">
                        <strong>Restart</strong>: same UUID + domain. &nbsp;
                        <strong>Fix & Redeploy</strong>: re-injects env vars, same UUID. &nbsp;
                        <strong>Wipe & Recreate</strong>: deletes and provisions fresh.
                    </p>
                @endif
            </div>

            {{-- Lifecycle --}}
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Service Lifecycle</h3>
                <div class="flex flex-wrap gap-3">
                    @if($record->status === 'active')
                        <div x-data="{ c: false }">
                            <button x-show="!c" @click="c=true" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-orange-400 text-orange-700 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 transition-colors">
                                <x-phosphor-pause-circle class="w-4 h-4" /> Suspend
                            </button>
                            <div x-show="c" x-cloak class="flex items-center gap-2">
                                <span class="text-sm text-zinc-500">Suspend service?</span>
                                <button @click="$wire.suspend(); c=false" class="px-3 py-1.5 text-sm font-medium rounded-lg bg-orange-600 text-white hover:bg-orange-700 transition-colors">Confirm</button>
                                <button @click="c=false" class="px-3 py-1.5 text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">Cancel</button>
                            </div>
                        </div>
                    @endif
                    @if($record->status === 'suspended')
                        <div x-data="{ c: false }">
                            <button x-show="!c" @click="c=true" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-green-400 text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 transition-colors">
                                <x-phosphor-play-circle class="w-4 h-4" /> Reactivate
                            </button>
                            <div x-show="c" x-cloak class="flex items-center gap-2">
                                <span class="text-sm text-zinc-500">Reactivate?</span>
                                <button @click="$wire.reactivate(); c=false" class="px-3 py-1.5 text-sm font-medium rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors">Confirm</button>
                                <button @click="c=false" class="px-3 py-1.5 text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">Cancel</button>
                            </div>
                        </div>
                    @endif
                    @if(! in_array($record->status, ['terminated', 'pending']))
                        <div x-data="{ c: false }">
                            <button x-show="!c" @click="c=true" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-red-400 text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 transition-colors">
                                <x-phosphor-prohibit class="w-4 h-4" /> Terminate
                            </button>
                            <div x-show="c" x-cloak class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm text-red-600 dark:text-red-400 font-medium">Permanently terminate?</span>
                                <button @click="$wire.terminate(); c=false" class="px-3 py-1.5 text-sm font-medium rounded-lg bg-red-700 text-white hover:bg-red-800 transition-colors">Yes, Terminate</button>
                                <button @click="c=false" class="px-3 py-1.5 text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">Cancel</button>
                            </div>
                        </div>
                    @endif
                    <a href="{{ \App\Filament\Resources\Deployments\DeploymentResource::getUrl('edit', ['record' => $record->id]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                        <x-phosphor-pencil-simple class="w-4 h-4" /> Edit Details
                    </a>
                </div>
            </div>

            {{-- Credentials --}}
            @php $creds = $record->credentials_encrypted ?? []; @endphp
            @if(count($creds) > 0)
            <div x-data="{ revealed: false }" class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Stored Credentials</h3>
                    <button @click="revealed = !revealed" class="text-xs text-zinc-500 hover:text-zinc-900 dark:hover:text-white underline transition-colors" x-text="revealed ? 'Hide' : 'Reveal'"></button>
                </div>
                <div x-show="revealed" x-transition class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4 font-mono text-xs space-y-2">
                    @foreach($creds as $key => $value)
                        <div class="flex items-start gap-3">
                            <span class="text-zinc-400 w-36 flex-shrink-0 capitalize">{{ str_replace('_', ' ', $key) }}</span>
                            <span class="text-zinc-900 dark:text-white break-all select-all">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
                <p x-show="!revealed" class="text-xs text-zinc-400">Hidden. Click Reveal to show.</p>
            </div>
            @endif

        </div>{{-- /overview --}}

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- TAB 2: Modules                                             --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div x-show="tab === 'modules'" x-cloak>
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <div class="flex items-start justify-between gap-4 mb-5">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Enabled Modules</h3>
                        <p class="text-xs text-zinc-400 mt-1">Toggle modules on/off, then hit "Save & Push" to re-inject <code>ENABLED_MODULES</code> and redeploy the container.</p>
                    </div>
                    <button wire:click="saveModules"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-60 cursor-wait"
                            class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:opacity-90 transition-opacity">
                        <span wire:loading.remove wire:target="saveModules">
                            <x-phosphor-floppy-disk class="w-4 h-4 inline -mt-0.5" />
                            Save & Push
                        </span>
                        <span wire:loading wire:target="saveModules">Saving…</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($allModules as $key => $label)
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 cursor-pointer transition-colors">
                        <input type="checkbox"
                               wire:model="enabledModules"
                               value="{{ $key }}"
                               class="w-4 h-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500" />
                        <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>

                @if(! $record->coolify_app_id)
                    <p class="mt-4 text-xs text-amber-600 dark:text-amber-400">
                        No container linked yet — changes will be saved to the database but cannot be pushed until the container is provisioned.
                    </p>
                @endif
            </div>
        </div>{{-- /modules --}}

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- TAB 3: Billing                                             --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div x-show="tab === 'billing'" x-cloak class="space-y-5">

            {{-- Summary --}}
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide mb-4">Billing Summary</h3>
                @if($up)
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-zinc-400 text-xs uppercase tracking-wide mb-1">Total Paid</p>
                        <p class="font-semibold text-zinc-900 dark:text-white text-lg">₦{{ number_format((float) $up->amount_paid, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-zinc-400 text-xs uppercase tracking-wide mb-1">Setup Fee</p>
                        <p class="font-medium text-zinc-700 dark:text-zinc-300">₦{{ number_format((float) ($up->setup_amount ?? 0), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-zinc-400 text-xs uppercase tracking-wide mb-1">Billing Type</p>
                        <p class="font-medium text-zinc-700 dark:text-zinc-300">
                            {{ $up->next_renewal_date ? 'Recurring' : 'One-time / Perpetual' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-zinc-400 text-xs uppercase tracking-wide mb-1">Next Renewal</p>
                        <p class="font-medium {{ $up->next_renewal_date?->isPast() ? 'text-red-600 dark:text-red-400' : 'text-zinc-700 dark:text-zinc-300' }}">
                            {{ $up->next_renewal_date?->format('d M Y') ?? '—' }}
                        </p>
                    </div>
                </div>

                @if($up->orderAddons->count() > 0)
                <div class="mt-5 pt-5 border-t border-zinc-200 dark:border-zinc-700">
                    <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-3">Add-ons</p>
                    <div class="space-y-1.5">
                        @foreach($up->orderAddons as $oa)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-zinc-700 dark:text-zinc-300">{{ $oa->addon?->name ?? $oa->addon?->module_key ?? '—' }}</span>
                            <span class="text-zinc-500">₦{{ number_format((float) $oa->amount_paid, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @else
                <p class="text-sm text-zinc-400 italic">No purchase record linked to this deployment.</p>
                @endif
            </div>

            {{-- Invoices --}}
            @php
                $invoices = \Wave\Invoice::where('deployment_id', $record->id)->latest()->get();
            @endphp
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Invoices</h3>
                    @if($record->user_id)
                    <button wire:click="mountAction('create_invoice')"
                            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                        <x-phosphor-paper-plane class="w-4 h-4" />
                        Create Invoice
                    </button>
                    @endif
                </div>

                @if($invoices->isEmpty())
                    <p class="text-sm text-zinc-400 italic">No invoices yet for this deployment.</p>
                @else
                <div class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @foreach($invoices as $inv)
                    <div class="py-3 flex items-center justify-between text-sm">
                        <div>
                            <span class="font-medium text-zinc-900 dark:text-white">INV-{{ str_pad($inv->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="ml-3 text-zinc-400">{{ $inv->created_at->format('d M Y') }}</span>
                            @if($inv->due_date)
                                <span class="ml-2 text-zinc-400">· Due {{ \Carbon\Carbon::parse($inv->due_date)->format('d M Y') }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="font-semibold text-zinc-900 dark:text-white">₦{{ number_format((float) $inv->amount, 2) }}</span>
                            <span class="px-2 py-0.5 text-xs rounded-full font-medium
                                {{ $inv->status === 'paid' ? 'bg-green-100 text-green-700' :
                                   ($inv->status === 'sent' ? 'bg-blue-100 text-blue-700' : 'bg-zinc-100 text-zinc-600') }}">
                                {{ ucfirst($inv->status) }}
                            </span>
                            @if($inv->pdf_path)
                            <a href="{{ Storage::url($inv->pdf_path) }}" target="_blank" class="text-xs text-zinc-500 hover:text-zinc-900 dark:hover:text-white underline">PDF</a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>{{-- /billing --}}

    </div>{{-- /tabs --}}

    {{-- Loading overlay --}}
    <div wire:loading.flex class="fixed inset-0 bg-black/20 dark:bg-black/40 z-50 items-center justify-center">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl px-8 py-6 flex items-center gap-3">
            <svg class="animate-spin w-5 h-5 text-zinc-600 dark:text-zinc-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-sm text-zinc-700 dark:text-zinc-300 font-medium">Working…</span>
        </div>
    </div>

</x-filament-panels::page>
