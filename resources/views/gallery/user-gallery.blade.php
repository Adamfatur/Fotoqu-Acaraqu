<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Foto Anda - Fotoku</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .gallery-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .photo-item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .photo-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        .download-btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        .download-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .frame-highlight {
            border: 3px solid #667eea;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="gallery-container p-8 mb-8 text-center">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">
                    🎉 Galeri Foto Anda
                </h1>
                <p class="text-gray-600 text-lg">
                    Terima kasih telah menggunakan Fotoku! Berikut adalah koleksi foto dan frame Anda.
                </p>
                @if($session)
                    <div class="mt-4 inline-block bg-blue-100 text-blue-800 px-4 py-2 rounded-full">
                        <strong>Sesi:</strong> {{ $session->session_code }}
                    </div>
                @endif
            </div>

            @if($frames && $frames->count() > 0)
                <!-- Final Frames Section -->
                <div class="gallery-container p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        🖼️ Frame Final Anda
                        <span class="ml-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white px-3 py-1 rounded-full text-sm">
                            Siap Cetak!
                        </span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($frames as $frame)
                            <div class="photo-item frame-highlight">
                                <div class="aspect-[3/4] bg-gray-200 relative overflow-hidden">
                                    @if($frame->s3_url)
                                        <img 
                                            src="{{ $frame->s3_url }}" 
                                            alt="Frame {{ $frame->id }}"
                                            class="w-full h-full object-cover"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-500">
                                            <div class="text-center">
                                                <div class="text-4xl mb-2">🖼️</div>
                                                <p>Frame sedang diproses...</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-800 mb-2">
                                        Frame Final #{{ $loop->iteration }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-4">
                                        Template: {{ $frame->template->name ?? 'Standard' }}
                                        <br>
                                        Layout: {{ $frame->template->slot_count ?? 4 }} Foto
                                    </p>
                                    @if($frame->s3_url)
                                        <button 
                                            onclick="downloadImage('{{ $frame->s3_url }}', 'fotoku-frame-{{ $frame->id }}.jpg')"
                                            class="download-btn w-full"
                                        >
                                            📥 Download Frame
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($photos && $photos->count() > 0)
                <!-- Individual Photos Section -->
                <div class="gallery-container p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        📸 Foto Individual Anda
                        <span class="ml-3 bg-blue-500 text-white px-3 py-1 rounded-full text-sm">
                            {{ $photos->count() }} Foto
                        </span>
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($photos as $photo)
                            <div class="photo-item">
                                <div class="aspect-square bg-gray-200 relative overflow-hidden">
                                    @if($photo->s3_url)
                                        <img 
                                            src="{{ $photo->s3_url }}" 
                                            alt="Foto {{ $photo->id }}"
                                            class="w-full h-full object-cover cursor-pointer"
                                            onclick="openLightbox('{{ $photo->s3_url }}', 'Foto #{{ $loop->iteration }}')"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-500">
                                            <div class="text-center">
                                                <div class="text-2xl mb-1">📷</div>
                                                <p class="text-xs">Loading...</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <p class="text-sm font-medium text-gray-800 mb-2">
                                        Foto #{{ $loop->iteration }}
                                    </p>
                                    @if($photo->s3_url)
                                        <button 
                                            onclick="downloadImage('{{ $photo->s3_url }}', 'fotoku-photo-{{ $photo->id }}.jpg')"
                                            class="download-btn w-full text-sm py-2"
                                        >
                                            📥 Download
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if((!$photos || $photos->count() === 0) && (!$frames || $frames->count() === 0))
                <!-- No Content -->
                <div class="gallery-container p-8 text-center">
                    <div class="text-6xl mb-4">🔍</div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Belum Ada Foto</h2>
                    <p class="text-gray-600">
                        Sesi foto ini belum memiliki foto atau frame yang tersedia.
                        <br>
                        Silakan hubungi admin jika ini adalah kesalahan.
                    </p>
                </div>
            @endif

            <!-- Footer Info -->
            <div class="gallery-container p-6 mt-8 text-center text-sm text-gray-500">
                <p>
                    📱 Simpan halaman ini untuk mengakses foto Anda kapan saja
                    <br>
                    🔗 Link ini berlaku selama 30 hari
                    <br>
                    💡 Tip: Screenshot QR code untuk akses mudah di masa depan
                </p>
            </div>
        </div>
    </div>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full">
            <button 
                onclick="closeLightbox()"
                class="absolute top-4 right-4 text-white text-2xl bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75 transition-all z-10"
            >
                ✕
            </button>
            <img id="lightbox-image" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
            <div id="lightbox-caption" class="absolute bottom-4 left-4 right-4 text-white text-center bg-black bg-opacity-50 rounded-lg p-2">
            </div>
        </div>
    </div>

    <script>
        // Download function
        function downloadImage(url, filename) {
            try {
                const link = document.createElement('a');
                link.href = url;
                link.download = filename;
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (error) {
                console.error('Download error:', error);
                // Fallback: open in new tab
                window.open(url, '_blank');
            }
        }

        // Lightbox functions
        function openLightbox(imageUrl, caption) {
            const lightbox = document.getElementById('lightbox');
            const image = document.getElementById('lightbox-image');
            const captionElement = document.getElementById('lightbox-caption');
            
            image.src = imageUrl;
            image.alt = caption;
            captionElement.textContent = caption;
            
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        // Close lightbox on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });

        // Close lightbox on background click
        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });

        // Lazy loading fallback for older browsers
        if ('loading' in HTMLImageElement.prototype) {
            // Native lazy loading supported
        } else {
            // Implement fallback if needed
            console.log('Native lazy loading not supported');
        }
    </script>
</body>
</html>
