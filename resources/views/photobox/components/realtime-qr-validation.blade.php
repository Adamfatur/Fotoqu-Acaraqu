{{-- Real-Time QR Validation Component --}}
<script>
// Add real-time validation overlay to photobox interface
document.addEventListener('DOMContentLoaded', function() {
    // Only run in debug mode
    @if(config('app.debug'))
    
    console.log('🔬 REAL-TIME QR VALIDATION: Starting...');
    
    // Create validation overlay
    const overlay = document.createElement('div');
    overlay.id = 'qr-validation-overlay';
    overlay.style.cssText = `
        position: fixed;
        bottom: 10px;
        left: 10px;
        background: rgba(0, 0, 0, 0.9);
        color: white;
        font-family: monospace;
        font-size: 11px;
        padding: 10px;
        border-radius: 5px;
        z-index: 9998;
        max-width: 300px;
        border: 2px solid #10b981;
    `;
    
    overlay.innerHTML = `
        <div style="font-weight: bold; color: #10b981; margin-bottom: 5px;">🔬 QR VALIDATION</div>
        <div id="validation-status">Initializing...</div>
        <div style="margin-top: 5px; font-size: 10px;">
            <button id="toggle-validation" style="background: #3b82f6; color: white; border: none; padding: 2px 6px; border-radius: 3px; margin-right: 5px;">Toggle</button>
            <button id="force-validate" style="background: #10b981; color: white; border: none; padding: 2px 6px; border-radius: 3px;">Validate</button>
        </div>
    `;
    
    document.body.appendChild(overlay);
    
    let validationActive = true;
    
    // Validation function
    function validateQRSync() {
        if (!validationActive) return;
        
        const status = [];
        
        // Check DOM container
        const domContainer = document.getElementById('session-data-container');
        const domCode = domContainer?.dataset.sessionCode;
        
        // Check currentSession
        const currentCode = (typeof currentSession === 'object' && currentSession) ? currentSession.session_code : null;
        
        // Check completedSession
        const completedCode = (typeof window.completedSession === 'object' && window.completedSession) ? window.completedSession.session_code : null;
        
        // Check reliable function
        const reliableCode = (typeof window.getReliableSessionCode === 'function') ? window.getReliableSessionCode() : null;
        
        // Check gallery URL
        const galleryUrl = (typeof window.getGalleryUrl === 'function') ? window.getGalleryUrl() : null;
        
        // Status display
        status.push(`<div style="color: ${domCode ? '#10b981' : '#ef4444'};">DOM: ${domCode || 'NULL'}</div>`);
        status.push(`<div style="color: ${currentCode ? '#10b981' : '#ef4444'};">Current: ${currentCode || 'NULL'}</div>`);
        status.push(`<div style="color: ${completedCode ? '#10b981' : '#ef4444'};">Completed: ${completedCode || 'NULL'}</div>`);
        status.push(`<div style="color: ${reliableCode ? '#10b981' : '#ef4444'};">Reliable: ${reliableCode || 'NULL'}</div>`);
        
        // Check if all sources match
        const allCodes = [domCode, currentCode, completedCode, reliableCode].filter(Boolean);
        const isConsistent = allCodes.length > 0 && allCodes.every(code => code === allCodes[0]);
        
        status.push(`<div style="color: ${isConsistent ? '#10b981' : '#f59e0b'}; font-weight: bold; margin-top: 3px;">
            ${isConsistent ? '✅ SYNCED' : '⚠️ MISMATCHED'}
        </div>`);
        
        if (galleryUrl) {
            const urlMatch = galleryUrl.includes(reliableCode || domCode || currentCode || '');
            status.push(`<div style="color: ${urlMatch ? '#10b981' : '#ef4444'}; font-size: 10px; margin-top: 2px;">
                URL: ${urlMatch ? '✅' : '❌'} ${galleryUrl.split('/').pop()?.split('?')[0] || 'N/A'}
            </div>`);
        }
        
        document.getElementById('validation-status').innerHTML = status.join('');
        
        // Console log for serious mismatches
        if (!isConsistent && allCodes.length > 1) {
            console.warn('🚨 QR VALIDATION: Session codes are not consistent!', {
                dom: domCode,
                current: currentCode,
                completed: completedCode,
                reliable: reliableCode
            });
        }
    }
    
    // Event listeners
    document.getElementById('toggle-validation').addEventListener('click', function() {
        validationActive = !validationActive;
        this.textContent = validationActive ? 'Pause' : 'Resume';
        if (!validationActive) {
            document.getElementById('validation-status').innerHTML = '<span style="color: #6b7280;">Paused</span>';
        }
    });
    
    document.getElementById('force-validate').addEventListener('click', function() {
        validateQRSync();
        console.log('🔬 FORCED VALIDATION:', {
            timestamp: new Date().toISOString(),
            currentSession: currentSession,
            completedSession: window.completedSession,
            domContainer: domContainer?.dataset
        });
    });
    
    // Initial validation
    setTimeout(validateQRSync, 1000);
    
    // Periodic validation
    setInterval(validateQRSync, 3000);
    
    // Monitor currentSession changes
    let lastCurrentSession = null;
    setInterval(function() {
        if (currentSession !== lastCurrentSession) {
            lastCurrentSession = currentSession;
            console.log('🔄 currentSession changed, validating...', currentSession?.session_code);
            setTimeout(validateQRSync, 100);
        }
    }, 1000);
    
    // Monitor DOM container changes
    if (domContainer) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName?.startsWith('data-')) {
                    console.log('🔄 DOM container data changed, validating...');
                    setTimeout(validateQRSync, 100);
                }
            });
        });
        
        observer.observe(domContainer, {
            attributes: true,
            attributeFilter: ['data-session-code', 'data-session-id']
        });
    }
    
    console.log('🔬 REAL-TIME QR VALIDATION: Ready');
    
    @endif
});
</script>
