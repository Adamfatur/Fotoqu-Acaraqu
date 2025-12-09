<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FOTOKU') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('logo-fotoku-favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('logo-fotoku-favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased" style="font-family:'Plus Jakarta Sans','Inter',system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif">
        <!-- Background with brand gradient and subtle pattern -->
        <div class="min-h-screen relative overflow-hidden" style="background:linear-gradient(135deg,#1a90d6 0%,#053962 100%)">
            <div class="absolute inset-0 bg-black/10"></div>
            <!-- Decorative blobs -->
            <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full blur-3xl opacity-25" style="background:radial-gradient(circle at 30% 30%, #1fa6ee, transparent 60%)"></div>
            <div class="absolute -bottom-24 -right-24 w-72 h-72 rounded-full blur-3xl opacity-25" style="background:radial-gradient(circle at 70% 70%, var(--carrot-orange), transparent 60%)"></div>
            
            <div class="relative flex flex-col items-center justify-center min-h-screen px-6 py-12">
                <!-- Logo and Brand -->
                <div class="text-center mb-8">
                    <x-application-logo class="mx-auto mb-4" />
                    <p class="text-white/90 text-lg font-semibold">Admin Dashboard</p>
                    <p class="text-white/70 text-sm mt-1">Photobox Otomatis</p>
                </div>

                <!-- Login Card -->
                <div class="w-full max-w-md">
                    <div class="relative p-[1px] rounded-2xl shadow-2xl" style="background:linear-gradient(135deg,var(--picton-blue),var(--carrot-orange))">
                        <div class="bg-white/95 backdrop-blur-xl rounded-2xl p-8">
                            {{ $slot }}
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-white/80 text-sm">
                        © {{ date('Y') }} FOTOKU. Semua hak dilindungi.
                    </p>
                </div>
            </div>
        </div>

    <style>
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
        </style>
    </body>
</html>
