<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Foto - {{ $session->session_code }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ios-blue: #007AFF;
            --ios-blue-light: #5AC8FA;
            --ios-gray: #8E8E93;
            --ios-gray-light: #F2F2F7;
            --ios-white: #FFFFFF;
            --ios-black: #000000;
            --fotoku-blue: #3b82f6;
            --fotoku-blue-dark: #1d4ed8;
            --fotoku-green: #059669;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Inter', sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #e2e8f0 100%);
            color: var(--ios-black);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* iOS-style containers */
        .ios-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            border: 0.5px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .ios-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 40px rgba(59, 130, 246, 0.15);
        }

        /* iOS-style buttons */
        .ios-btn {
            font-family: inherit;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-size: 16px;
            transition: all 0.2s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .ios-btn-primary {
            background: linear-gradient(135deg, var(--fotoku-blue), var(--fotoku-blue-dark));
            color: white;
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
        }

        .ios-btn-primary:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 25px rgba(59, 130, 246, 0.4);
        }

        .ios-btn-secondary {
            background: rgba(59, 130, 246, 0.1);
            color: var(--fotoku-blue);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .ios-btn-secondary:hover {
            background: rgba(59, 130, 246, 0.15);
            transform: scale(1.02);
        }

        /* Photo grid */
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
            padding: 0;
        }

        @media (max-width: 640px) {
            .photo-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 12px;
            }
        }

        .photo-item {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 0.5px solid rgba(255, 255, 255, 0.3);
        }

        .photo-item:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 12px 40px rgba(59, 130, 246, 0.2);
        }

        .photo-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        @media (max-width: 640px) {
            .photo-item img {
                height: 180px;
            }
        }

        .photo-item:hover img {
            transform: scale(1.05);
        }

        /* Typography */
        .ios-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #1e293b, var(--fotoku-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .ios-subtitle {
            font-size: 1.1rem;
            color: var(--ios-gray);
            font-weight: 500;
        }

        /* Section headers */
        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            padding: 0 4px;
        }

        .section-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
        }

        .section-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
            margin: 24px 0;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            border: 0.5px solid rgba(59, 130, 246, 0.1);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--fotoku-blue), var(--fotoku-blue-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--ios-gray);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Responsive design */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Frame section specific */
        .frame-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            align-items: center;
        }

        @media (max-width: 768px) {
            .frame-container {
                grid-template-columns: 1fr;
                gap: 24px;
            }
        }

        .frame-image {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .frame-image img {
            width: 100%;
            height: auto;
            transition: transform 0.3s ease;
        }

        .frame-image:hover img {
            transform: scale(1.02);
        }

        /* Share section */
        .share-input-group {
            display: flex;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            border: 1px solid rgba(59, 130, 246, 0.2);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .share-input-group:focus-within {
            border-color: var(--fotoku-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .share-input {
            flex: 1;
            padding: 16px;
            border: none;
            background: transparent;
            font-family: inherit;
            font-size: 16px;
            color: #1e293b;
        }

        .share-input:focus {
            outline: none;
        }

        /* Loading and animations */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Success message */
        .success-message {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        /* Footer */
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        @media (max-width: 640px) {
            .footer-content {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(59, 130, 246, 0.15);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.1);
        }
        
        .photo-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.2);
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(59, 130, 246, 0.25);
        }
        
        .photo-card img {
            transition: all 0.3s ease;
        }
        
        .photo-card:hover img {
            transform: scale(1.05);
        }
        
        /* Simple Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .animate-slide-in-left {
            animation: slideInLeft 0.6s ease-out;
        }
        
        .animate-slide-in-right {
            animation: slideInRight 0.6s ease-out;
        }
        
        /* Light Text Gradients */
        .text-gradient {
            background: linear-gradient(135deg, #1e293b, #475569, #64748b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .text-gradient-blue {
            background: linear-gradient(135deg, #3b82f6, #2563eb, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .text-gradient-navy {
            background: linear-gradient(135deg, #1e40af, #1e3a8a, #1e3a8a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Responsive design */
        @media (max-width: 640px) {
        }
        
        /* Remove complex animations */
        .glass-card {
            backdrop-filter: blur(8px);
        }
        
        /* Light scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(59, 130, 246, 0.1);
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.3);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="bg-animated">
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-5xl mx-auto">
            
            <!-- Header Section -->
            <header class="text-center mb-12 animate-fade-in-up">
                <div class="glass-card rounded-2xl p-8 mb-6">
                    <!-- Simple Logo -->
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-full mb-6">
                        <i class="fas fa-camera text-blue-600 text-2xl"></i>
                    </div>
                    
                    <!-- Clean Title -->
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">
                        <span class="text-gradient">Gallery Foto</span>
                    </h1>
                    
                    <!-- Session Info Card -->
                    <div class="glass-card rounded-xl p-6 mb-6 mx-auto max-w-lg">
                        <div class="space-y-2">
                            <p class="text-gradient-blue text-xl font-bold">{{ $session->session_code }}</p>
                            <p class="text-gray-800 font-semibold">{{ $session->customer_name }}</p>
                            <p class="text-gray-600 text-sm">{{ $session->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="flex justify-center gap-8">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-gradient-blue mb-1">{{ $photos->count() }}</div>
                            <div class="text-gray-600 text-sm uppercase tracking-wider font-medium">Foto Tersedia</div>
                        </div>
                        @if($frame)
                        <div class="text-center">
                            <div class="text-3xl font-bold text-gradient-navy mb-1">1</div>
                            <div class="text-gray-600 text-sm uppercase tracking-wider font-medium">Frame Premium</div>
                        </div>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Frame Section -->
            @if($frame)
            <section class="mb-12 animate-slide-in-left">
                <div class="glass-card rounded-2xl p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gradient mb-3 flex items-center justify-center">
                            <i class="fas fa-crown text-yellow-500 mr-3 text-xl"></i>
                            Frame Premium Anda
                        </h2>
                        <p class="text-gray-700">Karya seni digital siap cetak profesional</p>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <!-- Frame Image -->
                        <div class="order-2 md:order-1">
                            <div class="glass-card rounded-xl p-4">
                                <img src="{{ $frame->preview_url ?? asset('images/frame-placeholder.png') }}" 
                                     alt="Frame Final" 
                                     class="w-full h-auto rounded-lg shadow-lg transition-all duration-300 hover:scale-105">
                            </div>
                        </div>
                        
                        <!-- Frame Info -->
                        <div class="order-1 md:order-2 text-center md:text-left space-y-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gradient mb-3">✨ Perfect!</h3>
                                <p class="text-gray-800 text-lg mb-2">Frame berisi {{ $photos->count() }} foto pilihan terbaik</p>
                                <p class="text-gray-600">Resolusi tinggi untuk hasil cetak premium</p>
                            </div>
                            
                            <div class="space-y-4">
                                <a href="{{ $frame->download_url ?? '#' }}" 
                                   class="btn-primary text-base px-8 py-3 w-full md:w-auto {{ !isset($frame->download_url) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                   {{ !isset($frame->download_url) ? 'disabled' : '' }}>
                                    <i class="fas fa-download"></i>
                                    Unduh Frame Premium
                                </a>
                                
                                <div class="glass-card rounded-lg p-4 bg-gradient-to-r from-blue-500/10 to-blue-600/10">
                                    <p class="text-gray-700 flex items-center justify-center md:justify-start text-sm">
                                        <i class="fas fa-check-circle text-blue-600 mr-2"></i>
                                        <span class="font-medium">300 DPI • Format Premium • Siap Cetak</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @endif

            <!-- Photos Section -->
            <section class="mb-12 animate-slide-in-right">
                <div class="glass-card rounded-2xl p-8">
                    <!-- Section Header -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div class="text-center md:text-left mb-4 md:mb-0">
                            <h2 class="text-2xl font-bold text-gradient mb-2 flex items-center justify-center md:justify-start">
                                <i class="fas fa-images text-blue-500 mr-3"></i>
                                Koleksi Foto Premium
                            </h2>
                            <p class="text-gray-700">Setiap momen berharga dalam kualitas terbaik</p>
                        </div>
                        <div class="glass-card rounded-full px-4 py-2 bg-gradient-to-r from-blue-500/10 to-blue-600/10">
                            <span class="text-gray-800 font-semibold">{{ $photos->count() }} foto tersedia</span>
                        </div>
                    </div>
                    
                    @if($photos->count() > 0)
                    <!-- Photos Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
                        @foreach($photos as $photo)
                        <div class="photo-card animate-fade-in-up">
                            <!-- Photo Container -->
                            <div class="aspect-square bg-gray-100 rounded-t-2xl overflow-hidden relative group">
                                <img src="{{ $photo->thumbnail_url ?? $photo->preview_url }}" 
                                     alt="Foto {{ $photo->sequence_number }}" 
                                     class="w-full h-full object-cover"
                                     loading="lazy">
                                
                                <!-- Simple Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <div class="absolute bottom-4 left-4">
                                        <span class="text-white font-semibold">Foto #{{ $photo->sequence_number }}</span>
                                        <p class="text-white/80 text-sm">Klik untuk mengunduh</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Photo Info -->
                            <div class="p-4 text-center">
                                <p class="text-gray-800 font-medium mb-3">Foto #{{ $photo->sequence_number }}</p>
                                <a href="{{ $photo->preview_url }}" 
                                   download="fotoku-{{ $session->session_code }}-foto-{{ $photo->sequence_number }}.jpg"
                                   class="btn-secondary w-full">
                                    <i class="fas fa-download"></i>
                                    <span class="hidden sm:inline">Unduh Foto</span>
                                    <span class="sm:hidden">↓</span>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Download All Section -->
                    <div class="text-center border-t border-gray-200 pt-8">
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gradient mb-2">Unduh Koleksi Lengkap</h3>
                            <p class="text-gray-700">Dapatkan semua foto dalam satu paket premium</p>
                        </div>
                        
                        @php
                        $expires = time() + 86400;
                        $signature = hash_hmac('sha256', $session->id . $expires, config('app.key'));
                        @endphp
                        <a href="{{ route('photobox.download-all-photos', [
                            'session' => $session->id, 
                            'expires' => $expires,
                            'signature' => $signature,
                            'zip' => 1
                        ]) }}" 
                           class="btn-primary text-lg px-10 py-4">
                            <i class="fas fa-archive"></i>
                            Unduh Semua Foto (ZIP)
                        </a>
                        
                        <div class="glass-card rounded-lg p-4 mt-6 bg-gradient-to-r from-green-500/10 to-blue-500/10 max-w-xl mx-auto">
                            <p class="text-gray-700 flex items-center justify-center text-sm">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                <span class="font-medium">File ZIP berisi semua foto dalam resolusi penuh</span>
                            </p>
                        </div>
                    </div>
                    @else
                    <!-- No Photos State -->
                    <div class="text-center py-16">
                        <div class="glass-card rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6 bg-gray-100">
                            <i class="fas fa-images text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-600 mb-3">Belum Ada Foto</h3>
                        <p class="text-gray-500">Foto akan muncul di sini setelah sesi fotografi selesai</p>
                    </div>
                    @endif
                </div>
            </section>

            <!-- Share Section -->
            <section class="mb-12 animate-fade-in-up">
                <div class="glass-card rounded-2xl p-8">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gradient mb-3 flex items-center justify-center">
                            <i class="fas fa-share-alt text-blue-500 mr-3"></i>
                            Bagikan Kebahagiaan
                        </h3>
                        <p class="text-gray-700">Biarkan keluarga dan teman menikmati momen indah Anda</p>
                    </div>
                    
                    <div class="max-w-xl mx-auto space-y-4">
                        <div class="glass-card rounded-xl p-4 bg-gradient-to-r from-blue-500/10 to-blue-600/10">
                            <div class="flex gap-3">
                                <input type="text" 
                                       value="{{ url()->current() }}" 
                                       class="flex-1 bg-transparent text-gray-800 placeholder-gray-500 border-none outline-none font-medium"
                                       readonly
                                       id="gallery-link">
                                <button onclick="copyToClipboard()" 
                                        class="btn-secondary px-4 py-2 shrink-0">
                                    <i class="fas fa-copy"></i>
                                    <span class="hidden sm:inline ml-2">Salin</span>
                                </button>
                            </div>
                        </div>
                        
                        <div id="copy-success" class="hidden glass-card rounded-xl p-3 bg-gradient-to-r from-green-500/20 to-emerald-500/20">
                            <p class="text-green-700 flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i>
                                Link berhasil disalin ke clipboard!
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="text-center animate-fade-in-up">
                <div class="glass-card rounded-2xl p-8">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                        <div class="text-center md:text-left">
                            <h4 class="text-2xl font-bold text-gradient mb-2">FOTOKU</h4>
                            <p class="text-gray-700">Capture Your Precious Moments</p>
                        </div>
                        <div class="text-center md:text-right text-gray-600 space-y-1">
                            <p>© {{ date('Y') }} Fotoku - Premium Photo Experience</p>
                            <p class="text-sm">Gallery tersedia hingga {{ now()->addDays(30)->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script>
        // Simple copy to clipboard
        function copyToClipboard() {
            const linkInput = document.getElementById('gallery-link');
            const successMessage = document.getElementById('copy-success');
            
            linkInput.select();
            linkInput.setSelectionRange(0, 99999);
            
            try {
                document.execCommand('copy') || navigator.clipboard?.writeText(linkInput.value);
                
                // Show success message
                successMessage.classList.remove('hidden');
                
                setTimeout(() => {
                    successMessage.classList.add('hidden');
                }, 3000);
                
            } catch (err) {
                console.error('Failed to copy:', err);
            }
        }
    </script>
</body>
</html>
