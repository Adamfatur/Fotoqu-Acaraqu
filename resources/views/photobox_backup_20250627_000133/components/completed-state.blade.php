{{-- Completed State - Frame is ready, show preview and download options --}}
<div id="completed-state" class="h-full hidden flex flex-col items-center justify-center text-center p-4 overflow-y-auto">
    <div class="relative mb-6">
        <div class="w-24 h-24 bg-gradient-to-r from-green-400 via-emerald-500 to-teal-500 rounded-full flex items-center justify-center shadow-2xl animate-bounce">
            <i class="fas fa-heart text-white text-3xl"></i>
        </div>
        <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center animate-ping">
            <i class="fas fa-crown text-white text-sm"></i>
        </div>
    </div>
    
    <h2 class="text-3xl font-bold text-white mb-2 bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">🎉 Masterpiece Created! 🎉</h2>
    <h3 class="text-xl font-semibold text-white/90 mb-3">Frame Keren Anda Sudah Siap!</h3>
    <p class="text-white/80 text-base mb-6 max-w-md">Frame spektakuler Anda telah dikirim ke email dan siap untuk diunduh! 📧✨</p>
    
    <div id="frame-preview-container" class="bg-white/10 backdrop-blur-md rounded-2xl p-4 max-w-xs mb-6 border border-white/20 shadow-xl">
        <div class="text-white/80 text-sm mb-2 flex items-center justify-center">
            <i class="fas fa-image mr-2"></i>
            Preview Frame Anda
        </div>
        <div id="frame-preview" class="rounded-xl overflow-hidden bg-gray-200 min-h-[150px] flex items-center justify-center">
            <div class="text-gray-500 text-sm">Loading preview...</div>
        </div>
    </div>

    <div class="flex flex-col space-y-3 w-full max-w-sm">
        <button onclick="resetToWaiting()" 
                class="touch-btn bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg border border-blue-400 py-3">
            <i class="fas fa-home mr-2"></i>
            Sesi Baru (10 detik)
        </button>
        
        <button onclick="cancelAutoReturn()" 
                class="touch-btn bg-white/20 text-white rounded-xl hover:bg-white/30 transition-all duration-200 border border-white/30 py-3">
            <i class="fas fa-pause mr-2"></i>
            Tahan Dulu
        </button>
    </div>
    
    <div id="auto-return-countdown" class="mt-4 text-white/60 text-sm">
        Otomatis kembali ke menu utama dalam <span id="countdown-timer">10</span> detik
    </div>
</div>
