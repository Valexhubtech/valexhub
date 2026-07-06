<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Domain Setup Fee (Kobo)
    |--------------------------------------------------------------------------
    | One-time fee charged on top of the raw domain cost.
    | 200000 kobo = ₦2,000
    */
    'setup_fee_kobo' => 200000,

    /*
    |--------------------------------------------------------------------------
    | Exchange Rate Fallback
    |--------------------------------------------------------------------------
    | Live USD→NGN rate is fetched from open.er-api.com (cached 6 hours).
    | This value is only used if that API is unreachable.
    */
    'fallback_usd_to_ngn_rate' => 1600,

    /*
    |--------------------------------------------------------------------------
    | TLD Whitelist
    |--------------------------------------------------------------------------
    | Only these TLDs are shown in domain search results.
    | Pricing for each one is fetched live from Hostinger's catalog.
    | Unlisted TLDs (there are 400+) are silently excluded.
    */
    'tld_whitelist' => [
        // Global staples
        'com', 'net', 'org', 'info', 'biz', 'co', 'io',
        // Short & modern
        'ai', 'me', 'xyz', 'pro', 'dev', 'app',
        // Country codes useful for Nigerian businesses
        'us', 'ca', 'uk', 'de', 'fr', 'au', 'in', 'za',
        // Nigerian ccTLDs
        'com.ng', 'ng', 'org.ng',
        // E-commerce & business
        'store', 'shop', 'business', 'company', 'solutions', 'services',
        // Digital & tech
        'online', 'website', 'site', 'digital', 'cloud', 'tech', 'software', 'systems', 'network',
        // Creative & agency
        'agency', 'studio', 'design', 'media', 'marketing', 'consulting',
        // Sector-specific
        'health', 'finance', 'legal',
        // African / global reach
        'africa', 'global', 'group',
    ],

    /*
    |--------------------------------------------------------------------------
    | Recommended TLDs
    |--------------------------------------------------------------------------
    | Flagged as "Best choice" in the UI when available.
    */
    'recommended_tlds' => ['com', 'com.ng'],

    /*
    |--------------------------------------------------------------------------
    | TLD Descriptions
    |--------------------------------------------------------------------------
    | Short text shown under each TLD in search results.
    | TLDs without an entry here still show — just no description line.
    */
    'tld_descriptions' => [
        'com'         => 'Most recognised worldwide — ideal for any business',
        'net'         => 'Great alternative when .com is taken',
        'org'         => 'Ideal for non-profits, schools, and associations',
        'info'        => 'Good for information and knowledge-based services',
        'biz'         => 'Clearly signals a commercial or business operation',
        'co'          => 'A popular startup-friendly .com alternative',
        'io'          => 'Popular with tech startups and developer tools',
        'ai'          => 'Trendy for AI-powered products and startups',
        'me'          => 'Personal branding and professional portfolios',
        'xyz'         => 'Budget-friendly modern extension for any project',
        'pro'         => 'Signals professionalism in any industry',
        'dev'         => 'Built for developers and technical products',
        'app'         => 'Great for mobile apps and SaaS products',
        'us'          => 'US-based businesses and American audiences',
        'ca'          => 'Targets Canadian customers',
        'uk'          => 'Targets UK customers',
        'za'          => 'South African country domain',
        'com.ng'      => 'Affordable local Nigerian commercial domain',
        'ng'          => 'Premium Nigerian national domain',
        'org.ng'      => 'Nigerian non-profit and organisation domain',
        'store'       => 'Perfect for e-commerce and retail businesses',
        'shop'        => 'Another strong choice for online stores',
        'business'    => 'Straightforward branding for any business',
        'company'     => 'Clean and professional business identity',
        'solutions'   => 'Ideal for consulting and problem-solving firms',
        'services'    => 'Perfect for service-based businesses',
        'online'      => 'Modern option for any online presence',
        'website'     => 'Budget-friendly and descriptive',
        'site'        => 'Short, memorable, and versatile',
        'digital'     => 'Great for digital agencies and online brands',
        'cloud'       => 'Ideal for cloud software and SaaS',
        'tech'        => 'Ideal for tech companies and software products',
        'software'    => 'Perfect for software products and companies',
        'systems'     => 'Great for IT and enterprise technology firms',
        'network'     => 'Good for networking, ISPs, and connectivity',
        'agency'      => 'Establishes authority for agencies of all types',
        'studio'      => 'Creative studios, film, music, and design',
        'design'      => 'Stand out for any design-led brand',
        'media'       => 'Broadcasting, publishing, and content brands',
        'marketing'   => 'Marketing agencies and digital consultancies',
        'consulting'  => 'Professional consulting and advisory firms',
        'health'      => 'Healthcare, wellness, and medical brands',
        'finance'     => 'Financial services, fintech, and accounting',
        'legal'       => 'Law firms, legal tech, and compliance',
        'africa'      => 'Pan-African projects and continental brands',
        'global'      => 'International and cross-border businesses',
        'group'       => 'Holding companies and corporate groups',
    ],

    /*
    |--------------------------------------------------------------------------
    | TLD Groups (UI grouping)
    |--------------------------------------------------------------------------
    | 'popular'  — well-recognised global extensions (shown first)
    | 'nigerian' — .ng country-code extensions
    | 'other'    — specialist or modern extensions
    | Unlisted TLDs default to 'other'.
    */
    'tld_groups' => [
        'com'     => 'popular',
        'net'     => 'popular',
        'org'     => 'popular',
        'info'    => 'popular',
        'biz'     => 'popular',
        'co'      => 'popular',
        'io'      => 'popular',
        'ai'      => 'popular',
        'me'      => 'popular',
        'xyz'     => 'popular',
        'pro'     => 'popular',
        'com.ng'  => 'nigerian',
        'ng'      => 'nigerian',
        'org.ng'  => 'nigerian',
        'dev'     => 'other',
        'app'     => 'other',
        'store'   => 'other',
        'shop'    => 'other',
        'online'  => 'other',
        'website' => 'other',
        'site'    => 'other',
        'digital' => 'other',
        'cloud'   => 'other',
        'tech'    => 'other',
        'software'    => 'other',
        'systems'     => 'other',
        'network'     => 'other',
        'agency'      => 'other',
        'studio'      => 'other',
        'design'      => 'other',
        'media'       => 'other',
        'marketing'   => 'other',
        'consulting'  => 'other',
        'business'    => 'other',
        'company'     => 'other',
        'solutions'   => 'other',
        'services'    => 'other',
        'health'      => 'other',
        'finance'     => 'other',
        'legal'       => 'other',
        'africa'      => 'other',
        'global'      => 'other',
        'group'       => 'other',
        'us'          => 'other',
        'ca'          => 'other',
        'uk'          => 'other',
        'de'          => 'other',
        'fr'          => 'other',
        'au'          => 'other',
        'in'          => 'other',
        'za'          => 'other',
    ],
];
