<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
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
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased text-slate-900 bg-white">
        <div class="flex min-h-full">
            
            <!-- Left Side (Visuals) -->
            <div class="relative hidden w-0 flex-1 lg:block">
                <div class="absolute inset-0 h-full w-full object-cover bg-gradient-to-br from-[#053962] to-[#1a90d6]">
                     <!-- Branding Pattern/Texture options -->
                     <div class="absolute inset-0 bg-black/10 mix-blend-multiply"></div>
                     <div class="absolute inset-0" style="background-image: url('{{ asset('portofolio/foto1.jpg') }}'); background-size: cover; background-position: center; opacity: 0.4; mix-blend-overlay;"></div>
                </div>
                
                <!-- Content Overlay -->
                <div class="absolute inset-0 z-10 flex flex-col justify-between p-12 text-white/90">
                    <div>
                         <x-application-logo class="w-16 h-16 opacity-90" />
                    </div>
                    <div class="max-w-md">
                        <blockquote class="text-2xl font-semibold leading-relaxed">
                            "Abadikan setiap momen berharga dengan kualitas studio terbaik. Cepat, mudah, dan langsung jadi!"
                        </blockquote>
                        <div class="mt-4 flex items-center gap-3">
                            <div class="h-1 w-8 bg-white/50 rounded-full"></div>
                            <p class="text-sm font-medium">FotoQu Team</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side (Form) -->
            <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
                <div class="mx-auto w-full max-w-sm lg:w-96">
                    <div class="text-center lg:text-left mb-10">
                         <!-- Mobile Logo -->
                        <div class="lg:hidden flex justify-center mb-6">
                            <x-application-logo class="w-16 h-16 text-[#1a90d6]" />
                        </div>
                        
                        <h2 class="text-2xl font-bold leading-9 tracking-tight text-slate-900">
                            Masuk ke Dashboard
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Kelola photobooth dan galeri Anda dengan mudah.
                        </p>
                    </div>

                    <div class="mt-4">
                        {{ $slot }}
                    </div>

                    <div class="mt-8 text-center text-xs text-slate-400">
                        &copy; {{ date('Y') }} FotoQu. All rights reserved.
                    </div>
                </div>
            </div>
            
        </div>
    </body>
</html>
