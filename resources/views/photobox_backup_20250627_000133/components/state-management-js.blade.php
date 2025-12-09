{{-- State Management JavaScript --}}
<script>
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
</script>
