{{-- Camera Settings Panel --}}
<div id="camera-settings-panel" class="hidden fixed top-0 left-0 right-0 z-50 bg-slate-800/95 backdrop-blur-sm border-b border-slate-700 px-6 py-4">
    <div class="max-w-4xl mx-auto flex items-center justify-between">
        <div class="flex items-center space-x-6">
            <h3 class="text-white font-semibold">Pengaturan Kamera</h3>
            
            <div class="flex items-center space-x-3">
                <label class="text-white text-sm">Device:</label>
                <select id="camera-device-select" class="bg-slate-700 text-white border border-slate-600 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
                    <option value="">Pilih Kamera...</option>
                </select>
            </div>
            
            <div class="flex items-center space-x-2">
                <button onclick="refreshCameraDevices()" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
                <button onclick="testCamera()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                    <i class="fas fa-camera mr-2"></i>Test
                </button>
                <button onclick="forceStopSession()" 
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm transition-colors"
                        title="Emergency Stop - Force stop current session">
                    <i class="fas fa-stop mr-2"></i>Stop
                </button>
            </div>
        </div>
        
        <button onclick="toggleCameraSettings()" 
                class="text-white hover:text-gray-300 text-xl">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

{{-- Main Header --}}
<header class="bg-white/10 backdrop-blur-md border-b border-white/20 p-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-900 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-camera text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Fotoku Photobox</h1>
                <p class="text-white/80">{{ $photobox->code }} - {{ $photobox->name }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            {{-- Fullscreen Toggle Button --}}
            <button id="fullscreen-toggle" onclick="toggleFullscreen()" 
                    class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110 shadow-lg"
                    title="Toggle Fullscreen Mode">
                <i id="fullscreen-icon" class="fas fa-expand text-white text-sm"></i>
            </button>
            
            <button id="emergency-stop-btn" onclick="forceStopSession()" 
                    class="hidden w-10 h-10 bg-red-600 hover:bg-red-700 rounded-lg flex items-center justify-center transition-all duration-200 pulse-red shadow-lg"
                    title="Admin: Emergency Stop Session">
                <i class="fas fa-stop text-white text-lg"></i>
            </button>
            
            <button id="camera-settings-toggle" onclick="toggleCameraSettings()" 
                    class="w-8 h-8 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-all duration-200 opacity-30 hover:opacity-100">
                <i class="fas fa-cog text-white text-sm"></i>
            </button>
            
            <div class="text-right">
                <div class="text-white text-sm">Status</div>
                <div id="status-indicator" class="text-white font-semibold flex items-center">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-2"></div>
                    Menunggu Sesi...
                </div>
            </div>
        </div>
    </div>
</header>
