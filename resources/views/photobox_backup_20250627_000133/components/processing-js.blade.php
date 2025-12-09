{{-- Processing and Completion JavaScript --}}
<script>
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
</script>
