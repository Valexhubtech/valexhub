<x-filament-panels::page>
    <form wire:submit.prevent="deploy">
        {{ $this->form }}

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ \App\Filament\Resources\Deployments\DeploymentResource::getUrl('index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-wait"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors">
                <span wire:loading.remove wire:target="deploy">
                    <x-phosphor-rocket-launch class="w-4 h-4 inline -mt-0.5" />
                    Deploy Now
                </span>
                <span wire:loading wire:target="deploy">Deploying…</span>
            </button>
        </div>
    </form>
</x-filament-panels::page>
