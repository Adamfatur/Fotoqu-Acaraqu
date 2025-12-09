{{-- State Management JavaScript --}}
<script>
    function showWaitingState() {
        hideAllStates();
        document.getElementById('waiting-state').classList.remove('hidden');
        document.getElementById('status-indicator').innerHTML = '<div class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-2"></div>Menunggu Sesi...';
        hideEmergencyStop();
        
        // Update session info
        updateSessionInfo();

    // Start lightweight background camera preview
    startWaitingCameraPreview();
    }

    function showCaptureState() {
        hideAllStates();
        document.getElementById('capture-state').classList.remove('hidden');
        document.getElementById('status-indicator').innerHTML = '<div class="w-2 h-2 bg-red-400 rounded-full animate-pulse mr-2"></div>Mengambil Foto';
        showEmergencyStop();
        
        // Enable full screen camera mode
        enableCaptureFullscreenMode();
    // Stop waiting preview if running
    stopWaitingCameraPreview();

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
        // If local previews exist from capture flow, render them instantly
        try {
            if (Array.isArray(capturedPhotos) && capturedPhotos.length > 0 && typeof window.renderPhotosGrid === 'function') {
                window.renderPhotosGrid(capturedPhotos);
            }
        } catch (e) { /* no-op */ }

        // Load photos with error handling
        loadPhotos().catch(error => {
            debugLog('ERROR: Failed to load photos in selection state', error);
            const grid = document.getElementById('photo-grid');
            if (grid) {
                displayErrorMessage(grid, error);
            }
        });
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

    // New state functions for frame design and photo filter
    function showFrameDesignState() {
        hideAllStates();
        document.getElementById('frame-design-state').classList.remove('hidden');
        document.getElementById('status-indicator').innerHTML = '<div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse mr-2"></div>Pilih Frame';
        showEmergencyStop();
        
        // Use safe initialization
        if (typeof window.safeInitializeFrameDesign === 'function') {
            window.safeInitializeFrameDesign();
        } else if (typeof initializeFrameDesign === 'function') {
            initializeFrameDesign();
        } else {
            console.warn('🔄 Frame design functions not available in showFrameDesignState, retrying...');
            setTimeout(() => {
                if (typeof window.safeInitializeFrameDesign === 'function') {
                    window.safeInitializeFrameDesign();
                } else if (typeof initializeFrameDesign === 'function') {
                    initializeFrameDesign();
                } else {
                    console.error('❌ Frame design functions still not available after timeout in showFrameDesignState');
                }
            }, 200);
        }
    }

    function showPhotoFilterState() {
        hideAllStates();
        document.getElementById('photo-filter-state').classList.remove('hidden');
        document.getElementById('status-indicator').innerHTML = '<div class="w-2 h-2 bg-pink-400 rounded-full animate-pulse mr-2"></div>Edit Foto';
        showEmergencyStop();
        initializePhotoFilter();
    }

    function hideAllStates() {
        // Cleanup photo filter state if leaving it
        const photoFilterState = document.getElementById('photo-filter-state');
        if (photoFilterState && !photoFilterState.classList.contains('hidden')) {
            if (typeof cleanupPhotoFilter === 'function') {
                cleanupPhotoFilter();
            }
        }
        
        // Disable full screen camera mode when leaving capture state
        const captureState = document.getElementById('capture-state');
        if (captureState && !captureState.classList.contains('hidden')) {
            disableCaptureFullscreenMode();
        }

        // Stop waiting preview when leaving waiting state
        const waitingState = document.getElementById('waiting-state');
        if (waitingState && !waitingState.classList.contains('hidden')) {
            stopWaitingCameraPreview();
        }
        
        document.getElementById('waiting-state').classList.add('hidden');
        document.getElementById('capture-state').classList.add('hidden');
        document.getElementById('selection-state').classList.add('hidden');
        document.getElementById('frame-design-state').classList.add('hidden');
        document.getElementById('photo-filter-state').classList.add('hidden');
        document.getElementById('processing-state').classList.add('hidden');
        document.getElementById('completed-state').classList.add('hidden');
    }

    // === WAITING STATE CAMERA PREVIEW ===
    let waitingPreviewStream = null;
    async function startWaitingCameraPreview() {
        try {
            const video = document.getElementById('waiting-camera-preview');
            if (!video) return;
            if (waitingPreviewStream) return; // already running

            // Use very flexible, low-impact constraints but prefer HD
            const constraints = {
                video: {
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                    frameRate: { ideal: 24, max: 30 }
                },
                audio: false
            };

            // Try to reuse current selected device if any
            const select = document.getElementById('camera-device-select');
            if (select && select.value) {
                constraints.video.deviceId = { ideal: select.value };
            }

            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = stream;
            video.muted = true;
            waitingPreviewStream = stream;
        } catch (e) {
            console.warn('Waiting preview failed to start with preferred constraints, retrying with video:true:', e);
            try {
                const video = document.getElementById('waiting-camera-preview');
                if (!video) return;
                const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                video.srcObject = stream;
                video.muted = true;
                waitingPreviewStream = stream;
            } catch (e2) {
                console.warn('Waiting preview fallback also failed:', e2);
            }
        }
    }

    function stopWaitingCameraPreview() {
        try {
            if (waitingPreviewStream) {
                waitingPreviewStream.getTracks().forEach(t => t.stop());
                waitingPreviewStream = null;
            }
            const video = document.getElementById('waiting-camera-preview');
            if (video) video.srcObject = null;
        } catch (e) {
            console.warn('Waiting preview cleanup error:', e);
        }
    }

    // Enhanced state transition function
    function showState(stateName) {
        console.log('Transitioning to state:', stateName);
        
        // Hide all states first
        hideAllStates();
        
        // Add small delay for smooth transition
        setTimeout(() => {
            const stateElement = document.getElementById(stateName);
            if (stateElement) {
                stateElement.classList.remove('hidden');
                stateElement.classList.add('fade-in');
                
                // Update status indicator based on state
                updateStatusIndicator(stateName);
                
                // Initialize state-specific functions
                initializeStateSpecificFunctions(stateName);
            } else {
                console.error('State element not found:', stateName);
            }
        }, 100);
    }
    
    function updateStatusIndicator(stateName) {
        const statusIndicator = document.getElementById('status-indicator');
        if (!statusIndicator) return;
        
        const statusMap = {
            'waiting-state': '<div class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-2"></div>Menunggu Sesi...',
            'capture-state': '<div class="w-2 h-2 bg-red-400 rounded-full animate-pulse mr-2"></div>Mengambil Foto',
            'selection-state': '<div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse mr-2"></div>Memilih Foto',
            'frame-design-state': '<div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse mr-2"></div>Pilih Frame',
            'photo-filter-state': '<div class="w-2 h-2 bg-pink-400 rounded-full animate-pulse mr-2"></div>Edit Foto',
            'processing-state': '<div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse mr-2"></div>Memproses',
            'completed-state': '<div class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-2"></div>Selesai'
        };
        
        statusIndicator.innerHTML = statusMap[stateName] || statusMap['waiting-state'];
    }
    
    function initializeStateSpecificFunctions(stateName) {
        switch (stateName) {
            case 'selection-state':
                if (typeof loadPhotos === 'function') {
                    loadPhotos().catch(error => {
                        console.error('Failed to load photos in selection state:', error);
                    });
                }
                break;
            case 'frame-design-state':
                if (typeof initializeFrameDesign === 'function') {
                    initializeFrameDesign();
                }
                break;
            case 'photo-filter-state':
                if (typeof initializePhotoFilter === 'function') {
                    initializePhotoFilter();
                }
                break;
            case 'processing-state':
                // Processing state should be initialized externally via startProcessing()
                break;
        }
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

    // === FULL SCREEN CAMERA CAPTURE FUNCTIONS === 
    
    function enableCaptureFullscreenMode() {
        console.log('🚀 Enabling simple full screen camera capture mode');
        
        const app = document.getElementById('app');
        const body = document.body;
        
        if (!app || !body) {
            console.error('❌ ERROR: Missing required elements for fullscreen mode');
            return;
        }
        
        // Add full screen class
        app.classList.add('capture-fullscreen-mode');
        body.classList.add('capture-fullscreen-mode');
        body.style.overflow = 'hidden';
        
        console.log('✅ Simple fullscreen mode enabled');
    }
    
    function disableCaptureFullscreenMode() {
        console.log('🔙 Disabling simple full screen camera capture mode');
        
        const app = document.getElementById('app');
        const body = document.body;
        
        // Remove full screen classes
        if (app) app.classList.remove('capture-fullscreen-mode');
        if (body) {
            body.classList.remove('capture-fullscreen-mode');
            body.style.overflow = '';
        }
        
        console.log('✅ Simple fullscreen mode disabled');
    }
    
    // Make functions globally available for debugging and external access
    window.enableCaptureFullscreenMode = enableCaptureFullscreenMode;
    window.disableCaptureFullscreenMode = disableCaptureFullscreenMode;

    
    // === CAMERA CONTROL FUNCTIONS ===

    // Auto-start waiting preview on initial load if waiting state is visible
    document.addEventListener('DOMContentLoaded', () => {
        const waitingEl = document.getElementById('waiting-state');
        if (waitingEl && !waitingEl.classList.contains('hidden')) {
            startWaitingCameraPreview();
        }
    });
    
    function switchCamera() {
        console.log('🔄 Switching camera device');
        
        try {
            // Always use device-based switching first
            const select = document.getElementById('camera-device-select');
            
            if (select && select.options && select.options.length > 1) {
                console.log('📷 Using camera device switching');
                
                // Get current selected index
                const currentIndex = select.selectedIndex;
                
                // Switch to next camera device (cycle through available devices)
                const nextIndex = (currentIndex + 1) % select.options.length;
                select.selectedIndex = nextIndex;
                
                const selectedOption = select.options[nextIndex];
                const cameraName = selectedOption.text || `Device ${nextIndex + 1}`;
                
                console.log('📷 Switching to device:', {
                    index: nextIndex,
                    deviceId: selectedOption.value,
                    name: cameraName
                });
                
                // Apply the camera change
                if (typeof changeCamera === 'function') {
                    changeCamera();
                    console.log('✅ Camera device switched to:', cameraName);
                    showCameraSwitchNotification(cameraName);
                } else {
                    console.error('❌ changeCamera function not available');
                    // Try to reload devices and switch manually
                    switchCameraManually(selectedOption.value, cameraName);
                }
                
            } else if (select) {
                console.log('📷 Only one camera device available or no devices found');
                
                // Try to reload camera devices first
                if (typeof loadCameraDevices === 'function') {
                    loadCameraDevices().then(() => {
                        // Check again after loading devices
                        if (select.options.length > 1) {
                            // Retry switching
                            setTimeout(() => switchCamera(), 500);
                        } else {
                            showCameraSwitchNotification('Hanya 1 kamera tersedia');
                        }
                    }).catch(error => {
                        console.error('❌ Failed to reload camera devices:', error);
                        showCameraSwitchNotification('Gagal memuat daftar kamera');
                    });
                } else {
                    showCameraSwitchNotification('Hanya 1 kamera tersedia');
                }
                
            } else {
                console.log('📷 Camera select element not found, trying to create it');
                // Try to initialize camera devices first
                if (typeof loadCameraDevices === 'function') {
                    loadCameraDevices().then(() => {
                        console.log('📷 Camera devices loaded, retrying switch');
                        setTimeout(() => switchCamera(), 500);
                    }).catch(error => {
                        console.error('❌ Failed to initialize camera devices:', error);
                        showCameraSwitchNotification('Tidak dapat mengakses daftar kamera');
                    });
                } else {
                    console.error('❌ loadCameraDevices function not available');
                    showCameraSwitchNotification('Fungsi kamera tidak tersedia');
                }
            }
            
        } catch (error) {
            console.error('❌ Error switching camera:', error);
            showCameraSwitchNotification('Gagal mengganti kamera');
        }
    }
    
    function switchCameraManually(deviceId, cameraName) {
        console.log('� Switching camera manually to device:', deviceId);
        
        try {
            // Stop current camera
            if (typeof stopCamera === 'function') {
                stopCamera();
            }
            
            // Start new camera with selected device
            setTimeout(() => {
                navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        deviceId: { exact: deviceId },
                        width: { ideal: 1920, max: 3840 },
                        height: { ideal: 1080, max: 2160 },
                        frameRate: { ideal: 30, max: 60 }
                    } 
                }).then(stream => {
                    const video = document.getElementById('camera-preview');
                    if (video) {
                        video.srcObject = stream;
                        window.cameraStream = stream;
                        console.log('✅ Manual camera switch successful');
                        showCameraSwitchNotification(cameraName);
                    }
                }).catch(error => {
                    console.error('❌ Manual camera switch failed:', error);
                    showCameraSwitchNotification('Gagal beralih ke kamera: ' + cameraName);
                    
                    // Fallback: reinitialize original camera
                    if (typeof initializeCamera === 'function') {
                        initializeCamera();
                    }
                });
            }, 300);
            
        } catch (error) {
            console.error('❌ Error in manual camera switch:', error);
            showCameraSwitchNotification('Error switching camera');
        }
    }
    
    function switchCameraDirectly() {
        console.log('🔄 Switching camera directly');
        
        try {
            // Stop current camera stream
            if (typeof stopCamera === 'function') {
                stopCamera();
            }
            
            // Initialize currentCameraFacing if not set
            if (!window.currentCameraFacing) {
                window.currentCameraFacing = 'user';
            }
            
            // Toggle between front and back camera
            window.currentCameraFacing = window.currentCameraFacing === 'user' ? 'environment' : 'user';
            
            console.log('📷 Switching to camera facing:', window.currentCameraFacing);
            
            // Reinitialize camera with new facing mode
            setTimeout(() => {
                if (typeof initializeCamera === 'function') {
                    initializeCamera();
                } else {
                    console.error('❌ initializeCamera function not available');
                }
            }, 500);
            
            const cameraType = window.currentCameraFacing === 'user' ? 'Kamera Depan' : 'Kamera Belakang';
            
            // Safe notification call
            try {
                showCameraSwitchNotification(cameraType);
            } catch (notificationError) {
                console.error('❌ Notification error:', notificationError);
                console.log(`📷 Switched to: ${cameraType}`);
            }
            
        } catch (error) {
            console.error('❌ Error in switchCameraDirectly:', error);
            throw error; // Re-throw to be caught by parent function
        }
    }
    
    function showCameraSwitchNotification(cameraName) {
        try {
            // Remove existing notification if any
            const existingNotification = document.getElementById('camera-switch-notification');
            if (existingNotification && existingNotification.parentNode) {
                existingNotification.remove();
            }
            
            // Create new notification
            const notification = document.createElement('div');
            notification.id = 'camera-switch-notification';
            notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-blue-500/90 backdrop-blur-sm text-white px-6 py-3 rounded-lg z-50 flex items-center gap-2 transition-all duration-300';
            notification.innerHTML = `
                <i class="fas fa-camera text-lg"></i>
                <span>Beralih ke: ${cameraName}</span>
            `;
            
            // Ensure body exists before appending
            if (document.body) {
                document.body.appendChild(notification);
                
                // Auto remove after 2 seconds
                setTimeout(() => {
                    if (notification && notification.parentNode && notification.style) {
                        notification.style.opacity = '0';
                        notification.style.transform = 'translate(-50%, -20px)';
                        setTimeout(() => {
                            if (notification && notification.parentNode) {
                                notification.remove();
                            }
                        }, 300);
                    }
                }, 2000);
            } else {
                console.warn('⚠️ Document body not available for notification');
            }
            
        } catch (error) {
            console.error('❌ Error showing camera switch notification:', error);
            // Fallback to simple alert
            console.log(`📷 ${cameraName}`);
        }
    }
    
    function toggleFullscreen() {
        console.log('🖥️ Toggling fullscreen');
        
        if (!document.fullscreenElement) {
            // Enter fullscreen
            document.documentElement.requestFullscreen().then(() => {
                console.log('✅ Entered fullscreen mode');
            }).catch(err => {
                console.error('❌ Failed to enter fullscreen:', err);
            });
        } else {
            // Exit fullscreen
            document.exitFullscreen().then(() => {
                console.log('✅ Exited fullscreen mode');
            }).catch(err => {
                console.error('❌ Failed to exit fullscreen:', err);
            });
        }
    }
    
    // Make camera control functions globally available
    window.switchCamera = switchCamera;
    window.switchCameraManually = switchCameraManually;
    window.switchCameraDirectly = switchCameraDirectly;
    window.showCameraSwitchNotification = showCameraSwitchNotification;
    window.toggleFullscreen = toggleFullscreen;
    
    // === KEYBOARD SHORTCUTS ===
    
    document.addEventListener('keydown', function(event) {
        // F12 for fullscreen toggle
        if (event.key === 'F12') {
            event.preventDefault();
            toggleFullscreen();
        }
    });
    
    // === END CAMERA CONTROL FUNCTIONS ===
    
    // === END FULL SCREEN CAMERA CAPTURE FUNCTIONS ===
</script>
