{{-- Frame Design Management JavaScript --}}
<script>
// Frame Design State Management
let selectedFrameDesign = 'default';
let availableFrameTemplates = [];

// Load available frame templates
async function loadFrameTemplates() {
    try {
        console.log('📋 Loading frame templates from:', `/photobox/${photoboxCode}/frame-templates`);
        console.log('📦 photoboxCode value:', photoboxCode);
        
        const response = await axios.get(`/photobox/${photoboxCode}/frame-templates`);
        
        console.log('📥 Frame templates response:', response.data);
        console.log('📊 Response status:', response.status);
        
        if (response.data && response.data.success && response.data.templates) {
            // Sort: Default first, then Recommended, then newest (created_at desc)
            availableFrameTemplates = [...response.data.templates].sort((a, b) => {
                // Default first
                if ((b.is_default ? 1 : 0) !== (a.is_default ? 1 : 0)) {
                    return (b.is_default ? 1 : 0) - (a.is_default ? 1 : 0);
                }
                // Recommended next
                if ((b.is_recommended ? 1 : 0) !== (a.is_recommended ? 1 : 0)) {
                    return (b.is_recommended ? 1 : 0) - (a.is_recommended ? 1 : 0);
                }
                // Newest first by created_at
                const ad = a.created_at ? new Date(a.created_at).getTime() : 0;
                const bd = b.created_at ? new Date(b.created_at).getTime() : 0;
                return bd - ad;
            });
            console.log('✅ Available templates loaded:', availableFrameTemplates.length);
            console.log('📝 Templates data:', availableFrameTemplates);
            
            if (availableFrameTemplates.length === 0) {
                console.warn('⚠️  No templates found in database');
            }
            
            renderFrameTemplates();
        } else {
            console.warn('⚠️  No templates received or API error:', response.data);
            availableFrameTemplates = [];
        }
    } catch (error) {
        console.error('❌ Error loading frame templates:', error);
        console.error('📍 Error details:', {
            message: error.message,
            status: error.response?.status,
            data: error.response?.data
        });
        // Continue with empty templates array
        availableFrameTemplates = [];
    }
}

// Render frame templates in the UI
function renderFrameTemplates() {
    console.log('🎨 Starting renderFrameTemplates...');
    
    const container = document.getElementById('frame-templates-grid');
    if (!container) {
        console.error('❌ Container frame-templates-grid not found!');
        return;
    }
    console.log('✅ Container found:', container);

    let templatesHtml = '';
    
    console.log('📋 Templates to render:', availableFrameTemplates.length);
    console.log('📋 Templates data:', availableFrameTemplates);
    
    if (availableFrameTemplates.length === 0) {
        console.warn('⚠️  No templates available to render');
        container.innerHTML = `
            <div class="col-span-full text-center py-8">
                <div class="text-white/60">
                    <div class="text-4xl mb-4">🖼️</div>
                    <p>Belum ada template frame tersedia</p>
                    <p class="text-sm mt-2">Silakan hubungi admin untuk menambahkan template</p>
                </div>
            </div>
        `;
        return;
    }
    
    // Only show dynamic frame templates from database (no default template)
    // Admin determines default/recommended status in admin panel
    availableFrameTemplates.forEach((template, index) => {
        console.log(`🎨 Rendering template ${index + 1}:`, template);
        
        // Note: template status is already filtered to 'active' on server side
        templatesHtml += `
            <div class="frame-option group cursor-pointer" data-frame-id="${template.id}" data-frame-name="${template.name}" data-preview-url="${template.preview_url || ''}">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border-2 border-transparent group-hover:border-white/30 transition-all duration-300 frame-preview-card">
                    <div class="aspect-[3/4] bg-white rounded-lg relative overflow-hidden mb-3">
                        <button type="button" class="absolute top-2 left-2 z-10 preview-btn w-9 h-9 rounded-full flex items-center justify-center bg-white/90 text-gray-900 hover:bg-white shadow-md border border-white/70" title="Perbesar" aria-label="Perbesar preview frame">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        ${template.preview_url ? 
                            `<img src="${template.preview_url}" alt="${template.name}" class="w-full h-full object-cover"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                             <div class="absolute inset-4 border-2 border-gray-200 rounded-lg items-center justify-center" style="display:none;">
                                <div class="grid grid-cols-2 gap-1 w-full h-full p-2">
                                    <div class="bg-gray-100 rounded"></div>
                                    <div class="bg-gray-100 rounded"></div>
                                    <div class="bg-gray-100 rounded"></div>
                                    <div class="bg-gray-100 rounded"></div>
                                </div>
                             </div>` :
                            `<div class="absolute inset-4 border-2 border-gray-200 rounded-lg flex items-center justify-center">
                                <div class="grid grid-cols-2 gap-1 w-full h-full p-2">
                                    <div class="bg-gray-100 rounded"></div>
                                    <div class="bg-gray-100 rounded"></div>
                                    <div class="bg-gray-100 rounded"></div>
                                    <div class="bg-gray-100 rounded"></div>
                                </div>
                            </div>`
                        }
                    </div>
                    <div class="text-center">
                        <h3 class="text-white font-semibold">${template.name}</h3>
                        <p class="text-white/70 text-sm">${template.description || 'Frame custom'}</p>
                        <div class="mt-1 flex items-center justify-center gap-1 flex-wrap">
                            ${template.is_default ? '<span class="inline-flex items-center gap-1 bg-yellow-500/90 text-black text-[11px] px-2 py-0.5 rounded-full">🏷️ Default</span>' : ''}
                            ${template.is_recommended ? '<span class="inline-flex items-center gap-1 bg-green-500/90 text-white text-[11px] px-2 py-0.5 rounded-full">⭐ Rekomendasi</span>' : ''}
                            ${template.created_at ? '<span class="inline-flex items-center gap-1 bg-blue-500/80 text-white text-[11px] px-2 py-0.5 rounded-full">🆕 Baru</span>' : ''}
                        </div>
                    </div>
                    <div class="frame-check hidden absolute top-2 right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white text-sm"></i>
                    </div>
                </div>
            </div>
        `;
    });

    console.log('🖼️  Setting container HTML...');
    container.innerHTML = templatesHtml;
    console.log('✅ HTML set, attaching events...');
    
    attachFrameSelectionEvents();
    attachFramePreviewEvents();
    
    // Auto-select frame: use current selection or auto-select first available template
    setTimeout(() => {
        console.log('⏰ Auto-selection timeout triggered');
        if (selectedFrameDesign && selectedFrameDesign !== '') {
            // Re-select the previously selected frame to update UI
            const existingElement = document.querySelector(`[data-frame-id="${selectedFrameDesign}"]`);
            if (existingElement) {
                const frameName = existingElement.dataset.frameName || 'Selected Frame';
                selectFrameDesign(selectedFrameDesign, frameName);
                console.log('✅ Re-selected existing frame:', selectedFrameDesign);
            } else {
                // Selected frame no longer exists, auto-select first available template
                autoSelectFirstTemplate();
            }
        } else {
            // No frame selected, auto-select first available template
            autoSelectFirstTemplate();
        }
    }, 100);
}

// Auto-select the first available template (or default template from DB)
function autoSelectFirstTemplate() {
    if (availableFrameTemplates.length > 0) {
        // Look for a template marked as default first
        const defaultTemplate = availableFrameTemplates.find(t => t.is_default === true);
        if (defaultTemplate) {
            selectFrameDesign(defaultTemplate.id, defaultTemplate.name);
            console.log('Auto-selected default template from DB:', defaultTemplate.name);
        } else {
            // If no default, select the first template
            const firstTemplate = availableFrameTemplates[0];
            selectFrameDesign(firstTemplate.id, firstTemplate.name);
            console.log('Auto-selected first template:', firstTemplate.name);
        }
    } else {
        console.log('No frame templates available');
        selectedFrameDesign = null;
    }
}

// Attach click events to frame options
function attachFrameSelectionEvents() {
    document.querySelectorAll('.frame-option').forEach(option => {
        option.addEventListener('click', function(e) {
            // If click originated from a preview button, skip selection here
            if (e.target && (e.target.closest('.preview-btn'))) return;
            const frameId = this.dataset.frameId;
            const frameName = this.dataset.frameName;
            selectFrameDesign(frameId, frameName);
        });
    });
}

// Attach modal preview events
function attachFramePreviewEvents() {
    const modal = document.getElementById('frame-preview-modal');
    const img = document.getElementById('frame-preview-image');
    const caption = document.getElementById('frame-preview-caption');
    const closeBtn = document.getElementById('frame-preview-close');
    if (!modal || !img || !closeBtn) return;

    // Open modal when clicking preview button or image
    document.querySelectorAll('.frame-option').forEach(option => {
        const previewBtn = option.querySelector('.preview-btn');
        const previewUrl = option.getAttribute('data-preview-url');
        const name = option.getAttribute('data-frame-name');
        if (previewBtn && previewUrl) {
            previewBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                openFramePreview(previewUrl, name);
            });
        }
        // Clicking the image area (but not selection) can also open modal with Alt/Option key
        const imageArea = option.querySelector('img');
        if (imageArea && previewUrl) {
            imageArea.addEventListener('click', (e) => {
                if (e.altKey) {
                    e.preventDefault();
                    e.stopPropagation();
                    openFramePreview(previewUrl, name);
                }
            });
        }
    });

    function openFramePreview(url, name) {
        img.src = url;
        caption.textContent = name || '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        // Focus close for accessibility
        setTimeout(() => closeBtn.focus(), 0);
        // Disable background scroll
        document.body.style.overflow = 'hidden';
    }

    function closePreview() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        img.src = '';
        caption.textContent = '';
        document.body.style.overflow = '';
    }

    closeBtn.addEventListener('click', (e) => {
        e.preventDefault();
        closePreview();
    });

    // Click outside image closes modal
    modal.addEventListener('click', (e) => {
        const within = e.target.closest('#frame-preview-image');
        const isButton = e.target.closest('#frame-preview-close');
        if (!within && !isButton) closePreview();
    });

    // ESC to close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closePreview();
        }
    });
}

// Select frame design
function selectFrameDesign(frameId, frameName) {
    selectedFrameDesign = frameId;
    
    console.log('=== FRAME SELECTION DEBUG ===');
    console.log('Selected frame ID:', frameId);
    console.log('Selected frame name:', frameName);
    console.log('Frame ID type:', typeof frameId);
    console.log('Global selectedFrameDesign set to:', selectedFrameDesign);
    
    // Update UI
    document.querySelectorAll('.frame-option .frame-preview-card').forEach(card => {
        card.classList.remove('border-green-400', 'bg-green-500/10');
        card.classList.add('border-transparent');
    });
    
    document.querySelectorAll('.frame-check').forEach(check => {
        check.classList.add('hidden');
    });

    // Highlight selected frame
    const selectedOption = document.querySelector(`[data-frame-id="${frameId}"]`);
    if (selectedOption) {
        const card = selectedOption.querySelector('.frame-preview-card');
        const check = selectedOption.querySelector('.frame-check');
        
        card.classList.remove('border-transparent');
        card.classList.add('border-green-400', 'bg-green-500/10');
        check.classList.remove('hidden');
        
        console.log('UI updated for selected frame');
    } else {
        console.warn('Could not find frame option element for ID:', frameId);
    }

    // Update selected frame name display
    const frameNameDisplay = document.getElementById('selected-frame-name');
    if (frameNameDisplay) {
        frameNameDisplay.textContent = frameName;
    }

    console.log('Frame design selection completed successfully');
}

// Navigation functions for frame design state
function backToPhotoSelection() {
    hideAllStates();
    showState('selection-state');
}

function proceedToPhotoFilter() {
    // Auto-select first available template if none selected
    if (!selectedFrameDesign || selectedFrameDesign === '') {
        autoSelectFirstTemplate();
        if (!selectedFrameDesign) {
            console.error('No frame template available, cannot proceed');
            return;
        }
    }
    
    console.log('Frame selected:', selectedFrameDesign);
    
    // Save frame selection to session data before processing
    if (!window.sessionData) {
        window.sessionData = {};
    }
    window.sessionData.selectedFrame = selectedFrameDesign;
    
    console.log('💾 Frame selection saved to session data:', {
        selectedFrameDesign: selectedFrameDesign,
        'window.sessionData.selectedFrame': window.sessionData.selectedFrame,
        'window.sessionData': window.sessionData
    });
    
    // TEMPORARY: Skip photo filter step - will be re-enabled in future
    // TODO: Re-enable photo filter functionality when needed
    // Original flow: frame-design -> photo-filter -> processing
    // Current flow: frame-design -> processing (skip photo-filter)
    
    /* 
    // COMMENTED OUT: Photo filter step (will be re-enabled later)
    hideAllStates();
    showState('photo-filter-state');
    initializePhotoFilter();
    */
    
    // Set default photo filters (no filters applied)
    selectedPhotoFilters = {};
    
    // Go directly to processing state (skip photo filter)
    hideAllStates();
    showState('processing-state');
    
    // Start frame processing
    if (typeof startProcessing === 'function') {
        console.log('✅ Found startProcessing function, calling it...');
        startProcessing();
    } else if (typeof window.startProcessing === 'function') {
        console.log('✅ Found window.startProcessing function, calling it...');
        window.startProcessing();
    } else {
        console.error('❌ startProcessing function not found, retrying...');
        console.log('Available functions check:');
        console.log('- typeof startProcessing:', typeof startProcessing);
        console.log('- typeof window.startProcessing:', typeof window.startProcessing);
        
        // Retry mechanism
        let retryCount = 0;
        const maxRetries = 10;
        
        const retryStartProcessing = () => {
            retryCount++;
            
            if (typeof startProcessing === 'function') {
                console.log('✅ Found startProcessing after retry, calling it...');
                startProcessing();
            } else if (typeof window.startProcessing === 'function') {
                console.log('✅ Found window.startProcessing after retry, calling it...');
                window.startProcessing();
            } else if (retryCount < maxRetries) {
                console.log(`🔄 Retry ${retryCount}/${maxRetries} for startProcessing in 150ms...`);
                setTimeout(retryStartProcessing, 150);
            } else {
                console.error('❌ Failed to find startProcessing function after maximum retries');
                
                // Fallback: try to simulate basic processing
                console.warn('⚠️  Falling back to simulateProcessing...');
                if (typeof simulateProcessing === 'function') {
                    simulateProcessing();
                } else {
                    console.error('❌ Even simulateProcessing is not available!');
                }
            }
        };
        
        setTimeout(retryStartProcessing, 100);
    }
}

// Initialize frame design state
function initializeFrameDesign() {
    console.log('🚀 Initializing frame design state...');
    console.log('🎯 Current selectedFrameDesign:', selectedFrameDesign);
    console.log('📦 photoboxCode available:', typeof photoboxCode !== 'undefined' ? photoboxCode : 'NOT DEFINED');
    
    // Don't reset to default if user already made a selection
    // Only set to default if truly no selection was made
    if (!selectedFrameDesign || selectedFrameDesign === '' || selectedFrameDesign === null) {
        console.log('⏳ No frame selected yet, will wait for user selection');
        // Don't auto-select default - let user choose
    } else {
        console.log('✅ Preserving existing frame selection:', selectedFrameDesign);
    }
    
    // Check if photoboxCode is available
    if (typeof photoboxCode === 'undefined' || !photoboxCode) {
        console.error('❌ photoboxCode is not defined! Cannot load templates.');
        return;
    }
    
    // Load frame templates (this will render templates from database)
    loadFrameTemplates();
}

// Debug functions for testing
function testFrameDesignState() {
    console.log('=== TESTING FRAME DESIGN STATE ===');
    hideAllStates();
    showState('frame-design-state');
    initializeFrameDesign();
    console.log('Frame design state should now be visible');
}

function testPhotoFilterState() {
    console.log('=== TESTING PHOTO FILTER STATE ===');
    // Mock some selected photos for testing
    if (selectedPhotos.length === 0) {
        selectedPhotos = [
            { id: 1, url: '/images/placeholder-photo.jpg', filename: 'placeholder-photo.jpg' },
            { id: 2, url: '/images/placeholder-photo.jpg', filename: 'placeholder-photo.jpg' },
            { id: 3, url: '/images/placeholder-photo.jpg', filename: 'placeholder-photo.jpg' },
            { id: 4, url: '/images/placeholder-photo.jpg', filename: 'placeholder-photo.jpg' }
        ];
    }
    
    hideAllStates();
    showState('photo-filter-state');
    initializePhotoFilter();
    console.log('Photo filter state should now be visible');
}

function debugCurrentState() {
    console.log('=== CURRENT STATE DEBUG ===');
    const states = ['waiting-state', 'capture-state', 'selection-state', 'frame-design-state', 'photo-filter-state', 'processing-state', 'completed-state'];
    
    states.forEach(stateId => {
        const element = document.getElementById(stateId);
        if (element) {
            const isVisible = !element.classList.contains('hidden');
            console.log(`${stateId}: ${isVisible ? 'VISIBLE' : 'hidden'}`);
        } else {
            console.log(`${stateId}: NOT FOUND`);
        }
    });
    
    console.log('Selected photos:', selectedPhotos);
    console.log('Selected frame design:', selectedFrameDesign);
    console.log('Available templates:', availableFrameTemplates);
}

// Expose debug functions globally for console testing - only in development
@if(config('app.debug'))
window.testFrameDesignState = testFrameDesignState;
window.testPhotoFilterState = testPhotoFilterState;
window.debugCurrentState = debugCurrentState;
@endif

// Add to state initialization
if (typeof initializeStates === 'function') {
    const originalInitializeStates = initializeStates;
    initializeStates = function() {
        originalInitializeStates();
        // Frame design will be initialized when state is shown
    };
}

// Ensure function is globally available
window.initializeFrameDesign = initializeFrameDesign;

// Better initialization with retry mechanism
function safeInitializeFrameDesign() {
    console.log('🔧 Safe initialization of frame design...');
    
    // Check if all dependencies are available
    const dependencies = {
        photoboxCode: typeof photoboxCode !== 'undefined' && photoboxCode !== null && photoboxCode !== '',
        axios: typeof axios !== 'undefined',
        hideAllStates: typeof hideAllStates === 'function',
        showState: typeof showState === 'function'
    };
    
    console.log('📋 Dependencies check:', dependencies);
    
    const allDependenciesReady = Object.values(dependencies).every(dep => dep === true);
    
    if (allDependenciesReady) {
        console.log('✅ All dependencies ready, initializing frame design');
        initializeFrameDesign();
    } else {
        console.warn('⚠️  Some dependencies not ready, retrying in 200ms...');
        setTimeout(safeInitializeFrameDesign, 200);
    }
}

// Make safe initialization available globally too
window.safeInitializeFrameDesign = safeInitializeFrameDesign;

// Debug log
console.log('✅ Frame Design JS loaded, initializeFrameDesign defined:', typeof initializeFrameDesign);
console.log('✅ Window.initializeFrameDesign:', typeof window.initializeFrameDesign);
console.log('✅ SafeInitializeFrameDesign:', typeof safeInitializeFrameDesign);
</script>
