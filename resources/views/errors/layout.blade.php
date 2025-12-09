<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Fotoku</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'pastel-pink': '#FFE5E5',
                        'pastel-blue': '#E5F3FF',
                        'pastel-purple': '#F0E5FF',
                        'pastel-yellow': '#FFF9E5',
                        'pastel-green': '#E5FFE5',
                        'fotoku-primary': '#FF6B9D',
                        'fotoku-secondary': '#4ECDC4',
                        'fotoku-accent': '#45B7D1'
                    }
                }
            }
        }
    </script>
    
    <!-- Custom Styles -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #FFE5E5 0%, #E5F3FF 50%, #F0E5FF 100%);
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="h-full gradient-bg">
    <div class="min-h-full flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <!-- Logo/Brand -->
            <div class="mb-8">
                <div class="mx-auto h-16 w-16 bg-fotoku-primary rounded-full flex items-center justify-center floating">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h1 class="mt-4 text-2xl font-bold text-gray-800">Fotoku</h1>
                <p class="text-sm text-gray-600">Photobox Otomatis</p>
            </div>

            <!-- Error Content -->
            <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-8 shadow-xl border border-white/20">
                @yield('content')
            </div>

            <!-- Navigation -->
            <div class="space-y-4">
                @yield('actions')
                
                <div class="flex justify-center space-x-4 text-sm">
                    <a href="{{ url('/') }}" class="text-fotoku-primary hover:text-fotoku-secondary transition-colors duration-200 font-medium">
                        🏠 Beranda
                    </a>
                    <span class="text-gray-400">|</span>
                    <button onclick="history.back()" class="text-fotoku-primary hover:text-fotoku-secondary transition-colors duration-200 font-medium">
                        ← Kembali
                    </button>
                    <span class="text-gray-400">|</span>
                    <button onclick="location.reload()" class="text-fotoku-primary hover:text-fotoku-secondary transition-colors duration-200 font-medium">
                        🔄 Refresh
                    </button>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-xs text-gray-500 space-y-2">
                <p>Jika masalah berlanjut, silakan hubungi administrator</p>
                <div class="flex justify-center items-center space-x-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full pulse-slow"></div>
                    <span>Sistem berjalan normal</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Add some ambient animation -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-10 left-10 w-4 h-4 bg-fotoku-secondary/20 rounded-full floating" style="animation-delay: 0s;"></div>
        <div class="absolute top-20 right-20 w-3 h-3 bg-fotoku-primary/20 rounded-full floating" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-20 left-20 w-5 h-5 bg-fotoku-accent/20 rounded-full floating" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-10 right-10 w-2 h-2 bg-fotoku-secondary/20 rounded-full floating" style="animation-delay: 0.5s;"></div>
    </div>
</body>
</html>
