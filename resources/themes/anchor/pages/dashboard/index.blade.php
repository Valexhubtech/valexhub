<?php
    use function Laravel\Folio\{middleware, name};
    middleware('auth');
    name('dashboard');
?>

<x-layouts.app>
    <x-app.container x-data class="lg:space-y-6" x-cloak>

        <x-app.heading
            title="Dashboard"
            description="Overview of your deployed software and account activity."
            :border="false"
        />

        <livewire:dashboard-overview />

    </x-app.container>
</x-layouts.app>
