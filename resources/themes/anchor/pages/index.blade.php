<?php

use function Laravel\Folio\{name};

name('home');
?>

<x-layouts.marketing
    :seo="[
        'title'         => setting('site.title', 'valexhub'),
        'description'   => setting('site.description', 'Software as a Service Starter Kit'),
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]">

    <x-marketing.sections.hero />

    <x-container class="py-12 border-t sm:py-24 border-zinc-200">
        <x-marketing.sections.features />
    </x-container>



    <x-container class="py-12 border-t sm:py-24 border-zinc-200">
        <x-marketing.sections.faq />
    </x-container>

    <x-container class="py-12 border-t sm:py-24 border-zinc-200">
        <x-marketing.sections.final-cta />
    </x-container>

</x-layouts.marketing>