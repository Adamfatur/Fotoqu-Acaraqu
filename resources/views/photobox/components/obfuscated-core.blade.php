{{-- Obfuscated Core Functions --}}
<script>
(function(_0x4f5e, _0x2d3a) {
    'use strict';
    
    // Obfuscated variable names for core functions
    const _0x1a2b = {
        _0x3c4d: '{{ $photobox->code }}',
        _0x5e6f: null,
        _0x7a8b: [],
        _0x9c0d: false
    };
    
    // Protected session management
    function _0x2b3c(_0x4d5e) {
        const _0x6f7a = _0x1a2b._0x3c4d;
        if (!_0x6f7a || _0x6f7a.length < 3) return false;
        
        return window.FOTOKU_SECURE.secureRequest('/validate', {
            operation: 'session_check',
            data: _0x4d5e
        });
    }
    
    // Protected photo processing
    function _0x8b9c(_0x0d1e, _0x2f3a) {
        if (!_0x1a2b._0x9c0d) return null;
        
        const _0x4b5c = {
            photos: _0x0d1e,
            config: _0x2f3a,
            box: _0x1a2b._0x3c4d
        };
        
        return window.FOTOKU_SECURE.secureRequest('/process', {
            operation: 'photo_process',
            data: _0x4b5c
        });
    }
    
    // Protected frame generation
    function _0x6d7e(_0x8f9a) {
        const _0x1c2d = _0x1a2b._0x7a8b;
        if (_0x1c2d.length === 0) return false;
        
        return window.FOTOKU_SECURE.secureRequest('/generate', {
            operation: 'frame_create',
            data: {
                selected: _0x1c2d,
                template: _0x8f9a
            }
        });
    }
    
    // Anti-tampering check
    function _0x9e0f() {
        const _0x3b4c = document.body.innerHTML;
        const _0x5d6e = _0x3b4c.length;
        
        if (_0x5d6e < 1000 || _0x5d6e > 50000) {
            window.location.reload();
        }
        
        // Check for injected scripts
        const _0x7f8a = document.querySelectorAll('script[src*="debug"], script[src*="tamper"]');
        if (_0x7f8a.length > 0) {
            window.location.href = '/';
        }
    }
    
    // Integrity validation
    setInterval(_0x9e0f, 5000);
    
    // Export obfuscated functions to protected namespace
    window._FOTOKU_CORE = {
        _0x2b3c,
        _0x8b9c,
        _0x6d7e,
        _state: _0x1a2b
    };
    
    // Legacy function mapping for backward compatibility
    window.secureSessionCheck = _0x2b3c;
    window.securePhotoProcess = _0x8b9c;
    window.secureFrameGenerate = _0x6d7e;
    
})();
</script>
