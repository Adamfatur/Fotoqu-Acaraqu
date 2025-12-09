{{-- Processing State - Frame creation in progress --}}
<div id="processing-state" class="h-full hidden flex flex-col items-center justify-center text-center p-4">
    {{-- Animated Icon --}}
    <div class="relative mb-8">
        <div class="w-32 h-32 bg-gradient-to-r from-purple-600 via-pink-500 to-blue-500 rounded-full flex items-center justify-center shadow-2xl animate-pulse">
            <i class="fas fa-magic text-white text-5xl animate-bounce"></i>
        </div>
        <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center animate-spin">
            <i class="fas fa-sparkles text-white text-sm"></i>
        </div>
    </div>
    
    {{-- Dynamic Title --}}
    <h2 id="processing-title" class="text-3xl lg:text-4xl font-bold text-white mb-4 bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">✨ Menciptakan Keajaiban ✨</h2>
    
    {{-- Dynamic Subtitle --}}
    <p id="processing-subtitle" class="text-white/90 text-lg lg:text-xl mb-8 font-medium max-w-lg">AI sedang menyusun foto terbaik Anda...</p>
    
    {{-- Enhanced Progress Bar --}}
    <div class="w-80 max-w-sm bg-white/20 rounded-full p-1 border border-white/30 shadow-lg mb-6">
        <div id="processing-progress" class="bg-gradient-to-r from-purple-500 via-pink-500 to-blue-500 h-4 rounded-full transition-all duration-1000 flex items-center justify-end pr-2" style="width: 0%">
            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
        </div>
    </div>
    
    {{-- Percentage Display --}}
    <div class="text-white/70 text-xl lg:text-2xl font-bold mb-4">
        <span id="processing-percentage">0</span>% selesai
    </div>
    
    {{-- Dynamic Status --}}
    <div id="processing-status" class="text-white/60 text-sm lg:text-base italic max-w-md">
        Memulai proses...
    </div>
    
    {{-- Processing Tips (Optional) --}}
    <div class="mt-8 text-white/50 text-xs lg:text-sm max-w-lg text-center">
        Proses ini membutuhkan waktu beberapa detik. Mohon tunggu hingga selesai...
    </div>
</div>
