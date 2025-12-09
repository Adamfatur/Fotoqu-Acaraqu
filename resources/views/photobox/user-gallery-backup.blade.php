<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Foto - {{ $session->session_code }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="font-sans">
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-md rounded-full mb-4">
                    <i class="fas fa-images text-white text-3xl"></i>
                </div>
                <h1 class="text-4xl font-bold text-white mb-2">Gallery Foto Anda</h1>
                <p class="text-white/80 text-lg">Sesi: {{ $session->session_code }}</p>
                <p class="text-white/60 text-sm">{{ $session->created_at->format('d M Y, H:i') }}</p>
            </div>

            <!-- Frame Section -->
            @if($frame)
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 mb-8 border border-white/20">
                <h2 class="text-2xl font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-crown mr-2 text-yellow-400"></i>
                    Frame Final Anda
                </h2>
                
                <div class="grid md:grid-cols-2 gap-6 items-center">
                    <div class="order-2 md:order-1">
                        <div class="bg-white rounded-lg p-2">
                            <img src="{{ $frame->preview_url ?? asset('images/frame-placeholder.png') }}" 
                                 alt="Frame Final" 
                                 class="w-full h-auto rounded-lg shadow-lg">
                        </div>
                    </div>
                    <div class="order-1 md:order-2 text-center md:text-left">
                        <h3 class="text-xl font-semibold text-white mb-3">🎉 Masterpiece Anda!</h3>
                        <p class="text-white/80 mb-4">Frame spektakuler dengan {{ $photos->count() }} foto terbaik Anda telah siap!</p>
                        
                        <div class="space-y-3">
                            <a href="{{ $frame->download_url ?? '#' }}" 
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg {{ !isset($frame->download_url) ? 'opacity-50 cursor-not-allowed' : '' }}"
                               {{ !isset($frame->download_url) ? 'disabled' : '' }}>
                                <i class="fas fa-download mr-2"></i>
                                Unduh Frame (Resolusi Terbaik)
                            </a>
                            
                            <div class="text-white/60 text-sm">
                                <i class="fas fa-info-circle mr-1"></i>
                                File akan diunduh dalam kualitas tertinggi
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Individual Photos Section -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                <h2 class="text-2xl font-semibold text-white mb-4 flex items-center">
                    <i class="fas fa-camera mr-2 text-blue-400"></i>
                    Semua Foto Sesi ({{ $photos->count() }} foto)
                </h2>
                
                @if($photos->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($photos as $photo)
                    <div class="bg-white/5 rounded-lg p-2 hover:bg-white/10 transition-all duration-200">
                        <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden mb-2">
                            <img src="{{ $photo->thumbnail_url ?? $photo->preview_url }}" 
                                 alt="Foto {{ $photo->sequence_number }}" 
                                 class="w-full h-full object-cover cursor-pointer"
                                 onclick="showPhotoModal('{{ $photo->preview_url }}', '{{ $photo->sequence_number }}')">
                        </div>
                        <div class="text-center">
                            <p class="text-white/80 text-sm font-medium">Foto #{{ $photo->sequence_number }}</p>
                            <a href="{{ $photo->download_url }}" 
                               class="inline-flex items-center text-xs text-blue-300 hover:text-blue-200 mt-1">
                                <i class="fas fa-download mr-1"></i>
                                Unduh
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Download All Button -->
                <div class="text-center mt-6">
                    @php
                    $expires = time() + 86400; // 24 hours
                    $signature = hash_hmac('sha256', $session->id . $expires, config('app.key'));
                    @endphp
                    <a href="{{ route('photobox.download-all-photos', [
                        'session' => $session->id, 
                        'expires' => $expires,
                        'signature' => $signature,
                        'zip' => 1
                    ]) }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg">
                        <i class="fas fa-download mr-2"></i>
                        Unduh Semua Foto (ZIP)
                    </a>
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-images text-white/30 text-4xl mb-4"></i>
                    <p class="text-white/60">Tidak ada foto tersedia</p>
                </div>
                @endif
            </div>

            <!-- Share Section -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 mt-8 border border-white/20 text-center">
                <h2 class="text-xl font-semibold text-white mb-4 flex items-center justify-center">
                    <i class="fas fa-share-alt mr-2 text-pink-400"></i>
                    Bagikan Gallery Ini
                </h2>
                
                <p class="text-white/80 mb-4">Scan QR code atau salin link untuk berbagi gallery foto Anda</p>
                
                <div class="flex flex-col md:flex-row items-center justify-center gap-6">
                    <!-- QR Code -->
                    <div class="bg-white p-4 rounded-lg">
                        <div id="qr-code"></div>
                    </div>
                    
                    <!-- Share Link -->
                    <div class="flex-1 max-w-md">
                        <div class="flex items-center bg-white/10 rounded-lg p-3">
                            <input type="text" 
                                   id="share-url" 
                                   value="{{ $qrCodeUrl }}" 
                                   readonly 
                                   class="flex-1 bg-transparent text-white placeholder-white/50 border-none outline-none text-sm">
                            <button onclick="copyShareUrl()" 
                                    class="ml-2 px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm transition-colors">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <p class="text-white/60 text-xs mt-2">Link ini dapat diakses kapan saja</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8">
                <p class="text-white/60 text-sm">Powered by 
                    <span class="font-semibold text-white">FOTOKU</span> - 
                    Kreatif Desain
                </p>
            </div>
        </div>
    </div>

    <!-- Photo Modal -->
    <div id="photo-modal" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4" onclick="hidePhotoModal()">
        <div class="relative max-w-4xl max-h-full">
            <img id="modal-image" src="" alt="" class="max-w-full max-h-full rounded-lg">
            <button onclick="hidePhotoModal()" 
                    class="absolute top-4 right-4 w-10 h-10 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black/70 transition-colors">
                <i class="fas fa-times"></i>
            </button>
            <div id="modal-caption" class="absolute bottom-4 left-4 bg-black/50 text-white px-3 py-1 rounded"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
    <script>
        // Generate QR Code
        document.addEventListener('DOMContentLoaded', function() {
            const qr = qrcode(0, 'M');
            qr.addData('{{ $qrCodeUrl }}');
            qr.make();
            document.getElementById('qr-code').innerHTML = qr.createImgTag(4);
        });

        // Copy share URL
        function copyShareUrl() {
            const input = document.getElementById('share-url');
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).then(function() {
                // Show success feedback
                const button = event.target.closest('button');
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.add('bg-green-500');
                button.classList.remove('bg-blue-500');
                
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('bg-green-500');
                    button.classList.add('bg-blue-500');
                }, 2000);
            });
        }

        // Photo modal functions
        function showPhotoModal(imageUrl, caption) {
            document.getElementById('modal-image').src = imageUrl;
            document.getElementById('modal-caption').textContent = caption;
            document.getElementById('photo-modal').classList.remove('hidden');
            document.getElementById('photo-modal').classList.add('flex');
        }

        function hidePhotoModal() {
            document.getElementById('photo-modal').classList.add('hidden');
            document.getElementById('photo-modal').classList.remove('flex');
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hidePhotoModal();
            }
        });
    </script>
</body>
</html>
