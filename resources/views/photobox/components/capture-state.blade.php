{{-- Audio Effects --}}
<audio id="countdown-sound" src="/music/timer.mp3" preload="auto"></audio>
<audio id="shutter-sound" src="/music/camera.wav" preload="auto"></audio>

{{-- Capture State - Full Screen Camera preview and automatic photo capture --}}
<div id="capture-state" class="h-full hidden">
    {{-- Full Screen Camera Container --}}
    <div class="absolute inset-0 bg-black">
        <video id="camera-preview" 
               class="w-full h-full object-cover" 
               autoplay 
               playsinline 
               muted
               style="transform: translate3d(0,0,0); will-change: transform; -webkit-backface-visibility: hidden; backface-visibility: hidden;"></video>
        
        {{-- Countdown Overlay --}}
        <div id="countdown-overlay" class="absolute inset-0 bg-black/50 flex items-center justify-center hidden z-30">
            <div class="relative w-48 h-48">
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" stroke="rgba(255,255,255,0.3)" stroke-width="8" fill="none"/>
                    <circle cx="50" cy="50" r="40" stroke="white" stroke-width="8" fill="none" 
                            class="countdown-circle" stroke-linecap="round"
                            stroke-dasharray="251.2" stroke-dashoffset="0"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span id="countdown-number" class="text-6xl font-bold text-white">3</span>
                </div>
            </div>
        </div>

        {{-- Flash Effect --}}
        <div id="flash-overlay" class="absolute inset-0 bg-white opacity-0 transition-opacity duration-100 z-20"></div>
        
        {{-- Fullscreen Toggle - Top Left --}}
        <div class="absolute top-6 left-6 z-40">
            <button onclick="toggleFullscreen()" 
                    title="Toggle Fullscreen (F12)"
                    class="bg-purple-500/80 hover:bg-purple-600/80 backdrop-blur-sm text-white w-12 h-12 rounded-full border border-white/20 transition-all duration-200 flex items-center justify-center">
                <i class="fas fa-expand text-lg"></i>
            </button>
        </div>
        
        {{-- Photo Progress Info - Full Screen Style --}}
        <div class="absolute bottom-6 left-6 right-6 z-40">
            <div class="flex items-center justify-between">
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                    <div class="text-white text-sm">Progress Foto</div>
                    <div class="text-green-400 font-bold text-xl">
                        <span id="photo-count">0</span> / {{ $settings['total_photos'] }}
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                    <div class="text-white text-sm">Foto Saat Ini</div>
                    <div class="text-green-400 font-bold text-xl">
                        #<span id="current-photo">1</span>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                    <div class="text-white text-sm">Sesi Auto</div>
                    <div class="text-green-400 font-bold text-xl">
                        <i class="fas fa-play-circle mr-2"></i>Berjalan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
