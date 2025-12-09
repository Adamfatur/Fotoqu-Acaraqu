{{-- Core JavaScript functions and initialization --}}
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
</script>
