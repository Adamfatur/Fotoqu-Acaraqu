<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fotoku Photobox - {{ $photobox->code }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Custom animations and styles */
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap');
        
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #164e63 50%, #059669 100%);
            overflow: hidden;
        }
        
        .pulse-bg {
            animation: pulse-bg 2s infinite;
        }
        
        @keyframes pulse-bg {
            0%, 100% { background-color: rgba(16, 185, 129, 0.1); }
            50% { background-color: rgba(16, 185, 129, 0.3); }
        }
        
        .pulse-red {
            animation: pulse-red 1.5s infinite;
        }
        
        @keyframes pulse-red {
            0%, 100% { 
                background-color: #dc2626;
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
            }
            50% { 
                background-color: #b91c1c;
                box-shadow: 0 0 0 10px rgba(220, 38, 38, 0);
            }
        }
        
        .countdown-circle {
            animation: countdown-fill 1s linear;
        }
        
        @keyframes countdown-fill {
            0% { stroke-dasharray: 0 251.2; }
            100% { stroke-dasharray: 251.2 0; }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            padding: 1rem;
            min-height: 200px;
        }
        
        .photo-item {
            aspect-ratio: 1;
            border-radius: 1rem;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            border: 2px solid transparent;
            background: #f3f4f6;
        }
        
        .photo-item:hover {
            transform: scale(1.05);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .photo-item.selected {
            border-color: #10b981;
            border-width: 3px;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
        }
        
        .photo-item img {
            transition: opacity 0.3s ease;
            opacity: 0;
        }
        
        .photo-item img[src] {
            opacity: 1;
        }
        
        /* Touch-friendly buttons */
        .touch-btn {
            min-height: 60px;
            min-width: 120px;
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        /* Fullscreen styles */
        .fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 9999;
        }
    </style>
</head>
<body class="h-screen">
    <div id="app" class="h-full flex flex-col">
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

        <main class="flex-1 p-6 overflow-hidden">
            <div id="waiting-state" class="h-full flex flex-col items-center justify-center text-center">
                <div class="w-32 h-32 bg-white/10 rounded-full flex items-center justify-center mb-8 pulse-bg shadow-xl">
                    <i class="fas fa-camera text-white text-5xl"></i>
                </div>
                <h2 class="text-4xl font-bold text-white mb-4">Siap Untuk Foto?</h2>
                <p class="text-white/80 text-xl mb-8">Tekan tombol di bawah untuk memeriksa sesi foto Anda</p>
                
                <div id="session-info-container" class="max-w-md mb-8">
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
            </div>

            <div id="capture-state" class="h-full hidden">
                <div class="h-full flex flex-col">
                    <div class="flex-1 bg-black/20 rounded-2xl overflow-hidden mb-6 relative">
                        <video id="camera-preview" class="w-full h-full object-cover" autoplay playsinline></video>
                        
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

                        <div id="flash-overlay" class="absolute inset-0 bg-white opacity-0 transition-opacity duration-100"></div>
                    </div>

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

            <div id="selection-state" class="h-full hidden">
                <div class="h-full flex flex-col">
                    <div class="mb-6 text-center">
                        <h2 class="text-3xl font-bold text-white mb-2">Pilih Foto Terbaik</h2>
                        <p class="text-white/80 text-lg">Pilih <span id="required-photos">{{ $activeSession ? $activeSession->frame_slots : 4 }}</span> foto untuk frame Anda</p>
                        <div class="mt-4">
                            <span class="bg-white/10 backdrop-blur-md text-white px-4 py-2 rounded-full">
                                <span id="selected-count">0</span> / <span id="max-selection">{{ $activeSession ? $activeSession->frame_slots : 4 }}</span> terpilih
                            </span>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto">
                        <div id="photo-grid" class="photo-grid">
                            </div>
                    </div>

                    <div class="mt-6 flex justify-center space-x-4">
                        <button onclick="resetSelection()" 
                                class="touch-btn bg-gray-600/80 hover:bg-gray-700/80 text-white rounded-xl transition-all duration-200 border border-gray-500">
                            <i class="fas fa-undo mr-2"></i>
                            Reset Pilihan
                        </button>
                        
                        <button onclick="debugButtonStatus()" 
                                class="touch-btn bg-purple-600/80 hover:bg-purple-700/80 text-white rounded-xl transition-all duration-200 border border-purple-500"
                                style="display: {{ config('app.debug') ? 'inline-flex' : 'none' }}">
                            <i class="fas fa-bug mr-2"></i>
                            Debug
                        </button>
                        
                        <button id="confirm-selection-btn" onclick="confirmSelection()" disabled
                                class="touch-btn bg-gradient-to-r from-green-600 to-emerald-500 text-white rounded-xl hover:from-green-700 hover:to-emerald-600 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed border border-green-400">
                            <i class="fas fa-check mr-2"></i>
                            Konfirmasi Pilihan
                        </button>
                    </div>
                </div>
            </div>

            <div id="processing-state" class="h-full hidden flex flex-col items-center justify-center text-center">
                <div class="relative mb-8">
                    <div class="w-32 h-32 bg-gradient-to-r from-purple-600 via-pink-500 to-blue-500 rounded-full flex items-center justify-center shadow-2xl animate-pulse">
                        <i class="fas fa-magic text-white text-5xl animate-bounce"></i>
                    </div>
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center animate-spin">
                        <i class="fas fa-sparkles text-white text-sm"></i>
                    </div>
                </div>
                
                <h2 id="processing-title" class="text-4xl font-bold text-white mb-4 bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">✨ Menciptakan Keajaiban ✨</h2>
                
                <p id="processing-subtitle" class="text-white/90 text-xl mb-8 font-medium">AI sedang menyusun foto terbaik Anda...</p>
                
                <div class="w-80 bg-white/20 rounded-full p-1 border border-white/30 shadow-lg mb-6">
                    <div id="processing-progress" class="bg-gradient-to-r from-purple-500 via-pink-500 to-blue-500 h-4 rounded-full transition-all duration-1000 flex items-center justify-end pr-2" style="width: 0%">
                        <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                    </div>
                </div>
                
                <div class="text-white/70 text-lg font-semibold mb-4">
                    <span id="processing-percentage">0</span>% selesai
                </div>
                
                <div id="processing-status" class="text-white/60 text-sm italic">
                    Memulai proses...
                </div>
            </div>

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
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Debug logging function
        function debugLog(message, data = null) {
            const timestamp = new Date().toISOString();
            console.log(`[${timestamp}] FOTOKU DEBUG: ${message}`, data || '');
            
            // Also log to localStorage for persistent debugging
            try {
                const logs = JSON.parse(localStorage.getItem('fotoku_debug_logs') || '[]');
                logs.push({ timestamp, message, data });
                
                // Keep only last 100 logs
                if (logs.length > 100) {
                    logs.splice(0, logs.length - 100);
                }
                
                localStorage.setItem('fotoku_debug_logs', JSON.stringify(logs));
            } catch (e) {
                console.warn('Failed to store debug log:', e);
            }
        }
        
        // Function to get debug logs
        function getDebugLogs() {
            try {
                return JSON.parse(localStorage.getItem('fotoku_debug_logs') || '[]');
            } catch (e) {
                return [];
            }
        }
        
        // Function to clear debug logs
        function clearDebugLogs() {
            localStorage.removeItem('fotoku_debug_logs');
            debugLog('Debug logs cleared');
        }
        
        // Make debug functions globally available
        window.debugLog = debugLog;
        window.getDebugLogs = getDebugLogs;
        window.clearDebugLogs = clearDebugLogs;
        
        // Global variables
        let currentSession = @json($activeSession);
        let capturedPhotos = [];
        let selectedPhotos = [];
        let cameraStream = null;
        let settings = @json($settings);
        let photoboxCode = '{{ $photobox->code }}';
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
            
            // No auto-polling - user will manually check
            debugLog('App initialized - manual session checking mode');
        });

        function initializeApp() {
            console.log('Initializing photobox interface...');
            console.log('Photobox code:', photoboxCode);
            console.log('Current session:', currentSession);
            
            // Verify all required elements exist
            const requiredElements = [
                'waiting-state', 'capture-state', 'selection-state', 
                'processing-state', 'completed-state', 'photo-grid',
                'status-indicator'
            ];
            
            const missingElements = requiredElements.filter(id => !document.getElementById(id));
            if (missingElements.length > 0) {
                console.error('Missing required elements:', missingElements);
                alert('Error: Missing interface elements. Please refresh the page.');
                return;
            }
            
            // Set up CSRF token for axios
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
            } else {
                console.warn('CSRF token not found');
            }
            
            // Initialize cameras
            loadCameraDevices();
            
            // Add change event listener to camera selector
            const cameraSelect = document.getElementById('camera-device-select');
            if (cameraSelect) {
                cameraSelect.addEventListener('change', switchCamera);
            }
            
            // Show appropriate state
            if (currentSession) {
                console.log('Session status:', currentSession.session_status);
                if (currentSession.session_status === 'approved') {
                    showWaitingState();
                } else if (currentSession.session_status === 'in_progress') {
                    showCaptureState();
                } else if (currentSession.session_status === 'photo_selection') {
                    console.log('Initializing selection state');
                    showSelectionState();
                } else if (currentSession.session_status === 'processing') {
                    showProcessingState();
                } else if (currentSession.session_status === 'completed') {
                    showCompletedState();
                } else {
                    console.log('Unknown session status, showing waiting state');
                    showWaitingState();
                }
            } else {
                console.log('No current session, showing waiting state');
                showWaitingState();
            }
        }

        function showWaitingState() {
            hideAllStates();
            document.getElementById('waiting-state').classList.remove('hidden');
            document.getElementById('status-indicator').innerHTML = '<div class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-2"></div>Menunggu Sesi...';
            hideEmergencyStop();
            
            // Update session info
            updateSessionInfo();
        }

        function showCaptureState() {
            hideAllStates();
            document.getElementById('capture-state').classList.remove('hidden');
            document.getElementById('status-indicator').innerHTML = '<div class="w-2 h-2 bg-red-400 rounded-full animate-pulse mr-2"></div>Mengambil Foto';
            showEmergencyStop();
            initializeCamera();
        }

        function showSelectionState() {
            debugLog('Showing selection state');
            hideAllStates();
            
            const selectionState = document.getElementById('selection-state');
            if (!selectionState) {
                debugLog('ERROR: Selection state element not found', null);
                alert('Error: Selection interface not found. Please refresh the page.');
                return;
            }
            
            selectionState.classList.remove('hidden');
            document.getElementById('status-indicator').innerHTML = '<div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse mr-2"></div>Memilih Foto';
            showEmergencyStop();
            
            // Reset selection state
            selectedPhotos = [];
            updateSelectionUI();
            
            debugLog('Loading photos for selection state');
            
            // Load photos with error handling
            loadPhotos().catch(error => {
                debugLog('ERROR: Failed to load photos in selection state', error);
                const grid = document.getElementById('photo-grid');
                if (grid) {
                    displayErrorMessage(grid, error);
                }
            });
        }
        
        function updateSelectionUI() {
            const selectedCountEl = document.getElementById('selected-count');
            const maxSelectionEl = document.getElementById('max-selection');
            const confirmBtn = document.getElementById('confirm-selection-btn');
            const requiredPhotosEl = document.getElementById('required-photos');
            
            if (selectedCountEl) selectedCountEl.textContent = selectedPhotos.length;
            
            // Ensure frame_slots is treated as integer
            const requiredCount = currentSession ? parseInt(currentSession.frame_slots) || 4 : 4;
            
            if (maxSelectionEl) maxSelectionEl.textContent = requiredCount;
            if (requiredPhotosEl) requiredPhotosEl.textContent = requiredCount;
            
            debugLog('updateSelectionUI called', {
                selectedCount: selectedPhotos.length,
                requiredCount: requiredCount,
                currentSession: currentSession,
                frameSlots: currentSession?.frame_slots,
                frameSlotsParsed: parseInt(currentSession?.frame_slots)
            });
            
            if (confirmBtn) {
                const shouldEnable = selectedPhotos.length === requiredCount && selectedPhotos.length > 0;
                
                debugLog('Button enable check', {
                    selectedLength: selectedPhotos.length,
                    requiredCount: requiredCount,
                    shouldEnable: shouldEnable,
                    buttonCurrentlyDisabled: confirmBtn.disabled
                });
                
                if (shouldEnable) {
                    confirmBtn.disabled = false;
                    confirmBtn.classList.remove('disabled:opacity-50', 'disabled:cursor-not-allowed');
                    confirmBtn.classList.add('hover:from-green-700', 'hover:to-emerald-600');
                    debugLog('✅ Confirm button ENABLED');
                } else {
                    confirmBtn.disabled = true;
                    confirmBtn.classList.add('disabled:opacity-50', 'disabled:cursor-not-allowed');
                    confirmBtn.classList.remove('hover:from-green-700', 'hover:to-emerald-600');
                    debugLog('❌ Confirm button DISABLED');
                }
            } else {
                debugLog('❌ Confirm button element not found');
            }
        }

        function showProcessingState() {
            hideAllStates();
            document.getElementById('processing-state').classList.remove('hidden');
            document.getElementById('status-indicator').innerHTML = '<div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse mr-2"></div>Memproses';
            showEmergencyStop();
            simulateProcessing();
        }

        function showCompletedState() {
            hideAllStates();
            document.getElementById('completed-state').classList.remove('hidden');
            document.getElementById('status-indicator').innerHTML = '<div class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-2"></div>Selesai';
            hideEmergencyStop();
        }

        function hideAllStates() {
            document.getElementById('waiting-state').classList.add('hidden');
            document.getElementById('capture-state').classList.add('hidden');
            document.getElementById('selection-state').classList.add('hidden');
            document.getElementById('processing-state').classList.add('hidden');
            document.getElementById('completed-state').classList.add('hidden');
        }

        async function startSession() {
            debugLog('startSession called');
            try {
                const response = await axios.post(`/photobox/${photoboxCode}/start`);
                debugLog('Start session response', response.data);
                
                if (response.data.success) {
                    currentSession = response.data.session;
                    debugLog('Session started successfully', currentSession);
                    showCaptureState();
                } else {
                    throw new Error(response.data.error || 'Unknown error');
                }
            } catch (error) {
                debugLog('ERROR starting session', error);
                alert('Gagal memulai sesi: ' + (error.response?.data?.error || error.message));
            }
        }

        async function initializeCamera() {
            try {
                // Stop existing stream if any
                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                }

                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { width: 1280, height: 720, facingMode: 'user' } 
                });
                document.getElementById('camera-preview').srcObject = stream;
                cameraStream = stream;
                
                // Start auto-capture sequence
                startAutoCapture();
            } catch (error) {
                debugLog('Camera access error', error);
                alert('Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.');
            }
        }

        async function startAutoCapture() {
            const totalPhotos = settings.total_photos || 10;
            const intervalSeconds = settings.photo_interval_seconds || 5;
            const countdownSeconds = settings.countdown_seconds || 3;
            
            for (let i = 1; i <= totalPhotos; i++) {
                // Update photo counter
                document.getElementById('photo-count').textContent = i - 1;
                document.getElementById('current-photo').textContent = i;
                
                // Show countdown for this photo
                await showCountdown(countdownSeconds);
                
                // Capture photo
                await capturePhoto(i);
                
                // Wait interval between photos (except last photo)
                if (i < totalPhotos) {
                    await waitInterval(intervalSeconds);
                }
            }
            
            // Auto move to selection state
            setTimeout(() => {
                showSelectionState();
            }, 1000);
        }

        async function waitInterval(seconds) {
            const overlay = document.getElementById('interval-overlay');
            const timerEl = document.getElementById('interval-timer');
            
            overlay.classList.remove('hidden');
            
            for (let i = seconds; i > 0; i--) {
                timerEl.textContent = `Foto selanjutnya dalam ${i} detik...`;
                await new Promise(resolve => setTimeout(resolve, 1000));
            }
            
            overlay.classList.add('hidden');
        }

        async function capturePhoto(sequenceNumber) {
            if (!cameraStream) return;

            // Capture photo
            const video = document.getElementById('camera-preview');
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);
            
            // Show flash effect
            showFlash();
            
            // Convert to base64
            const photoData = canvas.toDataURL('image/jpeg', 0.8);

            try {
                const response = await axios.post(`/photobox/${photoboxCode}/capture`, {
                    photo_data: photoData,
                    sequence_number: sequenceNumber
                });

                if (response.data.success) {
                    capturedPhotos.push(response.data.photo);
                    document.getElementById('photo-count').textContent = response.data.captured_count;
                }
            } catch (error) {
                alert('Gagal mengambil foto: ' + (error.response?.data?.error || error.message));
                throw error; // Stop auto-capture on error
            }
        }

        async function showCountdown(seconds) {
            const overlay = document.getElementById('countdown-overlay');
            const numberEl = document.getElementById('countdown-number');
            
            overlay.classList.remove('hidden');
            
            for (let i = seconds; i > 0; i--) {
                numberEl.textContent = i;
                await new Promise(resolve => setTimeout(resolve, 1000));
            }
            
            overlay.classList.add('hidden');
        }

        function showFlash() {
            const flash = document.getElementById('flash-overlay');
            flash.style.opacity = '1';
            setTimeout(() => {
                flash.style.opacity = '0';
            }, 100);
        }

        async function loadPhotos() {
            const grid = document.getElementById('photo-grid');
            debugLog('Starting loadPhotos function', { photoboxCode });
            
            if (!grid) {
                debugLog('ERROR: Photo grid element not found');
                alert('Error: Photo grid element not found. Please refresh the page.');
                return;
            }
            
            grid.innerHTML = '<div class="col-span-full text-center text-white py-8"><i class="fas fa-spinner fa-spin text-4xl mb-4"></i><br>Memuat foto...</div>';
            
            try {
                debugLog('Making API request to get photos');
                
                // Add timeout to the request
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout
                
                const response = await axios.get(`/photobox/${photoboxCode}/photos`, {
                    signal: controller.signal,
                    timeout: 30000
                });
                
                clearTimeout(timeoutId);
                debugLog('Photos API response received', response.data);
                
                if (response.data.success && response.data.photos && response.data.photos.length > 0) {
                    const photos = response.data.photos;
                    debugLog('Processing photos', { count: photos.length });
                    
                    grid.innerHTML = '';

                    photos.forEach((photo, index) => {
                        debugLog(`Processing photo ${index + 1}`, photo);
                        
                        const photoDiv = document.createElement('div');
                        photoDiv.className = 'photo-item bg-gray-200 relative cursor-pointer hover:scale-105 transition-transform duration-200 border-2 border-transparent';
                        photoDiv.setAttribute('data-photo-id', photo.id);
                        
                        // Multiple fallback image URLs
                        const photoUrl = photo.url 
                                        || photo.public_url 
                                        || photo.file_path 
                                        || `/storage/photos/${photo.filename}` 
                                        || `/storage/sessions/${photo.session_id}/${photo.filename}` 
                                        || '/images/placeholder-photo.svg';
                        
                        debugLog(`Photo URL for #${photo.sequence_number}`, photoUrl);
                        
                        photoDiv.innerHTML = `
                            <img src="${photoUrl}" 
                                 alt="Photo ${photo.sequence_number}" 
                                 class="w-full h-full object-cover rounded-lg"
                                 onerror="handleImageError(this, ${photo.id}, '${photo.sequence_number}')"
                                 onload="handleImageLoad(this)">
                            <div class="absolute top-2 left-2 bg-black/70 text-white text-sm px-2 py-1 rounded">
                                #${photo.sequence_number}
                            </div>
                            <div class="absolute top-2 right-2 selection-indicator hidden">
                                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                            </div>
                            <div class="absolute inset-0 bg-black/20 opacity-0 hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                                <div class="text-white text-sm font-medium">Klik untuk pilih</div>
                            </div>
                        `;
                        
                        photoDiv.addEventListener('click', () => togglePhotoSelection(photo.id, photoDiv));
                        grid.appendChild(photoDiv);
                    });
                    
                    // Update capturedPhotos array for selection logic
                    capturedPhotos = photos;
                    debugLog('Photos loaded successfully', { totalPhotos: photos.length });
                    
                    // Ensure grid is visible and properly styled
                    grid.style.display = 'grid';
                    grid.classList.remove('hidden');
                    
                    // Update UI elements
                    updateSelectionUI();
                } else {
                    debugLog('No photos found or invalid response', response.data);
                    displayNoPhotosMessage(grid);
                }
            } catch (error) {
                debugLog('ERROR loading photos', { 
                    message: error.message, 
                    code: error.code, 
                    response: error.response?.data 
                });
                displayErrorMessage(grid, error);
            }
        }

        function displayNoPhotosMessage(grid) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <div class="bg-yellow-500/20 text-yellow-200 rounded-xl p-6 border border-yellow-400/30 max-w-md mx-auto">
                        <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2">Tidak Ada Foto</h3>
                        <p class="mb-4">Tidak ada foto yang tersedia untuk dipilih</p>
                        <button onclick="showCaptureState()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-camera mr-2"></i>Ambil Foto Lagi
                        </button>
                    </div>
                </div>
            `;
        }

        function displayErrorMessage(grid, error) {
            const errorMessage = error.code === 'ECONNABORTED' ? 'Request timeout' : 
                                 (error.response?.data?.error || error.message || 'Unknown error');
            
            grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <div class="bg-red-500/20 text-red-200 rounded-xl p-6 border border-red-400/30 max-w-md mx-auto">
                        <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2">Error Memuat Foto</h3>
                        <p class="mb-4 text-sm">Terjadi kesalahan: ${errorMessage}</p>
                        <div class="space-x-2">
                            <button onclick="loadPhotos()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-sync-alt mr-2"></i>Coba Lagi
                            </button>
                            <button onclick="showCaptureState()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-camera mr-2"></i>Ambil Foto Baru
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Helper function for image error handling
        function handleImageError(img, photoId, sequenceNumber) {
            console.warn('Image load error for photo:', photoId, 'sequence:', sequenceNumber);
            
            // Try different fallback URLs
            const fallbackUrls = [
                '/images/placeholder-photo.svg',
                '/images/placeholder-photo.jpg',
                '/images/no-image.png',
                'data:image/svg+xml;base64,' + btoa(`
                    <svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300">
                        <rect width="400" height="300" fill="#f3f4f6"/>
                        <text x="50%" y="50%" text-anchor="middle" fill="#6b7280" font-family="Arial" font-size="16">
                            Photo #${sequenceNumber}
                        </text>
                        <text x="50%" y="65%" text-anchor="middle" fill="#9ca3af" font-family="Arial" font-size="12">
                            Image not available
                        </text>
                    </svg>
                `)
            ];
            
            const currentSrc = img.src;
            let nextFallback = null;
            
            for (let i = 0; i < fallbackUrls.length; i++) {
                if (!currentSrc.includes(fallbackUrls[i])) {
                    nextFallback = fallbackUrls[i];
                    break;
                }
            }
            
            if (nextFallback) {
                img.src = nextFallback;
            } else {
                // Last resort: create a placeholder div
                const parentDiv = img.parentElement;
                parentDiv.innerHTML = `
                    <div class="w-full h-full bg-gray-300 flex items-center justify-center text-gray-600">
                        <div class="text-center">
                            <i class="fas fa-image text-3xl mb-2"></i>
                            <div class="text-sm">Photo #${sequenceNumber}</div>
                            <div class="text-xs">Not available</div>
                        </div>
                    </div>
                    <div class="absolute top-2 left-2 bg-black/70 text-white text-sm px-2 py-1 rounded">
                        #${sequenceNumber}
                    </div>
                    <div class="absolute top-2 right-2 selection-indicator hidden">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                `;
            }
        }

        function handleImageLoad(img) {
            console.log('Image loaded successfully:', img.src);
            img.style.opacity = '1';
            img.onerror = null; // Prevent infinite loop
        }

        function togglePhotoSelection(photoId, element) {
            debugLog('Photo selection toggled', { photoId, selectedPhotos });
            
            // Get max selection from current session with proper parsing
            const maxSelection = currentSession ? parseInt(currentSession.frame_slots) || 4 : 4;
            
            debugLog('Max selection determined', { 
                maxSelection, 
                frameSlots: currentSession?.frame_slots,
                currentSession: currentSession
            });
            
            if (selectedPhotos.includes(photoId)) {
                // Deselect
                selectedPhotos = selectedPhotos.filter(id => id !== photoId);
                element.classList.remove('selected');
                element.style.borderColor = 'transparent';
                
                const indicator = element.querySelector('.selection-indicator');
                if (indicator) indicator.classList.add('hidden');
                
                debugLog('Photo deselected', { photoId, remainingSelected: selectedPhotos });
            } else if (selectedPhotos.length < maxSelection) {
                // Select
                selectedPhotos.push(photoId);
                element.classList.add('selected');
                element.style.borderColor = '#10b981';
                element.style.borderWidth = '3px';
                
                const indicator = element.querySelector('.selection-indicator');
                if (indicator) indicator.classList.remove('hidden');
                
                debugLog('Photo selected', { photoId, totalSelected: selectedPhotos });
                
                // Haptic feedback (if available)
                if (navigator.vibrate) {
                    navigator.vibrate(50);
                }
            } else {
                // Show message if trying to select more than allowed
                debugLog('Selection limit reached', { maxSelection, currentSelection: selectedPhotos.length });
                alert(`Maksimal ${maxSelection} foto yang dapat dipilih`);
                return; // Don't update UI if selection failed
            }
            
            updateSelectionUI();
        }

        function resetSelection() {
            selectedPhotos = [];
            document.querySelectorAll('.photo-item').forEach(item => {
                item.classList.remove('selected');
                item.style.borderColor = 'transparent';
                item.style.borderWidth = '2px';
                
                const indicator = item.querySelector('.selection-indicator');
                if (indicator) indicator.classList.add('hidden');
            });
            updateSelectionUI();
        }

        // Debug function to check button status
        function debugButtonStatus() {
            const confirmBtn = document.getElementById('confirm-selection-btn');
            const selectedCountEl = document.getElementById('selected-count');
            const maxSelectionEl = document.getElementById('max-selection');
            
            console.log('=== BUTTON DEBUG STATUS ===');
            console.log('Button element found:', !!confirmBtn);
            console.log('Button disabled:', confirmBtn?.disabled);
            console.log('Button classes:', confirmBtn?.className);
            console.log('Selected photos:', selectedPhotos);
            console.log('Selected count:', selectedPhotos.length);
            console.log('Current session:', currentSession);
            console.log('Frame slots (raw):', currentSession?.frame_slots);
            console.log('Frame slots (parsed):', parseInt(currentSession?.frame_slots));
            console.log('Selected count element text:', selectedCountEl?.textContent);
            console.log('Max selection element text:', maxSelectionEl?.textContent);
            console.log('Should enable button:', selectedPhotos.length === parseInt(currentSession?.frame_slots || 4) && selectedPhotos.length > 0);
            console.log('========================');
        }

        async function confirmSelection() {
            if (selectedPhotos.length === 0) return;

            try {
                showProcessingState();
                
                const response = await axios.post(`/photobox/${photoboxCode}/select`, {
                    selected_photos: selectedPhotos
                });

                if (response.data.success) {
                    setTimeout(() => {
                        showCompletedState();
                    }, 3000);
                }
            } catch (error) {
                alert('Gagal memproses foto: ' + (error.response?.data?.error || error.message));
                showSelectionState();
            }
        }

        function simulateProcessing() {
            const progressBar = document.getElementById('processing-progress');
            const percentage = document.getElementById('processing-percentage');
            const title = document.getElementById('processing-title');
            const subtitle = document.getElementById('processing-subtitle');
            const status = document.getElementById('processing-status');
            
            let progress = 0;
            let step = 0;
            
            const steps = [
                { title: "✨ Menciptakan Keajaiban ✨", subtitle: "AI sedang menyusun foto terbaik Anda...", status: "Memulai proses..." },
                { title: "🎨 Mendesain Layout ✨", subtitle: "Menyusun komposisi yang sempurna...", status: "Menganalisis foto terbaik..." },
                { title: "🌟 Menyempurnakan Detail ✨", subtitle: "Menambahkan sentuhan akhir yang memukau...", status: "Mengoptimalkan kualitas gambar..." },
                { title: "🚀 Hampir Selesai! ✨", subtitle: "Sedang mengirim ke email Anda...", status: "Mempersiapkan pengiriman..." },
                { title: "💎 Final Touch! ✨", subtitle: "Memberikan sentuhan terakhir...", status: "Menyelesaikan masterpiece..." }
            ];
            
            const interval = setInterval(() => {
                progress += Math.random() * 15 + 5; // Progress 5-20% each time
                
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                    
                    // Show completion and move to completed state
                    setTimeout(() => {
                        checkFrameCompletion();
                    }, 1000);
                }
                
                // Update step based on progress
                const newStep = Math.floor((progress / 100) * steps.length);
                if (newStep !== step && newStep < steps.length) {
                    step = newStep;
                    title.innerHTML = steps[step].title;
                    subtitle.innerHTML = steps[step].subtitle;
                    status.innerHTML = steps[step].status;
                }
                
                progressBar.style.width = progress + '%';
                percentage.textContent = Math.floor(progress);
            }, 800);
        }
        
        let autoReturnTimer = null;
        let countdownTimer = null;
        
        async function checkFrameCompletion() {
            try {
                // Check if frame is completed on server
                const response = await axios.get(`/photobox/${photoboxCode}/status`);
                
                if (response.data.success && response.data.session) {
                    const session = response.data.session;
                    
                    if (session.session_status === 'completed') {
                        // Load frame preview and show completed state
                        await loadFramePreview(session.id);
                        showCompletedState();
                        startAutoReturnCountdown();
                    } else {
                        // Still processing, check again in 2 seconds
                        setTimeout(checkFrameCompletion, 2000);
                    }
                } else {
                    // Fallback: show completed state anyway after timeout
                    setTimeout(() => {
                        showCompletedState();
                        startAutoReturnCountdown();
                    }, 3000);
                }
            } catch (error) {
                debugLog('Error checking frame completion', error);
                // Fallback to completed state
                setTimeout(() => {
                    showCompletedState();
                    startAutoReturnCountdown();
                }, 3000);
            }
        }

        async function loadFramePreview(sessionId) {
            const previewContainer = document.getElementById('frame-preview');
            if (!previewContainer) return;
            
            // Show initial loading state
            previewContainer.innerHTML = `
                <div class="text-white/70 text-sm p-4 flex flex-col items-center">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <div>Memuat preview...</div>
                </div>
            `;
            
            let attempts = 0;
            const maxAttempts = 3;
            
            const tryLoadPreview = async () => {
                attempts++;
                
                try {
                    // Add timeout to prevent hanging
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
                    
                    const response = await axios.get(`/admin/sessions/${sessionId}/frame-preview`, {
                        signal: controller.signal
                    });
                    
                    clearTimeout(timeoutId);
                    
                    if (response.data.success && response.data.frame_url) {
                        previewContainer.innerHTML = `
                            <img src="${response.data.frame_url}" 
                                 alt="Frame Preview" 
                                 class="w-full h-auto rounded-lg shadow-lg"
                                 onload="this.style.opacity='1'"
                                 style="opacity: 0; transition: opacity 0.3s ease;"
                                 onerror="showFrameError()">
                        `;
                        debugLog('Frame preview loaded successfully');
                    } else {
                        throw new Error('Frame not ready yet');
                    }
                } catch (error) {
                    debugLog(`Frame preview attempt ${attempts} failed`, error);
                    
                    if (attempts < maxAttempts && !error.name === 'AbortError') {
                        // Retry after delay
                        previewContainer.innerHTML = `
                            <div class="text-white/70 text-sm p-4 flex flex-col items-center">
                                <i class="fas fa-sync fa-spin text-2xl mb-2"></i>
                                <div>Sedang memproses frame...</div>
                                <div class="text-xs mt-1 opacity-70">Percobaan ${attempts}/${maxAttempts}</div>
                            </div>
                        `;
                        setTimeout(tryLoadPreview, 3000); // Retry after 3 seconds
                    } else {
                        // Final fallback
                        showFrameSuccess();
                    }
                }
            };
            
            tryLoadPreview();
        }
        
        function showFrameError() {
            const previewContainer = document.getElementById('frame-preview');
            if (previewContainer) {
                previewContainer.innerHTML = `
                    <div class="text-white/70 text-sm p-4 flex flex-col items-center">
                        <i class="fas fa-exclamation-circle text-2xl mb-2 text-yellow-400"></i>
                        <div>Preview tidak tersedia</div>
                        <div class="text-xs mt-1 opacity-70">Cek email untuk hasil lengkap</div>
                    </div>
                `;
            }
        }

        function showFrameSuccess() {
            const previewContainer = document.getElementById('frame-preview');
            if (previewContainer) {
                previewContainer.innerHTML = `
                    <div class="text-white/70 text-sm p-4 flex flex-col items-center">
                        <i class="fas fa-heart text-2xl mb-2 text-pink-400"></i>
                        <div>Frame Anda sudah siap!</div>
                        <div class="text-xs mt-1 opacity-70">Cek email untuk melihat hasil</div>
                    </div>
                `;
            }
        }
        
        function startAutoReturnCountdown() {
            let countdown = 10;
            const countdownElement = document.getElementById('countdown-timer');
            const buttonElement = document.querySelector('button[onclick="resetToWaiting()"]');
            
            if (countdownElement) countdownElement.textContent = countdown;
            
            countdownTimer = setInterval(() => {
                countdown--;
                if (countdownElement) countdownElement.textContent = countdown;
                
                if (buttonElement) {
                    buttonElement.innerHTML = `<i class="fas fa-home mr-2"></i>Sesi Baru (${countdown} detik)`;
                }
                
                if (countdown <= 0) {
                    clearInterval(countdownTimer);
                    resetToWaiting();
                }
            }, 1000);
            
            // Auto return after 10 seconds
            autoReturnTimer = setTimeout(() => {
                resetToWaiting();
            }, 10000);
        }
        
        function cancelAutoReturn() {
            if (autoReturnTimer) {
                clearTimeout(autoReturnTimer);
                autoReturnTimer = null;
            }
            if (countdownTimer) {
                clearInterval(countdownTimer);
                countdownTimer = null;
            }
            
            // Update UI
            const countdownElement = document.getElementById('auto-return-countdown');
            const buttonElement = document.querySelector('button[onclick="resetToWaiting()"]');
            
            if (countdownElement) {
                countdownElement.innerHTML = '<span class="text-green-400"><i class="fas fa-check mr-2"></i>Auto-return dibatalkan</span>';
            }
            
            if (buttonElement) {
                buttonElement.innerHTML = '<i class="fas fa-home mr-2"></i>Kembali ke Menu Utama';
            }
            
            // Hide cancel button
            const cancelButton = document.querySelector('button[onclick="cancelAutoReturn()"]');
            if (cancelButton) {
                cancelButton.style.display = 'none';
            }
        }
        
        function resetToWaiting() {
            // Clean up
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
            
            capturedPhotos = [];
            selectedPhotos = [];
            currentSession = null;
            
            showWaitingState();
            
            // Reload page to check for new sessions
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }

        async function checkSessionStatus() {
            try {
                const response = await axios.get(`/photobox/${photoboxCode}/status`);
                
                if (response.data.success) {
                    const serverSession = response.data.session;
                    
                    // If there is a new session or the status has changed
                    if (serverSession && (!currentSession || serverSession.id !== currentSession.id || serverSession.session_status !== currentSession.session_status)) {
                        debugLog('Session status changed', { 
                            oldSession: currentSession, 
                            newSession: serverSession 
                        });
                        
                        currentSession = serverSession;
                        
                        // Update the view based on status
                        if (serverSession.session_status === 'approved') {
                            showWaitingState();
                            updateSessionInfo();
                        } else if (serverSession.session_status === 'in_progress') {
                            showCaptureState();
                        } else if (serverSession.session_status === 'photo_selection') {
                            showSelectionState();
                        } else if (serverSession.session_status === 'processing') {
                            showProcessingState();
                        } else if (serverSession.session_status === 'completed') {
                            showCompletedState();
                        }
                    }
                    
                    // If there is no active session but there was one before
                    if (!serverSession && currentSession) {
                        debugLog('Session ended, resetting to waiting');
                        currentSession = null;
                        showWaitingState();
                    }
                }
            } catch (error) {
                debugLog('Error checking session status', error);
                // No need for an alert, this is a background check
            }
        }

        function updateSessionInfo() {
            const container = document.getElementById('session-info-container');
            if (!container) return;
            
            if (!currentSession) {
                // No session available
                container.innerHTML = `
                    <div class="bg-white/5 backdrop-blur-md rounded-2xl p-6 border border-white/10 shadow-xl">
                        <div class="text-white/60 flex items-center justify-center">
                            <i class="fas fa-hourglass-half mr-2"></i>
                            Menunggu admin membuat sesi untuk Anda
                        </div>
                    </div>
                `;
                return;
            }
            
            // Session available
            const isApproved = currentSession.session_status === 'approved';
            
            container.innerHTML = `
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-xl">
                    <h3 class="text-white font-semibold text-lg mb-2 flex items-center">
                        <i class="fas fa-user-circle mr-2 text-green-400"></i>
                        Sesi Tersedia
                    </h3>
                    <div class="space-y-2 mb-4">
                        <p class="text-white/80 flex items-center">
                            <i class="fas fa-user mr-2 text-green-400 w-4"></i>
                            ${currentSession.customer_name}
                        </p>
                        <p class="text-white/80 flex items-center">
                            <i class="fas fa-images mr-2 text-green-400 w-4"></i>
                            ${currentSession.frame_slots} slot frame
                        </p>
                    </div>
                    
                    ${isApproved ? `
                        <button onclick="startSession()" 
                                class="w-full touch-btn bg-gradient-to-r from-green-600 to-emerald-500 text-white rounded-xl hover:from-green-700 hover:to-emerald-600 transition-all duration-200 shadow-lg border border-green-400">
                            <i class="fas fa-play mr-2"></i>
                            Mulai
                        </button>
                    ` : `
                        <div class="bg-yellow-500/20 text-yellow-200 rounded-xl p-3 border border-yellow-400/30">
                            <i class="fas fa-clock mr-2"></i>
                            ${currentSession.session_status === 'pending' ? 'Menunggu persetujuan admin' : 'Menunggu konfirmasi'}
                        </div>
                    `}
                </div>
            `;
            
            debugLog('Session info updated', { 
                session: currentSession, 
                isApproved: isApproved 
            });
        }

        // Emergency stop functions
        function showEmergencyStop() {
            const emergencyBtn = document.getElementById('emergency-stop-btn');
            if (emergencyBtn) {
                emergencyBtn.classList.remove('hidden');
            }
        }

        function hideEmergencyStop() {
            const emergencyBtn = document.getElementById('emergency-stop-btn');
            if (emergencyBtn) {
                emergencyBtn.classList.add('hidden');
            }
        }

        async function forceStopSession() {
            const confirmMessage = 'PERINGATAN: Ini akan menghentikan sesi secara paksa!\n\n' +
                                 'Semua foto yang telah diambil akan hilang.\n' +
                                 'Pelanggan harus memulai sesi baru.\n\n' +
                                 'Apakah Anda yakin ingin melanjutkan?';
            
            if (!confirm(confirmMessage)) {
                return;
            }

            try {
                // Show loading state
                const emergencyBtn = document.getElementById('emergency-stop-btn');
                const originalHTML = emergencyBtn.innerHTML;
                emergencyBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-white text-lg"></i>';
                emergencyBtn.disabled = true;

                // Stop all camera streams immediately
                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                    cameraStream = null;
                }

                // Clear any ongoing timers/intervals
                clearAllTimers();

                // Send force stop request to server
                const response = await axios.post(`/photobox/${photoboxCode}/force-stop`, {
                    reason: 'Emergency stop by admin'
                });

                if (response.data.success) {
                    // Reset all data
                    capturedPhotos = [];
                    selectedPhotos = [];
                    currentSession = null;
                    
                    // Show success message
                    alert('Sesi telah dihentikan secara paksa. Photobox direset ke status awal.');
                    
                    // Return to waiting state
                    showWaitingState();
                    
                    // Reload page after short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    throw new Error(response.data.error || 'Gagal menghentikan sesi');
                }
            } catch (error) {
                console.error('Force stop error:', error);
                alert('Error menghentikan sesi: ' + (error.response?.data?.error || error.message) + 
                      '\n\nSesi akan tetap dihentikan secara lokal.');
                
                // Force local reset even if server call fails
                forceLocalReset();
            } finally {
                // Restore emergency button
                const emergencyBtn = document.getElementById('emergency-stop-btn');
                if (emergencyBtn) {
                    emergencyBtn.innerHTML = '<i class="fas fa-stop text-white text-lg"></i>';
                    emergencyBtn.disabled = false;
                }
            }
        }

        function forceLocalReset() {
            // Force reset all local state
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
            clearAllTimers();
            resetToWaiting();
        }
        
        function clearAllTimers() {
            // Clear any timers created
            if(autoReturnTimer) clearTimeout(autoReturnTimer);
            if(countdownTimer) clearInterval(countdownTimer);
            // Add any other timers here if needed
        }

        // Camera settings panel functions
        function toggleCameraSettings() {
            const panel = document.getElementById('camera-settings-panel');
            panel.classList.toggle('hidden');
        }

        async function loadCameraDevices() {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoDevices = devices.filter(device => device.kind === 'videoinput');
                const select = document.getElementById('camera-device-select');
                
                select.innerHTML = '<option value="">Pilih Kamera...</option>'; // Reset
                
                videoDevices.forEach(device => {
                    const option = document.createElement('option');
                    option.value = device.deviceId;
                    option.textContent = device.label || `Kamera ${select.length}`;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading camera devices:', error);
                const select = document.getElementById('camera-device-select');
                if(select) {
                   select.innerHTML = '<option value="">Error loading cameras</option>';
                }
            }
        }
        
        function refreshCameraDevices() {
             loadCameraDevices();
        }

        function switchCamera() {
            const select = document.getElementById('camera-device-select');
            const deviceId = select.value;

            if (deviceId) {
                // Stop current stream
                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                }

                // Start new stream with selected camera
                navigator.mediaDevices.getUserMedia({
                    video: { deviceId: { exact: deviceId }, width: 1280, height: 720 }
                }).then(stream => {
                    document.getElementById('camera-preview').srcObject = stream;
                    cameraStream = stream;
                }).catch(error => {
                    console.error('Error switching camera:', error);
                    alert('Gagal beralih kamera. Pastikan kamera terhubung dan dapat diakses.');
                });
            }
        }

        function testCamera() {
            const select = document.getElementById('camera-device-select');
            const deviceId = select.value;

            if (deviceId) {
                 // The camera is already previewing, a simple success message is enough
                 alert('Tes kamera berhasil! Kamera siap digunakan.');
            } else {
                alert('Pilih kamera terlebih dahulu.');
            }
        }

        // Make critical functions globally available
        window.startSession = startSession;
        window.showCaptureState = showCaptureState;
        window.showSelectionState = showSelectionState;
        window.showWaitingState = showWaitingState;
        window.loadPhotos = loadPhotos;
        window.togglePhotoSelection = togglePhotoSelection;
        window.confirmSelection = confirmSelection;
        window.resetSelection = resetSelection;
        window.forceStopSession = forceStopSession;
        window.toggleCameraSettings = toggleCameraSettings;
        window.switchCamera = switchCamera;
        window.refreshCameraDevices = refreshCameraDevices;
        window.testCamera = testCamera;
        window.debugButtonStatus = debugButtonStatus;
        window.checkForSession = checkForSession;
        window.showFrameError = showFrameError;
        window.showFrameSuccess = showFrameSuccess;
        window.resetToWaiting = resetToWaiting;
        window.cancelAutoReturn = cancelAutoReturn;

        // Manual session check function for user interaction
        async function checkForSession(event) {
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i>Memeriksa...';
            
            try {
                debugLog('Manual session check initiated by user');
                await checkSessionStatus();
                
                // Update session info immediately
                updateSessionInfo();
                
                // Show success feedback
                button.innerHTML = '<i class="fas fa-check mr-3"></i>Diperiksa!';
                
                setTimeout(() => {
                    button.disabled = false;
                    button.innerHTML = checkForSessionoriginalText;
                }, 2000);
                
            } catch (error) {
                debugLog('Error in manual session check', error);
                
                // Show error feedback
                button.innerHTML = '<i class="fas fa-exclamation-triangle mr-3"></i>Coba Lagi';
                
                setTimeout(() => {
                    button.disabled = false;
                    button.innerHTML = originalText;
                }, 3000);
            }
        }
    </script>
</body>
</html>