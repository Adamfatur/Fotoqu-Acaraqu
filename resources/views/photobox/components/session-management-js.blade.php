{{-- Session Management and API Communication JavaScript --}}
<script>
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
                    
                    // Only clear reset flag if this is truly a NEW session (different ID) 
                    // AND the session was created AFTER the reset timestamp
                    if (!currentSession || serverSession.id !== currentSession.id) {
                        try {
                            const lastResetTime = sessionStorage.getItem('fotoku_reset_time');
                            if (lastResetTime) {
                                const resetTime = parseInt(lastResetTime);
                                const sessionCreatedTime = new Date(serverSession.created_at).getTime();
                                
                                // Only clear reset flag if session was created AFTER user reset
                                if (sessionCreatedTime > resetTime) {
                                    sessionStorage.removeItem('fotoku_user_reset');
                                    sessionStorage.removeItem('fotoku_reset_time');
                                    console.log('✅ Reset flag cleared for new session created after reset');
                                } else {
                                    console.log('⏸️ Keeping reset flag - session is older than reset timestamp');
                                }
                            } else {
                                // No reset flag, safe to proceed
                                console.log('✅ No reset flag to clear');
                            }
                        } catch (e) {
                            console.warn('Could not check reset flag timestamp:', e);
                        }
                    }
                    
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
                    
                    // Clear all session data when no active session
                    window.completedSession = null;
                    window.sessionData = null;
                    
                    // Clear localStorage
                    try {
                        localStorage.removeItem('fotoku_latest_session');
                        localStorage.removeItem('fotoku_session_data');
                        localStorage.removeItem('fotoku_completed_session');
                        console.log('✅ Session data cleared - no active session found');
                    } catch (e) {
                        console.warn('Could not clear localStorage:', e);
                    }
                    
                    // Clear DOM container
                    const dataContainer = document.getElementById('session-data-container');
                    if (dataContainer) {
                        dataContainer.dataset.sessionId = '';
                        dataContainer.dataset.sessionCode = '';
                        dataContainer.dataset.timestamp = '';
                    }
                    
                    showWaitingState();
                }
                
                // If there is no active session at all (first check or after session end)
                if (!serverSession) {
                    debugLog('No active session found on server');
                    
                    // Ensure all client-side data is cleared
                    currentSession = null;
                    window.completedSession = null;
                    window.sessionData = null;
                    
                    // Clear localStorage if there's any stale data
                    try {
                        const hasStaleData = localStorage.getItem('fotoku_latest_session') || 
                                           localStorage.getItem('fotoku_session_data') || 
                                           localStorage.getItem('fotoku_completed_session');
                        
                        if (hasStaleData) {
                            localStorage.removeItem('fotoku_latest_session');
                            localStorage.removeItem('fotoku_session_data');
                            localStorage.removeItem('fotoku_completed_session');
                            console.log('🧹 Cleared stale session data from localStorage');
                        }
                    } catch (e) {
                        console.warn('Could not check/clear localStorage:', e);
                    }
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
                button.innerHTML = originalText;
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
</script>
