<?php
    use function Laravel\Folio\{middleware, name};
    middleware('auth');
    name('dashboard.deployments');
?>

<x-layouts.app>
    <x-app.container x-data class="lg:space-y-6" x-cloak>

        <x-app.heading
            title="My Deployments"
            description="Track the status of apps you've purchased and deployed."
            :border="false"
        />

        <livewire:deployment-status />
    </x-app.container>
</x-layouts.app>
