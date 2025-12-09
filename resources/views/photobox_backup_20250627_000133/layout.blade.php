<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FOTOQU') }} - Photobooth</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            overflow: hidden;
        }
        .glassmorphism { 
            backdrop-filter: blur(20px); 
            background: rgba(255, 255, 255, 0.1); 
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .countdown-circle {
            stroke-dasharray: 440;
            stroke-dashoffset: 440;
            transition: stroke-dashoffset 1s linear;
        }
        .photo-grid {
            display: grid;
            gap: 10px;
            padding: 20px;
        }
        .photo-grid.grid-4 { grid-template-columns: repeat(2, 1fr); }
        .photo-grid.grid-6 { grid-template-columns: repeat(3, 1fr); }
        .photo-grid.grid-8 { grid-template-columns: repeat(4, 1fr); }
        
        .photo-slot {
            aspect-ratio: 3/4;
            border: 3px dashed rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.05);
        }
        .photo-slot.filled {
            border: 3px solid #10b981;
            background: none;
        }
        .photo-slot img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 17px;
        }
        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite alternate;
        }
        @keyframes pulse-glow {
            0% { box-shadow: 0 0 20px rgba(16, 185, 129, 0.4); }
            100% { box-shadow: 0 0 40px rgba(16, 185, 129, 0.8); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    @yield('content')

    <script>
        // CSRF token for AJAX requests
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    
    @stack('scripts')
</body>
</html>
