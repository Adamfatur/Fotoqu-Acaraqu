{{-- Stable Security Protection Layer --}}
<script>
(function() {
    'use strict';
    
    // Environment detection
    const ENV = {
        isDev: {{ config('app.debug') ? 'true' : 'false' }},
        isProd: '{{ config("app.env") }}' === 'production'
    };
    
    // Security configuration
    const SECURITY_CONFIG = {
        enableAntiDebug: ENV.isProd,
        enableConsoleProtection: ENV.isProd,
        enableRightClickProtection: false, // Disabled for production
        enableKeyboardProtection: ENV.isProd,
        enableDragProtection: ENV.isProd
    };
    
    // Console protection (production only)
    if (SECURITY_CONFIG.enableConsoleProtection) {
        const originalConsole = window.console;
        window.console = {
            log: function() {},
            warn: function() {},
            error: function() {},
            info: function() {},
            debug: function() {},
            table: function() {},
            trace: function() {},
            clear: originalConsole.clear.bind(originalConsole)
        };
    }
    
    // Right-click protection (production only)
    if (SECURITY_CONFIG.enableRightClickProtection) {
        document.addEventListener('contextmenu', function(e) {
            // Allow right-click on input fields for usability
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                return true;
            }
            e.preventDefault();
            showSecurityMessage('Right-click is disabled for security reasons.');
            return false;
        });
    }
    
    // Keyboard shortcuts protection (production only)
    if (SECURITY_CONFIG.enableKeyboardProtection) {
        document.addEventListener('keydown', function(e) {
            // F12, Ctrl+Shift+I, Ctrl+U, Ctrl+Shift+J
            if (e.keyCode === 123 || 
                (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) ||
                (e.ctrlKey && e.keyCode === 85)) {
                e.preventDefault();
                showSecurityMessage('Developer tools access is restricted.');
                return false;
            }
        });
    }
    
    // Drag protection for images (production only)
    if (SECURITY_CONFIG.enableDragProtection) {
        document.addEventListener('dragstart', function(e) {
            if (e.target.tagName === 'IMG' || e.target.classList.contains('photo-item')) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Conservative DevTools detection (production only, no auto-refresh)
    if (SECURITY_CONFIG.enableAntiDebug) {
        let devToolsWarnings = 0;
        const maxWarnings = 3;
        let lastCheck = Date.now();
        
        setInterval(function() {
            const now = Date.now();
            // Only check every 5 seconds to avoid performance issues
            if (now - lastCheck < 5000) return;
            lastCheck = now;
            
            const heightDiff = window.outerHeight - window.innerHeight;
            const widthDiff = window.outerWidth - window.innerWidth;
            
            // Very conservative threshold
            if (heightDiff > 400 || widthDiff > 400) {
                devToolsWarnings++;
                if (devToolsWarnings >= maxWarnings) {
                    showSecurityMessage('Please close developer tools for optimal experience.');
                    devToolsWarnings = 0; // Reset counter
                }
            } else {
                // Slowly reduce warnings if tools are closed
                if (devToolsWarnings > 0) {
                    devToolsWarnings = Math.max(0, devToolsWarnings - 1);
                }
            }
        }, 5000);
    }
    
    // Security message display
    function showSecurityMessage(message) {
        // Create a subtle notification instead of alert
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(239, 68, 68, 0.9);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 10000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            max-width: 300px;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }
    
    // Development helpers
    if (ENV.isDev) {
        window.FOTOKU_DEV = {
            security: SECURITY_CONFIG,
            enableDebug: function() {
                Object.keys(SECURITY_CONFIG).forEach(key => {
                    SECURITY_CONFIG[key] = false;
                });
                console.log('%c🔧 Security disabled for debugging', 'color: blue; font-weight: bold;');
            },
            showMessage: showSecurityMessage
        };
        
        console.log('%c🔧 FOTOKU DEVELOPMENT MODE', 'color: blue; font-size: 16px; font-weight: bold;');
        console.log('Security features:', SECURITY_CONFIG);
        console.log('Use FOTOKU_DEV.enableDebug() to disable security for debugging');
    }
    
    // Export environment info
    window.FOTOKU_ENV = {
        isDev: ENV.isDev,
        isProd: ENV.isProd,
        security: SECURITY_CONFIG
    };
    
})();
</script>
