<?php

use function Laravel\Folio\{name};

name('home');
?>

<x-layouts.marketing
    :seo="[
        'title'         => 'ValexHub - Powerful Business Tools Built for Growth | Custom Software Solutions Nigeria',
        'description'   => 'Transform your Nigerian business with custom software solutions. From school management to hotel booking systems, pharmacy POS to restaurant management - we build tailored digital solutions that help recover lost revenue and scale profitably.',
        'keywords'      => 'custom software development Nigeria, business management systems, school management software, hotel booking system, pharmacy POS, restaurant management, property management software, Nigerian SME solutions, business automation tools',
        'image'         => url('/og_image.png'),
        'type'          => 'website',
        'canonical'     => url('/'),
        'author'        => 'ValexHub',
        'robots'        => 'index, follow',
        'og_locale'     => 'en_NG'
    ]">


    <!-- Main Content -->
    <main>
        <x-marketing.sections.hero />

        <!-- Marquee Divider -->
        <x-marketing.sections.marquee-divider />

        <!-- <section aria-labelledby="features-heading"> -->
        <x-container class="py-8">
            <h2 id="features-heading" class="sr-only">Our Software Solutions</h2>
            <x-marketing.sections.features />
        </x-container>
        <!-- </section> -->

        <section aria-labelledby="development-timeline-heading">
            <h2 id="development-timeline-heading" class="sr-only">Development Timeline</h2>
            <x-marketing.sections.development-services />
        </section>

        <section aria-labelledby="faq-heading">
            <x-container class="py-12 border-t sm:py-24 border-zinc-200">
                <h2 id="faq-heading" class="sr-only">Frequently Asked Questions</h2>
                <x-marketing.sections.faq />
            </x-container>
        </section>

        <section aria-labelledby="cta-heading">
            <x-container class="py-12 border-t sm:py-24 border-zinc-200">
                <h2 id="cta-heading" class="sr-only">Get Started with ValexHub</h2>
                <x-marketing.sections.final-cta />
            </x-container>
        </section>
    </main>

</x-layouts.marketing>