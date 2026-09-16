<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Go Umroh - Marketplace Paket Umrah Terpercaya. Temukan ratusan paket umroh resmi dengan harga transparan dan kepastian keberangkatan.">
    <title>Go Umroh — Marketplace Umroh Terpercaya</title>

    {{-- Open Graph / Social Media Preview (default, akan di-override oleh Vue di halaman detail) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Go Umroh">
    <meta property="og:title" content="Go Umroh — Marketplace Umroh Terpercaya">
    <meta property="og:description" content="Temukan ratusan paket umroh resmi dengan jaminan fasilitas terbaik, kepastian keberangkatan, dan transparansi harga.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Go Umroh — Marketplace Umroh Terpercaya">
    <meta name="twitter:description" content="Temukan ratusan paket umroh resmi dengan jaminan fasilitas terbaik, kepastian keberangkatan, dan transparansi harga.">
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