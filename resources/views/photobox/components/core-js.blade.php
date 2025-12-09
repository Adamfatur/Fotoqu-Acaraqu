{{-- Core JavaScript functions and initialization --}}
<script>
    // Debug logging function - now enabled in production for debugging
    function debugLog(message, data = null) {
        // Always log - enabled for production debugging
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
        } catch (error) {
            console.warn('Failed to save debug log to localStorage:', error);
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
    
    // Make debug functions globally available only in development
    @if(config('app.debug'))
    window.debugLog = debugLog;
    window.getDebugLogs = getDebugLogs;
    window.clearDebugLogs = clearDebugLogs;
    @endif
    
    // Debug functions - only available in development
    @if(config('app.debug'))
    function debugSessionStatus() {
        console.log('=== SESSION STATUS DEBUG ===');
        fetch(`/photobox/${photoboxCode}/debug-status`)
            .then(response => response.json())
            .then(data => {
                console.log('Session debug data:', data);
                alert('Debug data logged to console. Check console for details.');
            })
            .catch(error => {
                console.error('Debug request failed:', error);
                alert('Debug request failed: ' + error.message);
            });
    }
    
    // Make debug function globally available
    window.debugSessionStatus = debugSessionStatus;
    @else
    // Empty functions for production
    function debugSessionStatus() { /* disabled in production */ }
    window.debugSessionStatus = debugSessionStatus;
    @endif
    
    // Global variables
    let currentSession = @json($activeSession);
    let capturedPhotos = [];
    let selectedPhotos = [];
    let cameraStream = null;
    let settings = @json($settings);
    let photoboxCode = '{{ $photobox->code }}';
    // Read token from URL
    let urlToken = null;
    try {
        const u = new URL(window.location.href);
        urlToken = u.searchParams.get('token');
    } catch (e) { urlToken = null; }
    // Persist photobox token for reliable status checks
    try {
        if (urlToken) {
            localStorage.setItem('fotoku_photobox_token', urlToken);
        } else if (!urlToken) {
            const persisted = localStorage.getItem('fotoku_photobox_token');
            if (persisted) {
                urlToken = persisted;
            }
        }
    } catch (e) { /* ignore storage errors */ }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        initializeApp();
        
        // Check if there's a hash in URL for direct state testing
        const hash = window.location.hash.substring(1);
        if (hash && hash.includes('-state')) {
            console.log('Hash detected, showing state:', hash);
            setTimeout(() => {
                hideAllStates();
                showState(hash);
                
                // Initialize specific states
                if (hash === 'frame-design-state') {
                    if (typeof window.safeInitializeFrameDesign === 'function') {
                        window.safeInitializeFrameDesign();
                    } else if (typeof initializeFrameDesign === 'function') {
                        initializeFrameDesign();
                    } else {
                        console.warn('🔄 Frame design functions not available in hash navigation, retrying...');
                        setTimeout(() => {
                            if (typeof window.safeInitializeFrameDesign === 'function') {
                                window.safeInitializeFrameDesign();
                            } else if (typeof initializeFrameDesign === 'function') {
                                initializeFrameDesign();
                            } else {
                                console.error('❌ Frame design functions still not available after timeout in hash navigation');
                            }
                        }, 300);
                    }
                } else if (hash === 'photo-filter-state') {
                    // Mock selected photos for testing
                    selectedPhotos = [
                        { id: 1, url: '/images/placeholder-photo.jpg', filename: 'placeholder-photo.jpg' },
                        { id: 2, url: '/images/placeholder-photo.jpg', filename: 'placeholder-photo.jpg' },
                        { id: 3, url: '/images/placeholder-photo.jpg', filename: 'placeholder-photo.jpg' },
                        { id: 4, url: '/images/placeholder-photo.jpg', filename: 'placeholder-photo.jpg' }
                    ];
                    initializePhotoFilter();
                }
            }, 1000);
        }
        
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
        
        // Attach token to axios if available
        if (urlToken) {
            axios.defaults.headers.common['X-Photobox-Token'] = urlToken;
            try { console.log('🔐 Photobox token attached to axios'); } catch (e) {}
        } else {
            try { console.warn('⚠️ No photobox token available for axios'); } catch (e) {}
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
    
    // EARLY DEFINITION: Placeholder for startProcessing function to prevent "not defined" errors
    // This will be overwritten by the actual implementation in processing-js.blade.php
    window.startProcessing = function() {
        console.warn('startProcessing called before actual implementation loaded!');
        console.warn('This is a placeholder. The real function should be defined in processing-js.blade.php');
    };
    
    console.log('=== CORE JS LOADED ===');
    console.log('Early startProcessing placeholder type:', typeof window.startProcessing);
</script>
