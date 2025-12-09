{{-- Fullscreen JavaScript Component --}}
<script>
    // Fullscreen functionality
    let isFullscreen = false;
    let fullscreenSupported = false;
    
    // Check fullscreen support
    function checkFullscreenSupport() {
        fullscreenSupported = document.fullscreenEnabled ||
                            document.webkitFullscreenEnabled ||
                            document.mozFullScreenEnabled ||
                            document.msFullscreenEnabled;
        
        debugLog('Fullscreen support check', { 
            supported: fullscreenSupported,
            vendor: getFullscreenVendor()
        });
        
        return fullscreenSupported;
    }
    
    // Get vendor-specific fullscreen API
    function getFullscreenVendor() {
        if (document.fullscreenEnabled) return 'standard';
        if (document.webkitFullscreenEnabled) return 'webkit';
        if (document.mozFullScreenEnabled) return 'moz';
        if (document.msFullscreenEnabled) return 'ms';
        return 'none';
    }
    
    // Enter fullscreen
    function enterFullscreen() {
        debugLog('Attempting to enter fullscreen');
        
        const element = document.documentElement;
        
        try {
            if (element.requestFullscreen) {
                return element.requestFullscreen();
            } else if (element.webkitRequestFullscreen) {
                return element.webkitRequestFullscreen();
            } else if (element.mozRequestFullScreen) {
                return element.mozRequestFullScreen();
            } else if (element.msRequestFullscreen) {
                return element.msRequestFullscreen();
            } else {
                // Fallback for devices without native fullscreen API
                return enterPseudoFullscreen();
            }
        } catch (error) {
            debugLog('Native fullscreen failed, using fallback', error);
            return enterPseudoFullscreen();
        }
    }
    
    // Exit fullscreen
    function exitFullscreen() {
        debugLog('Attempting to exit fullscreen');
        
        try {
            if (document.exitFullscreen) {
                return document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                return document.webkitExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                return document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                return document.msExitFullscreen();
            } else {
                return exitPseudoFullscreen();
            }
        } catch (error) {
            debugLog('Native fullscreen exit failed, using fallback', error);
            return exitPseudoFullscreen();
        }
    }
    
    // Pseudo fullscreen for devices without native support
    function enterPseudoFullscreen() {
        debugLog('Entering pseudo fullscreen mode');
        
        const body = document.body;
        const app = document.getElementById('app');
        
        // Add fullscreen classes
        body.classList.add('fullscreen-mode');
        if (app) app.classList.add('fullscreen-mode');
        
        // Hide browser UI on mobile
        try {
            if (window.screen && window.screen.orientation) {
                // Mobile fullscreen
                document.documentElement.style.overflow = 'hidden';
                document.body.style.overflow = 'hidden';
                window.scrollTo(0, 0);
                
                // Try to hide address bar
                setTimeout(() => {
                    window.scrollTo(0, 1);
                    window.scrollTo(0, 0);
                }, 100);
            }
        } catch (e) {
            debugLog('Mobile fullscreen optimization failed', e);
        }
        
        isFullscreen = true;
        updateFullscreenUI();
        
        // Show notification
        showFullscreenNotification('Mode layar penuh diaktifkan. Tekan ESC atau tombol exit untuk keluar.');
        
        return Promise.resolve();
    }
    
    // Exit pseudo fullscreen
    function exitPseudoFullscreen() {
        debugLog('Exiting pseudo fullscreen mode');
        
        const body = document.body;
        const app = document.getElementById('app');
        
        // Remove fullscreen classes
        body.classList.remove('fullscreen-mode');
        if (app) app.classList.remove('fullscreen-mode');
        
        // Restore scrolling
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
        
        isFullscreen = false;
        updateFullscreenUI();
        
        return Promise.resolve();
    }
    
    // Check if currently in fullscreen
    function isCurrentlyFullscreen() {
        return !!(document.fullscreenElement ||
                 document.webkitFullscreenElement ||
                 document.mozFullScreenElement ||
                 document.msFullscreenElement ||
                 document.body.classList.contains('fullscreen-mode'));
    }
    
    // Toggle fullscreen
    async function toggleFullscreen() {
        debugLog('Toggling fullscreen', { currentState: isCurrentlyFullscreen() });
        
        try {
            if (isCurrentlyFullscreen()) {
                await exitFullscreen();
            } else {
                await enterFullscreen();
            }
        } catch (error) {
            debugLog('Fullscreen toggle error', error);
            
            // Show user-friendly error message
            alert('Tidak dapat mengubah mode layar penuh. Silakan coba lagi atau gunakan tombol fullscreen browser.');
        }
    }
    
    // Update fullscreen UI elements
    function updateFullscreenUI() {
        const icon = document.getElementById('fullscreen-icon');
        const button = document.getElementById('fullscreen-toggle');
        
        if (icon) {
            if (isCurrentlyFullscreen()) {
                icon.className = 'fas fa-compress text-white text-sm';
            } else {
                icon.className = 'fas fa-expand text-white text-sm';
            }
        }
        
        if (button) {
            const title = isCurrentlyFullscreen() ? 'Exit Fullscreen Mode' : 'Enter Fullscreen Mode';
            button.setAttribute('title', title);
        }
        
        debugLog('Fullscreen UI updated', { isFullscreen: isCurrentlyFullscreen() });
    }
    
    // Show fullscreen notification
    function showFullscreenNotification(message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-black/80 text-white px-6 py-3 rounded-xl z-[10001] backdrop-blur-md border border-white/20 max-w-md text-center';
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas fa-info-circle text-blue-400"></i>
                <span class="text-sm font-medium">${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
        
        // Remove on click
        notification.addEventListener('click', () => {
            notification.remove();
        });
    }
    
    // Handle fullscreen change events
    function handleFullscreenChange() {
        const wasFullscreen = isFullscreen;
        isFullscreen = isCurrentlyFullscreen();
        
        debugLog('Fullscreen change detected', { 
            wasFullscreen, 
            isFullscreen, 
            element: document.fullscreenElement || document.webkitFullscreenElement 
        });
        
        updateFullscreenUI();
        
        // Handle UI adjustments for fullscreen mode
        if (isFullscreen && !wasFullscreen) {
            // Entering fullscreen
            onEnterFullscreen();
        } else if (!isFullscreen && wasFullscreen) {
            // Exiting fullscreen
            onExitFullscreen();
        }
    }
    
    // Fullscreen enter handler
    function onEnterFullscreen() {
        debugLog('Entered fullscreen mode');
        
        // Optimize for tablet experience
        if (window.innerWidth >= 768 && window.innerWidth <= 1024) {
            // iPad/tablet optimizations
            const header = document.querySelector('header');
            const main = document.querySelector('main');
            
            if (header) {
                header.style.padding = '20px 32px';
            }
            
            if (main) {
                main.style.padding = '32px';
            }
        }
        
        // Show fullscreen tips for first time users
        const hasSeenTips = localStorage.getItem('fotoku_fullscreen_tips');
        if (!hasSeenTips) {
            setTimeout(() => {
                showFullscreenNotification('Tips: Gunakan gesture swipe atau tombol navigasi untuk pengalaman terbaik!');
                localStorage.setItem('fotoku_fullscreen_tips', 'true');
            }, 2000);
        }
    }
    
    // Fullscreen exit handler
    function onExitFullscreen() {
        debugLog('Exited fullscreen mode');
        
        // Reset any fullscreen-specific styling
        const header = document.querySelector('header');
        const main = document.querySelector('main');
        
        if (header) {
            header.style.padding = '';
        }
        
        if (main) {
            main.style.padding = '';
        }
    }
    
    // Keyboard shortcut handler
    function handleKeyboardShortcuts(event) {
        // ESC to exit fullscreen
        if (event.key === 'Escape' && isCurrentlyFullscreen()) {
            exitFullscreen();
        }
        
        // F11 to toggle fullscreen (if supported)
        if (event.key === 'F11') {
            event.preventDefault();
            toggleFullscreen();
        }
        
        // F for fullscreen toggle
        if (event.key === 'f' || event.key === 'F') {
            if (event.ctrlKey || event.metaKey) {
                event.preventDefault();
                toggleFullscreen();
            }
        }
    }
    
    // Initialize fullscreen functionality
    function initializeFullscreen() {
        debugLog('Initializing fullscreen functionality');
        
        // Check support
        checkFullscreenSupport();
        
        // Add event listeners for fullscreen changes
        document.addEventListener('fullscreenchange', handleFullscreenChange);
        document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
        document.addEventListener('mozfullscreenchange', handleFullscreenChange);
        document.addEventListener('MSFullscreenChange', handleFullscreenChange);
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', handleKeyboardShortcuts);
        
        // Initial UI update
        updateFullscreenUI();
        
        // Touch/mobile optimizations
        if ('ontouchstart' in window) {
            debugLog('Touch device detected, applying optimizations');
            
            // Prevent pull-to-refresh in fullscreen
            document.addEventListener('touchmove', function(e) {
                if (isCurrentlyFullscreen() && e.touches.length === 1) {
                    // Allow vertical scrolling within components
                    const target = e.target.closest('.overflow-y-auto, .custom-scrollbar');
                    if (!target) {
                        e.preventDefault();
                    }
                }
            }, { passive: false });
            
            // Optimize for iPad gestures
            if (navigator.userAgent.match(/iPad/i)) {
                document.addEventListener('gesturestart', function(e) {
                    if (isCurrentlyFullscreen()) {
                        e.preventDefault();
                    }
                });
            }
        }
        
        debugLog('Fullscreen initialization complete', {
            supported: fullscreenSupported,
            vendor: getFullscreenVendor(),
            isTouch: 'ontouchstart' in window
        });
    }
    
    // Auto-fullscreen for tablets (optional)
    function suggestFullscreenForTablet() {
        // Check if device is likely a tablet
        const isTablet = window.innerWidth >= 768 && window.innerWidth <= 1024 && 'ontouchstart' in window;
        const hasShownSuggestion = localStorage.getItem('fotoku_fullscreen_suggested');
        
        if (isTablet && !hasShownSuggestion && !isCurrentlyFullscreen()) {
            debugLog('Tablet detected, suggesting fullscreen mode');
            
            setTimeout(() => {
                const suggest = confirm('Untuk pengalaman terbaik di tablet, apakah Anda ingin menggunakan mode layar penuh?');
                if (suggest) {
                    toggleFullscreen();
                }
                localStorage.setItem('fotoku_fullscreen_suggested', 'true');
            }, 3000);
        }
    }
    
    // Make functions globally available
    window.toggleFullscreen = toggleFullscreen;
    window.enterFullscreen = enterFullscreen;
    window.exitFullscreen = exitFullscreen;
    window.isCurrentlyFullscreen = isCurrentlyFullscreen;
    window.initializeFullscreen = initializeFullscreen;
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeFullscreen);
    } else {
        initializeFullscreen();
    }
    
    // Suggest fullscreen for tablets after interface loads
    setTimeout(suggestFullscreenForTablet, 5000);
</script>
