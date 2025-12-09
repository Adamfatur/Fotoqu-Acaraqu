{{-- Environment-based Security Controls --}}
<script>
(function() {
    'use strict';
    
    // Environment detection
    const ENV = {
        isDev: {{ config('app.debug') ? 'true' : 'false' }},
        isProd: '{{ config("app.env") }}' === 'production',
        domain: '{{ config("app.url") }}'
    };
    
    // Production-only security measures
    if (ENV.isProd) {
        // Disable console in production
        if (typeof console !== 'undefined') {
            console.log = function() {};
            console.warn = function() {};
            console.error = function() {};
            console.info = function() {};
            console.debug = function() {};
            console.table = function() {};
            console.trace = function() {};
        }
        
        // Domain validation (disabled in development)
        // if (window.location.hostname !== new URL(ENV.domain).hostname) {
        //     window.location.href = ENV.domain;
        //     return;
        // }
        
        // Disable view source
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 85) {
                e.preventDefault();
                alert('⚠️ Viewing source code is not allowed on this application.');
                return false;
            }
        });
        
        // Disable text selection on sensitive elements
        document.addEventListener('selectstart', function(e) {
            if (e.target.classList.contains('no-select') || 
                e.target.closest('.photobox-interface')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Disable drag and drop
        document.addEventListener('dragstart', function(e) {
            if (e.target.tagName === 'IMG' || e.target.classList.contains('photo-item')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Anti-automation detection (disabled for development)
        // let mouseMovements = 0;
        // let lastMouseMove = Date.now();
        
        // document.addEventListener('mousemove', function() {
        //     mouseMovements++;
        //     lastMouseMove = Date.now();
        // });
        
        // setInterval(function() {
        //     const timeSinceLastMove = Date.now() - lastMouseMove;
        //     if (timeSinceLastMove > 60000 && mouseMovements < 5) {
        //         // Possible automation detected
        //         console.log('Long inactivity detected');
        //         // window.location.reload(); // Disabled
        //     }
        //     mouseMovements = 0;
        // }, 60000);
        
        // Disable common debugging tools (disabled for development)
        // setInterval(function() {
        //     debugger;
        // }, 5000);
    }
    
    // Development-only features
    if (ENV.isDev) {
        window.FOTOKU_DEV = {
            showDebugInfo: true,
            enableConsole: true,
            bypassSecurity: true
        };
        
        console.log('%c🔧 FOTOKU DEVELOPMENT MODE', 'color: blue; font-size: 16px; font-weight: bold;');
        console.log('Debug features are enabled. This mode should not be used in production.');
    }
    
    // Secure environment variables export
    window.FOTOKU_ENV = {
        isDev: ENV.isDev,
        isProd: ENV.isProd,
        features: {
            debugging: ENV.isDev,
            console: ENV.isDev,
            rightClick: ENV.isDev,
            viewSource: ENV.isDev
        }
    };
    
})();
</script>
