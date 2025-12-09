{{-- Capture State - Camera preview and automatic photo capture --}}
<div id="capture-state" class="h-full hidden">
    <div class="h-full flex flex-col">
        <div class="flex-1 bg-black/20 rounded-2xl overflow-hidden mb-6 relative">
            <video id="camera-preview" class="w-full h-full object-cover" autoplay playsinline></video>
            
            {{-- Countdown Overlay --}}
            <div id="countdown-overlay" class="absolute inset-0 bg-black/50 flex items-center justify-center hidden">
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

            {{-- Interval Timer Overlay --}}
            <div id="interval-overlay" class="absolute inset-0 bg-black/70 flex items-center justify-center hidden">
                <div class="text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mb-4 mx-auto">
                        <i class="fas fa-clock text-white text-2xl"></i>
                    </div>
                    <div id="interval-timer" class="text-white text-2xl font-semibold">
                        Foto selanjutnya dalam 8 detik...
                    </div>
                </div>
            </div>

            {{-- Flash Effect --}}
            <div id="flash-overlay" class="absolute inset-0 bg-white opacity-0 transition-opacity duration-100"></div>
        </div>

        {{-- Photo Progress Info --}}
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
