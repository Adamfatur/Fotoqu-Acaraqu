{{-- Force QR Refresh Component --}}
<script>
// This script ensures the QR code always shows the current session
// by overriding how gallery URLs and QR codes are generated
document.addEventListener('DOMContentLoaded', function() {
    console.log('💉 QR CODE FIX: Loading reliable session QR code generator');
    
    // STEP 1: Define function to get current session data from the most reliable source
    window.getReliableSessionCode = function() {
        // Check DOM container first - most reliable and resistant to page refreshes
        const dataContainer = document.getElementById('session-data-container');
        if (dataContainer && dataContainer.dataset.sessionCode) {
            console.log('✅ Using session code from DOM container:', dataContainer.dataset.sessionCode);
            return dataContainer.dataset.sessionCode;
        }
        
        // Next check currentSession global variable
        if (typeof currentSession === 'object' && currentSession && currentSession.session_code) {
            console.log('⚠️ Using session code from currentSession:', currentSession.session_code);
            return currentSession.session_code;
        }
        
        // Next check completedSession
        if (typeof window.completedSession === 'object' && window.completedSession && window.completedSession.session_code) {
            console.log('⚠️ Using session code from window.completedSession:', window.completedSession.session_code);
            return window.completedSession.session_code;
        }
        
        console.error('❌ No reliable session code found!');
        return null;
    };
    
    // STEP 2: Override gallery URL generation to always use current session
    window._originalGetGalleryUrl = window.getGalleryUrl || function(){};
    window.getGalleryUrl = function() {
        const sessionCode = window.getReliableSessionCode();
        if (!sessionCode) {
            console.error('❌ QR CODE FIX: Cannot get gallery URL - no session code available');
            return null;
        }
        
        // Add cache-busting to prevent stale URLs
        const timestamp = new Date().getTime();
        const url = `${window.location.origin}/photobox/gallery/${sessionCode}?_nocache=${timestamp}`;
        console.log('🔗 QR CODE FIX: Generated gallery URL:', url);
        return url;
    };
    
    // STEP 3: Override QR code generation to use our reliable session data
    window._originalGenerateQRCode = window.generateQRCode || function(){};
    window.generateQRCode = function() {
        console.log('🔄 QR CODE FIX: Overridden generateQRCode called');
        
        const galleryUrl = window.getGalleryUrl();
        if (!galleryUrl) {
            console.error('❌ QR CODE FIX: Cannot generate QR code - no gallery URL');
            return;
        }
        
        const qrContainer = document.getElementById('qr-code-display');
        if (!qrContainer) {
            console.error('❌ QR CODE FIX: QR container not found');
            return;
        }
        
        // Use direct QR server API - most reliable approach
        const timestamp = new Date().getTime();
        const encodedUrl = encodeURIComponent(galleryUrl);
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodedUrl}&_t=${timestamp}`;
        
        // Create and show QR code with error handling
        qrContainer.innerHTML = `
            <div class="text-center">
                <div class="bg-white rounded-xl p-4 shadow-lg mb-4">
                    <img src="${qrUrl}" 
                         alt="QR Code for Gallery" 
                         class="mx-auto max-w-full h-auto"
                         onload="console.log('✅ QR code loaded successfully');"
                         onerror="this.onerror=null; this.style.display='none'; this.parentNode.innerHTML = '<div class=\\'text-red-500 p-4 text-center\\'>QR code tidak dapat dimuat</div>';">
                </div>
                <div class="text-white/70">
                    <p class="font-medium">Scan untuk melihat gallery</p>
                    <p class="text-xs mt-1">Session: ${window.getReliableSessionCode()}</p>
                    <p class="text-xs mt-1">${new Date().toLocaleTimeString()}</p>
                </div>
            </div>
        `;
        
        console.log('✅ QR CODE FIX: QR code generated successfully');
        
        // Also update DOM container with current session if needed
        const sessionCode = window.getReliableSessionCode();
        if (sessionCode) {
            const container = document.getElementById('session-data-container');
            if (!container) {
                const newContainer = document.createElement('div');
                newContainer.id = 'session-data-container';
                newContainer.style.display = 'none';
                newContainer.dataset.sessionCode = sessionCode;
                newContainer.dataset.timestamp = Date.now();
                document.body.appendChild(newContainer);
                console.log('✅ QR CODE FIX: Created session-data-container');
            } else if (container.dataset.sessionCode !== sessionCode) {
                container.dataset.sessionCode = sessionCode;
                container.dataset.timestamp = Date.now();
                console.log('✅ QR CODE FIX: Updated session-data-container');
            }
        }
    };
    
    // STEP 4: Make sure showCompletedState always updates the QR (but only if session is actually completed)
    const originalShowCompletedState = window.showCompletedState || function(){};
    window.showCompletedState = function() {
        console.log('🔄 QR CODE FIX: Overriding showCompletedState to ensure QR update');
        
        // Check if session is actually completed before proceeding
        if (typeof currentSession !== 'undefined' && currentSession) {
            console.log('🔍 QR CODE FIX: Current session status:', currentSession.session_status);
            
            if (currentSession.session_status !== 'completed') {
                console.log('❌ QR CODE FIX: Session not completed, skipping completed state display');
                return; // Don't call original function if session is not completed
            }
        }
        
        // Call original function only if session is completed
        originalShowCompletedState.apply(this, arguments);
        
        // Add a small delay to ensure everything is loaded
        setTimeout(function() {
            // Only force QR update if we're actually in completed state
            const completedElement = document.getElementById('completed-state');
            if (completedElement && !completedElement.classList.contains('hidden')) {
                window.generateQRCode();
                console.log('✅ QR CODE FIX: Force-updated QR code in completed state');
            }
        }, 500);
    };
    
    // STEP 5: Set up a periodic refresh to ensure QR stays current
    setInterval(function() {
        const qrContainer = document.getElementById('qr-code-display');
        if (qrContainer && !qrContainer.classList.contains('hidden') && document.visibilityState === 'visible') {
            console.log('🔄 QR CODE FIX: Periodic QR refresh');
            window.generateQRCode();
        }
    }, 30000); // Refresh every 30 seconds
    
    console.log('✅ QR CODE FIX: Reliable QR generator initialized');
});
</script>
