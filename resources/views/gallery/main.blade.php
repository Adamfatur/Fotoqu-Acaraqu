{{-- Gallery Main Layout --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO Meta Tags --}}
    <title>FotoQu Gallery | {{ $photoSession->customer_name }} — Sesi {{ $photoSession->session_code }}</title>
    <meta name="description" content="Galeri sesi FotoQu {{ $photoSession->session_code }} untuk {{ $photoSession->customer_name }}. Lihat & unduh foto berkualitas tinggi, frame premium siap cetak 4x6 (2 strip 2x3), serta bonus GIF untuk dibagikan.">
    <meta name="keywords" content="FotoQu, photobooth, galeri, sesi foto, booth, 4x6, strip, {{ $photoSession->session_code }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="theme-color" content="#053a63">
    <meta property="og:title" content="FotoQu Gallery • {{ $photoSession->customer_name }}">
    <meta property="og:description" content="Hasil sesi FotoQu {{ $photoSession->session_code }} — unduh foto original, frame premium siap cetak, dan bonus GIF.">
    <meta property="og:type" content="website">
    @if($frame)
    <meta property="og:image" content="{{ $frame->preview_url ?? '' }}">
    @else
    <meta property="og:image" content="{{ asset('logo-fotoku-landscape.png') }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="FotoQu Gallery • {{ $photoSession->customer_name }}">
    <meta name="twitter:description" content="Unduh foto, frame premium siap cetak 4x6, dan bonus GIF dari sesi {{ $photoSession->session_code }}.">
    @if($frame)
    <meta name="twitter:image" content="{{ $frame->preview_url ?? '' }}">
    @else
    <meta name="twitter:image" content="{{ asset('logo-fotoku-landscape.png') }}">
    @endif
    
    {{-- Preload Critical Resources --}}
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('logo-fotoku-favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('logo-fotoku-favicon.png') }}">

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    {{-- Include Gallery Styles --}}
    @include('gallery.styles.main')
</head>
<body>
    <div class="container">
        {{-- Header Section --}}
        @include('gallery.components.header')
        
        {{-- Frame Section --}}
        @if($frame)
            @include('gallery.components.frame-section')
        @endif

    {{-- Bonus GIF Section --}}
    @include('gallery.components.gif-section')
        
        {{-- Photos Section or No Photos State --}}
        @if($photos->count() > 0)
            @include('gallery.components.photos-section')
            
            {{-- Share Section --}}
            @include('gallery.components.share-section')
        @else
            @include('gallery.components.no-photos')
        @endif
        
        {{-- Footer Section --}}
        @include('gallery.components.footer')
    </div>

    {{-- Include Gallery Scripts --}}
    @include('gallery.scripts.main')

        <!-- JSON-LD Schema -->
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => 'FotoQu Gallery | ' . ($photoSession->customer_name ?? '') . ' — Sesi ' . ($photoSession->session_code ?? ''),
                'url' => url()->current(),
                'description' => 'Galeri sesi FotoQu ' . ($photoSession->session_code ?? '') . ' untuk ' . ($photoSession->customer_name ?? '') . '. Lihat & unduh foto berkualitas tinggi, frame premium siap cetak 4x6 (2 strip 2x3), serta bonus GIF untuk dibagikan.',
                'isPartOf' => [
                    '@type' => 'WebSite',
                    'name' => 'FotoQu Photobooth',
                    'url' => url('/'),
                ],
                'image' => ($frame && $frame->preview_url) ? $frame->preview_url : asset('logo-fotoku-landscape.png'),
            ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
        </script>
</body>
</html>
