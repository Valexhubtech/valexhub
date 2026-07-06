@php
if(isset($seo)){
$seo = (is_array($seo)) ? ((object)$seo) : $seo;
}
@endphp
@if(isset($seo->title))
<title>{{ $seo->title }}</title>
@else
<title>{{ setting('site.title', 'Laravel Wave') . ' - ' . setting('site.description', 'The Software as a Service Starter Kit built with Laravel') }}</title>
@endif

<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge"> <!-- † -->
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="url" content="{{ url('/') }}">

<x-favicon></x-favicon>

{{-- Social Share Open Graph Meta Tags --}}
@if(isset($seo->title) && isset($seo->description) && isset($seo->image))
<meta property="og:title" content="{{ $seo->title }}">
<meta property="og:url" content="{{ Request::url() }}">
<meta property="og:image" content="{{ $seo->image }}">
<meta property="og:type" content="@if(isset($seo->type)){{ $seo->type }}@else{{ 'article' }}@endif">
<meta property="og:description" content="{{ $seo->description }}">
<meta property="og:site_name" content="{{ setting('site.title') }}">

<meta itemprop="name" content="{{ $seo->title }}">
<meta itemprop="description" content="{{ $seo->description }}">
<meta itemprop="image" content="{{ $seo->image }}">

@if(isset($seo->image_w) && isset($seo->image_h))
<meta property="og:image:width" content="{{ $seo->image_w }}">
<meta property="og:image:height" content="{{ $seo->image_h }}">
@endif
@endif

<meta name="robots" content="index,follow">
<meta name="googlebot" content="index,follow">

@if(isset($seo->description))
<meta name="description" content="{{ $seo->description }}">
@endif

<!-- Meta Pixel Code -->
<script>
    ! function(f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function() {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1513860780184965');
    fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=1513860780184965&ev=PageView&noscript=1" /></noscript>
<!-- End Meta Pixel Code -->

@filamentStyles
@livewireStyles
@vite(['resources/themes/anchor/assets/css/app.css', 'resources/themes/anchor/assets/js/app.js'])