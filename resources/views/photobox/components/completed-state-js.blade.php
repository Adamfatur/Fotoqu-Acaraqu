{{-- Completed State JavaScript - QR Code, Gallery, and Print Functionality --}}
<script>
    // Direct QR code display function - standard black QR code
    // Uses QR Server directly with standard formatting
    function setQRCodeSrc(text, imgId = 'static-qr-code') {
        try {
            // Get the QR code image element
            const qrImg = document.getElementById(imgId);
            if (!qrImg) {
                console.error('QR code image element not found');
                return false;
            }

            // Set the source directly - standard black QR with white background
            const encodedText = encodeURIComponent(text);
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodedText}&format=png&t=${Date.now()}`;

            // Add load event to hide loading when ready and start countdown
            qrImg.onload = function () {
                qrImg.style.display = 'block';
                const loadingEl = document.getElementById('qr-loading');
                if (loadingEl) loadingEl.style.display = 'none';
                // Start 3-minute auto return only after QR is visible to user
                try {
                    if (typeof window.startAutoReturnCountdown === 'function') {
                        window.startAutoReturnCountdown();
                    }
                } catch (e) { /* ignore */ }
            };

            // Add error handler
            qrImg.onerror = function () {
                console.error('Failed to load QR code');
                this.src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';
            };

            // Set the source to load the QR code
            qrImg.src = qrUrl;
            console.log('QR code src set:', qrUrl);

            return true;
        } catch (e) {
            console.error('Error setting QR code src:', e);
            return false;
        }
    }

    // Test if we can access the QR code
    console.log('🔍 Testing inline QR code generator...');
    try {
        const testQR = createQRCodeImg('test');
        console.log('✅ Inline QR code generator working');
    } catch (err) {
        console.error('❌ Error testing QR code generator:', err);
    }
</script>
<script>
    // Completed state variables
    let currentSessionCode = null;
    let galleryUrl = null;

    /**
     * Initialize completed state with QR code and gallery functionality
     */
    function initializeCompletedState() {
        console.log('🚀 Initializing completed state...');
        console.log('Available session data:', typeof currentSession, currentSession);

        // Get current session data with better validation
        if (typeof currentSession !== 'undefined' && currentSession && currentSession.id) {
            currentSessionCode = currentSession.session_code;
            console.log('✅ Session code found:', currentSessionCode);
            console.log('✅ Session ID found:', currentSession.id);

            generateGalleryUrl();
            generateQRCode();
            setupGalleryActions();
            setupPrintButton(); // Setup printing based on package
        } else {
            console.error('❌ No current session available for completed state');
            console.log('currentSession type:', typeof currentSession);
            console.log('currentSession value:', currentSession);

            // Try to get session from DOM or other sources
            tryAlternativeSessionSources();
        }
    }

    /**
     * Setup Print Button Logic
     */
    function setupPrintButton() {
        // If no session or no package data, keep hidden
        if (!currentSession || !currentSession.package) {
            console.log('⚠️ No package data available for print check');
            // Try to extract from simple object if possible?
            // If strictly digital, default to hidden.
            return;
        }

        const pkg = currentSession.package;
        const btn = document.getElementById('print-frame-btn');
        const btnText = document.getElementById('print-btn-text');

        if (!btn) return;

        console.log('🖨️ Checking print settings:', pkg);

        // If package is digital only (print_type == 'none'), hide button
        if (!pkg.print_type || pkg.print_type === 'none') {
            console.log('🖨️ Digital only package, print button hidden');
            btn.style.display = 'none';
            return;
        }

        // Show button
        btn.style.display = 'block';

        // Customize text
        if (pkg.print_type === 'strip') {
            // Print 1x
            btnText.textContent = `Cetak Frame (1x)`;
        } else if (pkg.print_type === 'custom') {
            const count = pkg.print_count || 1;
            btnText.textContent = `Cetak Frame (${count} Lembar)`;
        }
    }

    /**
     * Trigger Print Action
     */
    function printFrame() {
        if (!currentSession) return;

        const btn = document.getElementById('print-frame-btn');
        const btnText = document.getElementById('print-btn-text');

        // Disable button to prevent double click
        btn.disabled = true;
        const originalText = btnText.textContent;
        btnText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';

        // Send request
        axios.post(`/photobox/${photoboxCode}/print-frame`, {
            session_id: currentSession.id
        })
            .then(response => {
                if (response.data.success) {
                    btnText.innerHTML = '<i class="fas fa-check mr-2"></i>Berhasil!';
                    btn.classList.remove('from-blue-500', 'to-indigo-600');
                    btn.classList.add('from-green-500', 'to-green-600');

                    // Show feedback toast or alert
                    // alert(`Sedang mencetak ${response.data.copies} lembar...`);

                    // Disable permanently for "Print 1" logic if strictly enforced
                    // "jika dia paket print 1 maka dia hanya bisa print 1x"
                    // If success, we assume 1x is done.

                    // Keep disabled state but maybe update text
                    setTimeout(() => {
                        btnText.innerHTML = '<i class="fas fa-print mr-2"></i>Sudah Dicetak';
                    }, 2000);
                } else {
                    throw new Error(response.data.error || 'Gagal mencetak');
                }
            })
            .catch(error => {
                console.error('Print failed:', error);
                const msg = error.response?.data?.error || error.message;
                alert(msg);

                // Re-enable if it was a failure (unless quota exceeded)
                if (!msg.includes('habis') && !msg.includes('already')) {
                    btn.disabled = false;
                    btnText.textContent = originalText;
                } else {
                    btnText.textContent = 'Kuota Habis';
                }
            });
    }

    /**
     * Try to get session data from alternative sources
     */
    function tryAlternativeSessionSources() {
        console.log('🔍 Trying alternative session sources...');

        // Try window.completedSession (set in checkFrameCompletion)
        if (typeof window.completedSession !== 'undefined' && window.completedSession) {
            console.log('✅ Found completedSession backup:', window.completedSession);

            // Use the backup session
            currentSession = window.completedSession;
            if (currentSession.session_code) {
                currentSessionCode = currentSession.session_code;
            }

            generateGalleryUrl();
            generateQRCode();
            setupGalleryActions();
            setupPrintButton(); // Setup printing
            // Kick background verify but do not auto-close immediately
            safeBackgroundVerify();
            return;
        }

        // Try to get from processing state
        if (typeof photoSessionId !== 'undefined' && photoSessionId) {
            console.log('✅ Found photoSessionId:', photoSessionId);

            // Get fresh session data using AJAX
            console.log('🔄 Fetching fresh session data using photoSessionId...');

            // Try to get session code from URL or any other source
            const urlParams = new URLSearchParams(window.location.search);
            const sessionCode = urlParams.get('session') || null;

            // Create minimal session object
            currentSession = {
                id: photoSessionId,
                session_code: sessionCode // Will fallback to download URL if null
            };

            // Try to get fresh session data
            axios.get(`/photobox/${photoboxCode}/status`)
                .then(response => {
                    if (response.data.success && response.data.session) {
                        console.log('✅ Got fresh session data:', response.data.session);
                        currentSession = response.data.session;
                        currentSessionCode = currentSession.session_code;
                    }
                    generateGalleryUrl();
                    generateQRCode();
                    setupGalleryActions();
                    setupPrintButton(); // Setup printing
                    safeBackgroundVerify();
                })
                .catch(error => {
                    console.error('❌ Error getting fresh session data:', error);
                    // Use what we have
                    generateGalleryUrl();
                    generateQRCode();
                    setupGalleryActions();
                    setupPrintButton(); // Setup printing
                    safeBackgroundVerify();
                });

            return;
        }

        // Last resort: Try to search for any completed session in localstorage or DOM
        try {
            console.log('🔍 Searching for session in DOM and localStorage...');

            // First check localStorage for the most recent session
            const storedSession = localStorage.getItem('fotoku_latest_session');
            if (storedSession) {
                try {
                    const parsedSession = JSON.parse(storedSession);
                    console.log('✅ Found session in localStorage:', parsedSession);

                    // Check if session has required data
                    if (parsedSession.session_code) {
                        console.log('✅ Using localStorage session data');

                        currentSession = {
                            id: parsedSession.id || '0',
                            session_code: parsedSession.session_code
                        };
                        currentSessionCode = parsedSession.session_code;

                        generateGalleryUrl();
                        generateQRCode();
                        setupGalleryActions();
                        setupPrintButton(); // Setup printing
                        safeBackgroundVerify();
                        return;
                    }
                } catch (parseError) {
                    console.error('❌ Error parsing localStorage session:', parseError);
                }
            }

            // Then check for any data-session-id or data-session-code in DOM
            const sessionElements = document.querySelectorAll('[data-session-id], [data-session-code]');
            if (sessionElements.length > 0) {
                console.log('✅ Found session elements in DOM:', sessionElements);
                const sessionId = sessionElements[0].getAttribute('data-session-id');
                const sessionCode = sessionElements[0].getAttribute('data-session-code');

                if (sessionId || sessionCode) {
                    currentSession = {
                        id: sessionId || '0',
                        session_code: sessionCode
                    };
                    currentSessionCode = sessionCode;

                    generateGalleryUrl();
                    generateQRCode();
                    setupGalleryActions();
                    setupPrintButton(); // Setup printing
                    return;
                }
            }
        } catch (e) {
            console.error('❌ Error searching for session:', e);
        }

        // Show error in QR container with manual input option
        console.error('❌ All session data sources failed!');
        const qrContainer = document.getElementById('qr-code-display');
        if (qrContainer) {
            qrContainer.innerHTML = `
            <div class="text-center">
                <div class="text-red-500 text-sm mb-2">Session data not found</div>
                <div class="text-xs text-gray-600 p-2 bg-gray-100 rounded">
                    <div class="mb-2">Check console for errors</div>
                    <a href="/photobox/${photoboxCode}" class="text-blue-600">Return to home</a>
                </div>
            </div>
        `;
        }

        const urlInput = document.getElementById('gallery-url');
        if (urlInput) {
            urlInput.value = 'Session data not available';
        }
    }

    // Background verification with gentle retries and short grace period
    async function safeBackgroundVerify(maxRetries = 4) {
        try {
            for (let i = 0; i < maxRetries; i++) {
                try {
                    const resp = await axios.get(`/photobox/${photoboxCode}/status`);
                    if (resp?.data?.session && resp.data.session.session_status === 'completed') {
                        // Fresh completed session; update globals but do not interrupt UI
                        currentSession = resp.data.session;
                        setupPrintButton(); // Update print button state if session refreshed
                        return true;
                    }
                } catch (e) {
                    // swallow and retry
                }
                await new Promise(r => setTimeout(r, 500 + i * 300));
            }
            return false;
        } catch (e) { return false; }
    }

    /**
     * Generate gallery URL for current session
     */
    function generateGalleryUrl() {
        console.log('🔍 generateGalleryUrl called with:', {
            currentSession: currentSession,
            photoboxCode: typeof photoboxCode !== 'undefined' ? photoboxCode : 'undefined',
            photoSessionId: typeof photoSessionId !== 'undefined' ? photoSessionId : 'undefined'
        });

        if (!currentSession) {
            console.error('❌ currentSession is null or undefined');
            // Use hardcoded URL as fallback
            galleryUrl = `${window.location.origin}/photobox/gallery/MANUAL-CHECK`;
            updateGalleryUrlDisplay(galleryUrl, true);
            return;
        }

        if (!currentSession.id) {
            console.error('❌ currentSession has no ID');
            // Use hardcoded URL as fallback
            galleryUrl = `${window.location.origin}/photobox/gallery/MISSING-ID`;
            updateGalleryUrlDisplay(galleryUrl, true);
            return;
        }

        // Generate URL based on available data
        if (currentSession.session_code) {
            // Use user gallery route with session code
            galleryUrl = `${window.location.origin}/photobox/gallery/${currentSession.session_code}`;
            console.log('✅ Using gallery URL with session code:', currentSession.session_code);
        } else {
            // Fallback to download URL for direct access
            const sessionId = currentSession.id;
            const expiresAt = Math.floor(Date.now() / 1000) + (7 * 24 * 60 * 60); // 7 days from now
            galleryUrl = `${window.location.origin}/photobox/session/${sessionId}/download-all?expires=${expiresAt}`;
            console.log('⚠️ Using download URL fallback for session ID:', sessionId);
        }

        // Update URL input field
        updateGalleryUrlDisplay(galleryUrl);

        console.log('✅ Gallery URL generated:', galleryUrl);
        return galleryUrl;
    }

    // Helper function to update gallery URL display
    function updateGalleryUrlDisplay(url, isError = false) {
        const urlInput = document.getElementById('gallery-url');
        if (urlInput) {
            urlInput.value = url;
            if (isError) {
                urlInput.classList.add('text-red-300');
            } else {
                urlInput.classList.remove('text-red-300');
            }
            console.log('✅ Gallery URL input updated with:', url);
        } else {
            console.error('❌ Gallery URL input element not found');
        }
    }

    /**
     * Generate QR code for gallery access
     */
    function generateQRCode() {
        console.log('🔗 generateQRCode called, galleryUrl:', galleryUrl);

        const qrContainer = document.getElementById('qr-code-display');
        if (!qrContainer) {
            console.error('❌ QR container not found');
            return;
        }

        // If no gallery URL yet, try to generate it again
        if (!galleryUrl) {
            console.log('⚠️ No gallery URL available, attempting to regenerate...');
            generateGalleryUrl();

            // If still no URL, retry after a short delay
            if (!galleryUrl) {
                console.log('⏳ Still no gallery URL, retrying in 1s...');
                setTimeout(generateQRCode, 1000);
                return;
            }
        }

        try {
            console.log('📱 Generating QR code for URL:', galleryUrl);

            // Use direct QR server API - most reliable approach
            const encodedUrl = encodeURIComponent(galleryUrl);
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodedUrl}`;

            console.log('🔄 Using QR Server API:', qrUrl);

            // Create and show QR code with error handling
            qrContainer.innerHTML = `
            <img src="${qrUrl}" 
                 alt="QR Code for Gallery" 
                 style="width:200px; height:200px;" 
                 onload="console.log('QR code loaded successfully'); document.getElementById('qr-loading')?.remove();"
                 onerror="this.onerror=null; this.src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='; 
                         this.style.padding='80px'; 
                         this.style.background='#f8f8f8'; 
                         this.alt='QR Code unavailable';
                         console.error('QR code failed to load');">
        `;

            console.log('✅ QR code HTML inserted into container');

            // Also update the gallery URL display
            const urlInput = document.getElementById('gallery-url');
            if (urlInput) {
                urlInput.value = galleryUrl;
                console.log('✅ Gallery URL input updated');
            }

            // Add a direct link below QR code as backup
            // Check if link already exists to avoid duplication
            if (!qrContainer.nextElementSibling || !qrContainer.nextElementSibling.classList.contains('qr-backup-link')) {
                qrContainer.insertAdjacentHTML('afterend', `
                <div class="mt-2 text-xs text-white/70 text-center qr-backup-link">
                    <a href="${galleryUrl}" target="_blank" class="text-blue-300 hover:underline">Buka Gallery →</a>
                </div>
            `);
            }

        } catch (error) {
            console.error('❌ Error generating QR code:', error);
            // Show fallback with text link
            qrContainer.innerHTML = `
            <div class="text-center">
                <div class="text-red-500 text-sm mb-2">QR generation failed</div>
                <div class="text-xs text-white p-2 bg-white/20 rounded">
                    <i class="fas fa-link mr-1"></i> Link Gallery:<br>
                    <a href="${galleryUrl}" target="_blank" class="text-blue-300 break-all">${galleryUrl}</a>
                </div>
                <button onclick="retryQR()" class="mt-2 px-3 py-1 bg-blue-500 text-white text-xs rounded">
                    <i class="fas fa-sync mr-1"></i> Coba Lagi
                </button>
            </div>
        `;
        }
    }

    // Helper function to retry QR code generation
    function retryQR() {
        console.log('🔄 Retrying QR code generation...');
        const qrContainer = document.getElementById('qr-code-display');
        if (qrContainer) {
            qrContainer.innerHTML = '<div class="text-white/70 text-sm animate-pulse">Generating QR code...</div>';
            setTimeout(() => generateQRCode(), 500);
        }
    }

    /**
     * Setup gallery action handlers
     */
    function setupGalleryActions() {
        // Copy gallery URL function
        window.copyGalleryUrl = function () {
            const input = document.getElementById('gallery-url');
            if (!input || !galleryUrl) return;

            navigator.clipboard.writeText(galleryUrl).then(function () {
                // Show success feedback
                const button = event.target.closest('button');
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.add('bg-green-500');
                button.classList.remove('bg-blue-500');

                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('bg-green-500');
                    button.classList.add('bg-blue-500');
                }, 2000);

                console.log('Gallery URL copied to clipboard');
            }).catch(function (err) {
                console.error('Failed to copy URL:', err);
                // Fallback for older browsers
                input.select();
                input.setSelectionRange(0, 99999);
                document.execCommand('copy');
            });
        };

        // Open gallery in new tab function - more robust
        window.openGalleryInNewTab = function () {
            // Get URL from input field or any available source
            let url = document.getElementById('gallery-url')?.value;

            // Fallback if no URL is found
            if (!url || url === 'Loading gallery URL...' || url === 'Session data not available') {
                console.log('No gallery URL found in input, checking other sources');

                // Try to construct URL from session code
                let sessionCode = null;

                // Check data container first
                const dataContainer = document.getElementById('session-data-container');
                if (dataContainer && dataContainer.dataset.sessionCode) {
                    sessionCode = dataContainer.dataset.sessionCode;
                }
                // Try localStorage as fallback
                else {
                    try {
                        const storedSession = localStorage.getItem('fotoku_latest_session');
                        if (storedSession) {
                            const parsedSession = JSON.parse(storedSession);
                            if (parsedSession && parsedSession.session_code) {
                                sessionCode = parsedSession.session_code;
                            }
                        }
                    } catch (e) {
                        console.error('Error checking localStorage:', e);
                    }
                }

                // If we found a session code, use it
                if (sessionCode) {
                    url = `${window.location.origin}/photobox/gallery/${sessionCode}`;
                }
                // Last resort - use photobox homepage
                else if (typeof photoboxCode !== 'undefined') {
                    const tokenQuery = (typeof urlToken !== 'undefined' && urlToken) ? `?token=${urlToken}` : '';
                    url = `${window.location.origin}/photobox/${photoboxCode}${tokenQuery}`;
                }
                // Absolute last resort
                else {
                    url = window.location.origin;
                }
            }

            // Open the URL
            console.log('Opening gallery URL:', url);
            window.open(url, '_blank');
        };
    }

    /**
     * Simplified showCompletedState function
     */
    async function showCompletedStateWithGallery() {
        console.log('🎉 showCompletedStateWithGallery called!');

        // FIRST: Check if user has manually reset (stored in sessionStorage for current browser session)
        // This should be checked BEFORE making any server calls
        try {
            const userResetFlag = sessionStorage.getItem('fotoku_user_reset');
            const lastResetTime = sessionStorage.getItem('fotoku_reset_time');

            if (userResetFlag === 'true' && lastResetTime) {
                console.log('❌ User has manually reset at timestamp:', lastResetTime);
                console.log('🔒 Reset protection active - blocking completed state display');

                // Clear all session data
                currentSession = null;
                window.completedSession = null;
                window.sessionData = null;

                try {
                    localStorage.removeItem('fotoku_latest_session');
                    localStorage.removeItem('fotoku_session_data');
                    localStorage.removeItem('fotoku_completed_session');
                } catch (e) { }

                const dataContainer = document.getElementById('session-data-container');
                if (dataContainer) {
                    dataContainer.dataset.sessionId = '';
                    dataContainer.dataset.sessionCode = '';
                    dataContainer.dataset.timestamp = '';
                }

                showWaitingState();
                return;
            } else {
                console.log('✅ No user reset flag found, proceeding with session check');
            }
        } catch (e) {
            console.warn('Could not check reset flag:', e);
        }

        // First check if there's an active session on the server
        try {
            console.log('🔍 Checking for active session on server...');
            const response = await axios.get(`/photobox/${photoboxCode}/status`);

            if (!response.data.success || !response.data.session) {
                console.log('⚠️ No server session yet; keep completed UI and verify in background');
                // Don’t bounce immediately; background verify with retries
                safeBackgroundVerify();
                // Fall through to continue completed display with local data
            }

            // Server has active session, proceed with completed state
            const serverSession = response.data.session;
            console.log('✅ Server has active session:', serverSession);
            console.log('🔍 Session status check:', serverSession.session_status);

            // Only show completed state if session status is actually completed
            if (serverSession && serverSession.session_status !== 'completed') {
                console.log('⚠️ Server says not completed; keep completed UI briefly and retry in background');
                safeBackgroundVerify();
                // Continue; do not immediately close
            }

            // Check if this session is too old (more than 5 minutes since last user interaction)
            if (serverSession) {
                const sessionUpdatedAt = new Date(serverSession.updated_at || serverSession.created_at);
                const now = new Date();
                const timeDiff = now - sessionUpdatedAt;
                const maxSessionAge = 10 * 60 * 1000; // relax to 10 minutes to avoid clock skew issues
                if (timeDiff > maxSessionAge) {
                    console.log('⚠️ Session appears old (' + Math.round(timeDiff / 1000) + 's); keeping UI but will not prolong');
                }
            }

            // Check if user has manually reset (stored in sessionStorage for current browser session)
            try {
                const userResetFlag = sessionStorage.getItem('fotoku_user_reset');
                const lastResetTime = sessionStorage.getItem('fotoku_reset_time');

                if (userResetFlag === 'true' && lastResetTime) {
                    const resetTime = parseInt(lastResetTime);
                    const sessionTime = sessionUpdatedAt.getTime();

                    // If user reset after this session was last updated, don't show it
                    if (resetTime > sessionTime) {
                        // Allow if same session ID (freshly completed) to prevent premature closing
                        if (!serverSession || (serverSession && currentSession && serverSession.id !== currentSession.id)) {
                            console.log('❌ User reset after this (different) session, returning to waiting');
                            currentSession = null;
                            window.completedSession = null;
                            window.sessionData = null;
                            try {
                                localStorage.removeItem('fotoku_latest_session');
                                localStorage.removeItem('fotoku_session_data');
                                localStorage.removeItem('fotoku_completed_session');
                            } catch (e) { }
                            const dataContainer = document.getElementById('session-data-container');
                            if (dataContainer) {
                                dataContainer.dataset.sessionId = '';
                                dataContainer.dataset.sessionCode = '';
                                dataContainer.dataset.timestamp = '';
                            }
                            showWaitingState();
                            return;
                        } else {
                            console.log('✅ Reset flag ignored for same just-completed session');
                        }
                    }
                }
            } catch (e) {
                console.warn('Could not check reset flag:', e);
            }

            console.log('✅ Session status is completed and fresh, proceeding with completed state display...');

        } catch (error) {
            console.error('❌ Error checking server session:', error);
            showWaitingState();
            return;
        }

        // Now proceed with the original completed state logic
        console.log('✅ Proceeding with completed state display...');

        hideAllStates();
        document.getElementById('completed-state').classList.remove('hidden');
        document.getElementById('status-indicator').innerHTML = '<div class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-2"></div>Selesai';
        hideEmergencyStop();

        // Try multiple sources for session code in order of reliability
        let sessionCode = null;
        let sessionId = null;

        // Source 1: Data container in DOM (most reliable)
        const dataContainer = document.getElementById('session-data-container');
        if (dataContainer && dataContainer.dataset.sessionCode) {
            sessionCode = dataContainer.dataset.sessionCode;
            sessionId = dataContainer.dataset.sessionId;
            console.log('✅ Found session code in DOM data container:', sessionCode);
        }
        // Source 2: Current session object
        else if (typeof currentSession !== 'undefined' && currentSession && currentSession.session_code) {
            sessionCode = currentSession.session_code;
            sessionId = currentSession.id;
            console.log('📦 Found session code in currentSession object:', sessionCode);
        }
        // Source 3: LocalStorage backup
        else {
            try {
                const storedSession = localStorage.getItem('fotoku_latest_session');
                if (storedSession) {
                    const parsedSession = JSON.parse(storedSession);
                    if (parsedSession && parsedSession.session_code) {
                        sessionCode = parsedSession.session_code;
                        sessionId = parsedSession.id;
                        console.log('📦 Found session code in localStorage:', sessionCode);
                    }
                }
            } catch (e) {
                console.error('Error accessing localStorage:', e);
            }
        }

        // If we have a session code, generate QR immediately - use the public route
        if (sessionCode) {
            // Always use /photobox/gallery/ for consistent public access
            const directGalleryUrl = `${window.location.origin}/photobox/gallery/${sessionCode}`;
            console.log('🌐 Direct gallery URL:', directGalleryUrl);

            // Set to hidden input field
            const urlInput = document.getElementById('gallery-url');
            if (urlInput) {
                urlInput.value = directGalleryUrl;
            }

            // Set QR code src directly - simplest approach
            setQRCodeSrc(directGalleryUrl);
        }

        // If no session code found yet, try direct API call
        else if (typeof photoboxCode !== 'undefined') {
            console.log('No session data found, using direct API approach...');

            // Show a temporary QR code while we fetch the real data
            const tempQrUrl = `${window.location.origin}/photobox/${photoboxCode}${(typeof urlToken !== 'undefined' && urlToken) ? `?token=${urlToken}` : ''}`;
            setQRCodeSrc(tempQrUrl);

            // Direct API call to get session data (async)
            axios.get(`/photobox/${photoboxCode}/status`)
                .then(response => {
                    if (response.data && response.data.session && response.data.session.session_code) {
                        const session = response.data.session;
                        console.log('Got session data from API:', session);

                        // Generate URL and set QR code
                        const directGalleryUrl = `${window.location.origin}/photobox/gallery/${session.session_code}`;

                        // Set to hidden input
                        const urlInput = document.getElementById('gallery-url');
                        if (urlInput) {
                            urlInput.value = directGalleryUrl;
                        }

                        // Set QR code image
                        setQRCodeSrc(directGalleryUrl);

                        // Save to localStorage for future use
                        try {
                            localStorage.setItem('fotoku_latest_session', JSON.stringify({
                                id: session.id,
                                session_code: session.session_code,
                                timestamp: new Date().getTime()
                            }));
                        } catch (e) {
                            console.warn('Could not save session to localStorage:', e);
                        }
                    } else {
                        console.error('API response missing session data');

                        // Use a fallback URL that works generically
                        const fallbackUrl = `${window.location.origin}/photobox/${photoboxCode}/latest${(typeof urlToken !== 'undefined' && urlToken) ? `?token=${urlToken}` : ''}`;
                        setQRCodeSrc(fallbackUrl);
                    }
                })
                .catch(error => {
                    console.error('Failed to get session from API:', error);

                    // Use a fallback URL as last resort
                    const fallbackUrl = `${window.location.origin}/photobox/${photoboxCode}${(typeof urlToken !== 'undefined' && urlToken) ? `?token=${urlToken}` : ''}`;
                    setQRCodeSrc(fallbackUrl);
                });
        }
        // Absolute fallback - no session data or photoboxCode available
        else {
            console.error('No session data sources available!');

            // Use homepage URL as fallback
            const fallbackUrl = window.location.origin;
            setQRCodeSrc(fallbackUrl);
        }

        setupPrintButton(); // Setup printing (after session data is hopefully loaded)

        // Start auto-return countdown will be triggered after QR onload
    }

    /**
     * Refresh session data from server
     */
    async function refreshSessionData() {
        try {
            const response = await axios.get(`/photobox/${photoboxCode}/status`);
            if (response.data && response.data.session) {
                currentSession = response.data.session;
                console.log('Session data refreshed:', currentSession);
                return currentSession;
            }
        } catch (error) {
            console.error('Error refreshing session data:', error);
            throw error;
        }
    }

    /**
     * Auto-return countdown functionality
     */
    // Use global autoReturnTimer if it exists, otherwise declare it
    if (typeof window.autoReturnTimer === 'undefined') {
        window.autoReturnTimer = null;
    }
    let countdownSeconds = 180; // 3 minutes
    let countdownTimerInterval = null;

    function startAutoReturnCountdown() {
        console.log('⏱️ Starting auto-return countdown (180s)...');

        // Clear any existing timers first
        if (window.autoReturnTimer) clearTimeout(window.autoReturnTimer);
        if (countdownTimerInterval) clearInterval(countdownTimerInterval);

        const countdownElement = document.getElementById('countdown-timer');
        const buttonElement = document.querySelector('button[onclick="resetToWaiting()"]');

        countdownSeconds = 180;

        // Helper to format time
        const formatTime = (seconds) => {
            const minutes = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${minutes}:${secs.toString().padStart(2, '0')}`;
        };

        // Initial display update
        if (countdownElement) countdownElement.textContent = formatTime(countdownSeconds);
        if (buttonElement) {
            buttonElement.innerHTML = `<i class="fas fa-home mr-2"></i>Sesi Baru (${formatTime(countdownSeconds)})`;
        }

        // Interval for UI updates
        countdownTimerInterval = setInterval(() => {
            countdownSeconds--;

            if (countdownElement) countdownElement.textContent = formatTime(countdownSeconds);
            if (buttonElement) {
                buttonElement.innerHTML = `<i class="fas fa-home mr-2"></i>Sesi Baru (${formatTime(countdownSeconds)})`;
            }

            if (countdownSeconds <= 0) {
                clearInterval(countdownTimerInterval);
                resetToWaiting();
            }
        }, 1000);

        // Auto execute timer (backup for interval)
        window.autoReturnTimer = setTimeout(() => {
            resetToWaiting();
        }, 180000);
    }

    function cancelAutoReturn() {
        if (window.autoReturnTimer) clearTimeout(window.autoReturnTimer);
        if (countdownTimerInterval) clearInterval(countdownTimerInterval);
        document.getElementById('auto-return-countdown').style.display = 'none';
        console.log('🛑 Auto-return cancelled by user');
    }
</script>