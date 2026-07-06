<?php
    use function Laravel\Folio\{middleware, name};
    middleware('auth');
    name('dashboard.affiliate-tracker');
?>

<x-layouts.app>
    <x-app.container x-data class="lg:space-y-6" x-cloak>
        <x-app.heading
            title="Affiliate Tracker"
            description="Earn commissions by referring clients to ValexHub."
            :border="false"
        />

        <livewire:affiliate-tracker />
    </x-app.container>
</x-layouts.app>
