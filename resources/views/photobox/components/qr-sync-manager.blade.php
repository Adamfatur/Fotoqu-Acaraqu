{{-- QR Sync Manager --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 QR Sync Manager Initialized');

    // 1. Create a dedicated DOM element to store session data reliably.
    let sessionDataContainer = document.getElementById('session-data-container');
    if (!sessionDataContainer) {
        sessionDataContainer = document.createElement('div');
        sessionDataContainer.id = 'session-data-container';
        sessionDataContainer.style.display = 'none';
        document.body.appendChild(sessionDataContainer);
        console.log('✅ Created #session-data-container for session data.');
    }

    // 2. Function to update the DOM data store.
    window.updateDOMSessionData = function(session) {
        if (!session || !session.session_code) {
            console.error('❌ Invalid session data provided to updateDOMSessionData.');
            return;
        }
        const container = document.getElementById('session-data-container');
        if (container) {
            container.dataset.sessionId = session.id;
            container.dataset.sessionCode = session.session_code;
            container.dataset.frameUrl = session.frame ? session.frame.frame_url : '';
            console.log(`✅ DOM data updated for session: ${session.session_code}`);
        }
    };

    // 3. Function to get the latest session data from the DOM.
    window.getLatestSessionData = function() {
        const container = document.getElementById('session-data-container');
        if (container && container.dataset.sessionCode) {
            return {
                id: container.dataset.sessionId,
                session_code: container.dataset.sessionCode,
                frame_url: container.dataset.frameUrl,
            };
        }
        console.warn('⚠️ Could not find session data in DOM. Falling back to global `currentSession`.');
        // Fallback to the global variable if the DOM container is not populated for some reason.
        return window.currentSession;
    };

    // 4. Override the QR code generation to use the reliable data source.
    window.generateCompletedQRCode = function() {
        const latestSession = window.getLatestSessionData();
        
        if (!latestSession || !latestSession.session_code) {
            console.error('❌ Cannot generate QR code: No session data available.');
            return;
        }

        const sessionCode = latestSession.session_code;
        const qrContainer = document.getElementById('completed-qr-code');
        
        if (qrContainer) {
            const galleryUrl = `{{ url('/photobox/gallery') }}/${sessionCode}`;
            const qrApiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(galleryUrl)}`;
            
            qrContainer.innerHTML = `
                <img src="${qrApiUrl}" alt="QR Code for Gallery" class="mx-auto rounded-lg">
                <p class="text-sm text-gray-600 mt-2">Scan to view gallery</p>
                <p class="text-xs text-gray-500 mt-1">Session: ${sessionCode}</p>
            `;
            console.log(`✅ Generated QR code for session: ${sessionCode}`);
        } else {
            console.error('❌ Element with ID "completed-qr-code" not found.');
        }
    };
});
</script>
