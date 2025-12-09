{{-- Waiting State - Fullscreen camera preview background so users can prepare while waiting --}}
<div id="waiting-state" class="relative min-h-screen h-full overflow-hidden -m-6">
    {{-- Live camera preview as background --}}
    <video id="waiting-camera-preview" class="absolute inset-0 w-full h-full object-cover z-0" autoplay playsinline muted></video>

    {{-- Subtle dark overlay for readability --}}
    <div class="absolute inset-0 bg-black/35 z-10"></div>

    {{-- Foreground content --}}
    <div class="relative z-20 h-full p-6 flex items-center">
        <div class="w-full max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-10 items-center">
                {{-- Left: heading + action --}}
                <div class="flex flex-col justify-center text-center md:text-left">
                    <div class="w-24 h-24 md:w-28 md:h-28 bg-white/10 rounded-full flex items-center justify-center mb-6 md:mb-8 backdrop-blur-sm border border-white/20 shadow-xl mx-auto md:mx-0">
                        <i class="fas fa-camera text-white text-3xl md:text-4xl"></i>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-3 drop-shadow">Siap Untuk Foto?</h2>
                    <p class="text-white/85 text-base md:text-lg mb-6 md:mb-8">Tekan tombol di bawah untuk memeriksa sesi foto Anda</p>

                    <div id="session-info-container" class="w-full max-w-md mb-6 md:mb-8 mx-auto md:mx-0"></div>

                    <div>
                        <button onclick="checkForSession(event)"
                                class="touch-btn bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg border border-blue-400 transform hover:scale-105 active:scale-95 px-7 md:px-8 py-3 md:py-4">
                            <i class="fas fa-search mr-3"></i>
                            Periksa Sesi Foto
                        </button>
                    </div>
                </div>

                {{-- Right: rules --}}
                <div class="w-full">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 md:p-8 lg:p-10 shadow-lg">
                        <div class="flex items-center mb-4 md:mb-6">
                            <i class="fas fa-list-check text-white/90 mr-3 text-2xl md:text-3xl"></i>
                            <h3 class="text-white font-bold text-2xl md:text-3xl tracking-tight">Aturan Sesi Foto</h3>
                        </div>
                        <ul class="space-y-4 md:space-y-5 text-white/90 text-base md:text-lg lg:text-xl leading-relaxed">
                            <li class="flex items-start">
                                <i class="fas fa-camera mr-4 mt-1.5 text-emerald-300 text-2xl md:text-3xl"></i>
                                <span>Setiap sesi akan mengambil <span class="font-semibold text-white">3 foto</span> secara otomatis.</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-ban mr-4 mt-1.5 text-rose-300 text-2xl md:text-3xl"></i>
                                <span>Tidak ada <em>retake</em>. Kamera memotret <span class="font-semibold text-white">berurutan</span> hingga selesai.</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-hourglass-start mr-4 mt-1.5 text-amber-300 text-2xl md:text-3xl"></i>
                                <span>Ada <span class="font-semibold text-white">hitung mundur 3 detik</span> sebelum setiap jepretan—pastikan posisi dan ekspresi sudah siap.</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-headset mr-4 mt-1.5 text-sky-300 text-2xl md:text-3xl"></i>
                                <span>Jika terjadi kendala, Anda dapat <span class="font-semibold text-white">menghubungi admin</span> untuk bantuan/pembatalan.</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-shield-alt mr-4 mt-1.5 text-violet-300 text-2xl md:text-3xl"></i>
                                <span>Pembatalan hanya diproses bila terjadi <span class="font-semibold text-white">masalah pada sistem</span>, bukan karena kesalahan pengguna.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Fullscreen tip for tablets - Hidden from client view --}}
            <div class="mt-6 max-w-sm hidden">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                    <div class="flex items-center justify-between text-white/80 text-sm">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-expand text-white/60"></i>
                            <span>Mode Layar Penuh</span>
                        </div>
                        <button onclick="toggleFullscreen()"
                                class="px-3 py-1 bg-white/20 hover:bg-white/30 rounded-lg transition-colors text-xs font-medium">
                            <span id="fullscreen-tip-text">Aktifkan</span>
                        </button>
                    </div>
                    <p class="text-white/60 text-xs mt-2">Untuk pengalaman foto terbaik di tablet</p>
                </div>
            </div>
        </div>
    </div>
</div>
