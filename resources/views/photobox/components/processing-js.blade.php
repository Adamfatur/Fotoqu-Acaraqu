{{-- Processing and Completion JavaScript --}}
<script>
    // Global variables for frame data
    let currentFrameData = null;
    let currentSessionId = null;

    function simulateProcessing() {
        console.log('🎬 Starting simulateProcessing...');

        const progressBar = document.getElementById('processing-progress');
        const percentage = document.getElementById('processing-percentage');
        const title = document.getElementById('processing-title');
        const subtitle = document.getElementById('processing-subtitle');
        const status = document.getElementById('processing-status');

        // Debug DOM elements
        console.log('📋 DOM Elements Check:');
        console.log('- progressBar:', progressBar ? '✅ Found' : '❌ Not found');
        console.log('- percentage:', percentage ? '✅ Found' : '❌ Not found');
        console.log('- title:', title ? '✅ Found' : '❌ Not found');
        console.log('- subtitle:', subtitle ? '✅ Found' : '❌ Not found');
        console.log('- status:', status ? '✅ Found' : '❌ Not found');

        if (!progressBar || !percentage) {
            console.error('❌ Critical DOM elements missing for progress simulation!');
            // Fallback to direct completion
            setTimeout(() => {
                console.log('⚠️  Fallback: proceeding to checkFrameCompletion');
                checkFrameCompletion();
            }, 2000);
            return;
        }

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

            console.log(`🔄 Progress update: ${Math.floor(progress)}%`);

            if (progress >= 100) {
                progress = 100;
                console.log('✅ Progress complete (100%), clearing interval...');
                clearInterval(interval);

                // Show completion and move to completed state
                setTimeout(() => {
                    console.log('🎯 Moving to checkFrameCompletion...');
                    checkFrameCompletion();
                }, 1000);
            }

            // Update step based on progress
            const newStep = Math.floor((progress / 100) * steps.length);
            if (newStep !== step && newStep < steps.length) {
                step = newStep;
                console.log(`📝 Step update: ${step} - ${steps[step].title}`);
                if (title) title.innerHTML = steps[step].title;
                if (subtitle) subtitle.innerHTML = steps[step].subtitle;
                if (status) status.innerHTML = steps[step].status;
            }

            progressBar.style.width = progress + '%';
            percentage.textContent = Math.floor(progress);
        }, 800);
    }

    // Use global autoReturnTimer to avoid conflicts
    if (typeof window.autoReturnTimer === 'undefined') {
        window.autoReturnTimer = null;
    }
    let countdownTimer = null;

    async function checkFrameCompletion() {
        try {
            // Check if frame is completed on server
            console.log('🔍 Checking frame completion status...');
            const response = await axios.get(`/photobox/${photoboxCode}/status`);

            if (response.data.success && response.data.session) {
                const session = response.data.session;
                console.log('📊 Session status from server:', session.session_status);

                // Update global currentSession
                if (typeof currentSession !== 'undefined') {
                    console.log('🔄 Updating currentSession with latest data');
                    currentSession = session;
                }

                if (session.session_status === 'completed') {
                    // Load frame preview and show completed state
                    console.log('✅ Session completed! Loading frame preview...');
                    await loadFramePreview(session.id);

                    // Force refresh currentSession global
                    currentSession = session;
                    console.log('🔄 Forced currentSession update:', currentSession.session_code);

                    // Clear any existing localStorage that might have old data
                    try {
                        localStorage.removeItem('fotoku_session');
                        localStorage.removeItem('fotoku_session_code');
                        console.log('🧹 Cleared any old localStorage session data');
                    } catch (e) {
                        console.warn('Could not clear localStorage:', e);
                    }

                    // Store the session before showing completed state
                    console.log('📦 Storing session data before showing completed state:', session);
                    window.completedSession = session; // Backup in case currentSession gets lost

                    // Clear any existing data container
                    const existingContainer = document.getElementById('session-data-container');
                    if (existingContainer) {
                        existingContainer.remove();
                        console.log('🧹 Removed existing session-data-container');
                    }

                    // Add data attributes to DOM elements to ensure session data persists
                    const dataContainer = document.createElement('div');
                    dataContainer.id = 'session-data-container';
                    dataContainer.style.display = 'none';
                    dataContainer.dataset.sessionId = session.id;
                    dataContainer.dataset.sessionCode = session.session_code;
                    dataContainer.dataset.completedTimestamp = new Date().getTime();
                    document.body.appendChild(dataContainer);

                    // Try to update DOM session data using the QR sync manager
                    if (typeof window.updateDOMSessionData === 'function') {
                        window.updateDOMSessionData(session);
                        console.log('✅ Updated DOM session data using QR sync manager');
                    }

                    // Also store in localStorage as an additional backup
                    try {
                        localStorage.setItem('fotoku_latest_session', JSON.stringify({
                            id: session.id,
                            session_code: session.session_code,
                            timestamp: new Date().getTime()
                        }));
                        console.log('💾 Session data stored in localStorage as backup');
                    } catch (e) {
                        console.warn('Could not store session in localStorage:', e);
                    }

                    console.log('📝 Added session data to DOM for persistence');

                    // Force refresh currentSession global
                    currentSession = session;

                    showCompletedState();
                    startAutoReturnCountdown();
                } else {
                    // Still processing, check again in 2 seconds
                    console.log('⏳ Session still processing, checking again in 2 seconds...');
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

                const response = await axios.get(`/photobox/session/${sessionId}/frame-preview`, {
                    signal: controller.signal
                });

                clearTimeout(timeoutId);

                if (response.data.success && response.data.frame_url) {
                    // Store frame data globally for download
                    currentFrameData = response.data.frame;
                    currentSessionId = sessionId;

                    previewContainer.innerHTML = `
                        <img src="${response.data.frame_url}" 
                             alt="Frame Preview" 
                             class="w-full h-auto rounded-lg shadow-lg"
                             onload="this.style.opacity='1'"
                             style="opacity: 0; transition: opacity 0.3s ease;"
                             onerror="showFrameError()">
                    `;

                    // Show download button
                    const downloadBtn = document.getElementById('download-frame-btn');
                    if (downloadBtn) {
                        downloadBtn.classList.remove('hidden');
                    }

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

    // Auto-return functions are now handled in completed-state-js.blade.php
    // to avoid duplicate definitions and ensure consistent behavior.


    function resetToWaiting() {
        console.log('🔄 Resetting to waiting state and clearing all session data...');

        // Mark that user has manually reset (important for preventing old session display)
        try {
            const resetTime = Date.now();
            sessionStorage.setItem('fotoku_user_reset', 'true');
            sessionStorage.setItem('fotoku_reset_time', resetTime.toString());
            console.log('✅ User reset flag set at timestamp:', resetTime);
            console.log('🔒 Reset protection activated - completed sessions will be blocked');
        } catch (e) {
            console.warn('Could not set reset flag:', e);
        }

        // Clean up camera resources
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }

        // Clear all session-related data
        capturedPhotos = [];
        selectedPhotos = [];
        currentSession = null;
        window.completedSession = null;
        window.sessionData = null;

        // Clear localStorage data
        try {
            localStorage.removeItem('fotoku_latest_session');
            localStorage.removeItem('fotoku_session_data');
            localStorage.removeItem('fotoku_completed_session');
            console.log('✅ LocalStorage cleared');
        } catch (e) {
            console.warn('Could not clear localStorage:', e);
        }

        // Clear DOM session data container
        const dataContainer = document.getElementById('session-data-container');
        if (dataContainer) {
            dataContainer.dataset.sessionId = '';
            dataContainer.dataset.sessionCode = '';
            dataContainer.dataset.timestamp = '';
            console.log('✅ DOM container cleared');
        }

        // Clear any QR code data
        const galleryUrlField = document.getElementById('gallery-url');
        if (galleryUrlField) {
            galleryUrlField.value = 'Loading gallery URL...';
        }

        showWaitingState();

        // Reload page to check for new sessions
        setTimeout(() => {
            console.log('🔄 Reloading page after session reset...');
            window.location.reload();
        }, 2000);
    }

    // Enhanced processing with frame and filter data - OVERRIDE THE PLACEHOLDER
    console.log('=== OVERRIDING startProcessing PLACEHOLDER ===');
    console.log('Before override - window.startProcessing type:', typeof window.startProcessing);

    window.startProcessing = async function () {
        console.log('🚀 ===== STARTING ENHANCED PROCESSING =====');
        console.log('✅ REAL startProcessing function is being executed!');
        console.log('Starting enhanced processing...');
        console.log('DEBUG: Current photoboxCode:', photoboxCode);
        console.log('DEBUG: Current selectedPhotos:', selectedPhotos);
        console.log('DEBUG: Current window.sessionData:', window.sessionData);
        console.log('DEBUG: Available global vars check:');
        console.log('- selectedFrameDesign:', typeof selectedFrameDesign !== 'undefined' ? selectedFrameDesign : 'UNDEFINED');
        console.log('- availableFrameTemplates:', typeof availableFrameTemplates !== 'undefined' ? availableFrameTemplates : 'UNDEFINED');

        // IMPORTANT: Force refresh current session to get latest photo data
        console.log('🔄 Force refreshing current session data...');
        try {
            const sessionResponse = await axios.get(`/photobox/${photoboxCode}/debug-status`);
            if (sessionResponse.data && sessionResponse.data.session_data) {
                const latestSession = sessionResponse.data.session_data;
                console.log('📊 Latest session from server:', latestSession);

                // Update selectedPhotos if they seem outdated
                if (latestSession.photos && latestSession.photos.length > 0) {
                    const serverPhotoIds = latestSession.photos.map(p => p.id);
                    const currentPhotoIds = selectedPhotos.map(p => typeof p === 'object' ? p.id : p);

                    console.log('📊 Photo ID comparison:');
                    console.log('- Server photo IDs:', serverPhotoIds);
                    console.log('- Current photo IDs:', currentPhotoIds);

                    // Check if current selectedPhotos are outdated
                    const hasOutdatedPhotos = currentPhotoIds.some(id => !serverPhotoIds.includes(id));
                    if (hasOutdatedPhotos || currentPhotoIds.length === 0) {
                        console.warn('⚠️  Current selectedPhotos seem outdated or empty, refreshing...');

                        // Get selected photos from latest session
                        const validSelectedPhotos = latestSession.photos.filter(p => {
                            // Use existing selection if available, otherwise use first N photos for frame slots
                            return p.is_selected === true;
                        });

                        // If no selected photos, auto-select first 3 photos
                        if (validSelectedPhotos.length === 0) {
                            console.log('📸 No selected photos found, auto-selecting first 3...');
                            const firstThreePhotos = latestSession.photos.slice(0, 3);
                            selectedPhotos = firstThreePhotos;
                        } else {
                            console.log('� Using server-selected photos');
                            selectedPhotos = validSelectedPhotos;
                        }

                        console.log('🔄 Updated selectedPhotos:', selectedPhotos);
                    } else {
                        console.log('✅ Current selectedPhotos are up to date');
                    }
                }
            }
        } catch (sessionError) {
            console.warn('⚠️  Could not refresh session data:', sessionError.message);

            // Fallback: if selectedPhotos seem to have invalid IDs, show error
            if (selectedPhotos && selectedPhotos.length > 0) {
                const currentPhotoIds = selectedPhotos.map(p => typeof p === 'object' ? p.id : p);
                console.warn('🔄 Using existing selectedPhotos (could not verify):', currentPhotoIds);
            } else {
                console.error('❌ No selectedPhotos available and could not refresh from server');
                alert('Error: Tidak dapat memuat data foto. Silakan kembali ke pemilihan foto.');
                hideAllStates();
                showState('selection-state');
                return;
            }
        }

        // Get frame and filter data from session data
        const frameDesignRaw = window.sessionData?.selectedFrame || selectedFrameDesign || 'default';
        const frameDesign = String(frameDesignRaw); // Convert to string as required by server
        const photoFilters = window.sessionData?.photoFilters || {};

        console.log('=== PROCESSING FRAME DESIGN DEBUG ===');
        console.log('Processing with frame design:', frameDesign);
        console.log('Frame design type:', typeof frameDesign);
        console.log('Frame design raw value:', frameDesignRaw, 'type:', typeof frameDesignRaw);
        console.log('Frame design converted:', frameDesign, 'type:', typeof frameDesign);
        console.log('Frame design details:', {
            'sessionData.selectedFrame': window.sessionData?.selectedFrame,
            'selectedFrameDesign (global var)': selectedFrameDesign,
            'final_value': frameDesign,
            'is_default': frameDesign === 'default',
            'is_numeric': !isNaN(frameDesign) && frameDesign !== '',
            'is_empty': frameDesign === '' || frameDesign === null || frameDesign === undefined
        });
        console.log('Available templates for reference:', availableFrameTemplates);

        // Validate selected photos
        if (!selectedPhotos || selectedPhotos.length === 0) {
            console.error('❌ No selected photos found');
            alert('Error: Tidak ada foto yang dipilih. Silakan kembali ke pemilihan foto.');
            hideAllStates();
            showState('selection-state');
            return;
        }

        console.log('📋 Selected photos validation:');
        console.log('- selectedPhotos array:', selectedPhotos);
        console.log('- selectedPhotos length:', selectedPhotos.length);
        console.log('- selectedPhotos types:', selectedPhotos.map(p => typeof p));

        try {
            // Extract IDs from photo objects for backend
            const photoIds = selectedPhotos.map(photo => {
                let id;
                if (typeof photo === 'object') {
                    id = Number.isInteger(photo.id) ? photo.id : photo.serverId;
                } else {
                    id = photo;
                }
                console.log(`📸 Photo mapping: ${JSON.stringify(photo)} → ID: ${id}`);
                return id;
            });

            console.log('📋 Final photo IDs to send:', photoIds);

            // Validate photo IDs
            if (photoIds.some(id => !id || isNaN(id))) {
                console.error('❌ Invalid photo IDs found:', photoIds);
                alert('Error: Ada foto yang tidak valid. Silakan pilih ulang foto.');
                hideAllStates();
                showState('selection-state');
                return;
            }

            // Send selection data with frame and filters to server
            const processingData = {
                selected_photos: photoIds,
                frame_design: frameDesign,
                photo_filters: photoFilters
            };

            console.log('Sending processing data:', processingData);
            console.log('About to send AJAX request to:', `/photobox/${photoboxCode}/select`);

            // Debug session status first
            try {
                const statusResponse = await axios.get(`/photobox/${photoboxCode}/debug-status`);
                console.log('Current session status before processing:', statusResponse.data);
            } catch (debugError) {
                console.warn('Could not fetch debug status:', debugError.message);
            }

            console.log('Sending POST request now...');
            const response = await axios.post(`/photobox/${photoboxCode}/select`, processingData);
            console.log('POST request completed, response:', response.data);

            if (response.data.success) {
                console.log('✅ Processing request successful, starting simulation...');
                console.log('🎬 About to call simulateProcessing()');

                // Small delay to ensure processing state is fully rendered
                setTimeout(() => {
                    console.log('⏰ Timeout reached, calling simulateProcessing now...');
                    simulateProcessing();
                }, 500);
            } else {
                console.error('Processing request failed:', response.data.error);
                throw new Error(response.data.error || 'Processing failed');
            }
        } catch (error) {
            console.error('Processing error:', error);

            // Better error handling
            let errorMessage = 'Gagal memproses foto: ';

            if (error.response) {
                // Server responded with error status
                const errorData = error.response.data;
                errorMessage += errorData.error || errorData.message || 'Server error';

                // Log additional debug info if available
                if (errorData.current_status) {
                    console.error('Current session status:', errorData.current_status);
                    console.error('Expected status:', errorData.expected_status);
                }
            } else if (error.request) {
                // Request was made but no response
                errorMessage += 'Tidak dapat terhubung ke server';
            } else {
                // Something else happened
                errorMessage += error.message;
            }

            alert(errorMessage);

            // Go back to selection if it's a validation error
            if (error.response && error.response.status === 400) {
                hideAllStates();
                showState('selection-state');
            } else {
                // For other errors, go back to frame design (since we skip photo filter)
                // TODO: Change back to 'photo-filter-state' when photo filter is re-enabled
                hideAllStates();
                showState('frame-design-state');
            }
        }
    };

    /**
     * Download frame in best quality
     */
    async function downloadFrame() {
        const downloadBtn = document.getElementById('download-frame-btn');
        if (!downloadBtn) return;

        const originalContent = downloadBtn.innerHTML;
        downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mempersiapkan unduhan...';
        downloadBtn.disabled = true;

        try {
            let downloadUrl;

            if (currentFrameData && currentFrameData.id) {
                downloadUrl = `/photobox/frame/${currentFrameData.id}/download`;
            } else if (currentSessionId) {
                const response = await axios.get(`/photobox/session/${currentSessionId}/frame-preview`);
                if (response.data.success && response.data.frame_url) {
                    downloadUrl = response.data.frame_url;
                }
            }

            if (downloadUrl) {
                const link = document.createElement('a');
                link.href = downloadUrl;
                if (!currentFrameData || !currentFrameData.id) {
                    link.download = `Fotoku_Frame_${Date.now()}.jpg`;
                }
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                downloadBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Berhasil Diunduh!';
                downloadBtn.classList.remove('from-green-600', 'to-emerald-600');
                downloadBtn.classList.add('from-green-500', 'to-green-600');

                setTimeout(() => {
                    downloadBtn.innerHTML = originalContent;
                    downloadBtn.disabled = false;
                    downloadBtn.classList.remove('from-green-500', 'to-green-600');
                    downloadBtn.classList.add('from-green-600', 'to-emerald-600');
                }, 3000);
            } else {
                throw new Error('Frame URL tidak tersedia');
            }
        } catch (error) {
            console.error('Download error:', error);
            downloadBtn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Gagal Unduh';
            downloadBtn.classList.remove('from-green-600', 'to-emerald-600');
            downloadBtn.classList.add('from-red-500', 'to-red-600');

            setTimeout(() => {
                downloadBtn.innerHTML = originalContent;
                downloadBtn.disabled = false;
                downloadBtn.classList.remove('from-red-500', 'to-red-600');
                downloadBtn.classList.add('from-green-600', 'to-emerald-600');
            }, 3000);

            alert('Gagal mengunduh frame. Silakan coba lagi atau gunakan link email.');
        }
    }

    // Ensure startProcessing is available globally
    console.log('=== PROCESSING JS LOADED ===');
    console.log('After override - window.startProcessing type:', typeof window.startProcessing);
    console.log('window.startProcessing defined:', window.startProcessing !== undefined);
    console.log('window.startProcessing is function:', typeof window.startProcessing === 'function');

    // Manual testing functions
    window.testProcessingState = function () {
        console.log('🧪 Testing processing state...');
        hideAllStates();
        showState('processing-state');

        setTimeout(() => {
            console.log('🧪 Testing simulateProcessing...');
            simulateProcessing();
        }, 1000);
    };

    window.testProcessingFlow = function () {
        console.log('🧪 Testing full processing flow...');
        window.sessionData = { selectedFrame: 2 };
        selectedFrameDesign = 2;
        selectedPhotos = [
            { id: 47, sequence_number: 2 },
            { id: 48, sequence_number: 3 },
            { id: 49, sequence_number: 4 }
        ];

        hideAllStates();
        showState('processing-state');

        setTimeout(() => {
            if (typeof window.startProcessing === 'function') {
                window.startProcessing();
            } else {
                simulateProcessing();
            }
        }, 500);
    };

    // Debug function to check current state
    window.debugProcessingState = function () {
        console.log('🔍 === PROCESSING DEBUG STATE ===');
        console.log('photoboxCode:', typeof photoboxCode !== 'undefined' ? photoboxCode : 'UNDEFINED');
        console.log('selectedPhotos:', typeof selectedPhotos !== 'undefined' ? selectedPhotos : 'UNDEFINED');
        console.log('selectedFrameDesign:', typeof selectedFrameDesign !== 'undefined' ? selectedFrameDesign : 'UNDEFINED');
        console.log('window.sessionData:', window.sessionData);
        console.log('startProcessing available:', typeof window.startProcessing === 'function');

        // Test session data fetch
        if (typeof photoboxCode !== 'undefined') {
            axios.get(`/photobox/${photoboxCode}/debug-status`)
                .then(response => {
                    console.log('📊 Current session from server:', response.data);
                })
                .catch(error => {
                    console.error('❌ Error fetching session:', error.message);
                });
        }
    };

    // Test that the function is actually callable
    if (typeof window.startProcessing === 'function') {
        console.log('✅ startProcessing function is properly overridden and callable!');
    } else {
        console.error('❌ startProcessing function override FAILED!');
        console.error('Current value:', window.startProcessing);
        console.error('Type:', typeof window.startProcessing);
    }
</script>