<div class="space-y-6">

    {{-- All pricing data preloaded for both types — Alpine switches instantly with no server call.
         Only "Proceed to Payment" (wire:click="checkout") touches the server. --}}
    <div x-data="{
        deploymentType:    @entangle('deploymentType'),
        selectedPricingId: @entangle('selectedPricingId'),
        selectedAddonIds:  @entangle('selectedAddonIds'),
        deployFor:         @entangle('deployFor').live,
        allData: {
            cloud:  @js($cloudData),
            onprem: @js($onpremData),
        },
        setType(type) {
            this.deploymentType    = type;
            this.selectedPricingId = null;
            this.selectedAddonIds  = [];
        },
        get current() {
            return this.allData[this.deploymentType]
                ?? { setupFees: [], recurringOptions: [], onetimeOptions: [], availableAddons: [] };
        },
        toggleAddon(id) {
            const idx = this.selectedAddonIds.indexOf(id);
            this.selectedAddonIds = idx === -1
                ? [...this.selectedAddonIds, id]
                : this.selectedAddonIds.filter(i => i !== id);
        },
        fmt(n) {
            return '₦' + parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },
        get breakdown() {
            const lines = [];
            let total = 0;
            for (const fee of (this.current.setupFees ?? [])) {
                lines.push({ label: fee.label, amount: fee.amount });
                total += Number(fee.amount);
            }
            const allPricing = [
                ...(this.current.recurringOptions ?? []),
                ...(this.current.onetimeOptions   ?? []),
            ];
            const sel = allPricing.find(p => p.id == this.selectedPricingId);
            if (sel) {
                lines.push({ label: sel.label + ' (first period)', amount: sel.amount });
                total += Number(sel.amount);
            }
            for (const addon of (this.current.availableAddons ?? [])) {
                if (this.selectedAddonIds.includes(addon.id)) {
                    lines.push({
                        label:  addon.name + (addon.is_recurring ? ' (' + addon.billing_cycle + ')' : ''),
                        amount: addon.price,
                    });
                    total += Number(addon.price);
                }
            }
            return { lines, total };
        },
    }" class="space-y-6">

        @error('general')
            <div class="p-4 text-sm text-red-700 bg-red-100 rounded-md">{{ $message }}</div>
        @enderror

        {{-- Cloud vs On-Prem toggle --}}
        @if($this->supportsCloud && $this->supportsOnPrem)
            <div>
                <h3 class="mb-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Deployment Type</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div @click="setType('cloud')"
                         :class="deploymentType === 'cloud' ? 'border-zinc-900 bg-zinc-50 dark:bg-neutral-700' : 'border-zinc-200 dark:border-neutral-600'"
                         class="cursor-pointer p-4 border-2 rounded-lg transition-colors">
                        <p class="font-semibold text-zinc-900 dark:text-white text-sm">Cloud</p>
                        <p class="text-xs text-zinc-500 mt-1">Hosted by us, fully managed</p>
                    </div>
                    <div @click="setType('onprem')"
                         :class="deploymentType === 'onprem' ? 'border-zinc-900 bg-zinc-50 dark:bg-neutral-700' : 'border-zinc-200 dark:border-neutral-600'"
                         class="cursor-pointer p-4 border-2 rounded-lg transition-colors">
                        <p class="font-semibold text-zinc-900 dark:text-white text-sm">On-Premises</p>
                        <p class="text-xs text-zinc-500 mt-1">Installed on your own servers</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Setup / License fees --}}
        <div x-show="current.setupFees && current.setupFees.length > 0">
            <h3 class="mb-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide"
                x-text="deploymentType === 'onprem' ? 'License & Setup' : 'Setup'"></h3>
            <div class="space-y-2">
                <template x-for="fee in current.setupFees" :key="fee.id">
                    <div class="flex items-center justify-between px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-lg dark:bg-neutral-800 dark:border-neutral-600">
                        <div>
                            <p class="text-sm font-medium text-zinc-900 dark:text-white" x-text="fee.label"></p>
                            <span x-show="fee.is_required" class="text-xs text-red-500">Required</span>
                        </div>
                        <p class="font-semibold text-zinc-900 dark:text-white" x-text="fmt(fee.amount)"></p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Recurring plan picker --}}
        <div x-show="current.recurringOptions && current.recurringOptions.length > 0">
            <h3 class="mb-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide"
                x-text="deploymentType === 'onprem' ? 'Activation / Maintenance Plan' : 'Subscription Plan'"></h3>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <template x-for="option in current.recurringOptions" :key="option.id">
                    <div @click="selectedPricingId = option.id"
                         :class="selectedPricingId == option.id ? 'border-zinc-900 bg-zinc-50 dark:bg-neutral-700' : 'border-zinc-200 dark:border-neutral-600'"
                         class="cursor-pointer p-4 border-2 rounded-lg transition-colors text-center">
                        <p class="text-xs text-zinc-500 uppercase tracking-wide" x-text="option.label"></p>
                        <p class="mt-1 text-xl font-bold text-zinc-900 dark:text-white" x-text="fmt(option.amount)"></p>
                        <p class="text-xs text-zinc-400" x-text="'per ' + option.price_type.replace('ly', '')"></p>
                    </div>
                </template>
            </div>
        </div>

        {{-- One-time options --}}
        <div x-show="current.onetimeOptions && current.onetimeOptions.length > 0">
            <h3 class="mb-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">One-time Purchase</h3>
            <div class="space-y-2">
                <template x-for="option in current.onetimeOptions" :key="option.id">
                    <div @click="selectedPricingId = option.id"
                         :class="selectedPricingId == option.id ? 'border-zinc-900 bg-zinc-50 dark:bg-neutral-700' : 'border-zinc-200 dark:border-neutral-600'"
                         class="cursor-pointer flex items-center justify-between px-4 py-3 border-2 rounded-lg transition-colors">
                        <p class="text-sm font-medium text-zinc-900 dark:text-white" x-text="option.label + ' — perpetual license'"></p>
                        <p class="font-semibold text-zinc-900 dark:text-white" x-text="fmt(option.amount)"></p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Add-ons --}}
        <div x-show="current.availableAddons && current.availableAddons.length > 0">
            <h3 class="mb-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">
                Add-ons <span class="text-zinc-400 normal-case font-normal">(optional)</span>
            </h3>
            <div class="space-y-2">
                <template x-for="addon in current.availableAddons" :key="addon.id">
                    <div @click="toggleAddon(addon.id)"
                         :class="selectedAddonIds.includes(addon.id) ? 'border-zinc-900 bg-zinc-50 dark:bg-neutral-700' : 'border-zinc-200 dark:border-neutral-600'"
                         class="cursor-pointer flex items-start gap-3 px-4 py-3 border rounded-lg transition-colors">
                        <div class="mt-0.5 flex-shrink-0 w-4 h-4 rounded border flex items-center justify-center transition-colors"
                             :class="selectedAddonIds.includes(addon.id) ? 'bg-zinc-900 border-zinc-900' : 'border-zinc-400 bg-white dark:bg-neutral-800'">
                            <svg x-show="selectedAddonIds.includes(addon.id)" class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 12 12">
                                <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white" x-text="addon.name"></p>
                            <p class="text-xs text-zinc-500 mt-0.5" x-show="addon.description" x-text="addon.description"></p>
                            <p class="text-xs text-zinc-400 mt-0.5"
                               x-text="addon.is_recurring ? 'Recurring · ' + addon.billing_cycle : 'One-time fee'"></p>
                        </div>
                        <p class="font-semibold text-zinc-900 dark:text-white whitespace-nowrap" x-text="fmt(addon.price)"></p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Deploying For --}}
        <div>
            <h3 class="mb-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">Deploying For</h3>
            <div class="grid grid-cols-2 gap-3">
                <div @click="deployFor = 'self'"
                     :class="deployFor === 'self' ? 'border-zinc-900 bg-zinc-50 dark:bg-neutral-700' : 'border-zinc-200 dark:border-neutral-600'"
                     class="cursor-pointer p-3 border-2 rounded-lg text-center transition-colors">
                    <p class="text-sm font-medium text-zinc-900 dark:text-white">Myself</p>
                </div>
                <div @click="deployFor = 'client'"
                     :class="deployFor === 'client' ? 'border-zinc-900 bg-zinc-50 dark:bg-neutral-700' : 'border-zinc-200 dark:border-neutral-600'"
                     class="cursor-pointer p-3 border-2 rounded-lg text-center transition-colors">
                    <p class="text-sm font-medium text-zinc-900 dark:text-white">A Client</p>
                </div>
            </div>

            {{-- Business name — always required; label adapts to self vs client --}}
            <div class="mt-3">
                @error('businessName') <p class="text-sm text-red-600 mb-1">{{ $message }}</p> @enderror
                <input type="text" wire:model="businessName"
                    :placeholder="deployFor === 'client' ? 'Client\'s business name' : 'Your business name'"
                    class="w-full px-3 py-2 text-sm border rounded-md border-zinc-300 dark:bg-neutral-700 dark:border-neutral-600">
                <p class="mt-1 text-xs text-zinc-400"
                   x-text="deployFor === 'client' ? 'The trading name of the business this software is for.' : 'The trading name of your business.'"></p>
            </div>

            {{-- Client contact details — only when deploying for a client --}}
            <div x-show="deployFor === 'client'" class="mt-2 space-y-2">
                @error('clientName') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                <input type="text" wire:model="clientName" placeholder="Client contact person's full name"
                    class="w-full px-3 py-2 text-sm border rounded-md border-zinc-300 dark:bg-neutral-700 dark:border-neutral-600">
                @error('clientEmail') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                <input type="email" wire:model="clientEmail" placeholder="Client contact email address"
                    class="w-full px-3 py-2 text-sm border rounded-md border-zinc-300 dark:bg-neutral-700 dark:border-neutral-600">
            </div>
        </div>

        {{-- Order summary (fully computed in Alpine — instant, no server call) --}}
        <template x-if="breakdown.total > 0">
            <div class="space-y-4">
                <div class="p-4 bg-zinc-50 border border-zinc-200 rounded-lg dark:bg-neutral-800 dark:border-neutral-700">
                    <h3 class="mb-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300">Order Summary</h3>
                    <div class="space-y-2">
                        <template x-for="(line, i) in breakdown.lines" :key="i">
                            <div class="flex justify-between text-sm">
                                <span class="text-zinc-600 dark:text-zinc-400" x-text="line.label"></span>
                                <span class="font-medium text-zinc-900 dark:text-white" x-text="fmt(line.amount)"></span>
                            </div>
                        </template>
                    </div>
                    <div class="flex justify-between pt-3 mt-3 border-t border-zinc-200 dark:border-neutral-700">
                        <span class="font-semibold text-zinc-900 dark:text-white">Total Due Today</span>
                        <span class="text-lg font-bold text-zinc-900 dark:text-white" x-text="fmt(breakdown.total)"></span>
                    </div>
                </div>

                <button
                    wire:click="checkout"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-not-allowed"
                    class="w-full px-6 py-3 text-sm font-semibold text-white rounded-md bg-zinc-900 hover:bg-zinc-800 transition-colors">
                    <span wire:loading.remove>Proceed to Payment</span>
                    <span wire:loading>Processing...</span>
                </button>
            </div>
        </template>

        <p x-show="breakdown.total <= 0" class="text-sm text-center text-zinc-400">
            Select a plan above to see your total.
        </p>

    </div>{{-- end Alpine scope --}}

</div>
