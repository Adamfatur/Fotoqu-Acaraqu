{{-- Security Protection Layer --}}
<script>
(function() {
    'use strict';
    
    // Only apply security in production environment
    @if(config('app.env') === 'production' && !config('app.debug'))
    
    // Anti-debugging measures (only in production)
    let devtools = {
        open: false,
        warnings: 0,
        maxWarnings: 5 // Increase threshold before any action
    };
    
    // Very conservative DevTools detection
    setInterval(function() {
        const threshold = 300; // Increased threshold to reduce false positives
        const heightDiff = window.outerHeight - window.innerHeight;
        const widthDiff = window.outerWidth - window.innerWidth;
        
        if (heightDiff > threshold || widthDiff > threshold) {
            if (!devtools.open) {
                devtools.open = true;
                devtools.warnings++;
                
                // Only show warning, never reload automatically
                if (devtools.warnings >= devtools.maxWarnings) {
                    console.clear();
                    console.log('%c🔒 FOTOKU SECURITY', 'color: red; font-size: 20px; font-weight: bold;');
                    console.log('%cThis is a protected application. Please close developer tools.', 'color: red; font-size: 14px;');
                    // Never auto-reload to prevent infinite loop
                }
            }
        } else {
            devtools.open = false;
            if (devtools.warnings > 0) {
                devtools.warnings = Math.max(0, devtools.warnings - 1); // Slowly reduce warnings
            }
        }
    }, 3000); // Reduced frequency to avoid performance issues
    
    @endif
    
    // Basic security measures for all environments
    // Disable right-click context menu (DISABLED for better UX)
    @if(false) // Right-click protection disabled
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    });
    @endif
    // Disable F12, Ctrl+Shift+I, Ctrl+U (only in production)
    document.addEventListener('keydown', function(e) {
        if (e.keyCode === 123 || // F12
            (e.ctrlKey && e.shiftKey && e.keyCode === 73) || // Ctrl+Shift+I
            (e.ctrlKey && e.keyCode === 85)) { // Ctrl+U
            e.preventDefault();
            return false;
        }
    });
    @endif
    
    // Secure API communication
    window.FOTOKU_SECURE = {
        token: null,
        apiBase: '/api/secure-photobox',
        
        async init() {
            try {
                const response = await fetch('/api/secure-photobox/token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        photobox_code: '{{ $photobox->code }}'
                    })
                });
                
                const data = await response.json();
                this.token = data.token;
                return true;
            } catch (e) {
                return false;
            }
        },
        
        async secureRequest(endpoint, data = {}) {
            if (!this.token) {
                await this.init();
            }
            
            try {
                const response = await fetch(`${this.apiBase}${endpoint}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Photobox-Token': this.token
                    },
                    body: JSON.stringify(data)
                });
                
                return await response.json();
            } catch (e) {
                console.error('Secure request failed:', e);
                return null;
            }
        }
    };
    
    // Initialize security
    window.FOTOKU_SECURE.init();
    
})();
</script>
