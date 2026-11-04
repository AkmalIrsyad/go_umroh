<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-0GRNKTTQ7L"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-0GRNKTTQ7L');
    </script>

    @php
        $pageTitle = isset($seo['title']) ? $seo['title'] : 'Go Umroh — Marketplace Umroh Terpercaya';
        $pageDesc = isset($seo['description']) ? $seo['description'] : 'Go Umroh - Marketplace Paket Umrah Terpercaya. Temukan ratusan paket umroh resmi dengan harga transparan dan kepastian keberangkatan.';
        $pageUrl = isset($seo['url']) ? $seo['url'] : url()->current();
        $pageImage = isset($seo['image']) ? $seo['image'] : asset('go_umroh.svg');
        $pageType = isset($seo['type']) ? $seo['type'] : 'website';
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    <link rel="canonical" href="{{ $pageUrl }}">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('go_umroh.svg') }}">

    {{-- Open Graph / Facebook / WhatsApp Preview --}}
    <meta property="og:type" content="{{ $pageType }}">
    <meta property="og:site_name" content="Go Umroh">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:image:alt" content="{{ $pageTitle }}">
    <meta property="og:logo" content="{{ asset('go_umroh.svg') }}">
    @if(isset($seo['price']))
    <meta property="product:price:amount" content="{{ $seo['price'] }}">
    <meta property="product:price:currency" content="IDR">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ $pageImage }}">

    @if(!empty($seo['package']))
    {{-- Schema.org Structured Data (JSON-LD) for Search Engines / Google Rich Snippets --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $seo['package']['name'],
        'image' => $seo['image'],
        'description' => $seo['description'],
        'offers' => [
            '@type' => 'Offer',
            'url' => $seo['url'],
            'priceCurrency' => 'IDR',
            'price' => (float) $seo['package']['price'],
            'availability' => $seo['package']['seats'] > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'seller' => [
                '@type' => 'Organization',
                'name' => $seo['package']['merchant'],
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet"
        href="https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet"
        href="https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-rounded/css/uicons-solid-rounded.css">

    <!-- Google Fonts: Lato -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
        rel="stylesheet">
</head>

<body class="bg-limestone text-ink font-sans">
    <div id="app"></div>
</body>

</html>
