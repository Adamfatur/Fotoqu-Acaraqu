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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            min-height: 100vh;
        }
        
        /* Animated Background */
        .bg-animated {
            background: linear-gradient(-45deg, #0f172a, #1e293b, #334155, #475569);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Glass Morphism Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .glass-card:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-4px);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        /* Enhanced Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8, #1e40af);
            color: white;
            padding: 12px 24px;
            border-radius: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4);
            position: relative;
            overflow: hidden;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 32px rgba(59, 130, 246, 0.5);
            background: linear-gradient(135deg, #2563eb, #1d4ed8, #1e40af);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        /* Enhanced Photo Cards */
        .photo-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
            position: relative;
        }
        
        .photo-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 51, 234, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }
        
        .photo-card:hover::before {
            opacity: 1;
        }
        
        .photo-card:hover {
            transform: translateY(-12px) rotateX(8deg) rotateY(5deg);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .photo-card img {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .photo-card:hover img {
            transform: scale(1.1);
        }
        
        /* Frame Section Enhancement */
        .frame-container {
            perspective: 1200px;
        }
        
        .frame-card {
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
        }
        
        .frame-card:hover {
            transform: rotateY(8deg) rotateX(4deg) scale(1.02);
        }
        
        /* Smooth Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% { 
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
            }
            50% { 
                transform: scale(1.05);
                box-shadow: 0 0 0 20px rgba(59, 130, 246, 0);
            }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
        
        .animate-slide-in-left {
            animation: slideInLeft 0.8s ease-out;
        }
        
        .animate-slide-in-right {
            animation: slideInRight 0.8s ease-out;
        }
        
        .animate-pulse {
            animation: pulse 3s infinite;
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        /* Staggered animations for photos */
        .photo-card:nth-child(1) { animation-delay: 0.1s; }
        .photo-card:nth-child(2) { animation-delay: 0.2s; }
        .photo-card:nth-child(3) { animation-delay: 0.3s; }
        .photo-card:nth-child(4) { animation-delay: 0.4s; }
        .photo-card:nth-child(5) { animation-delay: 0.5s; }
        .photo-card:nth-child(6) { animation-delay: 0.6s; }
        .photo-card:nth-child(7) { animation-delay: 0.7s; }
        .photo-card:nth-child(8) { animation-delay: 0.8s; }
        
        /* Enhanced Text Gradients */
        .text-gradient {
            background: linear-gradient(135deg, #ffffff, #e2e8f0, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .text-gradient-blue {
            background: linear-gradient(135deg, #60a5fa, #3b82f6, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .text-gradient-purple {
            background: linear-gradient(135deg, #a855f7, #8b5cf6, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Loading shimmer effect */
        .shimmer {
            position: relative;
            overflow: hidden;
        }
        
        .shimmer::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        
        /* Enhanced responsiveness */
        @media (max-width: 640px) {
            .glass-card {
                backdrop-filter: blur(15px);
                margin: 0 8px;
            }
            
            .photo-card:hover {
                transform: translateY(-8px) scale(1.02);
            }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        
        /* Particle effect */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s infinite linear;
        }
    </style>
</head>
<body class="bg-animated">
    <!-- Particle Background -->
    <div class="particles" id="particles"></div>
    
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-5xl mx-auto">
            
            <!-- Header Section -->
            <header class="text-center mb-16 animate-fade-in-up">
                <div class="glass-card rounded-3xl p-10 mb-8">
                    <!-- Animated Logo -->
                    <div class="inline-flex items-center justify-center w-28 h-28 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-full mb-8 animate-pulse">
                        <i class="fas fa-camera text-white text-4xl animate-float"></i>
                    </div>
                    
                    <!-- Title with Enhanced Gradient -->
                    <h1 class="text-5xl md:text-6xl font-bold mb-6">
                        <span class="text-gradient">Gallery Foto</span>
                    </h1>
                    
                    <!-- Session Info Card -->
                    <div class="glass-card rounded-2xl p-8 mb-8 mx-auto max-w-lg shimmer">
                        <div class="space-y-3">
                            <p class="text-gradient-blue text-2xl font-bold">{{ $session->session_code }}</p>
                            <p class="text-white/95 font-semibold text-lg">{{ $session->customer_name }}</p>
                            <p class="text-white/80">{{ $session->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    
                    <!-- Enhanced Stats -->
                    <div class="flex justify-center gap-12">
                        <div class="text-center group">
                            <div class="text-4xl font-bold text-gradient-blue mb-2 group-hover:scale-110 transition-transform duration-300">{{ $photos->count() }}</div>
                            <div class="text-white/80 text-sm uppercase tracking-widest font-medium">Foto Tersedia</div>
                        </div>
                        @if($frame)
                        <div class="text-center group">
                            <div class="text-4xl font-bold text-gradient-purple mb-2 group-hover:scale-110 transition-transform duration-300">1</div>
                            <div class="text-white/80 text-sm uppercase tracking-widest font-medium">Frame Premium</div>
                        </div>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Frame Section -->
            @if($frame)
            <section class="mb-20 animate-slide-in-left">
                <div class="glass-card rounded-3xl p-10">
                    <div class="text-center mb-10">
                        <h2 class="text-3xl font-bold text-gradient mb-4 flex items-center justify-center">
                            <i class="fas fa-crown text-yellow-400 mr-4 text-2xl animate-pulse"></i>
                            Frame Masterpiece Anda
                        </h2>
                        <p class="text-white/90 text-lg">Karya seni digital yang siap memukau dunia!</p>
                    </div>
                    
                    <div class="frame-container">
                        <div class="frame-card grid md:grid-cols-2 gap-10 items-center">
                            <!-- Frame Image -->
                            <div class="order-2 md:order-1">
                                <div class="glass-card rounded-2xl p-6 bg-gradient-to-br from-white/10 to-white/5 shimmer">
                                    <img src="{{ $frame->preview_url ?? asset('images/frame-placeholder.png') }}" 
                                         alt="Frame Final" 
                                         class="w-full h-auto rounded-xl shadow-2xl transition-all duration-700 hover:scale-105 hover:rotate-1">
                                </div>
                            </div>
                            
                            <!-- Frame Info -->
                            <div class="order-1 md:order-2 text-center md:text-left space-y-8">
                                <div>
                                    <h3 class="text-3xl font-bold text-gradient mb-4">🎉 Spectacular!</h3>
                                    <p class="text-white/95 text-xl mb-3">Frame berisi {{ $photos->count() }} foto pilihan terbaik</p>
                                    <p class="text-white/80 text-lg">Resolusi ultra tinggi untuk hasil cetak profesional</p>
                                </div>
                                
                                <div class="space-y-6">
                                    <a href="{{ $frame->download_url ?? '#' }}" 
                                       class="btn-primary text-lg px-10 py-4 w-full md:w-auto {{ !isset($frame->download_url) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                       {{ !isset($frame->download_url) ? 'disabled' : '' }}>
                                        <i class="fas fa-download text-xl"></i>
                                        Unduh Frame Premium
                                    </a>
                                    
                                    <div class="glass-card rounded-xl p-6 bg-gradient-to-r from-blue-500/10 to-purple-500/10">
                                        <p class="text-white/90 flex items-center justify-center md:justify-start">
                                            <i class="fas fa-sparkles text-yellow-400 mr-3 text-lg"></i>
                                            <span class="font-medium">Kualitas 300 DPI • Format Premium • Siap Cetak</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @endif

            <!-- Photos Section -->
            <section class="mb-20 animate-slide-in-right">
                <div class="glass-card rounded-3xl p-10">
                    <!-- Section Header -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-12">
                        <div class="text-center md:text-left mb-6 md:mb-0">
                            <h2 class="text-3xl font-bold text-gradient mb-4 flex items-center justify-center md:justify-start">
                                <i class="fas fa-images text-blue-400 mr-4 text-2xl"></i>
                                Koleksi Foto Premium
                            </h2>
                            <p class="text-white/90 text-lg">Setiap momen berharga dalam kualitas terbaik</p>
                        </div>
                        <div class="glass-card rounded-full px-6 py-3 bg-gradient-to-r from-blue-500/20 to-purple-500/20">
                            <span class="text-white font-bold text-lg">{{ $photos->count() }} foto tersedia</span>
                        </div>
                    </div>
                    
                    @if($photos->count() > 0)
                    <!-- Photos Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-8 mb-12">
                        @foreach($photos as $photo)
                        <div class="photo-card animate-fade-in-up">
                            <!-- Photo Container -->
                            <div class="aspect-square bg-gradient-to-br from-gray-900/50 to-gray-800/50 rounded-t-2xl overflow-hidden relative group">
                                <img src="{{ $photo->thumbnail_url ?? $photo->preview_url }}" 
                                     alt="Foto {{ $photo->sequence_number }}" 
                                     class="w-full h-full object-cover"
                                     loading="lazy">
                                
                                <!-- Enhanced Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500">
                                    <div class="absolute bottom-6 left-6">
                                        <span class="text-white font-bold text-lg">Foto #{{ $photo->sequence_number }}</span>
                                        <p class="text-white/80 text-sm">Klik untuk mengunduh</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Photo Info -->
                            <div class="p-6 text-center">
                                <p class="text-white/95 font-semibold mb-4 text-lg">Foto #{{ $photo->sequence_number }}</p>
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

                    <!-- Enhanced Download All Section -->
                    <div class="text-center border-t border-white/10 pt-10">
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold text-gradient mb-3">Unduh Koleksi Lengkap</h3>
                            <p class="text-white/90 text-lg">Dapatkan semua foto dalam satu paket premium</p>
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
                           class="btn-primary text-xl px-12 py-5">
                            <i class="fas fa-archive text-xl"></i>
                            Unduh Semua Foto (ZIP)
                        </a>
                        
                        <div class="glass-card rounded-xl p-6 mt-8 bg-gradient-to-r from-green-500/10 to-blue-500/10 max-w-2xl mx-auto">
                            <p class="text-white/90 text-lg flex items-center justify-center">
                                <i class="fas fa-info-circle text-blue-400 mr-3 text-xl"></i>
                                <span class="font-medium">File ZIP berisi semua foto dalam resolusi penuh</span>
                            </p>
                        </div>
                    </div>
                    @else
                    <!-- Enhanced No Photos State -->
                    <div class="text-center py-20">
                        <div class="glass-card rounded-full w-32 h-32 flex items-center justify-center mx-auto mb-8 bg-gradient-to-br from-gray-500/20 to-gray-600/20 animate-pulse">
                            <i class="fas fa-images text-gray-400 text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white/80 mb-4">Belum Ada Foto</h3>
                        <p class="text-white/70 text-lg">Foto akan muncul di sini setelah sesi fotografi selesai</p>
                    </div>
                    @endif
                </div>
            </section>

            <!-- Enhanced Share Section -->
            <section class="mb-20 animate-fade-in-up">
                <div class="glass-card rounded-3xl p-10">
                    <div class="text-center mb-10">
                        <h3 class="text-3xl font-bold text-gradient mb-4 flex items-center justify-center">
                            <i class="fas fa-share-alt text-blue-400 mr-4 text-2xl"></i>
                            Bagikan Kebahagiaan
                        </h3>
                        <p class="text-white/90 text-lg">Biarkan keluarga dan teman menikmati momen indah Anda</p>
                    </div>
                    
                    <div class="max-w-2xl mx-auto space-y-6">
                        <div class="glass-card rounded-2xl p-6 bg-gradient-to-r from-blue-500/10 to-purple-500/10">
                            <div class="flex gap-4">
                                <input type="text" 
                                       value="{{ url()->current() }}" 
                                       class="flex-1 bg-transparent text-white/95 placeholder-white/50 border-none outline-none text-lg font-medium"
                                       readonly
                                       id="gallery-link">
                                <button onclick="copyToClipboard()" 
                                        class="btn-secondary px-6 py-3 shrink-0">
                                    <i class="fas fa-copy text-lg"></i>
                                    <span class="hidden sm:inline ml-2">Salin</span>
                                </button>
                            </div>
                        </div>
                        
                        <div id="copy-success" class="hidden glass-card rounded-2xl p-4 bg-gradient-to-r from-green-500/20 to-emerald-500/20">
                            <p class="text-green-300 text-lg flex items-center justify-center">
                                <i class="fas fa-check mr-3"></i>
                                Link berhasil disalin ke clipboard!
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Enhanced Footer -->
            <footer class="text-center animate-fade-in-up">
                <div class="glass-card rounded-3xl p-10">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                        <div class="text-center md:text-left">
                            <h4 class="text-3xl font-bold text-gradient mb-3">FOTOKU</h4>
                            <p class="text-white/90 text-lg">Capture Your Precious Moments</p>
                        </div>
                        <div class="text-center md:text-right text-white/70 space-y-2">
                            <p class="text-lg">© {{ date('Y') }} Fotoku - Premium Photo Experience</p>
                            <p class="text-sm">Gallery akan tersedia hingga {{ now()->addDays(30)->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script>
        // Enhanced copy to clipboard with animation
        function copyToClipboard() {
            const linkInput = document.getElementById('gallery-link');
            const successMessage = document.getElementById('copy-success');
            const button = event.target.closest('button');
            
            // Create ripple effect
            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position: absolute;
                width: 100px;
                height: 100px;
                border-radius: 50%;
                background: rgba(255,255,255,0.3);
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            button.style.position = 'relative';
            button.style.overflow = 'hidden';
            button.appendChild(ripple);
            
            // Add keyframes for ripple
            if (!document.getElementById('ripple-keyframes')) {
                const style = document.createElement('style');
                style.id = 'ripple-keyframes';
                style.textContent = `
                    @keyframes ripple {
                        to {
                            transform: scale(4);
                            opacity: 0;
                        }
                    }
                `;
                document.head.appendChild(style);
            }
            
            linkInput.select();
            linkInput.setSelectionRange(0, 99999);
            
            try {
                document.execCommand('copy') || navigator.clipboard?.writeText(linkInput.value);
                
                // Show success message with animation
                successMessage.classList.remove('hidden');
                successMessage.style.transform = 'scale(0.8)';
                successMessage.style.opacity = '0';
                
                requestAnimationFrame(() => {
                    successMessage.style.transition = 'all 0.3s ease';
                    successMessage.style.transform = 'scale(1)';
                    successMessage.style.opacity = '1';
                });
                
                setTimeout(() => {
                    successMessage.style.transform = 'scale(0.8)';
                    successMessage.style.opacity = '0';
                    setTimeout(() => {
                        successMessage.classList.add('hidden');
                    }, 300);
                }, 3000);
                
                // Remove ripple
                setTimeout(() => ripple.remove(), 600);
                
            } catch (err) {
                console.error('Failed to copy:', err);
            }
        }
        
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 50;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = (Math.random() * 3 + 3) + 's';
                particlesContainer.appendChild(particle);
            }
        }
        
        // Initialize particles on load
        document.addEventListener('DOMContentLoaded', createParticles);
        
        // Enhanced modern clipboard API
        if (navigator.clipboard) {
            const copyButtons = document.querySelectorAll('button[onclick="copyToClipboard()"]');
            copyButtons.forEach(button => {
                button.onclick = async function() {
                    try {
                        await navigator.clipboard.writeText(document.getElementById('gallery-link').value);
                        copyToClipboard.call(this);
                    } catch (err) {
                        copyToClipboard.call(this);
                    }
                };
            });
        }
        
        // Smooth scroll reveal animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Observe all animated elements
        document.querySelectorAll('.animate-fade-in-up, .animate-slide-in-left, .animate-slide-in-right').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'all 0.8s ease-out';
            observer.observe(el);
        });
    </script>
</body>
</html>
