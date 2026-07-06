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

        <section aria-labelledby="domain-lookup-heading">
            <h2 id="domain-lookup-heading" class="sr-only">Domain Availability Check</h2>
            <x-marketing.sections.domain-lookup />
        </section>

        <section aria-labelledby="how-it-works-heading">
            <h2 id="how-it-works-heading" class="sr-only">How It Works</h2>
            <x-marketing.sections.how-it-works />
        </section>

        <section aria-labelledby="development-timeline-heading">
            <h2 id="development-timeline-heading" class="sr-only">Development Timeline</h2>
            <x-marketing.sections.development-services />
        </section>

        <section aria-labelledby="affiliate-heading">
            <h2 id="affiliate-heading" class="sr-only">Affiliate Program</h2>
            <x-marketing.sections.affiliate-program />
        </section>

        <section aria-labelledby="faq-heading">
            <h2 id="faq-heading" class="sr-only">Frequently Asked Questions</h2>
            <x-marketing.sections.faq />
        </section>

        <section aria-labelledby="cta-heading">
            <h2 id="cta-heading" class="sr-only">Get Started with ValexHub</h2>
            <x-marketing.sections.final-cta />
        </section>
    </main>

</x-layouts.marketing>
