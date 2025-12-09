{{-- Waiting State - User waits for session to be created and approved --}}
<div id="waiting-state" class="h-full flex flex-col items-center justify-center text-center">
    <div class="w-32 h-32 bg-white/10 rounded-full flex items-center justify-center mb-8 pulse-bg shadow-xl">
        <i class="fas fa-camera text-white text-5xl"></i>
    </div>
    <h2 class="text-4xl font-bold text-white mb-4">Siap Untuk Foto?</h2>
    <p class="text-white/80 text-xl mb-8">Tekan tombol di bawah untuk memeriksa sesi foto Anda</p>
    
    <div id="session-info-container" class="max-w-md mb-8">
        {{-- Session info will be populated by JavaScript --}}
    </div>
    
    <button onclick="checkForSession(event)" 
            class="touch-btn bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl 
                   hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg 
                   border border-blue-400 transform hover:scale-105 active:scale-95 px-8 py-4">
        <i class="fas fa-search mr-3"></i>
        Periksa Sesi Foto
    </button>
    
    <p class="text-white/60 text-sm mt-4">
        <i class="fas fa-info-circle mr-1"></i>
        Admin akan memberitahu Anda ketika sesi siap
    </p>
    
    {{-- Fullscreen tip for tablets --}}
    <div class="mt-6 max-w-sm">
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
