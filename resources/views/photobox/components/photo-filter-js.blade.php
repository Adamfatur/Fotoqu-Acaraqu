{{-- Photo Filter Management JavaScript --}}
<script>
// Photo Filter State Management
let currentPhotoIndex = 0;
let photoFilters = {}; // Store filter for each photo
let applyToAllMode = false;
let filterCanvas = null;
let filterContext = null;
let originalImageData = null; // Store original image data for filter application

// Photo filter functions
const FILTERS = {
    none: (imageData) => imageData,
    
    vivid: (imageData) => {
        const data = imageData.data;
        for (let i = 0; i < data.length; i += 4) {
            // Increase saturation and brightness
            data[i] = Math.min(255, data[i] * 1.3);     // Red
            data[i + 1] = Math.min(255, data[i + 1] * 1.3); // Green
            data[i + 2] = Math.min(255, data[i + 2] * 1.3); // Blue
        }
        return imageData;
    },
    
    dramatic: (imageData) => {
        const data = imageData.data;
        for (let i = 0; i < data.length; i += 4) {
            // High contrast
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            
            data[i] = r > 128 ? Math.min(255, r * 1.5) : Math.max(0, r * 0.5);
            data[i + 1] = g > 128 ? Math.min(255, g * 1.5) : Math.max(0, g * 0.5);
            data[i + 2] = b > 128 ? Math.min(255, b * 1.5) : Math.max(0, b * 0.5);
        }
        return imageData;
    },
    
    blackwhite: (imageData) => {
        const data = imageData.data;
        for (let i = 0; i < data.length; i += 4) {
            const gray = data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114;
            data[i] = gray;     // Red
            data[i + 1] = gray; // Green
            data[i + 2] = gray; // Blue
        }
        return imageData;
    },
    
    sepia: (imageData) => {
        const data = imageData.data;
        for (let i = 0; i < data.length; i += 4) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            
            data[i] = Math.min(255, (r * 0.393) + (g * 0.769) + (b * 0.189));
            data[i + 1] = Math.min(255, (r * 0.349) + (g * 0.686) + (b * 0.168));
            data[i + 2] = Math.min(255, (r * 0.272) + (g * 0.534) + (b * 0.131));
        }
        return imageData;
    },
    
    cool: (imageData) => {
        const data = imageData.data;
        for (let i = 0; i < data.length; i += 4) {
            data[i] = Math.max(0, data[i] * 0.8);     // Reduce red
            data[i + 1] = Math.min(255, data[i + 1] * 1.1); // Slight green boost
            data[i + 2] = Math.min(255, data[i + 2] * 1.2); // Blue boost
        }
        return imageData;
    },
    
    warm: (imageData) => {
        const data = imageData.data;
        for (let i = 0; i < data.length; i += 4) {
            data[i] = Math.min(255, data[i] * 1.2);     // Red boost
            data[i + 1] = Math.min(255, data[i + 1] * 1.1); // Slight green boost
            data[i + 2] = Math.max(0, data[i + 2] * 0.8);   // Reduce blue
        }
        return imageData;
    },
    
    negative: (imageData) => {
        const data = imageData.data;
        for (let i = 0; i < data.length; i += 4) {
            data[i] = 255 - data[i];     // Red
            data[i + 1] = 255 - data[i + 1]; // Green
            data[i + 2] = 255 - data[i + 2]; // Blue
        }
        return imageData;
    }
};

// Initialize photo filter state
function initializePhotoFilter() {
    console.log('Initializing photo filter state...');
    
    if (!selectedPhotos || selectedPhotos.length === 0) {
        console.error('No photos selected for filtering');
        return;
    }

    // Initialize canvas
    filterCanvas = document.getElementById('photo-preview-canvas');
    if (filterCanvas) {
        filterContext = filterCanvas.getContext('2d');
        
        // Add resize handler for responsive canvas
        window.addEventListener('resize', debounce(handleCanvasResize, 300));
    }

    // Reset filters
    photoFilters = {};
    selectedPhotos.forEach((photo, index) => {
        photoFilters[index] = 'none';
    });

    // Reset UI state
    currentPhotoIndex = 0;
    applyToAllMode = false;
    
    // Update UI
    updatePhotoFilterUI();
    loadCurrentPhoto();
    
    // Set default filter as active
    setActiveFilter('none');
}

// Handle canvas resize for responsiveness
function handleCanvasResize() {
    if (filterCanvas && selectedPhotos && selectedPhotos[currentPhotoIndex]) {
        console.log('Resizing canvas for responsive layout');
        loadCurrentPhoto(); // Reload current photo with new size
    }
}

// Debounce utility for resize events
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Load current photo in the canvas
function loadCurrentPhoto() {
    console.log('Loading current photo, index:', currentPhotoIndex);
    console.log('Selected photos:', selectedPhotos);
    
    if (!selectedPhotos || !selectedPhotos[currentPhotoIndex]) {
        console.error('No photo at current index:', currentPhotoIndex);
        return;
    }

    const photo = selectedPhotos[currentPhotoIndex];
    console.log('Current photo object:', photo);
    
    // Build photo URL with proper fallbacks
    let photoUrl = null;
    
    if (photo.url) {
        photoUrl = photo.url;
    } else if (photo.public_url) {
        photoUrl = photo.public_url;
    } else if (photo.file_path) {
        photoUrl = photo.file_path;
    } else if (photo.filename) {
        // Try multiple URL patterns
        const patterns = [
            `/storage/photos/${photo.filename}`,
            `/storage/sessions/${photo.session_id}/${photo.filename}`,
            `/photobox/photo/${photo.id}`,
            `/storage/app/public/photos/${photo.filename}`
        ];
        
        // Use first available pattern
        photoUrl = patterns[0];
    } else if (photo.id) {
        // Fallback to photo ID endpoint
        photoUrl = `/photobox/photo/${photo.id}`;
    } else {
        console.error('No valid URL found for photo:', photo);
        // Use placeholder image
        photoUrl = '/images/placeholder-photo.svg';
    }
    
    console.log('Using photo URL:', photoUrl);
    
    const img = new Image();
    
    img.onload = function() {
        console.log('Photo loaded successfully, dimensions:', img.width, 'x', img.height);
        
        // Calculate optimal canvas size for better preview
        const containerWidth = filterCanvas.parentElement.clientWidth - 20; // Account for padding
        const containerHeight = window.innerHeight * 0.6; // Use 60% of viewport height
        
        const maxWidth = Math.min(800, containerWidth); // Max 800px or container width
        const maxHeight = Math.min(600, containerHeight); // Max 600px or container height
        
        let { width, height } = img;
        
        // Calculate aspect ratio and scale down if needed
        const aspectRatio = width / height;
        
        if (width > maxWidth) {
            width = maxWidth;
            height = width / aspectRatio;
        }
        
        if (height > maxHeight) {
            height = maxHeight;
            width = height * aspectRatio;
        }
        
        // Set canvas size
        filterCanvas.width = width;
        filterCanvas.height = height;
        
        // Set canvas CSS size for proper display
        filterCanvas.style.width = width + 'px';
        filterCanvas.style.height = height + 'px';
        
        console.log('Canvas sized to:', width, 'x', height);
        
        // Draw original image
        filterContext.drawImage(img, 0, 0, width, height);
        
        // Store original image data for filter resets
        originalImageData = filterContext.getImageData(0, 0, width, height);
        
        // Apply current filter
        const currentFilter = photoFilters[currentPhotoIndex] || 'none';
        applyFilterToCanvas(currentFilter);
    };
    
    img.onerror = function() {
        console.error('Failed to load photo from URL:', photoUrl);
        
        // Show error placeholder in canvas with larger size
        if (filterCanvas && filterContext) {
            const containerWidth = filterCanvas.parentElement.clientWidth - 20;
            const containerHeight = window.innerHeight * 0.6;
            
            const canvasWidth = Math.min(600, containerWidth);
            const canvasHeight = Math.min(400, containerHeight);
            
            filterCanvas.width = canvasWidth;
            filterCanvas.height = canvasHeight;
            filterCanvas.style.width = canvasWidth + 'px';
            filterCanvas.style.height = canvasHeight + 'px';
            
            filterContext.fillStyle = '#f3f4f6';
            filterContext.fillRect(0, 0, canvasWidth, canvasHeight);
            
            filterContext.fillStyle = '#6b7280';
            filterContext.font = '18px sans-serif';
            filterContext.textAlign = 'center';
            filterContext.fillText('Foto tidak dapat dimuat', canvasWidth / 2, canvasHeight / 2 - 30);
            filterContext.font = '14px sans-serif';
            filterContext.fillText(`Photo #${currentPhotoIndex + 1}`, canvasWidth / 2, canvasHeight / 2);
            filterContext.font = '12px sans-serif';
            filterContext.fillText('URL: ' + (photoUrl.length > 50 ? photoUrl.substring(0, 50) + '...' : photoUrl), canvasWidth / 2, canvasHeight / 2 + 30);
        }
    };
    
    img.crossOrigin = 'anonymous'; // Allow canvas manipulation
    img.src = photoUrl;
}

// Apply filter to canvas
function applyFilterToCanvas(filterName) {
    if (!filterCanvas || !filterContext || !originalImageData) return;
    
    // Always start from original image data to prevent filter stacking
    const clonedImageData = new ImageData(
        new Uint8ClampedArray(originalImageData.data),
        originalImageData.width,
        originalImageData.height
    );
    
    // Apply the selected filter to the cloned original data
    const filteredData = FILTERS[filterName] ? FILTERS[filterName](clonedImageData) : clonedImageData;
    filterContext.putImageData(filteredData, 0, 0);
}

// Apply filter to current photo
function applyFilter(filterName) {
    console.log('Applying filter:', filterName);
    
    if (applyToAllMode) {
        // Apply to all photos
        selectedPhotos.forEach((photo, index) => {
            photoFilters[index] = filterName;
        });
    } else {
        // Apply to current photo only
        photoFilters[currentPhotoIndex] = filterName;
    }
    
    // Update canvas
    applyFilterToCanvas(filterName);
    
    // Update UI
    setActiveFilter(filterName);
    updateFilterStatusDisplay(filterName);
}

// Set active filter in UI
function setActiveFilter(filterName) {
    document.querySelectorAll('.filter-option').forEach(option => {
        option.classList.remove('active');
        const card = option.querySelector('div');
        card.classList.remove('border-green-400', 'bg-green-500/10');
        card.classList.add('border-transparent');
    });
    
    const activeOption = document.querySelector(`[data-filter="${filterName}"]`);
    if (activeOption) {
        activeOption.classList.add('active');
        const card = activeOption.querySelector('div');
        card.classList.remove('border-transparent');
        card.classList.add('border-green-400', 'bg-green-500/10');
    }
}

// Update filter status display
function updateFilterStatusDisplay(filterName) {
    const filterNames = {
        'none': 'Tanpa Filter',
        'vivid': 'Vivid',
        'dramatic': 'Dramatic',
        'blackwhite': 'Black & White',
        'sepia': 'Sepia',
        'cool': 'Cool',
        'warm': 'Warm',
        'negative': 'Negative'
    };
    
    const display = document.getElementById('current-filter-name');
    if (display) {
        display.textContent = filterNames[filterName] || 'Unknown';
    }
}

// Navigation functions
function previousPhoto() {
    if (currentPhotoIndex > 0) {
        currentPhotoIndex--;
        console.log('Previous photo - new index:', currentPhotoIndex);
        updatePhotoFilterUI();
        loadCurrentPhoto();
        
        // Update active filter based on current photo
        const currentFilter = photoFilters[currentPhotoIndex] || 'none';
        setActiveFilter(currentFilter);
        updateFilterStatusDisplay(currentFilter);
    }
}

function nextPhoto() {
    if (currentPhotoIndex < selectedPhotos.length - 1) {
        currentPhotoIndex++;
        console.log('Next photo - new index:', currentPhotoIndex);
        updatePhotoFilterUI();
        loadCurrentPhoto();
        
        // Update active filter based on current photo
        const currentFilter = photoFilters[currentPhotoIndex] || 'none';
        setActiveFilter(currentFilter);
        updateFilterStatusDisplay(currentFilter);
    }
}

// Toggle apply to all mode
function toggleApplyToAll() {
    applyToAllMode = !applyToAllMode;
    
    const button = document.getElementById('apply-to-all-btn');
    const text = document.getElementById('apply-to-all-text');
    
    if (applyToAllMode) {
        button.classList.add('bg-green-600/80', 'hover:bg-green-700/80', 'border-green-400');
        button.classList.remove('bg-blue-600/80', 'hover:bg-blue-700/80', 'border-blue-400');
        text.textContent = 'Mode: Semua Foto';
    } else {
        button.classList.add('bg-blue-600/80', 'hover:bg-blue-700/80', 'border-blue-400');
        button.classList.remove('bg-green-600/80', 'hover:bg-green-700/80', 'border-green-400');
        text.textContent = 'Filter ke Semua';
    }
}

// Reset all filters
function resetAllFilters() {
    selectedPhotos.forEach((photo, index) => {
        photoFilters[index] = 'none';
    });
    
    loadCurrentPhoto();
    setActiveFilter('none');
    updateFilterStatusDisplay('none');
    
    // Reset apply to all mode
    if (applyToAllMode) {
        toggleApplyToAll();
    }
}

// Update photo filter UI
function updatePhotoFilterUI() {
    const currentIndex = document.getElementById('current-photo-index');
    const totalPhotos = document.getElementById('total-selected-photos');
    const prevBtn = document.getElementById('prev-photo-btn');
    const nextBtn = document.getElementById('next-photo-btn');
    
    if (currentIndex) currentIndex.textContent = currentPhotoIndex + 1;
    if (totalPhotos) totalPhotos.textContent = selectedPhotos.length;
    
    // Update navigation buttons
    if (prevBtn) {
        prevBtn.disabled = currentPhotoIndex === 0;
        prevBtn.style.opacity = currentPhotoIndex === 0 ? '0.5' : '1';
    }
    
    if (nextBtn) {
        nextBtn.disabled = currentPhotoIndex === selectedPhotos.length - 1;
        nextBtn.style.opacity = currentPhotoIndex === selectedPhotos.length - 1 ? '0.5' : '1';
    }
}

// Navigation functions for photo filter state
function backToFrameSelection() {
    hideAllStates();
    showState('frame-design-state');
}

function proceedToProcessing() {
    // Show loading overlay instead of button change
    const processingOverlay = document.getElementById('filter-processing-indicator');
    
    if (processingOverlay) {
        processingOverlay.classList.remove('hidden');
    }
    
    // Store filter data in session data
    window.sessionData = window.sessionData || {};
    window.sessionData.selectedFrame = selectedFrameDesign;
    window.sessionData.photoFilters = photoFilters;
    
    console.log('=== STORING SESSION DATA ===');
    console.log('Storing Selected Frame:', selectedFrameDesign);
    console.log('Storing Photo Filters:', photoFilters);
    console.log('Storing Selected Photos:', selectedPhotos);
    console.log('Final sessionData:', window.sessionData);
    
    // Add a delay for better UX feedback
    setTimeout(() => {
        // Hide processing overlay
        if (processingOverlay) {
            processingOverlay.classList.add('hidden');
        }
        
        // Proceed to processing
        hideAllStates();
        showState('processing-state');
        
        // Start processing with frame and filters
        setTimeout(() => {
            console.log('=== DEBUG: CALLING startProcessing ===');
            console.log('About to call startProcessing...');
            console.log('window.startProcessing type:', typeof window.startProcessing);
            console.log('window.startProcessing value:', window.startProcessing);
            console.log('startProcessing in window:', 'startProcessing' in window);
            console.log('window properties with "process":', Object.getOwnPropertyNames(window).filter(name => name.toLowerCase().includes('process')));
            
            if (typeof window.startProcessing === 'function') {
                console.log('✅ Calling window.startProcessing...');
                try {
                    window.startProcessing();
                    console.log('✅ startProcessing called successfully');
                } catch (error) {
                    console.error('❌ Error calling startProcessing:', error);
                }
            } else {
                console.error('❌ window.startProcessing is not a function! Type:', typeof window.startProcessing);
                console.error('Available window functions with "Process":', Object.getOwnPropertyNames(window).filter(name => name.toLowerCase().includes('process')));
                
                // Try to find the function elsewhere
                console.log('Searching for startProcessing in global scope...');
                if (typeof startProcessing !== 'undefined') {
                    console.log('Found startProcessing in global scope, calling it...');
                    startProcessing();
                } else {
                    console.error('startProcessing not found anywhere!');
                    alert('Terjadi kesalahan sistem. Silakan refresh halaman dan coba lagi.');
                }
            }
        }, 100); // Wait 100ms for all scripts to load
    }, 1500);
}

// Cleanup function for photo filter state
function cleanupPhotoFilter() {
    console.log('Cleaning up photo filter state...');
    
    // Remove resize event listener
    if (window.handleCanvasResize) {
        window.removeEventListener('resize', window.handleCanvasResize);
    }
    
    // Clear canvas
    if (filterCanvas && filterContext) {
        filterContext.clearRect(0, 0, filterCanvas.width, filterCanvas.height);
    }
    
    // Reset global variables
    currentPhotoIndex = 0;
    photoFilters = {};
    applyToAllMode = false;
    filterCanvas = null;
    filterContext = null;
    originalImageData = null; // Reset original image data
}

// Clean up when leaving photo filter state
window.addEventListener('beforeunload', cleanupPhotoFilter);

// Debug helper for testing filter functionality
function testPhotoFilter() {
    console.log('=== TESTING PHOTO FILTER ===');
    console.log('Current photo index:', currentPhotoIndex);
    console.log('Photo filters:', photoFilters);
    console.log('Apply to all mode:', applyToAllMode);
    console.log('Filter canvas:', filterCanvas);
    console.log('Filter context:', filterContext);
    console.log('Selected photos:', selectedPhotos);
    
    if (selectedPhotos && selectedPhotos.length > 0) {
        console.log('Current photo object:', selectedPhotos[currentPhotoIndex]);
    }
}

// Debug function to test photo loading manually
function debugLoadPhoto(photoIndex = 0) {
    console.log('=== DEBUG LOAD PHOTO ===');
    
    if (!selectedPhotos || selectedPhotos.length === 0) {
        console.error('No selected photos available');
        return;
    }
    
    if (photoIndex >= selectedPhotos.length) {
        console.error('Photo index out of range:', photoIndex, 'Max:', selectedPhotos.length - 1);
        return;
    }
    
    currentPhotoIndex = photoIndex;
    console.log('Loading photo at index:', photoIndex);
    console.log('Photo data:', selectedPhotos[photoIndex]);
    
    loadCurrentPhoto();
}

// Test with mock data
function setupMockPhotos() {
    console.log('=== SETTING UP MOCK PHOTOS ===');
    selectedPhotos = [
        { id: 1, url: '/images/placeholder-photo.jpg', filename: 'placeholder-photo.jpg' },
        { id: 2, url: '/images/placeholder-photo.jpg', filename: 'placeholder-photo.jpg' },
        { id: 3, url: '/images/placeholder-photo.jpg', filename: 'placeholder-photo.jpg' },
        { id: 4, url: '/images/placeholder-photo.jpg', filename: 'placeholder-photo.jpg' }
    ];
    console.log('Mock photos set:', selectedPhotos);
    
    initializePhotoFilter();
}

// Test navigation functionality
function testNavigation() {
    console.log('=== TESTING NAVIGATION ===');
    console.log('Current photo index:', currentPhotoIndex);
    console.log('Total photos:', selectedPhotos.length);
    console.log('Previous button disabled:', currentPhotoIndex === 0);
    console.log('Next button disabled:', currentPhotoIndex === selectedPhotos.length - 1);
    
    // Test next
    if (currentPhotoIndex < selectedPhotos.length - 1) {
        console.log('Testing next photo...');
        nextPhoto();
    } else {
        console.log('Cannot go to next photo - at end');
    }
}

// Test filter application  
function testFilterApplication() {
    console.log('=== TESTING FILTER APPLICATION ===');
    console.log('Original image data available:', !!originalImageData);
    console.log('Current filter:', photoFilters[currentPhotoIndex] || 'none');
    
    // Test applying a filter
    console.log('Applying vivid filter...');
    applyFilter('vivid');
    
    setTimeout(() => {
        console.log('Applying sepia filter...');
        applyFilter('sepia');
        
        setTimeout(() => {
            console.log('Resetting to none filter...');
            applyFilter('none');
        }, 1000);
    }, 1000);
}

// Expose for console testing - only in development
@if(config('app.debug'))
window.testPhotoFilter = testPhotoFilter;
window.debugLoadPhoto = debugLoadPhoto;
window.setupMockPhotos = setupMockPhotos;
window.testNavigation = testNavigation;
window.testFilterApplication = testFilterApplication;
@endif
</script>
