{{-- Global Functions Export for Window Object --}}
<script>
    // Make critical functions globally available
    window.startSession = startSession;
    window.showCaptureState = showCaptureState;
    window.showSelectionState = showSelectionState;
    window.showWaitingState = showWaitingState;
    window.loadPhotos = loadPhotos;
    window.togglePhotoSelection = togglePhotoSelection;
    window.confirmSelection = confirmSelection;
    window.resetSelection = resetSelection;
    window.forceStopSession = forceStopSession;
    window.toggleCameraSettings = toggleCameraSettings;
    window.switchCamera = switchCamera;
    window.refreshCameraDevices = refreshCameraDevices;
    window.testCamera = testCamera;
    window.debugButtonStatus = debugButtonStatus;
    window.checkForSession = checkForSession;
    window.showFrameError = showFrameError;
    window.showFrameSuccess = showFrameSuccess;
    window.resetToWaiting = resetToWaiting;
    window.cancelAutoReturn = cancelAutoReturn;
    
    // Fullscreen functions
    window.toggleFullscreen = toggleFullscreen;
    window.enterFullscreen = enterFullscreen;
    window.exitFullscreen = exitFullscreen;
    window.isCurrentlyFullscreen = isCurrentlyFullscreen;
    
    // Update fullscreen tip text
    function updateFullscreenTipText() {
        const tipText = document.getElementById('fullscreen-tip-text');
        if (tipText) {
            tipText.textContent = isCurrentlyFullscreen() ? 'Nonaktifkan' : 'Aktifkan';
        }
    }
    
    // Update tip text when fullscreen state changes
    document.addEventListener('fullscreenchange', updateFullscreenTipText);
    document.addEventListener('webkitfullscreenchange', updateFullscreenTipText);
    document.addEventListener('mozfullscreenchange', updateFullscreenTipText);
    document.addEventListener('MSFullscreenChange', updateFullscreenTipText);
    
    // Update tip text on load
    setTimeout(updateFullscreenTipText, 100);
</script>
